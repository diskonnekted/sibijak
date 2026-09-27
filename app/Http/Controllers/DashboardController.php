<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Project;
use App\Models\ProjectLog;
use App\Models\ProjectPhoto;
use App\Models\Training;
use App\Models\Regulation;
use App\Models\NewsItem;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\RuasJalanService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    // Public landing page (Beranda)
    public function index(Request $request)
    {
        $stats = [
            'total_contractors' => Contractor::count(),
            'total_projects' => Project::count(),
            'total_contract_value' => Project::sum('nilai_kontrak'),
            'average_progress' => Project::avg('progress') ?? 0,
        ];
        $recentNews = NewsItem::orderBy('tanggal', 'desc')->take(3)->get();
        return view('portal', compact('stats', 'recentNews'));
    }

    // SIBIJAK: Badan Usaha Page
    public function badanUsaha(Request $request)
    {
        $search = $request->input('search');
        $bidang = $request->input('bidang');
        $kualifikasi = $request->input('kualifikasi');

        $query = Contractor::query();

        // Hanya badan usaha yang sudah disetujui admin yang tampil di halaman publik
        $query->where('status', 'approved');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('pj', 'like', "%{$search}%");
        }

        if ($bidang && $bidang !== 'all') {
            $query->where('bidang', $bidang);
        }

        if ($kualifikasi && $kualifikasi !== 'all') {
            $query->where('kualifikasi', $kualifikasi);
        }

        $contractors = $query->with('projects')->get();

        return view('sibijak.badanusaha', compact('contractors'));
    }

    // Public Contractor Detail Page with annual archives and visual documentation
    public function publicShowContractor(Contractor $contractor)
    {
        $contractor->load(['projects', 'trainings']);
        
        // Group projects by year
        $annualArchive = $contractor->projects->groupBy('tahun_anggaran')->sortKeysDesc();
        
        $stats = [
            'total_projects' => $contractor->projects->count(),
            'completed_projects' => $contractor->projects->where('status', 'Selesai')->count(),
            'total_value' => $contractor->projects->sum('nilai_kontrak'),
            'average_progress' => $contractor->projects->avg('progress') ?? 0,
        ];
        
        return view('sibijak.badanusaha-detail', compact('contractor', 'annualArchive', 'stats'));
    }

    // Public Project Detail Page with Leaflet map and visual documentation
    public function publicShowProject(Project $project)
    {
        $project->load(['contractor', 'logs', 'photos']);
        return view('sibijak.pekerjaan-detail', compact('project'));
    }

    // SIBIJAK: Pelatihan Page
    public function pelatihan()
    {
        $trainings = Training::orderBy('tanggal', 'asc')->get();
        return view('sibijak.pelatihan', compact('trainings'));
    }

    // SIBIJAK: Regulasi Page
    public function regulasi(Request $request)
    {
        $kategori = $request->input('kategori');
        $query = Regulation::query();

        if ($kategori && $kategori !== 'all') {
            $query->where('kategori', $kategori);
        }

        $regulations = $query->orderBy('tahun', 'desc')->get();
        $kategoriList = static::regulationCategories();
        return view('sibijak.regulasi', compact('regulations', 'kategoriList'));
    }

    // SIBIJAK: Berita Page
    public function berita()
    {
        $kategori = request('kategori');
        $query = NewsItem::where('status', 'publish');
        if ($kategori && $kategori !== 'all') {
            $query->where('kategori', $kategori);
        }
        $news = $query->orderBy('tanggal', 'desc')->get();
        $kategoriBerita = static::newsCategories();

        return view('sibijak.berita', compact('news', 'kategoriBerita'));
    }

    // SIBIJAK: Pendaftaran Page
    public function daftar()
    {
        $trainings = Training::where('status', 'Mendatang')->get();
        return view('sibijak.daftar', compact('trainings'));
    }

    // Submit Pendaftaran Badan Usaha (Kontraktor)
    public function submitDaftarBadanUsaha(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bidang' => 'required|string',
            'kualifikasi' => 'required|string',
            'nib' => 'required|string|unique:contractors,nib',
            'pj' => 'required|string|max:255',
            'alamat' => 'required|string',
            'email' => 'required|email|max:255',
            'telepon' => 'nullable|string',
        ]);

        $validated['rating'] = 5.0;
        // Pendaftaran baru masuk status "pending" - harus diverifikasi admin dulu
        // sebelum tampil di halaman publik dan sebelum akun login dibuat
        $validated['status'] = 'pending';

        Contractor::create($validated);

        // Catat ke audit trail
        ActivityLog::log('registration', $validated['name'] . ' mendaftar badan usaha (menunggu verifikasi).');

        return redirect()->route('daftar')->with('success', 'Pendaftaran Badan Usaha berhasil diajukan. Data Anda akan diverifikasi admin dalam 1-3 hari kerja.');
    }

    // Submit Pendaftaran Pelatihan
    public function submitDaftarPelatihan(Request $request)
    {
        $validated = $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'nama_peserta' => 'required|string|max:255',
            'nik' => 'required|string|max:16',
            'instansi' => 'required|string|max:255',
            'kontak' => 'required|string|max:15',
        ]);

        $training = Training::find($validated['training_id']);

        if ($training->pendaftar_count >= $training->kuota) {
            return redirect()->back()->with('error', 'Mohon maaf, kuota pelatihan sudah penuh.');
        }

        // Cegah pendaftaran ganda dengan NIK yang sama di pelatihan yang sama
        $sudahTerdaftar = $training->participants()->where('nik', $validated['nik'])->exists();
        if ($sudahTerdaftar) {
            return redirect()->back()->with('error', 'NIK tersebut sudah terdaftar pada pelatihan ini.');
        }

        // Simpan data peserta secara lengkap (nama/NIK/instansi/kontak) - tidak lagi dibuang
        $training->participants()->create($validated);

        // Jaga pendaftar_count tetap sinkron dengan jumlah peserta sebenarnya
        $training->increment('pendaftar_count');

        return redirect()->route('daftar')->with('success', 'Pendaftaran Pelatihan berhasil.');
    }

    // Helper: Anti Brute-Force Rate Limiter Key
    private function throttleKey(Request $request): string
    {
        return 'login_attempt:' . Str::lower($request->input('email')) . '|' . $request->ip();
    }

    // AUTH: Show Portal Hub Login
    public function showLogin()
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role === 'kontraktor') return redirect()->route('mobile.kontraktor');
            if ($user->role === 'pemeriksa_lapangan') return redirect()->route('mobile.pengawas');
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    // AUTH: Show Admin Login
    public function showAdminLogin()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login-admin');
    }

    // AUTH: Attempt Admin Login
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            ActivityLog::log('LOGIN_BLOCKED', 'Akses login Admin PUPR diblokir sementara (terlalu banyak percobaan)', null, $request);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login gagal (10x). Akses diblokir sementara selama {$minutes} menit.",
            ])->onlyInput('email');
        }

        if (auth()->attempt($credentials, $request->has('remember'))) {
            $user = auth()->user();
            if ($user->role !== 'admin_pupr') {
                auth()->logout();
                RateLimiter::hit($key, 900); // 15 mins decay
                ActivityLog::log('LOGIN_FAILED', 'Percobaan login Admin PUPR gagal: Akun tidak memiliki peran admin_pupr', $user, $request);
                return back()->withErrors([
                    'email' => 'Kredensial ini tidak memiliki hak akses sebagai Admin PUPR.',
                ])->onlyInput('email');
            }

            RateLimiter::clear($key);
            $request->session()->regenerate();
            ActivityLog::log('LOGIN_SUCCESS', 'Berhasil masuk ke sistem sebagai Admin PUPR', $user, $request);
            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($key, 900);
        ActivityLog::log('LOGIN_FAILED', 'Percobaan login Admin PUPR gagal: Kredensial tidak cocok', null, $request);

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    // AUTH: Show Kontraktor Login
    public function showKontraktorLogin()
    {
        if (auth()->check()) {
            return redirect()->route('kontraktor.dashboard');
        }
        return view('auth.login-kontraktor');
    }

    // AUTH: Attempt Kontraktor Login
    public function kontraktorLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            ActivityLog::log('LOGIN_BLOCKED', 'Akses login Kontraktor diblokir sementara (terlalu banyak percobaan)', null, $request);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login gagal (10x). Akses diblokir sementara selama {$minutes} menit.",
            ])->onlyInput('email');
        }

        if (auth()->attempt($credentials, $request->has('remember'))) {
            $user = auth()->user();
            if ($user->role !== 'kontraktor') {
                auth()->logout();
                RateLimiter::hit($key, 900);
                ActivityLog::log('LOGIN_FAILED', 'Percobaan login Kontraktor gagal: Akun tidak memiliki peran kontraktor', $user, $request);
                return back()->withErrors([
                    'email' => 'Kredensial ini tidak memiliki hak akses sebagai Kontraktor.',
                ])->onlyInput('email');
            }

            RateLimiter::clear($key);
            $request->session()->regenerate();
            ActivityLog::log('LOGIN_SUCCESS', 'Berhasil masuk ke sistem sebagai Kontraktor', $user, $request);
            return redirect()->intended(route('kontraktor.dashboard'));
        }

        RateLimiter::hit($key, 900);
        ActivityLog::log('LOGIN_FAILED', 'Percobaan login Kontraktor gagal: Kredensial tidak cocok', null, $request);

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    // AUTH: Show Pengawas Login
    public function showPengawasLogin()
    {
        if (auth()->check()) {
            return redirect()->route('pengawas.dashboard');
        }
        return view('auth.login-pengawas');
    }

    // AUTH: Attempt Pengawas Login
    public function pengawasLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            ActivityLog::log('LOGIN_BLOCKED', 'Akses login Pengawas diblokir sementara (terlalu banyak percobaan)', null, $request);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login gagal (10x). Akses diblokir sementara selama {$minutes} menit.",
            ])->onlyInput('email');
        }

        if (auth()->attempt($credentials, $request->has('remember'))) {
            $user = auth()->user();
            if ($user->role !== 'pemeriksa_lapangan') {
                auth()->logout();
                RateLimiter::hit($key, 900);
                ActivityLog::log('LOGIN_FAILED', 'Percobaan login Pengawas gagal: Akun tidak memiliki peran pemeriksa_lapangan', $user, $request);
                return back()->withErrors([
                    'email' => 'Kredensial ini tidak memiliki hak akses sebagai Pengawas Lapangan.',
                ])->onlyInput('email');
            }

            RateLimiter::clear($key);
            $request->session()->regenerate();
            ActivityLog::log('LOGIN_SUCCESS', 'Berhasil masuk ke sistem sebagai Pengawas Lapangan', $user, $request);
            return redirect()->intended(route('pengawas.dashboard'));
        }

        RateLimiter::hit($key, 900);
        ActivityLog::log('LOGIN_FAILED', 'Percobaan login Pengawas gagal: Kredensial tidak cocok', null, $request);

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    // AUTH: Attempt Generic Login (Portal Hub Fallback)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login gagal (10x). Akses diblokir sementara selama {$minutes} menit.",
            ])->onlyInput('email');
        }

        if (auth()->attempt($credentials, $request->has('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            
            $user = auth()->user();
            if ($user->role === 'kontraktor') {
                return redirect()->route('kontraktor.dashboard');
            } elseif ($user->role === 'pemeriksa_lapangan') {
                return redirect()->route('pengawas.dashboard');
            }
            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($key, 900);

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    // AUTH: Log Out
    public function logout(Request $request)
    {
        if (auth()->check()) {
            ActivityLog::log('LOGOUT', 'Pengguna keluar dari sistem', auth()->user(), $request);
        }
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal');
    }

    // Admin dashboard with multi-tenant filtering based on roles
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $role = $user ? $user->role : 'admin_pupr';

        if ($role === 'kontraktor') {
            // Contractors only see themselves and their own projects
            $contractors = Contractor::where('id', $user->contractor_id)->withCount('projects')->get();
            $projects = Project::where('contractor_id', $user->contractor_id)->with('contractor')->get();
            
            $stats = [
                'total_contractors' => 1,
                'total_projects' => $projects->count(),
                'total_contract_value' => $projects->sum('nilai_kontrak'),
                'average_progress' => $projects->avg('progress') ?? 0,
            ];
        } else {
            // Admin PUPR and Field Examiner see all records
            $contractors = Contractor::withCount('projects')->get();
            $projects = Project::with('contractor')->get();
            
            $stats = [
                'total_contractors' => Contractor::count(),
                'total_projects' => Project::count(),
                'total_contract_value' => Project::sum('nilai_kontrak'),
                'average_progress' => Project::avg('progress') ?? 0,
            ];
        }

        // Filter tahun anggaran (optional) untuk menyaring daftar paket pekerjaan
        $selectedTahun = $request->input('tahun');

        // Daftar proyek terfilter berdasarkan tahun anggaran (jika dipilih)
        $projects = Project::query()
            ->when($role === 'kontraktor', fn ($q) => $q->where('contractor_id', $user->contractor_id))
            ->when($selectedTahun, fn ($q) => $q->where('tahun_anggaran', $selectedTahun))
            ->with('contractor')
            ->get();

        // Daftar tahun anggaran unik untuk dropdown filter
        $availableYears = Project::query()
            ->when($role === 'kontraktor', fn ($q) => $q->where('contractor_id', $user->contractor_id))
            ->select('tahun_anggaran')
            ->distinct()
            ->orderByDesc('tahun_anggaran')
            ->pluck('tahun_anggaran')
            ->filter()
            ->values();

        // Pendaftaran badan usaha yang menunggu verifikasi admin
        $pendingContractors = Contractor::where('status', 'pending')->latest()->get();

        // Kontraktor terverifikasi (untuk dropdown penugasan proyek)
        $approvedContractors = Contractor::where('status', 'approved')->orderBy('name')->get();

        $trainings = Training::all();
        $regulations = Regulation::all();
        $news = NewsItem::all();

        // Approval berlapis:
        // Lapisan 1 — menunggu verifikasi pengawas lapangan (pengawas_verified_at kosong)
        $pendingVerifications = Project::where('verification_status', 'pending')
            ->whereNull('pengawas_verified_at')
            ->with('contractor')
            ->get();

        // Lapisan 2 — sudah diverifikasi pengawas, menunggu persetujuan final admin
        $pendingFinalVerifications = Project::where('verification_status', 'pending')
            ->whereNotNull('pengawas_verified_at')
            ->whereNull('final_verified_at')
            ->with('contractor')
            ->get();

        // Get upcoming deadlines for projects not yet finished
        $upcomingDeadlines = Project::whereIn('status', ['Persiapan', 'Pelaksanaan'])
            ->whereNotNull('tanggal_deadline')
            ->with('contractor')
            ->orderBy('tanggal_deadline', 'asc')
            ->take(5)
            ->get();

        // Get recent activity logs for mobile audit tab preview
        $activityLogs = ActivityLog::with('user')->orderBy('created_at', 'desc')->take(10)->get();

        // Daftar ruas jalan (geojson) untuk penautan lokasi proyek
        $ruasList = RuasJalanService::all();
        $ruasStats = RuasJalanService::stats();

        // Manajemen Pengawas Lapangan (role pemeriksa_lapangan)
        $pengawas = User::where('role', 'pemeriksa_lapangan')->orderBy('name')->get();
        $bidangPengawas = static::pengawasBidang();

        return view('admin.dashboard', compact('contractors', 'projects', 'stats', 'trainings', 'regulations', 'news', 'pendingVerifications', 'pendingFinalVerifications', 'upcomingDeadlines', 'activityLogs', 'pendingContractors', 'approvedContractors', 'ruasList', 'ruasStats', 'selectedTahun', 'availableYears', 'pengawas', 'bidangPengawas'));
    }

    protected static function pengawasBidang(): array
    {
        return [
            'Bina Marga',
            'Cipta Karya',
            'Sumber Daya Air',
            'Perumahan & Permukiman',
            'Jasa Konstruksi Umum',
        ];
    }

    public function storePengawas(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'nip' => 'nullable|string|max:50',
            'bidang' => 'nullable|string|max:100',
            'password' => 'required|string|min:6',
        ]);

        $validated['role'] = 'pemeriksa_lapangan';

        User::create($validated);

        ActivityLog::log('create_pengawas', 'Menambahkan pengawas lapangan: ' . $validated['name']);

        return redirect()->route('admin.dashboard')->with('success', 'Pengawas lapangan berhasil ditambahkan.');
    }

    public function updatePengawas(Request $request, User $pengawas)
    {
        if ($pengawas->role !== 'pemeriksa_lapangan') {
            return redirect()->route('admin.dashboard')->with('error', 'Pengguna ini bukan pengawas lapangan.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $pengawas->id,
            'nip' => 'nullable|string|max:50',
            'bidang' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $pengawas->update($validated);

        ActivityLog::log('update_pengawas', 'Memperbarui data pengawas lapangan: ' . $validated['name']);

        return redirect()->route('admin.dashboard')->with('success', 'Data pengawas lapangan berhasil diperbarui.');
    }

    public function deletePengawas(User $pengawas)
    {
        if ($pengawas->role !== 'pemeriksa_lapangan') {
            return redirect()->route('admin.dashboard')->with('error', 'Pengguna ini bukan pengawas lapangan.');
        }

        $nama = $pengawas->name;
        $pengawas->delete();

        ActivityLog::log('delete_pengawas', 'Menghapus pengawas lapangan: ' . $nama);

        return redirect()->route('admin.dashboard')->with('success', 'Pengawas lapangan berhasil dihapus.');
    }

    // Admin Map Monitoring Page filtered by role
    public function adminMap()
    {
        $user = auth()->user();
        $role = $user ? $user->role : 'admin_pupr';

        if ($role === 'kontraktor') {
            $projects = Project::where('contractor_id', $user->contractor_id)->with('contractor')->get();
        } else {
            $projects = Project::with('contractor')->get();
        }

        $stats = [
            'total_projects' => $projects->count(),
            'average_progress' => $projects->avg('progress') ?? 0,
        ];

        // Statistik penautan ruas jalan
        $ruasStats = [
            'total_ruas'   => RuasJalanService::stats()['total_ruas'],
            'ruas_linked'  => Project::whereNotNull('ruas_jalan_id')->distinct()->count('ruas_jalan_id'),
            'project_linked' => Project::whereNotNull('ruas_jalan_id')->count(),
        ];

        return view('admin.map', compact('projects', 'stats', 'ruasStats'));
    }

    // Admin Analysis Dashboard Page - Access Restricted for Contractors
    public function adminAnalysis()
    {
        $user = auth()->user();
        
        if ($user && $user->role === 'kontraktor') {
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Kontraktor tidak diizinkan membuka halaman analisis.');
        }

        $projects = Project::with('contractor')->get();
        $contractors = Contractor::with('projects')->get();
        
        $stats = [
            'total_projects' => $projects->count(),
            'total_contract_value' => $projects->sum('nilai_kontrak'),
            'average_progress' => $projects->avg('progress') ?? 0,
            
            // Status breakdown
            'status_persiapan' => $projects->where('status', 'Persiapan')->count(),
            'status_pelaksanaan' => $projects->where('status', 'Pelaksanaan')->count(),
            'status_selesai' => $projects->where('status', 'Selesai')->count(),

            // Rentang & distribusi nilai kontrak + per tahun anggaran (untuk grafik)
            'nilai_min' => $projects->min('nilai_kontrak') ?? 0,
            'nilai_max' => $projects->max('nilai_kontrak') ?? 0,
            'anggaran_per_tahun' => $projects->groupBy('tahun_anggaran')
                ->map(fn ($g, $tahun) => [
                    'tahun'  => $tahun,
                    'jumlah' => $g->count(),
                    'nilai'  => (float) $g->sum('nilai_kontrak'),
                ])
                ->sortKeys()
                ->values()
                ->all(),
        ];
        
        // Identify high-risk projects
        $highRiskProjects = [];
        foreach ($projects as $p) {
            if ($p->status !== 'Selesai' && $p->tanggal_deadline) {
                $deadline = \Carbon\Carbon::parse($p->tanggal_deadline);
                $daysRemaining = \Carbon\Carbon::now()->diffInDays($deadline, false);
                
                if ($daysRemaining < 90 || $p->progress < 50) {
                    $p->days_remaining = $daysRemaining;
                    $highRiskProjects[] = $p;
                }
            }
        }
        
        $recommendations = [];
        if ($stats['average_progress'] < 70) {
            $recommendations[] = [
                'tipe' => 'Warning',
                'pesan' => 'Rata-rata kemajuan fisik pekerjaan seluruh kabupaten berada di bawah target triwulan (70%). Diperlukan akselerasi pengerjaan lapangan.',
                'solusi' => 'Lakukan monitoring dan rapat koordinasi mingguan bersama penyedia jasa konstruksi terkait.'
            ];
        } else {
            $recommendations[] = [
                'tipe' => 'Info',
                'pesan' => 'Rata-rata kemajuan fisik pekerjaan tergolong baik (' . number_format($stats['average_progress'], 1) . '%). Pertahankan konsistensi audit berkala.',
                'solusi' => 'Optimalkan sistem pelaporan online pengawas bina konstruksi.'
            ];
        }
        
        foreach ($highRiskProjects as $hp) {
            if ($hp->progress < 40) {
                $recommendations[] = [
                    'tipe' => 'Danger',
                    'pesan' => "Pekerjaan '{$hp->nama_pekerjaan}' oleh pelaksana '{$hp->contractor->name}' mengalami keterlambatan kritis dengan kemajuan fisik baru {$hp->progress}%.",
                    'solusi' => "Segera terbitkan Surat Peringatan (SP-1) dan lakukan rapat Show Cause Meeting (SCM) untuk menuntut komitmen penyelesaian lapangan."
                ];
            } else {
                $recommendations[] = [
                    'tipe' => 'Warning',
                    'pesan' => "Pekerjaan '{$hp->nama_pekerjaan}' oleh pelaksana '{$hp->contractor->name}' mendekati tenggat deadline pada " . date('d M Y', strtotime($hp->tanggal_deadline)) . ".",
                    'solusi' => "Instruksikan pelaksana untuk menambah jam kerja (lembur) dan jumlah pekerja konstruksi guna mengejar ketertinggalan jadwal."
                ];
            }
        }

        foreach ($contractors as $c) {
            if ($c->rating < 3.5) {
                $recommendations[] = [
                    'tipe' => 'Warning',
                    'pesan' => "Badan usaha '{$c->name}' memiliki rating evaluasi kinerja rendah (" . number_format($c->rating, 1) . ").",
                    'solusi' => "Batasi keikutsertaan penyedia dalam lelang paket pekerjaan strategis berikutnya hingga nilai audit kualifikasinya meningkat."
                ];
            }
        }
        
        return view('admin.analysis', compact('stats', 'highRiskProjects', 'recommendations'));
    }

    // Halaman Khusus Log Aktivitas User & Audit Trail (Admin PUPR Only)
    public function activityLogs(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->role !== 'admin_pupr') {
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Log aktivitas hanya dapat diakses oleh Admin PUPR.');
        }

        $activityLogs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(25);
        return view('admin.logs', compact('activityLogs'));
    }

    // Admin Contractor Detail Page - Access Restricted for other Contractors
    public function showContractor(Contractor $contractor)
    {
        $user = auth()->user();

        if ($user && $user->role === 'kontraktor' && $user->contractor_id != $contractor->id) {
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Anda tidak diizinkan membuka data kontraktor lain.');
        }

        $contractor->load('projects');
        $totalContractValue = $contractor->projects->sum('nilai_kontrak');
        $averageProgress = $contractor->projects->avg('progress') ?? 0;
        
        return view('admin.contractor-detail', compact('contractor', 'totalContractValue', 'averageProgress'));
    }

    // CRUD Contractor - Admin PUPR Only
    public function storeContractor(Request $request)
    {
        if (auth()->user()->role !== 'admin_pupr') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin PUPR yang dapat menambah kontraktor.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bidang' => 'required|string',
            'kualifikasi' => 'required|string',
            'nib' => 'required|string|unique:contractors,nib',
            'pj' => 'required|string|max:255',
            'alamat' => 'required|string',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string',
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        Contractor::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Kontraktor berhasil ditambahkan.');
    }

    public function updateContractor(Request $request, Contractor $contractor)
    {
        if (auth()->user()->role !== 'admin_pupr') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin PUPR yang dapat memperbarui kontraktor.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bidang' => 'required|string',
            'kualifikasi' => 'required|string',
            'nib' => 'required|string|unique:contractors,nib,' . $contractor->id,
            'pj' => 'required|string|max:255',
            'alamat' => 'required|string',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string',
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        $contractor->update($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Kontraktor berhasil diperbarui.');
    }

    public function deleteContractor(Contractor $contractor)
    {
        if (auth()->user()->role !== 'admin_pupr') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin PUPR yang dapat menghapus kontraktor.');
        }

        $contractor->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Kontraktor berhasil dihapus.');
    }

    // Verifikasi pendaftaran badan usaha: setujui + buat akun login kontraktor
    public function approveContractor(Contractor $contractor)
    {
        if (auth()->user()->role !== 'admin_pupr') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin PUPR yang dapat menyetujui pendaftaran.');
        }

        $contractor->status = 'approved';
        $contractor->save();

        $email = $contractor->email;
        $existingUser = User::where('contractor_id', $contractor->id)
            ->orWhere(function ($q) use ($email) {
                $q->where('email', $email);
            })
            ->first();

        if ($existingUser) {
            $existingUser->update(['role' => 'kontraktor', 'contractor_id' => $contractor->id]);
            $message = 'Badan usaha disetujui. Akun login sudah tersedia (' . $existingUser->email . ').';
        } elseif ($email) {
            $password = Str::random(10);
            User::create([
                'name' => $contractor->name,
                'email' => $email,
                'password' => $password,
                'role' => 'kontraktor',
                'contractor_id' => $contractor->id,
            ]);
            $message = 'Badan usaha disetujui. Akun login dibuat — email: ' . $email . ', password sementara: ' . $password . ' (catat & bagikan ke kontraktor).';
        } else {
            $message = 'Badan usaha disetujui. Catatan: email tidak diisi, akun login dibuat manual oleh admin.';
        }

        ActivityLog::log('contractor_approved', 'Badan usaha "' . $contractor->name . '" disetujui.');

        return redirect()->route('admin.dashboard')->with('success', $message);
    }

    // Verifikasi pendaftaran badan usaha: tolak
    public function rejectContractor(Contractor $contractor)
    {
        if (auth()->user()->role !== 'admin_pupr') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin PUPR yang dapat menolak pendaftaran.');
        }

        $contractor->status = 'rejected';
        $contractor->save();

        ActivityLog::log('contractor_rejected', 'Badan usaha "' . $contractor->name . '" ditolak.');

        return redirect()->route('admin.dashboard')->with('success', 'Pendaftaran badan usaha ditolak.');
    }

    // ===== CMS Konten & Publikasi =====
    public function cms()
    {
        $trainings = Training::orderBy('tanggal', 'desc')->get();
        $regulations = Regulation::orderBy('tahun', 'desc')->get();
        $news = NewsItem::orderBy('tanggal', 'desc')->get();
        $kategoriList = static::regulationCategories();
        $kategoriBerita = static::newsCategories();

        return view('admin.cms', compact('trainings', 'regulations', 'news', 'kategoriList', 'kategoriBerita'));
    }

    protected static function regulationCategories(): array
    {
        return [
            'Undang-Undang',
            'Peraturan Pemerintah',
            'Peraturan Presiden',
            'Peraturan Menteri',
            'Peraturan Daerah',
            'Keputusan Kepala Daerah',
            'Standar & Pedoman Teknis',
            'Surat Edaran',
        ];
    }

    protected static function newsCategories(): array
    {
        return [
            'Pengumuman',
            'Lelang & Pengadaan',
            'Berita Kegiatan',
            'Pembangunan & Infrastruktur',
            'Pelatihan & Sertifikasi',
            'Sosialisasi',
            'Lainnya',
        ];
    }

    // --- Pelatihan ---
    public function storeTraining(Request $request)
    {
        $validated = $request->validate([
            'nama_pelatihan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'kuota' => 'required|integer|min:1',
            'status' => 'nullable|string|max:50',
            'detail' => 'nullable|string',
        ]);

        $training = Training::create($validated);

        ActivityLog::log('create_training', 'Membuat pelatihan baru: ' . $training->nama_pelatihan);

        return redirect()->route('admin.cms')->with('success', 'Pelatihan berhasil ditambahkan.');
    }

    public function updateTraining(Request $request, Training $training)
    {
        $validated = $request->validate([
            'nama_pelatihan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'kuota' => 'required|integer|min:1',
            'status' => 'nullable|string|max:50',
            'detail' => 'nullable|string',
        ]);

        $training->update($validated);

        ActivityLog::log('update_training', 'Memperbarui pelatihan: ' . $training->nama_pelatihan);

        return redirect()->route('admin.cms')->with('success', 'Pelatihan berhasil diperbarui.');
    }

    public function deleteTraining(Training $training)
    {
        $training->delete();

        ActivityLog::log('delete_training', 'Menghapus pelatihan: ' . $training->nama_pelatihan);

        return redirect()->route('admin.cms')->with('success', 'Pelatihan berhasil dihapus.');
    }

    // --- Regulasi ---
    public function storeRegulation(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'nomor' => 'nullable|string|max:100',
            'tahun' => 'nullable|integer|min:1900|max:2100',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        if ($request->hasFile('file')) {
            $validated['file'] = $request->file('file')->store('regulasi', 'public');
        }

        $regulation = Regulation::create($validated);

        ActivityLog::log('create_regulation', 'Membuat regulasi baru: ' . $regulation->judul);

        return redirect()->route('admin.cms')->with('success', 'Regulasi berhasil ditambahkan.');
    }

    public function updateRegulation(Request $request, Regulation $regulation)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'nomor' => 'nullable|string|max:100',
            'tahun' => 'nullable|integer|min:1900|max:2100',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        if ($request->hasFile('file')) {
            if ($regulation->file) {
                Storage::disk('public')->delete($regulation->file);
            }
            $validated['file'] = $request->file('file')->store('regulasi', 'public');
        }

        $regulation->update($validated);

        ActivityLog::log('update_regulation', 'Memperbarui regulasi: ' . $regulation->judul);

        return redirect()->route('admin.cms')->with('success', 'Regulasi berhasil diperbarui.');
    }

    public function deleteRegulation(Regulation $regulation)
    {
        if ($regulation->file) {
            Storage::disk('public')->delete($regulation->file);
        }

        $regulation->delete();

        ActivityLog::log('delete_regulation', 'Menghapus regulasi: ' . $regulation->judul);

        return redirect()->route('admin.cms')->with('success', 'Regulasi berhasil dihapus.');
    }

    public function downloadRegulation(Regulation $regulation)
    {
        if (! $regulation->file || ! Storage::disk('public')->exists($regulation->file)) {
            abort(404, 'Berkas regulasi tidak tersedia.');
        }

        $extension = pathinfo($regulation->file, PATHINFO_EXTENSION);
        $filename = Str::slug($regulation->judul).'.'.$extension;

        return Storage::disk('public')->download($regulation->file, $filename);
    }

    // --- Berita ---
    public function storeNews(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'tanggal' => 'required|date',
            'kategori' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,publish',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('news', 'public');
        }

        $news = NewsItem::create($validated);

        ActivityLog::log('create_news', 'Membuat berita baru: ' . $news->judul);

        return redirect()->route('admin.cms')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function updateNews(Request $request, NewsItem $news)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'tanggal' => 'required|date',
            'kategori' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,publish',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($news->cover_image) {
                Storage::disk('public')->delete($news->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('news', 'public');
        }

        $news->update($validated);

        ActivityLog::log('update_news', 'Memperbarui berita: ' . $news->judul);

        return redirect()->route('admin.cms')->with('success', 'Berita berhasil diperbarui.');
    }

    public function deleteNews(NewsItem $news)
    {
        if ($news->cover_image) {
            Storage::disk('public')->delete($news->cover_image);
        }

        $news->delete();

        ActivityLog::log('delete_news', 'Menghapus berita: ' . $news->judul);

        return redirect()->route('admin.cms')->with('success', 'Berita berhasil dihapus.');
    }

    // CRUD Project
    public function storeProject(Request $request)
    {
        if (auth()->user()->role !== 'admin_pupr') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin PUPR yang dapat membuat paket pekerjaan baru.');
        }

        $validated = $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'nama_pekerjaan' => 'required|string|max:255',
            'nilai_kontrak' => 'required|numeric|min:0',
            'tahun_anggaran' => 'required|integer|min:2000|max:2100',
            'status' => 'required|string',
            'progress' => 'required|numeric|min:0|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'detail_lokasi' => 'nullable|string',
            'ruas_jalan_id' => 'nullable|integer',
            'pengawas_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'pemeriksa_lapangan')],
            'tanggal_kontrak' => 'nullable|date',
            'tanggal_pelaksanaan' => 'nullable|date',
            'tanggal_pemeriksaan' => 'nullable|date',
            'tanggal_deadline' => 'nullable|date',
        ]);

        $this->applyRuasJalan($validated);

        Project::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Pekerjaan berhasil ditambahkan.');
    }

    public function updateProject(Request $request, Project $project)
    {
        $user = auth()->user();

        // 1. Contractor Restrictions
        if ($user->role === 'kontraktor') {
            if ($project->contractor_id !== $user->contractor_id) {
                return redirect()->back()->with('error', 'Akses ditolak. Anda tidak berwenang mengedit proyek ini.');
            }

            $validated = $request->validate([
                'nama_pekerjaan' => 'required|string|max:255',
                'nilai_kontrak' => 'required|numeric|min:0',
                'tahun_anggaran' => 'required|integer|min:2000|max:2100',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'detail_lokasi' => 'nullable|string',
                'tanggal_kontrak' => 'nullable|date',
                'tanggal_pelaksanaan' => 'nullable|date',
                'tanggal_deadline' => 'nullable|date',
            ]);

            $project->update($validated);
            return redirect()->route('admin.dashboard')->with('success', 'Detail pekerjaan berhasil diperbarui.');
        }

        // 2. Field Examiner Restrictions
        if ($user->role === 'pemeriksa_lapangan') {
            $validated = $request->validate([
                'status' => 'required|string',
                'progress' => 'required|numeric|min:0|max:100',
                'tanggal_pemeriksaan' => 'nullable|date',
            ]);

            $project->update($validated);
            return redirect()->route('admin.dashboard')->with('success', 'Progress audit pekerjaan berhasil diperbarui.');
        }

        // 3. Admin PUPR (Full Access)
        $validated = $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'nama_pekerjaan' => 'required|string|max:255',
            'nilai_kontrak' => 'required|numeric|min:0',
            'tahun_anggaran' => 'required|integer|min:2000|max:2100',
            'status' => 'required|string',
            'progress' => 'required|numeric|min:0|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'detail_lokasi' => 'nullable|string',
            'ruas_jalan_id' => 'nullable|integer',
            'pengawas_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'pemeriksa_lapangan')],
            'tanggal_kontrak' => 'nullable|date',
            'tanggal_pelaksanaan' => 'nullable|date',
            'tanggal_pemeriksaan' => 'nullable|date',
            'tanggal_deadline' => 'nullable|date',
        ]);

        $this->applyRuasJalan($validated);

        $project->update($validated);
        return redirect()->route('admin.dashboard')->with('success', 'Pekerjaan berhasil diperbarui.');
    }

    /**
     * Isi kolom snapshot ruas jalan (nomor & nama) berdasarkan ruas_jalan_id
     * yang dipilih. Dipanggil saat membuat/memperbarui proyek oleh admin.
     */
    protected function applyRuasJalan(array &$validated): void
    {
        $ruasId = isset($validated['ruas_jalan_id']) && $validated['ruas_jalan_id'] !== ''
            ? (int) $validated['ruas_jalan_id']
            : null;

        if ($ruasId === null) {
            $validated['ruas_jalan_id'] = null;
            $validated['ruas_jalan_nomor'] = null;
            $validated['ruas_jalan_nama'] = null;

            return;
        }

        $ruas = RuasJalanService::find($ruasId);

        if ($ruas === null) {
            // Id tidak dikenal -> jangan tautkan.
            $validated['ruas_jalan_id'] = null;
            $validated['ruas_jalan_nomor'] = null;
            $validated['ruas_jalan_nama'] = null;

            return;
        }

        $validated['ruas_jalan_id'] = $ruas['id'];
        $validated['ruas_jalan_nomor'] = $ruas['nomor_ruas'];
        $validated['ruas_jalan_nama'] = $ruas['nama_ruas'];
    }

    public function deleteProject(Project $project)
    {
        if (auth()->user()->role !== 'admin_pupr') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin PUPR yang dapat menghapus paket pekerjaan.');
        }

        $project->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Pekerjaan berhasil dihapus.');
    }

    // MOBILE: Contractor Main Page
    public function mobileContractor()
    {
        $user = auth()->user();
        if ($user->role !== 'kontraktor' && $user->role !== 'admin_pupr') {
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak.');
        }

        $contractorId = $user->contractor_id ?? Contractor::first()->id ?? 1;
        $contractor = Contractor::find($contractorId);
        $projects = Project::where('contractor_id', $contractorId)->get();

        return view('mobile.contractor', compact('contractor', 'projects', 'user'));
    }

    // MOBILE: Contractor Submit Progress Update & Upload Photo
    public function mobileContractorSubmitReport(Request $request, Project $project)
    {
        $user = auth()->user();
        if (($user->role !== 'kontraktor' && $user->role !== 'admin_pupr') || ($user->role === 'kontraktor' && $project->contractor_id !== $user->contractor_id)) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:2048'
        ]);

        $project->reported_progress = $validated['progress'];
        $project->reported_at = now();
        $project->verification_status = 'pending';
        // Reset lapisan approval untuk pengajuan baru
        $project->pengawas_verified_at = null;
        $project->pengawas_verified_by = null;
        $project->final_verified_at = null;
        $project->final_verified_by = null;
        $project->final_verification_note = null;
        $project->save();

        $log = ProjectLog::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => 'submission',
            'progress' => $validated['progress'],
            'photo' => null,
            'note' => 'Pengajuan progres fisik ' . number_format($validated['progress'], 0) . '% diajukan oleh penyedia jasa.',
        ]);

        $firstPhoto = true;
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('projects', 'public');
                $storedPath = 'storage/' . $path;

                ProjectPhoto::create([
                    'project_id' => $project->id,
                    'project_log_id' => $log->id,
                    'path' => $storedPath,
                    'caption' => 'Foto progres ' . number_format($validated['progress'], 0) . '%',
                ]);

                if ($firstPhoto) {
                    $project->reported_photo = $storedPath;
                    $project->save();
                    $log->update(['photo' => $storedPath]);
                    $firstPhoto = false;
                }
            }
        } elseif (!$project->reported_photo) {
            $project->reported_photo = 'storage/projects/sample_default.jpg';
            $project->save();
        }

        ActivityLog::log('SUBMIT_PROGRESS', "Pengajuan progres fisik {$validated['progress']}% untuk paket '{$project->nama_pekerjaan}'", $user, $request);

        return redirect()->back()->with('success', 'Laporan progres berhasil diajukan dan sedang menunggu verifikasi pengawas.');
    }

    // MOBILE: Supervisor/Examiner Main Page
    public function mobileSupervisor()
    {
        $user = auth()->user();
        if ($user->role !== 'pemeriksa_lapangan' && $user->role !== 'admin_pupr') {
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak.');
        }

        // Scope: proyek yang ditugaskan ke pengawas ini + proyek yang belum ditugaskan
        $assignedToMe = fn ($query) => $query->where(function ($q) use ($user) {
            $q->where('pengawas_id', $user->id)->orWhereNull('pengawas_id');
        });

        // Hanya lapisan 1: laporan yang belum diverifikasi pengawas
        $pendingProjects = Project::where('verification_status', 'pending')
            ->whereNull('pengawas_verified_at')
            ->with('contractor')
            ->when($user->role === 'pemeriksa_lapangan', $assignedToMe)
            ->get();
        $allProjects = Project::with('contractor')
            ->when($user->role === 'pemeriksa_lapangan', $assignedToMe)
            ->get();

        // Riwayat verifikasi oleh pengawas ini (approve/reject lapisan 1)
        $riwayatVerifikasi = ProjectLog::with('project.contractor', 'photos')
            ->where('user_id', $user->id)
            ->whereIn('action', ['approve', 'reject'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
        $jumlahDisetujui = $riwayatVerifikasi->where('action', 'approve')->count();
        $jumlahDitolak = $riwayatVerifikasi->where('action', 'reject')->count();

        return view('mobile.supervisor', compact('pendingProjects', 'allProjects', 'user', 'riwayatVerifikasi', 'jumlahDisetujui', 'jumlahDitolak'));
    }

    // MOBILE: Supervisor Approve/Reject Report
    public function mobileSupervisorVerifyReport(Request $request, Project $project)
    {
        $user = auth()->user();
        if ($user->role !== 'pemeriksa_lapangan' && $user->role !== 'admin_pupr') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // Cek penugasan: pengawas hanya boleh memverifikasi proyek yang ditugaskan kepadanya (atau yang belum ditugaskan)
        if ($user->role === 'pemeriksa_lapangan' && $project->pengawas_id && $project->pengawas_id !== $user->id) {
            return redirect()->back()->with('error', 'Paket ini ditugaskan kepada pengawas lain. Anda tidak berwenang memverifikasinya.');
        }

        $action = $request->input('action'); // approve or reject
        // Terima catatan dari form pengawas (name="note") maupun form admin (name="verification_note")
        $note = $request->input('verification_note') ?: $request->input('note');

        // Validasi foto dokumentasi lapangan (cross-check oleh pengawas)
        $request->validate([
            'foto_dokumentasi' => 'nullable|array|max:8',
            'foto_dokumentasi.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($action === 'approve') {
            // Lapisan 1 disetujui pengawas -> lanjut ke persetujuan final admin
            $project->pengawas_verified_at = now();
            $project->pengawas_verified_by = $user->id;
            $project->verification_status = 'pending';
            $project->verification_note = $note ?: 'Progres fisik terverifikasi pengawas lapangan dan diajukan untuk persetujuan akhir admin.';
            
            ActivityLog::log('VERIFY_APPROVE', "Verifikasi lapisan pengawas DISETUJUI progres {$project->reported_progress}% untuk paket '{$project->nama_pekerjaan}'", $user, $request);
        } else {
            // Reject report (ditolak di lapisan pengawas)
            $project->verification_status = 'rejected';
            $project->pengawas_verified_at = null;
            $project->pengawas_verified_by = null;
            $project->verification_note = $note ?: 'Pengajuan progres ditolak oleh pengawas lapangan.';

            ActivityLog::log('VERIFY_REJECT', "Verifikasi DITOLAK progres untuk paket '{$project->nama_pekerjaan}'", $user, $request);
        }

        $project->save();

        $log = ProjectLog::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => $action === 'approve' ? 'approve' : 'reject',
            'progress' => $project->reported_progress,
            'photo' => $project->reported_photo,
            'note' => $project->verification_note,
        ]);

        // Simpan foto dokumentasi lapangan pengawas (cross-check)
        if ($request->hasFile('foto_dokumentasi')) {
            foreach ($request->file('foto_dokumentasi') as $foto) {
                ProjectPhoto::create([
                    'project_id' => $project->id,
                    'project_log_id' => $log->id,
                    'path' => $foto->store('projects', 'public'),
                    'caption' => 'Dokumentasi pemeriksaan lapangan',
                    'type' => 'verification',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Status verifikasi progres fisik pekerjaan berhasil diperbarui.');
    }

    // ADMIN: Persetujuan Akhir (Lapisan Final) oleh Admin PUPR
    public function adminFinalVerifyProject(Request $request, Project $project)
    {
        $user = auth()->user();
        if ($user->role !== 'admin_pupr') {
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Hanya Admin PUPR yang dapat memberikan persetujuan akhir.');
        }

        $action = $request->input('action'); // approve or reject
        $note = $request->input('final_note');

        if ($action === 'approve') {
            // Lapisan akhir: terapkan progres resmi ke data proyek
            $project->progress = $project->reported_progress;
            if ($project->progress >= 100) {
                $project->status = 'Selesai';
            } else {
                $project->status = 'Pelaksanaan';
            }
            $project->tanggal_pemeriksaan = now()->toDateString();
            $project->final_verified_at = now();
            $project->final_verified_by = $user->id;
            $project->final_verification_note = $note ?: 'Persetujuan akhir diberikan Admin PUPR sesuai rekomendasi pengawas lapangan.';
            $project->verification_status = 'verified';

            ActivityLog::log('FINAL_APPROVE', "Persetujuan AKHIR progres {$project->progress}% untuk paket '{$project->nama_pekerjaan}'", $user, $request);
        } else {
            $project->verification_status = 'rejected';
            $project->final_verification_note = $note ?: 'Persetujuan akhir ditolak oleh Admin PUPR.';

            ActivityLog::log('FINAL_REJECT', "Persetujuan akhir DITOLAK untuk paket '{$project->nama_pekerjaan}'", $user, $request);
        }

        $project->save();

        ProjectLog::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => $action === 'approve' ? 'final_approve' : 'final_reject',
            'progress' => $project->progress ?? $project->reported_progress ?? 0,
            'photo' => $project->reported_photo,
            'note' => $project->final_verification_note,
        ]);

        return redirect()->back()->with('success', 'Persetujuan akhir progres fisik pekerjaan berhasil diperbarui.');
    }
}
