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
        <!-- Visual Documentation -->
        <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
          <div class="relative aspect-[16/9] bg-slate-100">
            <img src="https://picsum.photos/seed/project-{{ $project->id }}/800/450" alt="{{ $project->nama_pekerjaan }}" class="w-full h-full object-cover">
          </div>
          <div class="p-6 border-t border-slate-200">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Dokumentasi Visual Fisik Lapangan</span>
            <p class="text-xs text-slate-500 leading-relaxed">
              Foto di atas memperlihatkan kemajuan pengerjaan fisik berkala paket pekerjaan di lapangan yang diunggah oleh pengawas lapangan Dinas PUPR Kabupaten Banjarnegara.
            </p>
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

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
      maxZoom: 20
    }).addTo(map);

    // Custom styled circle marker
    const marker = L.circleMarker([lat, lng], {
      radius: 10,
      fillColor: '#10b981',
      color: '#ffffff',
      weight: 3,
      opacity: 1,
      fillOpacity: 0.95
    }).addTo(map);

    marker.bindPopup(`
      <div class="font-sans text-xs p-1">
        <div class="font-bold text-slate-900 leading-snug">${@json($project->nama_pekerjaan)}</div>
        <div class="text-gov-600 font-bold mt-1">${parseFloat({{$project->progress}}).toFixed(1)}% Selesai</div>
      </div>
    `).openPopup();
  </script>
@endsection
