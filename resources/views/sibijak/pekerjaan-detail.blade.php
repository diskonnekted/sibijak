@extends('layouts.sibijak')

@section('title')
  Detail Pekerjaan: {{ $project->nama_pekerjaan }} - SIBIJAK Banjarnegara
@endsection

@section('styles')
  <!-- Leaflet CSS & JS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <style>
    #single-map {
      height: 320px;
      z-index: 10;
    }
  </style>
@endsection

@section('content')
  <!-- TOP HEADER -->
  <section class="bg-gov-900 text-white py-12 border-b border-gov-950">
    <div class="max-w-7xl mx-auto px-6 space-y-2">
      <div class="flex items-center gap-2">
        <a href="{{ route('badanusaha.show', $project->contractor_id) }}" class="text-xs font-bold text-slate-300 hover:text-white uppercase tracking-wider flex items-center gap-1">
          <i class="ph ph-arrow-left"></i> Kembali ke Profil Kontraktor
        </a>
      </div>
      <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-gov-850 text-[10px] uppercase font-bold tracking-wider mb-2">
        Tahun Anggaran {{ $project->tahun_anggaran }}
      </span>
      <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">{{ $project->nama_pekerjaan }}</h1>
      <span class="block text-xs text-slate-300">Pelaksana Jasa: <a href="{{ route('badanusaha.show', $project->contractor_id) }}" class="underline font-bold hover:text-white">{{ $project->contractor->name }}</a></span>
    </div>
  </section>

  <!-- CONTENT -->
  <section class="py-16">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12">
      
      <!-- Left side: Photo & Details -->
      <div class="lg:col-span-8 space-y-8">
        <!-- Visual Documentation & Progress Gallery -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="relative aspect-[16/9] bg-slate-900 group">
            <img id="mainProgressImage" src="{{ asset(ltrim($project->reported_photo ?: 'storage/projects/sample_jembatan.jpg', '/')) }}" alt="{{ $project->nama_pekerjaan }}" class="w-full h-full object-cover transition-all duration-300" onerror="this.onerror=null; this.src='{{ asset('storage/projects/sample_default.jpg') }}';">
            
            <div class="absolute top-4 left-4 bg-slate-950/80 backdrop-blur-md px-3 py-1.5 rounded-full border border-slate-700/80 text-white flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              <span class="text-[10px] font-extrabold uppercase tracking-wider" id="mainPhotoStage">Foto Progres Fisik Terbaru ({{ number_format($project->progress, 0) }}%)</span>
            </div>

            <button type="button" onclick="openLightbox(document.getElementById('mainProgressImage').src, document.getElementById('mainPhotoStage').innerText)" class="absolute bottom-4 right-4 bg-slate-950/80 hover:bg-slate-900 text-white px-3 py-1.5 rounded-xl border border-slate-700 text-xs font-bold flex items-center gap-1.5 shadow-lg active:scale-95 transition-all">
              <i class="ph-bold ph-arrows-out text-sm"></i>
              <span>Perbesar Foto</span>
            </button>
          </div>

          <div class="p-6 border-t border-slate-200 space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">DOKUMENTASI VISUAL LAPANGAN</span>
                <h3 class="text-sm font-extrabold text-slate-900">Galeri Foto Tahapan Progres Pekerjaan</h3>
              </div>
              <span class="text-[10px] font-mono font-bold bg-slate-100 px-2.5 py-1 rounded text-slate-600 border border-slate-200">
                Terverifikasi Dinas PUPR
              </span>
            </div>

            <!-- Thumbnail Gallery List (Dinamis: Foto Lapangan Terunggah) -->
            @php
              $galleryPhotos = $project->photos->sortByDesc('id')->values()->take(9);
            @endphp
            @if($galleryPhotos->count() > 0)
              <div class="grid grid-cols-3 gap-3 pt-1">
                @foreach($galleryPhotos as $gp)
                  <div onclick="changeMainPhoto('{{ asset(ltrim($gp->path ?: 'storage/projects/sample_default.jpg', '/')) }}', '{{ $gp->caption ?: 'Dokumentasi Lapangan' }} ({{ date('d M Y', strtotime($gp->created_at)) }})')" class="cursor-pointer group relative aspect-video bg-slate-100 rounded-xl overflow-hidden border-2 {{ $loop->first ? 'border-gov-600' : 'border-transparent hover:border-gov-600' }} transition-all shadow-sm">
                    <img src="{{ asset(ltrim($gp->path ?: 'storage/projects/sample_default.jpg', '/')) }}" alt="{{ $gp->caption ?: 'Foto Progres' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" onerror="this.onerror=null; this.src='{{ asset('storage/projects/sample_default.jpg') }}';">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent p-2 flex items-end justify-between">
                      <span class="text-[9px] font-bold text-white uppercase tracking-wider">{{ $gp->caption ?: 'Foto Progres' }}</span>
                      @if($loop->first)
                        <span class="text-[9px] font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1">
                          <i class="ph-fill ph-check-circle"></i> Terkini
                        </span>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="pt-1 text-center py-4 bg-slate-50 border border-dashed border-slate-200 rounded-xl">
                <i class="ph-bold ph-images text-slate-300 text-2xl"></i>
                <p class="text-[10px] text-slate-400 font-semibold mt-1">Belum ada dokumentasi foto lapangan terunggah untuk paket ini.</p>
              </div>
            @endif
          </div>
        </div>

        <!-- Description -->
        <div class="bg-white p-8 rounded border border-slate-200 shadow-sm space-y-6">
          <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Detail & Rincian Pekerjaan</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs border-b border-slate-100 pb-6">
            <div>
              <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Nilai Kontrak</span>
              <span class="text-sm font-extrabold text-gov-800">Rp {{ number_format($project->nilai_kontrak, 0, ',', '.') }}</span>
            </div>
            <div>
              <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Status Lapangan</span>
              <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{
                $project->status === 'Selesai' ? 'bg-gov-100 text-gov-800' : 'bg-amber-100 text-amber-800'
              }}">
                {{ $project->status }}
              </span>
            </div>
            <div>
              <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Progress Fisik</span>
              <span class="text-sm font-extrabold text-slate-800">{{ number_format($project->progress, 2) }}%</span>
            </div>
          </div>

          <div class="space-y-2 text-xs">
            <span class="block text-slate-400 font-bold uppercase tracking-wider">Detail Keterangan Lokasi</span>
            <p class="text-sm text-slate-700 leading-relaxed font-medium">
              {{ $project->detail_lokasi ?: 'Kecamatan Banjarnegara, Kabupaten Banjarnegara, Jawa Tengah' }}
            </p>
          </div>

          <!-- Monitoring Jadwal & Tanggal -->
          <div class="space-y-4 pt-6 border-t border-slate-100">
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Jadwal & Pengawasan Tanggal</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div class="p-3 bg-slate-50 border border-slate-100 rounded text-center">
                <span class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Tanggal Kontrak</span>
                <span class="text-xs font-extrabold text-slate-800 font-mono">
                  {{ $project->tanggal_kontrak ? date('d M Y', strtotime($project->tanggal_kontrak)) : '-' }}
                </span>
              </div>
              <div class="p-3 bg-slate-50 border border-slate-100 rounded text-center">
                <span class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Mulai Pelaksanaan</span>
                <span class="text-xs font-extrabold text-slate-800 font-mono">
                  {{ $project->tanggal_pelaksanaan ? date('d M Y', strtotime($project->tanggal_pelaksanaan)) : '-' }}
                </span>
              </div>
              <div class="p-3 bg-slate-50 border border-slate-100 rounded text-center">
                <span class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Audit Pemeriksaan</span>
                <span class="text-xs font-extrabold text-slate-800 font-mono">
                  {{ $project->tanggal_pemeriksaan ? date('d M Y', strtotime($project->tanggal_pemeriksaan)) : '-' }}
                </span>
              </div>
              <div class="p-3 bg-red-50 border border-red-100 rounded text-center">
                <span class="block text-[9px] font-bold text-red-500 uppercase mb-1">Batas Deadline</span>
                <span class="text-xs font-extrabold text-red-700 font-mono">
                  {{ $project->tanggal_deadline ? date('d M Y', strtotime($project->tanggal_deadline)) : '-' }}
                </span>
              </div>
            </div>

            <!-- Catatan Review Audit Pengawas (Dinamis: Hijau jika Disetujui, Merah jika Ditolak) -->
            @php
              $isRejected = str_contains(strtolower($project->verification_note ?? ''), 'ditolak') || 
                            str_contains(strtolower($project->verification_note ?? ''), 'penolakan') ||
                            ($project->verification_status === 'clean' && !empty($project->verification_note));
            @endphp

            @if($project->verification_note)
              <div class="mt-4 p-4 rounded-xl space-y-2 border {{
                $isRejected ? 'bg-red-50/90 border-red-200/90 text-red-950' : 'bg-emerald-50/90 border-emerald-200/90 text-emerald-950'
              }}">
                <div class="flex items-center justify-between">
                  <span class="text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1.5 {{
                    $isRejected ? 'text-red-950' : 'text-emerald-950'
                  }}">
                    @if($isRejected)
                      <i class="ph-bold ph-x-circle text-red-600 text-base"></i> Catatan Audit Review Pengawas (Ditolak)
                    @else
                      <i class="ph-bold ph-clipboard-text text-emerald-600 text-base"></i> Catatan Audit Review Pengawas (Disetujui)
                    @endif
                  </span>
                  <span class="text-[10px] font-mono font-bold px-2.5 py-0.5 rounded-full border {{
                    $isRejected ? 'text-red-800 bg-red-100/90 border-red-300' : 'text-emerald-800 bg-emerald-100/90 border-emerald-300'
                  }}">
                    Audit: {{ $isRejected ? 'Ditolak' : 'Disetujui' }} ({{ $project->tanggal_pemeriksaan ? date('d M Y', strtotime($project->tanggal_pemeriksaan)) : date('d M Y') }})
                  </span>
                </div>
                <p class="text-xs font-medium leading-relaxed italic pl-6 border-l-2 {{
                  $isRejected ? 'text-red-950 border-red-500' : 'text-emerald-950 border-emerald-400'
                }}">
                  "{{ $project->verification_note }}"
                </p>
                <div class="text-[10px] font-semibold pt-1 flex items-center gap-1 {{
                  $isRejected ? 'text-red-700' : 'text-emerald-700'
                }}">
                  @if($isRejected)
                    <i class="ph-bold ph-warning-circle text-red-600"></i> Pengajuan ditolak pengawas. Kontraktor wajib melengkapi ulang dokumentasi fisik.
                  @else
                    <i class="ph-bold ph-seal-check text-emerald-600"></i> Diverifikasi oleh Pengawas Lapangan Dinas PUPR Kabupaten Banjarnegara
                  @endif
                </div>
              </div>
            @endif

            <!-- Catatan Persetujuan Akhir Admin (Lapisan Final) -->
            @if($project->final_verification_note)
              <div class="mt-4 p-4 rounded-xl space-y-2 border {{ $project->verification_status === 'verified' ? 'bg-emerald-50/90 border-emerald-200/90' : 'bg-red-50/90 border-red-200/90' }}">
                <div class="flex items-center justify-between">
                  <span class="text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1.5 {{ $project->verification_status === 'verified' ? 'text-emerald-950' : 'text-red-950' }}">
                    @if($project->verification_status === 'verified')
                      <i class="ph-bold ph-seal-check text-emerald-600 text-base"></i> Persetujuan Akhir Admin PUPR
                    @else
                      <i class="ph-bold ph-x-circle text-red-600 text-base"></i> Persetujuan Akhir Admin PUPR (Ditolak)
                    @endif
                  </span>
                  <span class="text-[10px] font-mono font-bold px-2.5 py-0.5 rounded-full border {{ $project->verification_status === 'verified' ? 'text-emerald-800 bg-emerald-100/90 border-emerald-300' : 'text-red-800 bg-red-100/90 border-red-300' }}">
                    {{ $project->final_verified_at ? date('d M Y H:i', strtotime($project->final_verified_at)) : date('d M Y') }}
                  </span>
                </div>
                <p class="text-xs font-medium leading-relaxed italic pl-6 border-l-2 {{ $project->verification_status === 'verified' ? 'text-emerald-950 border-emerald-400' : 'text-red-950 border-red-500' }}">
                  "{{ $project->final_verification_note }}"
                </p>
                <div class="text-[10px] font-semibold pt-1 flex items-center gap-1 {{ $project->verification_status === 'verified' ? 'text-emerald-700' : 'text-red-700' }}">
                  <i class="ph-bold ph-shield-check {{ $project->verification_status === 'verified' ? 'text-emerald-600' : 'text-red-600' }}"></i>
                  Diberikan oleh Admin PUPR Dinas Pekerjaan Umum dan Penataan Ruang Kabupaten Banjarnegara
                </div>
              </div>
            @endif

            <!-- Pengajuan Menunggu Review Alert -->
            @if($project->verification_status === 'pending')
              <div class="mt-4 p-4 bg-amber-50 border border-amber-300 rounded-xl space-y-2">
                <div class="flex items-center justify-between">
                  <span class="text-[10px] font-extrabold text-amber-950 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="ph-bold ph-hourglass-high text-amber-600 text-base"></i> {{ $project->pengawas_verified_at ? 'Menunggu Persetujuan Akhir Admin PUPR (Lapisan 2)' : 'Pengajuan Progres Baru Menunggu Verifikasi Pengawas (Lapisan 1)' }}
                  </span>
                  <span class="text-[10px] font-mono font-bold text-amber-900 bg-amber-200 px-2.5 py-0.5 rounded-full border border-amber-300 animate-pulse">
                    Usulan: {{ number_format($project->reported_progress, 0) }}%
                  </span>
                </div>
                <p class="text-xs text-amber-900 leading-relaxed">
                  Kontraktor telah mengajukan pembaharuan progres fisik dari <strong>{{ number_format($project->progress, 0) }}% → {{ number_format($project->reported_progress, 0) }}%</strong> dan saat ini {{ $project->pengawas_verified_at ? 'sudah DIVERIFIKASI pengawas lapangan dan menunggu PERSETUJUAN AKHIR Admin PUPR (lapisan 2).' : 'dalam tahap audit inspeksi oleh pengawas lapangan (lapisan 1).' }}
                </p>
              </div>
            @endif

            <!-- TIMELINE RIWAYAT AUDIT & REVIEW PENGAWAS (DATABASE LOGS) -->
            <div class="mt-6 pt-6 border-t border-slate-100 space-y-4">
              <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5 font-heading">
                  <i class="ph-bold ph-clock-counter-clockwise text-gov-800"></i> Timeline Riwayat Review & Progres Pekerjaan
                </h4>
                <span class="text-[10px] text-slate-500 font-mono">Tercatat: {{ $project->logs->count() }} Audit Log</span>
              </div>

              <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                
                @forelse($project->logs as $log)
                  @php
                    $isLogReject = in_array($log->action, ['reject', 'final_reject']);
                    $isLogFinalReject = $log->action === 'final_reject';
                    $isLogApprove = $log->action === 'approve';
                    $isLogFinalApprove = $log->action === 'final_approve';
                    $isLogSubmission = $log->action === 'submission';
                  @endphp
                  <div class="relative">
                    <div class="absolute -left-6 top-1 w-4 h-4 rounded-full border-2 border-white {{
                      $isLogReject ? 'bg-red-500 shadow-sm shadow-red-500/50' : (
                        ($isLogApprove || $isLogFinalApprove) ? 'bg-emerald-500 shadow-sm shadow-emerald-500/50' : 'bg-amber-500 shadow-sm'
                      )
                    }}"></div>
                    <div class="p-3.5 rounded-xl border {{
                      $isLogReject ? 'bg-red-50/60 border-red-200' : (
                        ($isLogApprove || $isLogFinalApprove) ? 'bg-emerald-50/60 border-emerald-200' : 'bg-amber-50/60 border-amber-200'
                      )
                    }} space-y-1">
                      <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold {{
                          $isLogReject ? 'text-red-900' : (
                            ($isLogApprove || $isLogFinalApprove) ? 'text-emerald-900' : 'text-amber-900'
                          )
                        }}">
                          {{ $isLogFinalReject ? 'Persetujuan Akhir Admin (Ditolak)' : ($isLogFinalApprove ? 'Persetujuan Akhir Admin (Disetujui)' : ($isLogReject ? 'Inspeksi Pengawas (Ditolak)' : ($isLogApprove ? 'Verifikasi Pengawas (Disetujui)' : 'Pengajuan Progres Kontraktor'))) }} 
                          &bull; Capaian {{ number_format($log->progress, 0) }}%
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{
                          $isLogReject ? 'bg-red-100 text-red-700 border border-red-300' : (
                            ($isLogApprove || $isLogFinalApprove) ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300'
                          )
                        }}">
                          @if($isLogFinalApprove)
                            <i class="ph-bold ph-seal-check text-emerald-600"></i> Disetujui Final
                          @elseif($isLogFinalReject)
                            <i class="ph-bold ph-x-circle text-red-600"></i> Ditolak Final
                          @elseif($isLogReject)
                            <i class="ph-bold ph-x-circle text-red-600"></i> Ditolak
                          @elseif($isLogApprove)
                            <i class="ph-bold ph-check-circle text-emerald-600"></i> Disetujui Pengawas
                          @else
                            <i class="ph-bold ph-paper-plane-tilt text-amber-600"></i> Diajukan
                          @endif
                        </span>
                      </div>
                      <p class="text-xs text-slate-700 leading-relaxed italic">"{{ $log->note }}"</p>
                      <span class="block text-[10px] text-slate-400 font-mono">
                        Waktu: {{ date('d M Y H:i', strtotime($log->created_at)) }} &bull; Oleh {{ $log->action === 'submission' ? 'Penyedia Jasa' : (in_array($log->action, ['approve', 'reject']) ? 'Pengawas Lapangan PUPR' : 'Admin PUPR') }}
                      </span>
                    </div>
                  </div>
                @empty
                  <!-- Fallback jika belum ada log terpisah di database -->
                  @if($project->verification_note)
                    <div class="relative">
                      <div class="absolute -left-6 top-1 w-4 h-4 rounded-full border-2 border-white {{ $isRejected ? 'bg-red-500' : 'bg-emerald-500' }}"></div>
                      <div class="p-3.5 rounded-xl border {{ $isRejected ? 'bg-red-50/60 border-red-200' : 'bg-emerald-50/60 border-emerald-200' }} space-y-1">
                        <div class="flex items-center justify-between">
                          <span class="text-xs font-bold {{ $isRejected ? 'text-red-900' : 'text-emerald-900' }}">
                            Hasil Audit Pengawas (Capaian {{ number_format($project->progress, 0) }}%)
                          </span>
                          <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $isRejected ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-emerald-100 text-emerald-700 border border-emerald-300' }}">
                            @if($isRejected)
                              <i class="ph-bold ph-x-circle text-red-600"></i> Ditolak
                            @else
                              <i class="ph-bold ph-check-circle text-emerald-600"></i> Disetujui
                            @endif
                          </span>
                        </div>
                        <p class="text-xs text-slate-700 leading-relaxed italic">"{{ $project->verification_note }}"</p>
                        <span class="block text-[10px] text-slate-400 font-mono">
                          Tanggal Audit: {{ $project->tanggal_pemeriksaan ? date('d M Y', strtotime($project->tanggal_pemeriksaan)) : date('d M Y') }}
                        </span>
                      </div>
                    </div>
                  @endif
                @endforelse

                <!-- Item Awal SPMK -->
                <div class="relative">
                  <div class="absolute -left-6 top-1 w-4 h-4 rounded-full border-2 border-white bg-slate-400"></div>
                  <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                    <div class="flex items-center justify-between">
                      <span class="text-xs font-bold text-slate-800">Mulai Pelaksanaan Kontrak</span>
                      <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-slate-200 text-slate-700 border border-slate-300">
                        Awal Kontrak
                      </span>
                    </div>
                    <p class="text-xs text-slate-600">Surat Perintah Mulai Kerja (SPMK) diterbitkan dan pelaksanaan fisik dimulai.</p>
                    <span class="block text-[10px] text-slate-400 font-mono">
                      Tanggal SPMK: {{ $project->tanggal_pelaksanaan ? date('d M Y', strtotime($project->tanggal_pelaksanaan)) : date('d M Y', strtotime($project->created_at)) }}
                    </span>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right side: Spatials (Map) & Progress stats -->
      <div class="lg:col-span-4 space-y-6">
        
        <!-- Leaflet map card -->
        <div class="bg-white p-6 rounded border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2">Lokasi Spasial Pekerjaan</h3>
          <div class="rounded border border-slate-200 overflow-hidden">
            <div id="single-map"></div>
          </div>
          <div class="font-mono text-[10px] text-slate-500 text-center">
            Koordinat: {{ $project->latitude }}, {{ $project->longitude }}
          </div>
          @if($project->ruas_jalan_id)
          <div class="mt-2 flex items-center gap-2.5 text-xs bg-slate-50 border border-slate-200 rounded-lg p-2.5">
            <i class="ph-bold ph-road-horizon text-lg text-gov-600"></i>
            <div class="min-w-0">
              <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Ruas Jalan</span>
              <span class="font-semibold text-slate-800 truncate">{{ $project->ruas_jalan_nomor ? $project->ruas_jalan_nomor.' — ' : '' }}{{ $project->ruas_jalan_nama ?: 'Ruas #'.$project->ruas_jalan_id }}</span>
            </div>
          </div>
          @endif
        </div>

        <!-- Progress meter card -->
        <div class="bg-slate-900 text-white p-6 rounded border border-slate-800 space-y-4">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Metrik Capaian</h3>
          <div class="space-y-4">
            <div class="space-y-1">
              <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase">
                <span>PROGRESS CAPAIAN</span>
                <span class="text-emerald-400">{{ number_format($project->progress, 0) }}%</span>
              </div>
              <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
              </div>
            </div>
            
            <div class="text-[10px] text-slate-400 leading-relaxed border-t border-slate-800 pt-4">
              Capaian progress diaudit secara berkala oleh tim teknis dinas bina konstruksi kabupaten Banjarnegara.
            </div>
          </div>
        </div>

      </div>

    </div>
  </section>
@endsection

@section('scripts')
  <script>
    // Leaflet map focused on single project coordinates
    const lat = {{ $project->latitude }};
    const lng = {{ $project->longitude }};
    
    const map = L.map('single-map').setView([lat, lng], 14);

    const streetMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
      attribution: '&copy; Esri &mdash; Esri, HERE, Garmin, USGS, Intermap, INCREMENT P, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom',
      maxZoom: 20
    }).addTo(map);

    const imageryMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
      attribution: '&copy; Esri &mdash; Esri, Maxar, Earthstar Geographics, and the GIS User Community',
      maxZoom: 20
    });

    const topoMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
      attribution: '&copy; Esri &mdash; Esri, HERE, Garmin, USGS, Intermap, INCREMENT P, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom',
      maxZoom: 20
    });

    L.control.layers({
      'Street Map': streetMap,
      'Citra Satelit': imageryMap,
      'Topografi': topoMap
    }, null, { position: 'topright' }).addTo(map);

    // Custom styled circle marker
    const marker = L.circleMarker([lat, lng], {
      radius: 10,
      fillColor: '#10b981',
      color: '#ffffff',
      weight: 3,
      opacity: 1,
      fillOpacity: 0.95
    }).addTo(map);

    const progressVal = parseFloat({{$project->progress}}).toFixed(1);
    const statusText = @json($project->status);
    const labelText = {{$project->progress}} >= 100 ? 'Progres: 100% (Selesai)' : `Progres: ${progressVal}% (${statusText})`;

    @if($project->ruas_jalan_id)
    // Tampilkan ruas jalan tempat proyek berada (jika ditautkan)
    fetch('/ruas_jalan.geojson')
      .then(r => r.json())
      .then(gj => {
        const feat = gj.features.find(f => f.properties && String(f.properties.id) === String({{ $project->ruas_jalan_id }}));
        if (feat) {
          const ruasLabel = [feat.properties.nomor_ruas, feat.properties.nama_ruas].filter(Boolean).join(' — ') || ('Ruas #' + feat.properties.id);
          L.geoJSON(feat, { style: { color: '#f59e0b', weight: 5, opacity: 0.9 } })
            .bindPopup(`<div class="font-sans text-xs"><strong>Ruas Jalan</strong><br>${ruasLabel}</div>`)
            .addTo(map);
        }
      })
      .catch(() => {});
    @endif

    marker.bindPopup(`
      <div class="font-sans text-xs p-1">
        <div class="font-bold text-slate-900 leading-snug">${@json($project->nama_pekerjaan)}</div>
        <div class="text-gov-600 font-bold mt-1">${labelText}</div>
      </div>
    `).openPopup();

    function changeMainPhoto(src, stageText) {
      const img = document.getElementById('mainProgressImage');
      const stage = document.getElementById('mainPhotoStage');
      img.classList.add('opacity-40');
      setTimeout(() => {
        img.src = src;
        stage.innerText = stageText;
        img.classList.remove('opacity-40');
      }, 150);
    }

    function openLightbox(src, title) {
      document.getElementById('lightboxImage').src = src;
      document.getElementById('lightboxTitle').innerText = title;
      document.getElementById('lightboxModal').classList.remove('hidden');
    }

    function closeLightbox() {
      document.getElementById('lightboxModal').classList.add('hidden');
    }
  </script>

  <!-- LIGHTBOX MODAL PERBESAR FOTO -->
  <div id="lightboxModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md transition-all">
    <div class="relative max-w-4xl w-full bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 shadow-2xl">
      <div class="p-4 bg-slate-950 border-b border-slate-800 flex justify-between items-center text-white">
        <span class="text-xs font-bold" id="lightboxTitle">Foto Dokumentasi Progres Lapangan</span>
        <button type="button" onclick="closeLightbox()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors">
          <i class="ph-bold ph-x text-lg"></i>
        </button>
      </div>
      <div class="p-4 bg-slate-950 flex items-center justify-center min-h-[300px] max-h-[80vh]">
        <img id="lightboxImage" src="" alt="Detail Foto Progres" class="max-h-[72vh] w-auto object-contain rounded-xl shadow-lg border border-slate-800">
      </div>
    </div>
  </div>
@endsection
