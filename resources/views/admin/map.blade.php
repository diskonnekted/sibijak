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
          <div class="flex items-center gap-3 text-xs text-slate-500 border-t border-slate-200 pt-3 mt-1">
            <span class="w-6 h-1 rounded-full bg-slate-400 inline-block"></span>
            <span>Garis ruas jalan menebal mengikuti skema warna progres di atas</span>
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
          <div class="flex justify-between border-b border-slate-200 pb-1.5">
            <span class="text-slate-500 font-medium">Ruas Jalan Ditautkan</span>
            <span class="font-bold text-slate-800">{{ $ruasStats['ruas_linked'] }} / {{ $ruasStats['total_ruas'] }} Ruas</span>
          </div>
          <div class="flex justify-between border-b border-slate-200 pb-1.5">
            <span class="text-slate-500 font-medium">Proyek di Ruas Jalan</span>
            <span class="font-bold text-slate-800">{{ $ruasStats['project_linked'] }} Paket</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500 font-medium">Format Wilayah</span>
            <span class="font-bold text-slate-800">20 Kecamatan</span>
          </div>
        </div>
      </div>
      
      <!-- GeoJSON notice -->
      <div class="text-[10px] text-slate-400 leading-relaxed bg-slate-50 p-4 rounded border border-slate-200">
        <i class="ph-bold ph-info text-gov-600"></i> Peta ini memuat data geospasial batas kecamatan dan jaringan ruas jalan Kabupaten Banjarnegara. Ruas jalan yang menebal menunjukkan ruas yang memiliki proyek, diwarnai sesuai kemajuan fisiknya.
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    // Leaflet map initialization focused on Banjarnegara
    const map = L.map('admin-map').setView([-7.39675, 109.69724], 11);

    // Basemaps Esri (gratis, tanpa API key) — bisa di-switch lewat control layer
    const streetMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
      attribution: '&copy; Esri &mdash; Esri, HERE, Garmin, USGS, Intermap, INCREMENT P, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom',
      maxZoom: 20
    });

    const imageryMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
      attribution: '&copy; Esri &mdash; Esri, Maxar, Earthstar Geographics, and the GIS User Community',
      maxZoom: 20
    });

    const topoMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
      attribution: '&copy; Esri &mdash; Esri, HERE, Garmin, USGS, Intermap, INCREMENT P, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom',
      maxZoom: 20
    });

    // Layer grup overlay untuk batas kecamatan (bisa di-toggle)
    const subdistrictLayer = L.layerGroup().addTo(map);

    // Layer grup overlay untuk titik proyek (bisa di-toggle)
    const projectLayer = L.layerGroup().addTo(map);

    // Pane khusus agar garis ruas jalan berada di bawah marker proyek
    map.createPane('ruasPane');
    map.getPane('ruasPane').style.zIndex = 600;
    map.getPane('ruasPane').style.pointerEvents = 'auto';

    // Layer grup overlay untuk jaringan ruas jalan (semua)
    const ruasBaseLayer = L.layerGroup();
    // Layer grup overlay untuk ruas jalan yang memuat proyek
    const ruasProjectLayer = L.layerGroup();

    const baseMaps = {
      'Street Map': streetMap,
      'Citra Satelit': imageryMap,
      'Topografi': topoMap
    };
    const overlayMaps = {
      'Batas Kecamatan': subdistrictLayer,
      'Jaringan Ruas Jalan': ruasBaseLayer,
      'Ruas Jalan Berproyek': ruasProjectLayer,
      'Titik Proyek': projectLayer
    };

    // Basemap default: Street Map
    streetMap.addTo(map);

    // Control layer (switcher) pojok kanan atas
    L.control.layers(baseMaps, overlayMaps, { position: 'topright' }).addTo(map);

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
        }).addTo(subdistrictLayer);
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
      }).addTo(projectLayer);

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

    // ---- Ruas Jalan (memanfaatkan public/ruas_jalan.geojson) ----
    // Kelompokkan proyek berdasarkan ruas jalan yang ditautkan.
    const projectByRuas = {};
    projectsList.forEach(p => {
      if (p.ruas_jalan_id) {
        (projectByRuas[p.ruas_jalan_id] = projectByRuas[p.ruas_jalan_id] || []).push(p);
      }
    });

    const fmtRuasLabel = (props) =>
      [props.nomor_ruas, props.nama_ruas].filter(Boolean).join(' — ') || ('Ruas #' + props.id);

    const ruasPopup = (props, linked) => {
      const plist = (linked || []).map(p => `
        <a href="/pekerjaan/${p.id}" target="_blank" class="block px-2 py-1 rounded bg-slate-100 hover:bg-gov-50 text-slate-700 no-underline text-[11px] mb-1">
          <strong>${p.nama_pekerjaan}</strong> — ${parseFloat(p.progress).toFixed(1)}%
        </a>`).join('');

      const orientasi = [props.titik_awal, props.titik_akhir].filter(Boolean).join(' → ');

      return `<div class="font-sans text-xs p-1" style="min-width:260px">
        <div class="font-extrabold text-slate-900 mb-1" style="font-size:13px">${fmtRuasLabel(props)}</div>
        ${orientasi ? `<div class="text-slate-500 mb-1">${orientasi}</div>` : ''}
        <div class="grid grid-cols-2 gap-2 my-2">
          <div><span class="block text-[9px] text-slate-400 font-bold uppercase">PANJANG</span><span class="font-bold text-slate-800">${(props.panjang_km || 0).toFixed(2)} km</span></div>
          <div><span class="block text-[9px] text-slate-400 font-bold uppercase">LEBAR</span><span class="font-bold text-slate-800">${props.lebar_m || 0} m</span></div>
        </div>
        ${linked && linked.length
          ? `<div class="pt-2 border-t border-slate-100"><span class="block text-[9px] text-slate-400 font-bold uppercase mb-1">Proyek di ruas ini (${linked.length})</span>${plist}</div>`
          : '<div class="pt-2 border-t border-slate-100 text-slate-400">Belum ada proyek pada ruas ini.</div>'}
      </div>`;
    };

    fetch('/ruas_jalan.geojson')
      .then(response => response.json())
      .then(geojsonData => {
        // a) Seluruh jaringan ruas jalan (garis tipis, referensi)
        L.geoJSON(geojsonData, {
          pane: 'ruasPane',
          style: () => ({ color: '#94a3b8', weight: 2, opacity: 0.55 }),
          onEachFeature: (feature, layer) => {
            const linked = projectByRuas[feature.properties.id] || [];
            layer.bindTooltip(fmtRuasLabel(feature.properties), { sticky: true, direction: 'top', className: 'text-[9px] font-bold text-slate-800 bg-white/90 border border-slate-200 rounded px-1' });
            layer.bindPopup(ruasPopup(feature.properties, linked));
          }
        }).addTo(ruasBaseLayer);

        // b) Ruas yang memuat proyek: ditebalkan & diwarnai sesuai progress
        const ruasProjectFeatures = geojsonData.features.filter(f => projectByRuas[f.properties.id]);
        L.geoJSON({ type: 'FeatureCollection', features: ruasProjectFeatures }, {
          pane: 'ruasPane',
          style: (feature) => {
            const ps = projectByRuas[feature.properties.id] || [];
            const avg = ps.reduce((s, x) => s + x.progress, 0) / ps.length;
            let color = '#ef4444';
            if (avg >= 80) color = '#10b981';
            else if (avg >= 40) color = '#f59e0b';
            return { color, weight: 5, opacity: 0.9 };
          },
          onEachFeature: (feature, layer) => {
            const linked = projectByRuas[feature.properties.id] || [];
            layer.bindPopup(ruasPopup(feature.properties, linked));
          }
        }).addTo(ruasProjectLayer);
      })
      .catch(err => console.error('Gagal memuat ruas jalan GeoJSON:', err));
  </script>
@endsection
