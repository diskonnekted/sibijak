<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Project;
use App\Models\Training;
use App\Models\Regulation;
use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Http\Request;

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
        $project->load('contractor');
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
            'email' => 'nullable|email',
            'telepon' => 'nullable|string',
        ]);

        $validated['rating'] = 5.0;

        Contractor::create($validated);

        return redirect()->route('badanusaha')->with('success', 'Pendaftaran Badan Usaha berhasil diajukan.');
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

        $training->increment('pendaftar_count');

        return redirect()->route('pelatihan')->with('success', 'Pendaftaran Pelatihan berhasil.');
    }

    // AUTH: Show Login Form
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    // AUTH: Attempt Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    // AUTH: Log Out
    public function logout(Request $request)
    {
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

        $trainings = Training::all();
        $regulations = Regulation::all();
        $news = NewsItem::all();

        return view('admin.dashboard', compact('contractors', 'projects', 'stats', 'trainings', 'regulations', 'news'));
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

            // Contractor can only update basic fields (excluding progress, status, inspection date)
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
            // Examiner can ONLY update progress, status, and tanggal_pemeriksaan
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
}
