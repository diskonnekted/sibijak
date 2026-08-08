@extends('layouts.admin')

@section('title', 'Peta Sebaran Pekerjaan - SIKAP Admin')
@section('page_title', 'Peta Spasial Monitoring Pekerjaan')

@section('styles')
  <!-- Leaflet CSS & JS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <style>
    #admin-map {
      width: 100%;
      height: 100%;
      z-index: 10;
    }
  </style>
@endsection

@section('content')
  <div class="bg-white p-6 rounded border border-slate-200 shadow-sm grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Left side map -->
    <div class="lg:col-span-8 space-y-4">
      <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <div>
          <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Sebaran Titik Proyek Fisik & Batas Administrasi</h2>
          <span class="text-[10px] text-slate-400 font-semibold uppercase">Peta Terintegrasi GeoJSON Kecamatan</span>
        </div>
        <span class="text-[10px] font-bold text-gov-800 bg-gov-50 px-2 py-1 rounded border border-gov-100">PUPR Banjarnegara</span>
      </div>
      
      <div class="aspect-[4/3] w-full rounded border border-slate-200 overflow-hidden shadow-inner relative bg-slate-100">
        <div id="admin-map" class="absolute inset-0"></div>
      </div>
    </div>

    <!-- Right side information panel -->
    <div class="lg:col-span-4 space-y-6">
      
      <!-- Big metric -->
      <div class="bg-gov-900 text-white p-6 rounded border border-gov-950 space-y-3 shadow-sm">
        <span class="block text-[9px] font-bold text-slate-300 uppercase tracking-wider">Rata-rata Progress Fisik</span>
        <div class="flex items-baseline gap-2">
          <span class="text-4xl font-extrabold">{{ number_format($stats['average_progress'], 1) }}%</span>
          <span class="text-xs text-slate-300 font-semibold">Tuntas Audit</span>
        </div>
        <div class="w-full bg-slate-850 h-1.5 rounded-full overflow-hidden">
          <div class="bg-emerald-400 h-1.5 rounded-full" style="width: {{ $stats['average_progress'] }}%"></div>
        </div>
      </div>

      <!-- Color Indicator Status -->
      <div class="p-6 rounded bg-slate-50 border border-slate-200 space-y-4">
        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Status Progress Fisik</h3>
        <div class="space-y-3">
          <div class="flex items-center gap-3 text-xs text-slate-600 font-semibold">
            <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white shadow"></span>
            <span>Progres Tinggi (>= 80%)</span>
          </div>
          <div class="flex items-center gap-3 text-xs text-slate-600 font-semibold">
            <span class="w-3.5 h-3.5 rounded-full bg-amber-500 border-2 border-white shadow"></span>
            <span>Progres Sedang (40% - 79%)</span>
          </div>
          <div class="flex items-center gap-3 text-xs text-slate-600 font-semibold">
            <span class="w-3.5 h-3.5 rounded-full bg-red-500 border-2 border-white shadow"></span>
            <span>Progres Awal (< 40%)</span>
          </div>
        </div>
      </div>

      <!-- General Statistics -->
      <div class="p-6 bg-slate-50 rounded border border-slate-200 space-y-4">
        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Statistik Pengawasan</h3>
        <div class="space-y-2.5 text-xs">
          <div class="flex justify-between border-b border-slate-200 pb-1.5">
            <span class="text-slate-500 font-medium">Total Terpantau</span>
            <span class="font-bold text-slate-800">{{ $stats['total_projects'] }} Paket</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500 font-medium">Format Wilayah</span>
            <span class="font-bold text-slate-800">20 Kecamatan</span>
          </div>
        </div>
      </div>
      
      <!-- GeoJSON notice -->
      <div class="text-[10px] text-slate-400 leading-relaxed bg-slate-50 p-4 rounded border border-slate-200">
        <i class="ph-bold ph-info text-gov-600"></i> Peta ini memuat data geospasial pembagian wilayah administratif kecamatan Kabupaten Banjarnegara yang diambil dari berkas GeoJSON.
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    // Leaflet map initialization focused on Banjarnegara
    const map = L.map('admin-map').setView([-7.39675, 109.69724], 11);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
      maxZoom: 20
    }).addTo(map);

    // Fetch and draw subdistrict geojson boundary
    fetch('/peta_kecamatan.geojson')
      .then(response => response.json())
      .then(geojsonData => {
        L.geoJSON(geojsonData, {
          style: function(feature) {
            return {
              color: "#0f3d24", // Deep emerald border
              weight: 1.5,
              opacity: 0.8,
              fillColor: "#16a34a", // Soft green fill
              fillOpacity: 0.08
            };
          },
          onEachFeature: function(feature, layer) {
            // Check for district name property
            const kecName = feature.properties.KECAMATAN || feature.properties.name || feature.properties.NAMOBJ;
            if (kecName) {
              layer.bindTooltip(kecName, {
                permanent: false,
                direction: "center",
                className: "text-[9px] font-bold text-gov-900 bg-white/90 border border-gov-100 rounded px-1"
              });
            }
          }
        }).addTo(map);
      })
      .catch(err => console.error("Gagal memuat batas GeoJSON kecamatan:", err));

    // Create high-priority pane for markers to stay above GeoJSON vector layers
    map.createPane('projectPane');
    map.getPane('projectPane').style.zIndex = 650;
    map.getPane('projectPane').style.pointerEvents = 'auto';

    // Render project markers
    const projectsList = @json($projects);

    projectsList.forEach(p => {
      let markerColor = 'red';
      if (p.progress >= 80) {
        markerColor = '#10b981';
      } else if (p.progress >= 40) {
        markerColor = '#f59e0b';
      } else {
        markerColor = '#ef4444';
      }

      const marker = L.circleMarker([p.latitude, p.longitude], {
        pane: 'projectPane',
        radius: 10,
        fillColor: markerColor,
        color: '#ffffff',
        weight: 2.5,
        opacity: 1,
        fillOpacity: 0.95
      }).addTo(map);

      const formatRupiah = (num) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);

      marker.bindPopup(`
        <div class="font-sans text-xs p-1" style="min-width:240px">
          <div class="font-extrabold text-slate-900 mb-1" style="font-size:13px">${p.nama_pekerjaan}</div>
          <div class="text-slate-500 mb-1">Pelaksana: <strong>${p.contractor.name}</strong></div>
          <div class="text-slate-500 mb-2">Lokasi: <em>${p.detail_lokasi || '-'}</em></div>
          <div class="grid grid-cols-2 gap-2 mt-2 pt-2 border-t border-slate-100">
            <div>
              <span class="block text-[9px] text-slate-400 font-bold uppercase">NILAI KONTRAK</span>
              <span class="font-bold text-slate-800">${formatRupiah(p.nilai_kontrak)}</span>
            </div>
            <div>
              <span class="block text-[9px] text-slate-400 font-bold uppercase">KEMAJUAN FISIK</span>
              <span class="font-bold text-gov-600">${parseFloat(p.progress).toFixed(1)}%</span>
            </div>
          </div>
          <div class="mt-3 pt-2 border-t border-slate-100">
            <a href="/pekerjaan/${p.id}" target="_blank" class="block w-full py-2 bg-slate-900 hover:bg-gov-900 text-white font-bold rounded text-[9px] uppercase tracking-wider text-center no-underline transition-all">
              Lihat Detail Pekerjaan ↗
            </a>
          </div>
        </div>
      `);
    });
  </script>
@endsection
