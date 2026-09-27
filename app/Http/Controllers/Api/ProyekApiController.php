<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProyekApiController extends Controller
{
    /**
     * Daftar data pekerjaan (nama pekerjaan, pelaksana, status progress, dll).
     *
     * Mendukung filter query string:
     *   status             : Persiapan|Pelaksanaan|Selesai
     *   verification_status: clean|pending|verified
     *   tahun              : tahun anggaran, mis. 2025
     *   search             : cari nama pekerjaan / pelaksana
     *   per_page           : jumlah per halaman (default 15)
     */
    public function index(Request $request)
    {
        $query = Project::query()
            ->with(['contractor', 'assignedPengawas'])
            ->orderByDesc('tahun_anggaran')
            ->orderByDesc('created_at');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($vs = $request->query('verification_status')) {
            $query->where('verification_status', $vs);
        }

        if ($tahun = $request->query('tahun')) {
            $query->where('tahun_anggaran', $tahun);
        }

        if ($q = trim((string) $request->query('search', ''))) {
            $query->where(function ($w) use ($q) {
                $w->where('nama_pekerjaan', 'like', "%{$q}%")
                    ->orWhere('detail_lokasi', 'like', "%{$q}%")
                    ->orWhereHas('contractor', function ($c) use ($q) {
                        $c->where('name', 'like', "%{$q}%");
                    });
            });
        }

        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));

        $projects = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $projects->map(fn (Project $p) => $this->transform($p)),
            'pagination' => [
                'total' => $projects->total(),
                'per_page' => $projects->perPage(),
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'next_page_url' => $projects->nextPageUrl(),
                'prev_page_url' => $projects->previousPageUrl(),
            ],
        ]);
    }

    /**
     * Detail satu pekerjaan beserta riwayat dan dokumentasi foto.
     */
    public function show(Project $project)
    {
        $project->load(['contractor', 'assignedPengawas', 'pengawasVerifier', 'finalVerifier', 'photos']);

        $data = $this->transform($project);

        $data['riwayat'] = $project->logs()
            ->with(['user', 'photos'])
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'progress' => $log->progress,
                    'note' => $log->note,
                    'oleh' => $log->user?->name,
                    'role' => $log->user?->role,
                    'waktu' => $log->created_at?->toIso8601String(),
                    'foto' => $log->photos->map(fn ($ph) => $this->photoUrl($ph->path))->values(),
                ];
            });

        $data['foto_dokumentasi'] = $project->photos
            ->map(fn ($ph) => [
                'url' => $this->photoUrl($ph->path),
                'keterangan' => $ph->caption,
                'tipe' => $ph->type,
            ])
            ->values();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Ringkasan statistik pekerjaan untuk dashboard eksternal.
     */
    public function statistik()
    {
        $byStatus = Project::query()
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $byVerification = Project::query()
            ->selectRaw('verification_status, COUNT(*) as jumlah')
            ->groupBy('verification_status')
            ->pluck('jumlah', 'verification_status');

        return response()->json([
            'success' => true,
            'data' => [
                'total_pekerjaan' => Project::count(),
                'total_nilai_kontrak' => (int) Project::sum('nilai_kontrak'),
                'rata_rata_progress' => round((float) Project::avg('progress'), 2),
                'per_status' => $byStatus,
                'per_status_verifikasi' => $byVerification,
                'tahun_anggaran' => Project::query()
                    ->distinct()
                    ->orderByDesc('tahun_anggaran')
                    ->pluck('tahun_anggaran'),
            ],
        ]);
    }

    /**
     * Format satu pekerjaan menjadi payload JSON yang ramah untuk
     * konsumsi aplikasi eksternal (inspektorat, OPD, dsb.).
     */
    protected function transform(Project $p): array
    {
        return [
            'id' => $p->id,
            'nama_pekerjaan' => $p->nama_pekerjaan,
            'pelaksana' => $p->contractor?->name,
            'pelaksana_bidang' => $p->contractor?->bidang,
            'nilai_kontrak' => (int) $p->nilai_kontrak,
            'tahun_anggaran' => $p->tahun_anggaran,
            'status' => $p->status,
            'progress' => round((float) $p->progress, 2),
            'status_verifikasi' => $p->verification_status,
            'pengawas' => $p->assignedPengawas?->name,
            'lokasi' => [
                'latitude' => $p->latitude,
                'longitude' => $p->longitude,
                'detail' => $p->detail_lokasi,
            ],
            'ruas_jalan' => [
                'nomor' => $p->ruas_jalan_nomor,
                'nama' => $p->ruas_jalan_nama,
            ],
            'jadwal' => [
                'tanggal_kontrak' => $p->tanggal_kontrak,
                'tanggal_pelaksanaan' => $p->tanggal_pelaksanaan,
                'tanggal_pemeriksaan' => $p->tanggal_pemeriksaan,
                'tanggal_deadline' => $p->tanggal_deadline,
            ],
            'verifikasi' => [
                'reported_progress' => $p->reported_progress,
                'reported_at' => $p->reported_at?->toIso8601String(),
                'reported_photo' => $p->reported_photo ? $this->photoUrl($p->reported_photo) : null,
                'pengawas_verified_at' => $p->pengawas_verified_at?->toIso8601String(),
                'pengawas_verifier' => $p->pengawasVerifier?->name,
                'final_verified_at' => $p->final_verified_at?->toIso8601String(),
                'final_verifier' => $p->finalVerifier?->name,
                'final_note' => $p->final_verification_note,
            ],
            'created_at' => $p->created_at?->toIso8601String(),
            'updated_at' => $p->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Ubah path foto relatif menjadi URL absolut.
     * Mendukung nilai yang sudah berawalan http(s) maupun path storage.
     */
    protected function photoUrl($path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim($path, '/');

        // Nilai yang sudah memuat prefix public storage (mis. hasil kolom
        // reported_photo yang disimpan dengan leading slash) tidak perlu
        // ditambahkan prefix lagi agar tidak dobel.
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }
}