<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Project;
use App\Models\ProjectLog;
use App\Models\Training;
use App\Models\Regulation;
use App\Models\NewsItem;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
        $project->load(['contractor', 'logs']);
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
        return view('sibijak.regulasi', compact('regulations'));
    }

    // SIBIJAK: Berita Page
    public function berita()
    {
        $news = NewsItem::orderBy('tanggal', 'desc')->get();
        return view('sibijak.berita', compact('news'));
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
    public function dashboard()
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

        // Pendaftaran badan usaha yang menunggu verifikasi admin
        $pendingContractors = Contractor::where('status', 'pending')->latest()->get();

        // Kontraktor terverifikasi (untuk dropdown penugasan proyek)
        $approvedContractors = Contractor::where('status', 'approved')->orderBy('name')->get();

        $trainings = Training::all();
        $regulations = Regulation::all();
        $news = NewsItem::all();

        // Get pending verification projects for admin dashboard alert
        $pendingVerifications = Project::where('verification_status', 'pending')->with('contractor')->get();

        // Get upcoming deadlines for projects currently in progress
        $upcomingDeadlines = Project::where('status', 'Dalam Proses')
            ->whereNotNull('tanggal_deadline')
            ->with('contractor')
            ->orderBy('tanggal_deadline', 'asc')
            ->take(5)
            ->get();

        // Get recent activity logs for mobile audit tab preview
        $activityLogs = ActivityLog::with('user')->orderBy('created_at', 'desc')->take(10)->get();

        return view('admin.dashboard', compact('contractors', 'projects', 'stats', 'trainings', 'regulations', 'news', 'pendingVerifications', 'upcomingDeadlines', 'activityLogs', 'pendingContractors', 'approvedContractors'));
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
        return view('admin.map', compact('projects', 'stats'));
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
            'tanggal_kontrak' => 'nullable|date',
            'tanggal_pelaksanaan' => 'nullable|date',
            'tanggal_pemeriksaan' => 'nullable|date',
            'tanggal_deadline' => 'nullable|date',
        ]);

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
            'tanggal_kontrak' => 'nullable|date',
            'tanggal_pelaksanaan' => 'nullable|date',
            'tanggal_pemeriksaan' => 'nullable|date',
            'tanggal_deadline' => 'nullable|date',
        ]);

        $project->update($validated);
        return redirect()->route('admin.dashboard')->with('success', 'Pekerjaan berhasil diperbarui.');
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
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('projects', 'public');
            $project->reported_photo = 'storage/' . $path;
        } else {
            // Default local storage image fallback if no file attached
            $project->reported_photo = 'storage/projects/sample_default.jpg';
        }

        $project->reported_progress = $validated['progress'];
        $project->reported_at = now();
        $project->verification_status = 'pending';
        $project->save();

        ProjectLog::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => 'submission',
            'progress' => $validated['progress'],
            'photo' => $project->reported_photo,
            'note' => 'Pengajuan progres fisik ' . number_format($validated['progress'], 0) . '% diajukan oleh penyedia jasa.',
        ]);

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

        $pendingProjects = Project::where('verification_status', 'pending')->with('contractor')->get();
        $allProjects = Project::with('contractor')->get();

        return view('mobile.supervisor', compact('pendingProjects', 'allProjects', 'user'));
    }

    // MOBILE: Supervisor Approve/Reject Report
    public function mobileSupervisorVerifyReport(Request $request, Project $project)
    {
        $user = auth()->user();
        if ($user->role !== 'pemeriksa_lapangan' && $user->role !== 'admin_pupr') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $action = $request->input('action'); // approve or reject
        $note = $request->input('verification_note');

        if ($action === 'approve') {
            $project->progress = $project->reported_progress;
            if ($project->progress >= 100) {
                $project->status = 'Selesai';
            } else {
                $project->status = 'Pelaksanaan';
            }
            $project->tanggal_pemeriksaan = now()->toDateString();
            $project->verification_status = 'verified';
            $project->verification_note = $note ?: 'Progres fisik terverifikasi dan disetujui sesuai hasil pengawasan lapangan.';
            
            ActivityLog::log('VERIFY_APPROVE', "Verifikasi DISETUJUI progres {$project->progress}% untuk paket '{$project->nama_pekerjaan}'", $user, $request);
        } else {
            // Reject report
            $project->verification_status = 'rejected';
            $project->verification_note = $note ?: 'Pengajuan progres ditolak oleh pengawas lapangan.';

            ActivityLog::log('VERIFY_REJECT', "Verifikasi DITOLAK progres untuk paket '{$project->nama_pekerjaan}'", $user, $request);
        }

        $project->save();

        ProjectLog::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => $action === 'approve' ? 'approve' : 'reject',
            'progress' => $project->progress,
            'photo' => $project->reported_photo,
            'note' => $project->verification_note,
        ]);

        return redirect()->back()->with('success', 'Status verifikasi progres fisik pekerjaan berhasil diperbarui.');
    }
}
