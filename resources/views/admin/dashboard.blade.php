@extends('layouts.admin')

@section('title', 'Dashboard Utama - SIKAP Admin')
@section('page_title', 'Dashboard Ringkasan & Data')

@section('styles')
  <!-- Leaflet CSS & JS for modal mapping -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <style>
    .modal-map {
      height: 250px;
    }
  </style>
@endsection

@section('content')
  @php
    $currentRole = auth()->user() ? auth()->user()->role : 'admin_pupr';
  @endphp

  <!-- 1. MODE DESKTOP COMMAND CENTER VIEW (Tampil pada Layar >=768px) -->
  <div class="hidden md:block space-y-8">
    
    <!-- Toast Alert Success / Error -->
    @if(session('success'))
      <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3 text-sm font-semibold">
          <i class="ph-bold ph-check-circle text-lg text-emerald-600"></i>
          <span>{{ session('success') }}</span>
        </div>
      </div>
    @endif
    @if(session('error'))
      <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3 text-sm font-semibold">
          <i class="ph-bold ph-warning-circle text-lg text-red-600"></i>
          <span>{{ session('error') }}</span>
        </div>
      </div>
    @endif

    <!-- PENDING VERIFICATIONS SECTION (ONLY FOR ADMIN & EXAMINER) -->
    @if($currentRole !== 'kontraktor' && isset($pendingVerifications) && $pendingVerifications->count() > 0)
      <div class="bg-amber-50/50 border border-amber-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-2 text-amber-800">
          <i class="ph-fill ph-bell text-xl"></i>
          <h2 class="text-xs font-extrabold tracking-wide uppercase">Lapisan 1 &bull; Verifikasi Pengawas Lapangan ({{ $pendingVerifications->count() }} Pengajuan)</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($pendingVerifications as $pv)
            <div class="bg-white border border-amber-200 rounded-xl p-4 flex flex-col justify-between shadow-sm space-y-4">
              <div class="space-y-2">
                <div class="flex justify-between items-start gap-2">
                  <span class="text-[9px] font-bold text-gov-600 bg-gov-50 px-2 py-0.5 rounded uppercase tracking-wider">{{ $pv->contractor->name }}</span>
                  <span class="text-[9px] font-mono text-slate-400">{{ date('d M Y H:i', strtotime($pv->reported_at)) }}</span>
                </div>
                <h3 class="text-xs font-bold text-slate-900 leading-snug">{{ $pv->nama_pekerjaan }}</h3>
              </div>

              <!-- Reported Progress Info with Photo -->
              <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 flex gap-3 items-center">
                <img src="{{ asset(ltrim($pv->reported_photo ?? 'storage/projects/sample_default.jpg', '/')) }}" class="w-16 h-12 object-cover rounded border border-slate-200 flex-shrink-0" alt="Foto Progres" onerror="this.onerror=null; this.src='/storage/projects/sample_default.jpg';">
                <div class="min-w-0">
                  <span class="block text-[9px] font-bold text-slate-400 uppercase">Progres Diajukan</span>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xs font-bold text-slate-500 line-through">{{ number_format($pv->progress, 0) }}%</span>
                    <i class="ph-bold ph-arrow-right text-slate-400 text-xs"></i>
                    <span class="text-xs font-extrabold text-gov-600 bg-gov-100 px-1.5 py-0.5 rounded">{{ number_format($pv->reported_progress, 0) }}%</span>
                  </div>
                </div>
              </div>

              <!-- Action buttons -->
              <div class="flex gap-2">
                <button type="button" onclick="openAdminReviewModal({{ $pv->id }}, '{{ addslashes($pv->nama_pekerjaan) }}', '{{ addslashes($pv->contractor->name) }}', {{ $pv->progress }}, {{ $pv->reported_progress }}, 'reject')" class="flex-1 py-1.5 text-[10px] font-bold uppercase text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg active:scale-[0.98] transition-all">
                  Tolak
                </button>
                <button type="button" onclick="openAdminReviewModal({{ $pv->id }}, '{{ addslashes($pv->nama_pekerjaan) }}', '{{ addslashes($pv->contractor->name) }}', {{ $pv->progress }}, {{ $pv->reported_progress }}, 'approve')" class="flex-1 py-1.5 text-[10px] font-bold uppercase text-white bg-gov-900 hover:bg-gov-950 rounded-lg active:scale-[0.98] transition-all shadow-sm">
                  Setujui
                </button>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    <!-- FINAL APPROVAL SECTION (LAPISAN AKHIR - ADMIN PUPR) -->
    @if($currentRole === 'admin_pupr' && isset($pendingFinalVerifications) && $pendingFinalVerifications->count() > 0)
      <div class="bg-emerald-50/50 border border-emerald-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-2 text-emerald-800">
          <i class="ph-fill ph-seal-check text-xl"></i>
          <h2 class="text-xs font-extrabold tracking-wide uppercase">Lapisan 2 &bull; Persetujuan Akhir Admin ({{ $pendingFinalVerifications->count() }} Pengajuan)</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($pendingFinalVerifications as $pv)
            <div class="bg-white border border-emerald-200 rounded-xl p-4 flex flex-col justify-between shadow-sm space-y-4">
              <div class="space-y-2">
                <div class="flex justify-between items-start gap-2">
                  <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded uppercase tracking-wider">{{ $pv->contractor->name }}</span>
                  <span class="text-[9px] font-mono text-slate-400">Pengawas: {{ $pv->pengawas_verified_at ? date('d M Y H:i', strtotime($pv->pengawas_verified_at)) : '-' }}</span>
                </div>
                <h3 class="text-xs font-bold text-slate-900 leading-snug">{{ $pv->nama_pekerjaan }}</h3>
              </div>

              <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 flex gap-3 items-center">
                <img src="{{ asset(ltrim($pv->reported_photo ?: 'storage/projects/sample_default.jpg', '/')) }}" class="w-16 h-12 object-cover rounded border border-slate-200 flex-shrink-0" alt="Foto Progres" onerror="this.onerror=null; this.src='/storage/projects/sample_default.jpg';">
                <div class="min-w-0">
                  <span class="block text-[9px] font-bold text-slate-400 uppercase">Usulan Progres Final</span>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xs font-bold text-slate-500 line-through">{{ number_format($pv->progress, 0) }}%</span>
                    <i class="ph-bold ph-arrow-right text-slate-400 text-xs"></i>
                    <span class="text-xs font-extrabold text-emerald-600 bg-emerald-100 px-1.5 py-0.5 rounded">{{ number_format($pv->reported_progress, 0) }}%</span>
                  </div>
                </div>
              </div>

              <div class="flex gap-2">
                <button type="button" onclick="openAdminFinalModal({{ $pv->id }}, '{{ addslashes($pv->nama_pekerjaan) }}', '{{ addslashes($pv->contractor->name) }}', {{ $pv->progress }}, {{ $pv->reported_progress }}, 'reject')" class="flex-1 py-1.5 text-[10px] font-bold uppercase text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg active:scale-[0.98] transition-all">
                  Tolak
                </button>
                <button type="button" onclick="openAdminFinalModal({{ $pv->id }}, '{{ addslashes($pv->nama_pekerjaan) }}', '{{ addslashes($pv->contractor->name) }}', {{ $pv->progress }}, {{ $pv->reported_progress }}, 'approve')" class="flex-1 py-1.5 text-[10px] font-bold uppercase text-white bg-emerald-700 hover:bg-emerald-800 rounded-lg active:scale-[0.98] transition-all shadow-sm">
                  Setujui Final
                </button>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    <!-- QUICK METRICS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white p-6 rounded border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Kontraktor</span>
          <span class="text-3xl font-extrabold text-slate-900 leading-none">{{ $stats['total_contractors'] }}</span>
        </div>
        <div class="w-12 h-12 rounded bg-slate-50 border border-slate-100 flex items-center justify-center">
          <i class="ph-bold ph-buildings text-2xl text-slate-500"></i>
        </div>
      </div>

      <div class="bg-white p-6 rounded border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Paket Pekerjaan</span>
          <span class="text-3xl font-extrabold text-slate-900 leading-none">{{ $stats['total_projects'] }}</span>
        </div>
        <div class="w-12 h-12 rounded bg-slate-50 border border-slate-100 flex items-center justify-center">
          <i class="ph-bold ph-hard-hat text-2xl text-slate-500"></i>
        </div>
      </div>

      <div class="bg-white p-6 rounded border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Kontrak</span>
          <span class="text-lg font-extrabold text-slate-900 leading-none">Rp {{ number_format($stats['total_contract_value']/1000000000, 2) }} M</span>
        </div>
        <div class="w-12 h-12 rounded bg-slate-50 border border-slate-100 flex items-center justify-center">
          <i class="ph-bold ph-currency-circle-dollar text-2xl text-slate-500"></i>
        </div>
      </div>

      <div class="bg-white p-6 rounded border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kemajuan Fisik</span>
          <span class="text-3xl font-extrabold text-gov-600 leading-none">{{ number_format($stats['average_progress'], 1) }}%</span>
        </div>
        <div class="w-12 h-12 rounded bg-slate-50 border border-slate-100 flex items-center justify-center">
          <i class="ph-bold ph-chart-line text-2xl text-slate-500"></i>
        </div>
      </div>
    </div>

    <!-- TABS CONTROLLER CARD -->
    <div class="bg-white rounded border border-slate-200 overflow-hidden shadow-sm">
      <div class="border-b border-slate-200 bg-slate-50/50 flex flex-wrap justify-between items-center px-6">
        <div class="flex gap-6">
          @if($currentRole !== 'kontraktor')
            <button onclick="switchTab('kontraktor')" id="btn-tab-kontraktor" class="py-4 text-sm font-bold border-b-2 border-gov-900 text-gov-900 focus:outline-none transition-all">
              Daftar Kontraktor
            </button>
          @endif
          <button onclick="switchTab('pekerjaan')" id="btn-tab-pekerjaan" class="py-4 text-sm font-bold border-b-2 @if($currentRole === 'kontraktor') border-gov-900 text-gov-900 @else border-transparent text-slate-500 hover:text-slate-800 @endif focus:outline-none transition-all">
            Daftar Pekerjaan / Proyek
          </button>
          @if($currentRole === 'admin_pupr')
            <button onclick="switchTab('pengawas')" id="btn-tab-pengawas" class="py-4 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 focus:outline-none transition-all">
              Pengawas Lapangan
            </button>
          @endif
        </div>
        
        <div class="py-3 flex gap-2">
          @if($currentRole === 'admin_pupr')
            <button onclick="openContractorModal()" id="btn-add-kontraktor" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold tracking-wider uppercase text-white bg-gov-900 hover:bg-gov-950 rounded transition-all active:scale-[0.98]">
              <i class="ph-bold ph-plus"></i> Kontraktor Baru
            </button>
            <button onclick="openProjectModal()" id="btn-add-pekerjaan" class="hidden inline-flex items-center gap-2 px-4 py-2 text-xs font-bold tracking-wider uppercase text-white bg-gov-900 hover:bg-gov-950 rounded transition-all active:scale-[0.98]">
              <i class="ph-bold ph-plus"></i> Pekerjaan Baru
            </button>
            <button onclick="openPengawasModal()" id="btn-add-pengawas" class="hidden inline-flex items-center gap-2 px-4 py-2 text-xs font-bold tracking-wider uppercase text-white bg-gov-900 hover:bg-gov-950 rounded transition-all active:scale-[0.98]">
              <i class="ph-bold ph-plus"></i> Pengawas Baru
            </button>
          @endif
        </div>
      </div>

      <!-- TAB KONTRAKTOR CONTENT -->
      @if($currentRole !== 'kontraktor')
        <div id="tab-content-kontraktor" class="p-6">
          @if($currentRole === 'admin_pupr' && $pendingContractors->isNotEmpty())
          <div class="mb-5 p-4 bg-amber-50 border border-amber-300 rounded-lg">
            <h3 class="text-sm font-bold text-amber-800 mb-1">Pendaftaran Badan Usaha Baru</h3>
            <p class="text-xs text-amber-600 mb-3">{{ $pendingContractors->count() }} badan usaha menunggu verifikasi.</p>
            <div class="space-y-2">
              @foreach($pendingContractors as $pc)
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-3 bg-white rounded border border-amber-200">
                <div class="min-w-0">
                  <p class="text-sm font-bold text-slate-900">{{ $pc->name }}</p>
                  <p class="text-xs text-slate-500">NIB {{ $pc->nib }} · {{ $pc->bidang }} · {{ $pc->kualifikasi }}{{ $pc->email ? ' · ' . $pc->email : '' }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                  <a href="{{ route('admin.contractors.show', $pc) }}" class="px-3 py-1.5 rounded border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Lihat</a>
                  <form action="{{ route('admin.contractors.approve', $pc) }}" method="POST" class="inline">@csrf<button type="submit" class="px-3 py-1.5 rounded bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700">Setujui</button></form>
                  <form action="{{ route('admin.contractors.reject', $pc) }}" method="POST" class="inline" onsubmit="return confirm('Tolak pendaftaran badan usaha ini?')">@csrf<button type="submit" class="px-3 py-1.5 rounded bg-red-600 text-white text-xs font-bold hover:bg-red-700">Tolak</button></form>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          @endif
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase tracking-wider">
                  <th class="pb-3 font-semibold">Nama Perusahaan</th>
                  <th class="pb-3 font-semibold">NIB</th>
                  <th class="pb-3 font-semibold">PJ</th>
                  <th class="pb-3 font-semibold">Bidang</th>
                  <th class="pb-3 font-semibold">Kualifikasi</th>
                  <th class="pb-3 font-semibold">Rating</th>
                  <th class="pb-3 font-semibold">Status</th>
                  <th class="pb-3 font-semibold text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($contractors as $c)
                  <tr>
                    <td class="py-4 font-bold text-slate-900">
                      <a href="{{ route('admin.contractors.show', $c) }}" class="hover:text-gov-900 transition-colors">
                        {{ $c->name }}
                      </a>
                    </td>
                    <td class="py-4 font-mono text-xs">{{ $c->nib }}</td>
                    <td class="py-4">{{ $c->pj }}</td>
                    <td class="py-4">{{ $c->bidang }}</td>
                    <td class="py-4"><span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-[10px] font-bold text-slate-600 uppercase">{{ $c->kualifikasi }}</span></td>
                    <td class="py-4 font-semibold text-amber-600"><i class="ph-fill ph-star"></i> {{ number_format($c->rating, 1) }}</td>
                    <td class="py-4">
                      @if($c->status === 'pending')
                        <span class="px-2 py-0.5 rounded bg-amber-100 border border-amber-300 text-[10px] font-bold text-amber-700 uppercase">Menunggu</span>
                      @elseif($c->status === 'rejected')
                        <span class="px-2 py-0.5 rounded bg-red-100 border border-red-300 text-[10px] font-bold text-red-700 uppercase">Ditolak</span>
                      @else
                        <span class="px-2 py-0.5 rounded bg-emerald-100 border border-emerald-300 text-[10px] font-bold text-emerald-700 uppercase">Disetujui</span>
                      @endif
                    </td>
                    <td class="py-4 text-right space-x-2">
                      <a href="{{ route('admin.contractors.show', $c) }}" class="p-1.5 text-slate-500 hover:text-gov-900 rounded hover:bg-slate-50 inline-block text-xs" title="Lihat Profil">
                        <i class="ph-bold ph-eye text-base"></i>
                      </a>
                      @if($currentRole === 'admin_pupr')
                        <button onclick="openContractorModal({{ json_encode($c) }})" class="p-1.5 text-slate-500 hover:text-gov-900 rounded hover:bg-slate-50" title="Edit">
                          <i class="ph-bold ph-pencil-simple text-base"></i>
                        </button>
                        <form action="{{ route('admin.contractors.delete', $c) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontraktor ini?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 rounded hover:bg-slate-50" title="Hapus">
                            <i class="ph-bold ph-trash text-base"></i>
                          </button>
                        </form>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="py-8 text-center text-slate-400 font-medium">Belum ada data kontraktor.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      @endif

      <!-- TAB PEKERJAAN CONTENT -->
      <div id="tab-content-pekerjaan" class="@if($currentRole !== 'kontraktor') hidden @endif p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
          <div>
            <span class="text-xs font-bold text-slate-700">Daftar Paket Pekerjaan</span>
            <span class="block text-[10px] text-slate-400 font-semibold">
              {{ $selectedTahun ? 'TA ' . $selectedTahun . ' — ' : '' }}{{ $projects->count() }} paket ditampilkan
            </span>
          </div>
          <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
            <label for="tahun-filter" class="text-[10px] font-bold text-slate-500 uppercase whitespace-nowrap">Tahun Anggaran</label>
            <select id="tahun-filter" name="tahun" onchange="this.form.submit()" class="text-xs font-semibold text-slate-700 border border-slate-300 rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-gov-600">
              <option value="">Semua Tahun</option>
              @foreach($availableYears as $y)
                <option value="{{ $y }}" @selected((string) $selectedTahun === (string) $y)>TA {{ $y }}</option>
              @endforeach
            </select>
          </form>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <th class="pb-3 font-semibold">Nama Pekerjaan</th>
                <th class="pb-3 font-semibold">Pelaksana</th>
                <th class="pb-3 font-semibold">Nilai Kontrak</th>
                <th class="pb-3 font-semibold">T.A.</th>
                <th class="pb-3 font-semibold">Progress</th>
                <th class="pb-3 font-semibold">Koordinat</th>
                <th class="pb-3 font-semibold text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
              @forelse($projects as $p)
                <tr>
                  <td class="py-4 font-bold text-slate-900 max-w-[250px] truncate" title="{{ $p->nama_pekerjaan }}">{{ $p->nama_pekerjaan }}</td>
                  <td class="py-4 text-xs font-medium text-slate-600">
                    <span class="block">{{ $p->contractor->name }}</span>
                    @if($p->assignedPengawas)
                      <span class="block text-[10px] text-gov-700 font-bold mt-0.5"><i class="ph-bold ph-user-focus"></i> {{ $p->assignedPengawas->name }}</span>
                    @elseif($currentRole === 'admin_pupr')
                      <span class="block text-[10px] text-slate-300 mt-0.5">Belum ditugaskan</span>
                    @endif
                  </td>
                  <td class="py-4 font-semibold text-slate-800">Rp {{ number_format($p->nilai_kontrak, 0, ',', '.') }}</td>
                  <td class="py-4 font-mono text-xs">{{ $p->tahun_anggaran }}</td>
                  <td class="py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-24 bg-slate-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-gov-600 h-2 rounded-full" style="width: {{ $p->progress }}%"></div>
                      </div>
                      <span class="font-bold text-xs">{{ number_format($p->progress, 0) }}%</span>
                    </div>
                  </td>
                  <td class="py-4 font-mono text-xs text-slate-500">{{ $p->latitude }}, {{ $p->longitude }}</td>
                  <td class="py-4 text-right space-x-2">
                    <a href="{{ route('pekerjaan.show', $p) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-gov-900 rounded hover:bg-slate-50 inline-block text-xs" title="Lihat Detail Pekerjaan">
                      <i class="ph-bold ph-eye text-base"></i>
                    </a>
                    
                    @if($currentRole !== 'pemeriksa_lapangan' || $p->status !== 'Selesai')
                      <button onclick="openProjectModal({{ json_encode($p) }})" class="p-1.5 text-slate-500 hover:text-gov-900 rounded hover:bg-slate-50" title="Edit">
                        <i class="ph-bold ph-pencil-simple text-base"></i>
                      </button>
                    @endif

                    @if($currentRole === 'admin_pupr')
                      <form action="{{ route('admin.projects.delete', $p) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pekerjaan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 rounded hover:bg-slate-50" title="Hapus">
                          <i class="ph-bold ph-trash text-base"></i>
                        </button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="py-8 text-center text-slate-400 font-medium">Belum ada data pekerjaan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB PENGAWAS CONTENT -->
      @if($currentRole === 'admin_pupr')
      <div id="tab-content-pengawas" class="hidden p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
          <div>
            <h3 class="text-lg font-extrabold text-slate-900 font-heading">Manajemen Pengawas Lapangan</h3>
            <p class="text-xs text-slate-500 mt-0.5">Akun pemeriksa lapangan yang memverifikasi laporan progres, melakukan cross-check dokumentasi di lapangan, dan memberi persetujuan lapisan pertama.</p>
          </div>
          <span class="px-3 py-1.5 rounded bg-slate-100 border border-slate-200 text-xs font-bold text-slate-600">{{ $pengawas->count() }} pengawas terdaftar</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <th class="pb-3 font-semibold">Nama Pengawas</th>
                <th class="pb-3 font-semibold">NIP</th>
                <th class="pb-3 font-semibold">Bidang</th>
                <th class="pb-3 font-semibold">Email</th>
                <th class="pb-3 font-semibold text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
              @forelse($pengawas as $p)
                <tr>
                  <td class="py-4 font-bold text-slate-900">
                    <div class="flex items-center gap-2.5">
                      <span class="w-8 h-8 rounded bg-gov-900 text-white flex items-center justify-center text-xs font-extrabold">{{ strtoupper(mb_substr($p->name, 0, 1)) }}</span>
                      <span>{{ $p->name }}</span>
                    </div>
                  </td>
                  <td class="py-4 font-mono text-xs">{{ $p->nip ?: '—' }}</td>
                  <td class="py-4">
                    @if($p->bidang)
                      <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-[10px] font-bold text-slate-600 uppercase">{{ $p->bidang }}</span>
                    @else
                      <span class="text-xs text-slate-300">—</span>
                    @endif
                  </td>
                  <td class="py-4 text-slate-500">{{ $p->email }}</td>
                  <td class="py-4 text-right space-x-2">
                    <button onclick="openPengawasModal({{ json_encode(['id' => $p->id, 'name' => $p->name, 'nip' => $p->nip, 'bidang' => $p->bidang, 'email' => $p->email]) }})" class="p-1.5 text-slate-500 hover:text-gov-900 rounded hover:bg-slate-50" title="Edit">
                      <i class="ph-bold ph-pencil-simple text-base"></i>
                    </button>
                    <form action="{{ route('admin.pengawas.delete', $p) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengawas ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 rounded hover:bg-slate-50" title="Hapus">
                        <i class="ph-bold ph-trash text-base"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-8 text-center text-slate-400 font-medium">Belum ada data pengawas lapangan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @endif
    </div>
  </div>

  <!-- ========================================================= -->
  <!-- 2. MODE MOBILE NATIVE VIEW (Tampil pada Layar <768px) -->
  <!-- ========================================================= -->
  <div class="block md:hidden min-h-screen bg-slate-950 text-slate-100 font-sans p-4 pb-24">
    
    <!-- TOP MOBILE HEADER NOTCH BAR -->
    <div class="bg-slate-900/95 border-b border-slate-800 p-3.5 sticky top-0 z-30 shadow-md backdrop-blur-md -mx-4 -mt-4 mb-4">
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded bg-gov-600 flex items-center justify-center text-white font-bold shadow-sm">
            <i class="ph-bold ph-shield-checkered text-lg"></i>
          </div>
          <div>
            <div class="inline-flex items-center gap-1 text-[8px] font-bold tracking-wider uppercase text-slate-300">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>PEMKAB BANJARNEGARA</span>
            </div>
            <h1 class="text-xs font-extrabold text-white leading-none font-heading uppercase">SIBIJAK ADMIN PUPR</h1>
          </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="w-8 h-8 rounded bg-red-950/80 border border-red-800 text-red-300 flex items-center justify-center shadow-md">
            <i class="ph-bold ph-sign-out text-sm"></i>
          </button>
        </form>
      </div>
    </div>

    <!-- MAIN TABS CONTENT MOBILE CONTAINER -->
    <main class="space-y-4 pt-1">
      
      <!-- ========================================== -->
      <!-- TAB 1: BERANDA MOBILE (EXECUTIVE COMMAND CENTER) -->
      <!-- ========================================== -->
      <div id="adm-m-tab-beranda" class="space-y-4">
        
        <!-- Executive Hero Banner Header Card -->
        <div class="relative rounded overflow-hidden border border-slate-800 bg-slate-950 p-5 shadow-2xl min-h-[150px] flex flex-col justify-end">
          <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-60" style="background-image: url('{{ asset('assets/contractor_hero.jpg') }}');"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/30"></div>

          <div class="relative z-10 space-y-2">
            <div class="flex items-center justify-between gap-2">
              <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-gov-900/90 border border-gov-750 text-emerald-300 text-[9px] font-bold tracking-wider uppercase backdrop-blur-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>PUSAT KOMANDO DINAS PUPR</span>
              </div>
              <span class="text-[9px] bg-gov-900/90 text-gov-300 px-2 py-0.5 rounded border border-gov-750 font-bold uppercase flex items-center gap-1 shrink-0 shadow-sm backdrop-blur-sm">
                <i class="ph-bold ph-shield-check text-emerald-400"></i> Admin System
              </span>
            </div>

            <div>
              <h2 class="text-base sm:text-lg font-extrabold text-white font-heading uppercase leading-tight drop-shadow-md">
                SIBIJAK ADMIN COMMAND CENTER
              </h2>
              <p class="text-[10px] sm:text-xs text-slate-200 font-medium leading-relaxed drop-shadow">
                Selamat datang, <strong class="text-white">{{ auth()->user()->name ?? 'Admin PUPR' }}</strong> — Sistem Informasi Pembinaan Jasa Konstruksi {{ date('l, d F Y') }}.
              </p>
            </div>
          </div>
        </div>

        <!-- ⚠️ WIDGET WARNING DEADLINE PEKERJAAN TERDEKAT -->
        @if(isset($upcomingDeadlines) && $upcomingDeadlines->count() > 0)
          <div class="bg-slate-950 border border-amber-500/40 rounded p-4 space-y-3 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
              <div class="flex items-center gap-2 text-amber-400">
                <i class="ph-bold ph-hourglass-high text-lg animate-bounce"></i>
                <h3 class="text-xs font-extrabold font-heading uppercase tracking-wider">Warning Deadline Terdekat</h3>
              </div>
              <span class="text-[9px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/30 px-2 py-0.5 rounded uppercase">
                {{ $upcomingDeadlines->count() }} Paket Monitor
              </span>
            </div>

            <div class="space-y-2.5">
              @foreach($upcomingDeadlines->take(3) as $ud)
                @php
                  $targetDate = \Carbon\Carbon::parse($ud->tanggal_deadline);
                  $daysLeft = (int) \Carbon\Carbon::now()->diffInDays($targetDate, false);
                @endphp
                <div class="bg-slate-900 border border-slate-800 p-3 rounded space-y-2">
                  <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0 flex-1">
                      <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block truncate">{{ $ud->contractor->name ?? 'Kontraktor' }}</span>
                      <h4 class="text-xs font-bold text-white leading-snug truncate font-heading uppercase">{{ $ud->nama_pekerjaan }}</h4>
                    </div>
                    @if($daysLeft < 0)
                      <span class="text-[9px] font-extrabold px-2 py-0.5 rounded bg-red-500/20 text-red-400 border border-red-500/30 whitespace-nowrap uppercase">
                        Terlewat {{ abs($daysLeft) }} Hari
                      </span>
                    @elseif($daysLeft <= 14)
                      <span class="text-[9px] font-extrabold px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 border border-amber-500/30 whitespace-nowrap uppercase">
                        Sisa {{ $daysLeft }} Hari
                      </span>
                    @else
                      <span class="text-[9px] font-extrabold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 whitespace-nowrap uppercase">
                        Sisa {{ $daysLeft }} Hari
                      </span>
                    @endif
                  </div>

                  <div class="space-y-1">
                    <div class="flex justify-between text-[9px] text-slate-400 font-medium">
                      <span>Target: {{ date('d M Y', strtotime($ud->tanggal_deadline)) }}</span>
                      <span class="text-amber-400 font-bold">Progres: {{ number_format($ud->progress, 0) }}%</span>
                    </div>
                    <div class="w-full bg-slate-950 h-1.5 rounded overflow-hidden border border-slate-800">
                      <div class="bg-amber-500 h-full rounded" style="width: {{ $ud->progress }}%"></div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <!-- PENDING VERIFICATIONS ALERT (IF ANY) -->
        @if(isset($pendingVerifications) && $pendingVerifications->count() > 0)
          <div class="bg-amber-950/40 border border-amber-700/50 p-4 rounded space-y-3 shadow-md">
            <div class="flex items-center justify-between text-amber-300">
              <div class="flex items-center gap-2">
                <i class="ph-fill ph-bell text-lg text-amber-400"></i>
                <h3 class="text-xs font-extrabold font-heading uppercase tracking-wider">Verifikasi Laporan Progres</h3>
              </div>
              <span class="text-[9px] bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded font-bold uppercase">
                {{ $pendingVerifications->count() }} Pengajuan
              </span>
            </div>
            <p class="text-[10px] text-amber-200/80 leading-relaxed">
              Terdapat {{ $pendingVerifications->count() }} pengajuan progres fisik baru dari kontraktor yang memerlukan persetujuan Admin/Pengawas.
            </p>
          </div>
        @endif

        <!-- 4 Stat Metrics Grid (2x2 Grid) -->
        <div class="grid grid-cols-2 gap-3">
          <div class="bg-slate-950 border border-slate-800 p-4 rounded space-y-1 shadow-sm">
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Total Kontraktor</span>
            <span class="text-xl font-extrabold text-white block font-heading">{{ $stats['total_contractors'] }}</span>
            <span class="text-[8px] text-gov-400 font-bold uppercase">Terverifikasi PUPR</span>
          </div>

          <div class="bg-slate-950 border border-slate-800 p-4 rounded space-y-1 shadow-sm">
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Paket Pekerjaan</span>
            <span class="text-xl font-extrabold text-white block font-heading">{{ $stats['total_projects'] }}</span>
            <span class="text-[8px] text-blue-400 font-bold uppercase">Fisik & Konstruksi</span>
          </div>

          <div class="bg-slate-950 border border-slate-800 p-4 rounded col-span-2 shadow-sm space-y-1">
            <div class="flex justify-between items-center">
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nilai Total Kontrak Active</span>
              <span class="text-[9px] text-emerald-400 font-bold uppercase">Progres Avg: {{ number_format($stats['average_progress'], 1) }}%</span>
            </div>
            <span class="text-lg font-extrabold text-emerald-400 block font-heading font-mono">
              Rp {{ number_format($stats['total_contract_value']/1000000000, 2) }} Milyar
            </span>
          </div>
        </div>

        <!-- Action Shortcuts Grid -->
        <div class="bg-slate-950 border border-slate-800 p-4 rounded space-y-3 shadow-sm">
          <h3 class="text-xs font-bold text-white uppercase tracking-wider font-heading">Menu Akses Cepat</h3>
          <div class="grid grid-cols-2 gap-2 text-xs font-bold">
            @if($currentRole === 'admin_pupr')
              <button type="button" onclick="openProjectModal()" class="p-3 bg-gov-600 hover:bg-gov-500 text-white rounded text-center flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.98] uppercase tracking-wider transition-all">
                <i class="ph-bold ph-plus text-sm"></i>
                <span>+ Paket Pekerjaan</span>
              </button>
              <button type="button" onclick="openContractorModal()" class="p-3 bg-white text-slate-950 hover:bg-slate-100 rounded text-center flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.98] uppercase tracking-wider transition-all">
                <i class="ph-bold ph-plus text-sm text-gov-600"></i>
                <span>+ Badan Usaha</span>
              </button>
            @endif
            <a href="{{ route('admin.map') }}" class="p-3 bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-800 rounded text-center flex items-center justify-center gap-1.5 shadow-sm uppercase tracking-wider">
              <i class="ph-bold ph-map-trifold text-sm text-amber-400"></i>
              <span>Peta GIS</span>
            </a>
            <a href="{{ route('admin.analysis') }}" class="p-3 bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-800 rounded text-center flex items-center justify-center gap-1.5 shadow-sm uppercase tracking-wider">
              <i class="ph-bold ph-chart-bar text-sm text-emerald-400"></i>
              <span>Analisis</span>
            </a>
          </div>
        </div>

      </div>

      <!-- ========================================== -->
      <!-- TAB 2: PEKERJAAN MOBILE (JOB DIRECTORY) -->
      <!-- ========================================== -->
      <div id="adm-m-tab-pekerjaan" class="hidden space-y-4">
        
        <div class="flex justify-between items-center">
          <div>
            <h2 class="text-xs font-bold text-white uppercase tracking-wider font-heading">Daftar Paket Pekerjaan</h2>
            <span class="text-[9px] text-slate-400">Total {{ $projects->count() }} Paket Fisik Registered</span>
          </div>
          @if($currentRole === 'admin_pupr')
            <button type="button" onclick="openProjectModal()" class="px-3 py-1.5 bg-gov-600 hover:bg-gov-500 text-white text-[10px] font-bold rounded uppercase tracking-wider shadow-md flex items-center gap-1">
              <i class="ph-bold ph-plus text-xs"></i> Paket Baru
            </button>
          @endif
        </div>

        <!-- Filter Status Pills -->
        <div class="flex gap-1.5 overflow-x-auto pb-1 text-[10px] font-bold no-scrollbar">
          <button type="button" onclick="filterAdminMobileProjects('all')" id="flt-prj-all" class="px-3 py-1.5 rounded bg-white text-slate-950 whitespace-nowrap uppercase tracking-wider">Semua ({{ $projects->count() }})</button>
          <button type="button" onclick="filterAdminMobileProjects('Persiapan')" id="flt-prj-persiapan" class="px-3 py-1.5 rounded bg-slate-950 text-slate-400 hover:text-white border border-slate-800 whitespace-nowrap uppercase tracking-wider">Persiapan</button>
          <button type="button" onclick="filterAdminMobileProjects('Pelaksanaan')" id="flt-prj-pelaksanaan" class="px-3 py-1.5 rounded bg-slate-950 text-slate-400 hover:text-white border border-slate-800 whitespace-nowrap uppercase tracking-wider">Pelaksanaan</button>
          <button type="button" onclick="filterAdminMobileProjects('Selesai')" id="flt-prj-selesai" class="px-3 py-1.5 rounded bg-slate-950 text-slate-400 hover:text-white border border-slate-800 whitespace-nowrap uppercase tracking-wider">Selesai</button>
        </div>

        <!-- Filter Tahun Anggaran Mobile -->
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center justify-between gap-2 bg-slate-900 border border-slate-800 rounded p-2">
          <label class="text-[9px] font-bold text-slate-400 uppercase whitespace-nowrap">Tahun Anggaran</label>
          <select name="tahun" onchange="this.form.submit()" class="bg-slate-950 text-slate-200 border border-slate-800 rounded px-2 py-1 text-xs font-semibold focus:outline-none focus:border-gov-600">
            <option value="">Semua Tahun</option>
            @foreach($availableYears as $y)
              <option value="{{ $y }}" @selected((string) $selectedTahun === (string) $y)>TA {{ $y }}</option>
            @endforeach
          </select>
        </form>

        <!-- Project Cards List -->
        <div class="space-y-3">
          @forelse($projects as $p)
            @php
              $daysLeft = $p->tanggal_deadline ? (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($p->tanggal_deadline), false) : null;
            @endphp
            <div class="bg-slate-950 border border-slate-800 p-4 rounded space-y-3 shadow-md adm-m-prj-card" data-status="{{ $p->status }}">
              
              <div class="flex justify-between items-start gap-2 border-b border-slate-800/80 pb-2.5">
                <div class="min-w-0 flex-1">
                  <span class="text-[8px] font-bold text-gov-400 uppercase tracking-widest block truncate">{{ $p->contractor->name ?? '-' }}</span>
                  <h3 class="text-xs font-extrabold text-white leading-snug font-heading uppercase">{{ $p->nama_pekerjaan }}</h3>
                </div>
                <span class="text-[8px] font-extrabold uppercase px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-slate-300 whitespace-nowrap">
                  {{ $p->status }}
                </span>
              </div>

              <!-- Deadline & Progress info -->
              <div class="space-y-2">
                <div class="flex justify-between items-center text-[10px]">
                  <span class="text-slate-400">Target Completion: <strong class="text-slate-200">{{ $p->tanggal_deadline ? date('d M Y', strtotime($p->tanggal_deadline)) : '-' }}</strong></span>
                  @if($daysLeft !== null && $p->status !== 'Selesai')
                    @if($daysLeft < 0)
                      <span class="text-[9px] font-bold text-red-400 uppercase">Terlewat {{ abs($daysLeft) }} Hari</span>
                    @else
                      <span class="text-[9px] font-bold text-amber-400 uppercase">Sisa {{ $daysLeft }} Hari</span>
                    @endif
                  @endif
                </div>

                <div class="space-y-1">
                  <div class="flex justify-between text-[10px] font-bold text-slate-400">
                    <span>Progres Fisik Field</span>
                    <span class="text-gov-400 font-extrabold font-mono">{{ number_format($p->progress, 0) }}%</span>
                  </div>
                  <div class="w-full bg-slate-900 h-2 rounded overflow-hidden border border-slate-800">
                    <div class="bg-gov-600 h-full rounded" style="width: {{ $p->progress }}%"></div>
                  </div>
                </div>
              </div>

              <!-- Nilai & Action Buttons -->
              <div class="flex items-center justify-between pt-2 border-t border-slate-800 text-xs">
                <span class="text-[10px] font-mono text-slate-300 font-bold">Rp {{ number_format($p->nilai_kontrak, 0, ',', '.') }}</span>
                <div class="flex gap-1.5">
                  <a href="{{ route('pekerjaan.show', $p->id) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white border border-slate-800 rounded text-[10px] font-bold uppercase tracking-wider">Detail</a>
                  @if($currentRole === 'admin_pupr')
                    <button type="button" onclick="openProjectModal({{ json_encode($p) }})" class="px-3 py-1.5 bg-gov-600 hover:bg-gov-500 text-white rounded text-[10px] font-bold uppercase tracking-wider">Edit</button>
                  @endif
                </div>
              </div>

            </div>
          @empty
            <div class="text-center py-12 text-slate-500 text-xs">Belum ada data pekerjaan.</div>
          @endforelse
        </div>

      </div>

      <!-- ========================================== -->
      <!-- TAB 3: KONTRAKTOR MOBILE (CONTRACTOR DIRECTORY) -->
      <!-- ========================================== -->
      <div id="adm-m-tab-kontraktor" class="hidden space-y-4">
        
        <div class="flex justify-between items-center">
          <div>
            <h2 class="text-xs font-bold text-white uppercase tracking-wider font-heading">Direktori Badan Usaha</h2>
            <span class="text-[9px] text-slate-400">Total {{ $contractors->count() }} Rekanan Terverifikasi</span>
          </div>
          @if($currentRole === 'admin_pupr')
            <button type="button" onclick="openContractorModal()" class="px-3 py-1.5 bg-white text-slate-950 hover:bg-slate-100 text-[10px] font-bold rounded uppercase tracking-wider shadow-md flex items-center gap-1">
              <i class="ph-bold ph-plus text-xs text-gov-600"></i> Badan Usaha
            </button>
          @endif
        </div>

        <!-- Contractor Cards List -->
        <div class="space-y-3">
          @forelse($contractors as $c)
            <div class="bg-slate-950 border border-slate-800 p-4 rounded space-y-3 shadow-md">
              <div class="flex justify-between items-start gap-2 border-b border-slate-800 pb-2.5">
                <div>
                  <h3 class="text-xs font-extrabold text-white leading-snug font-heading uppercase">{{ $c->name }}</h3>
                  <span class="text-[9px] text-gov-400 font-mono font-bold block mt-0.5">NIB: {{ $c->nib }}</span>
                </div>
                <span class="text-[9px] font-bold px-2.5 py-1 rounded bg-gov-950 text-gov-300 border border-gov-800 uppercase">
                  {{ $c->kualifikasi }}
                </span>
              </div>

              <div class="grid grid-cols-2 gap-2 text-[10px] text-slate-400 bg-slate-900 p-2.5 rounded border border-slate-800">
                <div>
                  <span class="text-slate-500 block text-[8px] uppercase">Penanggung Jawab</span>
                  <strong class="text-slate-200 truncate block">{{ $c->penanggung_jawab }}</strong>
                </div>
                <div>
                  <span class="text-slate-500 block text-[8px] uppercase">Pekerjaan Aktif</span>
                  <strong class="text-gov-400 block">{{ $c->projects_count }} Paket</strong>
                </div>
                <div>
                  <span class="text-slate-500 block text-[8px] uppercase">Bidang Usaha</span>
                  <strong class="text-slate-200 block">{{ $c->bidang ?? 'Sipil' }}</strong>
                </div>
                <div>
                  <span class="text-slate-500 block text-[8px] uppercase">Rating Kinerja</span>
                  <strong class="text-amber-400 block font-bold">⭐ {{ number_format($c->rating ?? 5.0, 1) }}</strong>
                </div>
              </div>

              @if($currentRole === 'admin_pupr')
                <div class="pt-1 flex justify-end gap-2">
                  <button type="button" onclick="openContractorModal({{ json_encode($c) }})" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white border border-slate-800 rounded text-[10px] font-bold uppercase tracking-wider">
                    Edit Badan Usaha
                  </button>
                </div>
              @endif
            </div>
          @empty
            <div class="text-center py-12 text-slate-500 text-xs">Belum ada data kontraktor.</div>
          @endforelse
        </div>

      </div>

      <!-- ========================================== -->
      <!-- TAB 4: PROFIL & SYSTEM CENTER MOBILE -->
      <!-- ========================================== -->
      <div id="adm-m-tab-profil" class="hidden space-y-4">
        
        <div class="space-y-1">
          <h2 class="text-xs font-bold text-white uppercase tracking-wider font-heading">Profil Administrator</h2>
          <span class="text-[9px] text-slate-400">Pengaturan Hak Akses Administrator SIBIJAK</span>
        </div>
        
        <div class="bg-slate-950 border border-slate-800 p-5 rounded space-y-5 text-xs shadow-md">
          
          <!-- Profile Badge Card -->
          <div class="flex items-center gap-3.5 border-b border-slate-800 pb-4">
            <div class="w-12 h-12 rounded bg-gov-900 flex items-center justify-center text-white border border-gov-750 text-xl shadow-md flex-shrink-0">
              <i class="ph-bold ph-shield-checkered"></i>
            </div>
            <div class="min-w-0">
              <h3 class="font-extrabold text-white text-sm font-heading uppercase truncate">{{ auth()->user()->name ?? 'Administrator PUPR' }}</h3>
              <span class="text-[10px] text-slate-400 block truncate">{{ auth()->user()->email ?? 'admin@pupr.banjarnegara.go.id' }}</span>
              <span class="text-[9px] text-gov-400 font-mono font-bold uppercase block mt-0.5">Dinas PUPR Kabupaten Banjarnegara</span>
            </div>
          </div>

          <!-- Quick Navigation Shortcuts -->
          <div class="space-y-2">
            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Modul Navigasi Admin</span>
            <div class="grid grid-cols-1 gap-2">
              <a href="{{ route('admin.map') }}" class="p-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded text-xs font-bold text-amber-300 flex items-center justify-between uppercase tracking-wider">
                <div class="flex items-center gap-2">
                  <i class="ph-bold ph-map-trifold text-base"></i>
                  <span>Peta Monitoring GIS Spasial</span>
                </div>
                <i class="ph-bold ph-arrow-right"></i>
              </a>
              <a href="{{ route('admin.analysis') }}" class="p-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded text-xs font-bold text-emerald-300 flex items-center justify-between uppercase tracking-wider">
                <div class="flex items-center gap-2">
                  <i class="ph-bold ph-chart-bar text-base"></i>
                  <span>Analisis Anggaran & Rekomendasi</span>
                </div>
                <i class="ph-bold ph-arrow-right"></i>
              </a>
              <a href="{{ route('admin.logs') }}" class="p-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded text-xs font-bold text-blue-300 flex items-center justify-between uppercase tracking-wider">
                <div class="flex items-center gap-2">
                  <i class="ph-bold ph-shield-check text-base"></i>
                  <span>Log Aktivitas & Audit Trail User</span>
                </div>
                <i class="ph-bold ph-arrow-right"></i>
              </a>
            </div>
          </div>

          <!-- Role Switcher Section (hanya aktif di environment lokal) -->
          @if(app()->environment('local'))
          <div class="space-y-2 pt-2 border-t border-slate-800">
            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Simulasi Uji Coba Peran (Switch Account)</span>
            <div class="grid grid-cols-1 gap-2">
              <a href="{{ route('sim-login', 2) }}" class="p-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded text-xs font-bold text-amber-300 flex items-center justify-between uppercase tracking-wider">
                <span>Switch ke Account Pengawas Lapangan</span>
                <i class="ph-bold ph-arrow-right"></i>
              </a>
              <a href="{{ route('sim-login', 1) }}" class="p-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded text-xs font-bold text-emerald-300 flex items-center justify-between uppercase tracking-wider">
                <span>Switch ke Account Kontraktor Pelaksana</span>
                <i class="ph-bold ph-arrow-right"></i>
              </a>
            </div>
          </div>
          @endif

          <!-- System Status Footer -->
          <div class="p-3 bg-slate-900 rounded border border-slate-800 text-[10px] space-y-1 text-slate-400 font-mono">
            <div class="flex justify-between">
              <span>Environment:</span>
              <strong class="text-gov-400">Laravel 12.65 / PHP 8.1</strong>
            </div>
            <div class="flex justify-between">
              <span>Database Status:</span>
              <strong class="text-emerald-400">Connected MySQL</strong>
            </div>
            <div class="flex justify-between">
              <span>Architecture:</span>
              <strong class="text-amber-400">SIBIJAK Dual Responsive v2.4</strong>
            </div>
          </div>

        </div>
      </div>

      <!-- ========================================== -->
      <!-- TAB 5: LOG AKTIVITAS USER MOBILE -->
      <!-- ========================================== -->
      @if($currentRole === 'admin_pupr')
        <div id="adm-m-tab-log" class="hidden space-y-4">
          <div class="space-y-1">
            <h2 class="text-xs font-bold text-white uppercase tracking-wider font-heading">Log Aktivitas Pengguna</h2>
            <span class="text-[9px] text-slate-400">Mencatat riwayat autentikasi, IP Address, dan tindakan pengguna</span>
          </div>

          <div class="space-y-3">
            @forelse($activityLogs as $log)
              <div class="bg-slate-950 border border-slate-800 p-3.5 rounded space-y-2 text-xs shadow-md">
                <div class="flex justify-between items-start">
                  <span class="text-[10px] font-mono text-slate-400">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                  @if(str_contains($log->action, 'SUCCESS'))
                    <span class="px-2 py-0.5 bg-emerald-950 text-emerald-300 border border-emerald-800 rounded text-[9px] font-extrabold uppercase">{{ $log->action }}</span>
                  @elseif(str_contains($log->action, 'FAILED') || str_contains($log->action, 'BLOCKED'))
                    <span class="px-2 py-0.5 bg-red-950 text-red-300 border border-red-800 rounded text-[9px] font-extrabold uppercase">{{ $log->action }}</span>
                  @else
                    <span class="px-2 py-0.5 bg-slate-900 text-slate-300 border border-slate-800 rounded text-[9px] font-extrabold uppercase">{{ $log->action }}</span>
                  @endif
                </div>

                <div>
                  <h4 class="font-extrabold text-white text-xs font-heading uppercase">{{ $log->user ? $log->user->name : ($log->email ?: 'Pengunjung / Guest') }}</h4>
                  <span class="text-[10px] font-mono text-slate-400 block">{{ $log->email }}</span>
                </div>

                <div class="flex justify-between items-center text-[10px] pt-1.5 border-t border-slate-800/80">
                  <span class="font-mono text-gov-400 font-bold">IP: {{ $log->ip_address }}</span>
                  <span class="text-slate-400 uppercase text-[9px] font-bold">{{ $log->role ?: 'guest' }}</span>
                </div>
                <p class="text-[11px] text-slate-300 italic leading-snug">{{ $log->description }}</p>
              </div>
            @empty
              <div class="p-6 text-center text-slate-500 text-xs">Belum ada log aktivitas tercatat.</div>
            @endforelse
          </div>

          <div class="pt-2 text-center">
            <a href="{{ route('admin.logs') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gov-600 hover:bg-gov-500 text-white font-bold text-xs uppercase tracking-wider rounded shadow-md transition-all">
              <i class="ph-bold ph-shield-check"></i>
              <span>Buka Halaman Audit Log Lengkap</span>
            </a>
          </div>
        </div>
      @endif

    </main>

    <!-- MOBILE STICKY BOTTOM NAVIGATION BAR -->
    <nav class="bg-slate-900/95 backdrop-blur-md border-t border-slate-800 fixed bottom-0 left-0 right-0 z-30 px-4 py-2">
      <div class="flex justify-around items-center max-w-md mx-auto">
        <button type="button" onclick="switchAdminMobileTab('beranda')" id="btn-adm-beranda" class="flex flex-col items-center gap-1 text-gov-400 transition-all font-bold">
          <i class="ph-bold ph-house text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Beranda</span>
        </button>
        <button type="button" onclick="switchAdminMobileTab('pekerjaan')" id="btn-adm-pekerjaan" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all">
          <i class="ph-bold ph-hard-hat text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Pekerjaan</span>
        </button>
        <button type="button" onclick="switchAdminMobileTab('kontraktor')" id="btn-adm-kontraktor" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all">
          <i class="ph-bold ph-buildings text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Kontraktor</span>
        </button>
        @if($currentRole === 'admin_pupr')
          <button type="button" onclick="switchAdminMobileTab('log')" id="btn-adm-log" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all">
            <i class="ph-bold ph-shield-check text-xl"></i>
            <span class="text-[9px] font-bold uppercase">Log User</span>
          </button>
        @endif
        <button type="button" onclick="switchAdminMobileTab('profil')" id="btn-adm-profil" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all">
          <i class="ph-bold ph-user-gear text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Profil</span>
        </button>
      </div>
    </nav>
  </div>

  <!-- MODAL CONTRACTOR (CREATE / EDIT) -->
  @if($currentRole === 'admin_pupr')
    <div id="modal-contractor" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" onclick="closeContractorModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
          <form id="form-contractor" action="{{ route('admin.contractors.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="method-contractor" value="POST">
            
            <div class="bg-gov-900 px-6 py-4 text-white">
              <h3 class="text-base font-bold" id="title-modal-contractor">Tambah Kontraktor Baru</h3>
            </div>

            <div class="p-6 space-y-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Perusahaan / Kontraktor</label>
                <input type="text" name="name" id="c-name" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIB</label>
                  <input type="text" name="nib" id="c-nib" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Rating Kinerja (0 - 5.0)</label>
                  <input type="number" step="0.1" name="rating" id="c-rating" min="0" max="5" value="5.0" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bidang Usaha</label>
                  <select name="bidang" id="c-bidang" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                    <option value="Sipil">Pekerjaan Sipil</option>
                    <option value="Arsitektur">Arsitektur</option>
                    <option value="Mekanikal">Mekanikal</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kualifikasi Usaha</label>
                  <select name="kualifikasi" id="c-kualifikasi" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                    <option value="Besar">Besar (B)</option>
                    <option value="Menengah">Menengah (M)</option>
                    <option value="Kecil">Kecil (K)</option>
                  </select>
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penanggung Jawab (Direktur)</label>
                <input type="text" name="pj" id="c-pj" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email</label>
                  <input type="email" name="email" id="c-email" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Telepon</label>
                  <input type="text" name="telepon" id="c-telepon" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Kantor</label>
                <textarea name="alamat" id="c-alamat" required rows="2" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all"></textarea>
              </div>
            </div>

            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-2 border-t border-slate-200">
              <button type="button" onclick="closeContractorModal()" class="px-4 py-2 text-xs font-bold uppercase text-slate-700 border border-slate-300 bg-white rounded active:scale-[0.98]">Batal</button>
              <button type="submit" class="px-4 py-2 text-xs font-bold uppercase text-white bg-gov-900 hover:bg-gov-950 rounded shadow active:scale-[0.98]">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

  <!-- MODAL PENGAWAS (CREATE / EDIT) -->
  @if($currentRole === 'admin_pupr')
    <div id="modal-pengawas" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" onclick="closePengawasModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
          <form id="form-pengawas" action="{{ route('admin.pengawas.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="method-pengawas" value="POST">

            <div class="bg-gov-900 px-6 py-4 text-white">
              <h3 class="text-base font-bold" id="title-modal-pengawas">Tambah Pengawas Lapangan</h3>
            </div>

            <div class="p-6 space-y-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="pw-name" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIP</label>
                  <input type="text" name="nip" id="pw-nip" placeholder="Opsional" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bidang Keahlian</label>
                  <select name="bidang" id="pw-bidang" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                    <option value="">— Pilih Bidang —</option>
                    @foreach($bidangPengawas as $bidang)
                      <option value="{{ $bidang }}">{{ $bidang }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email</label>
                <input type="email" name="email" id="pw-email" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password <span id="pw-password-label" class="text-slate-400 normal-case font-medium">(min. 6 karakter)</span></label>
                <input type="password" name="password" id="pw-password" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>

              <div class="bg-slate-50 border border-slate-200 rounded p-3 text-[11px] text-slate-500 leading-relaxed">
                <i class="ph-bold ph-info text-gov-700"></i>
                Pengawas lapangan dapat login ke portal <span class="font-bold">/pengawas</span> untuk memverifikasi laporan progres, mengunggah dokumentasi cross-check, dan memberikan persetujuan lapisan pertama sebelum persetujuan akhir admin.
              </div>
            </div>

            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-2 border-t border-slate-200">
              <button type="button" onclick="closePengawasModal()" class="px-4 py-2 text-xs font-bold uppercase text-slate-700 border border-slate-300 bg-white rounded active:scale-[0.98]">Batal</button>
              <button type="submit" class="px-4 py-2 text-xs font-bold uppercase text-white bg-gov-900 hover:bg-gov-950 rounded shadow active:scale-[0.98]">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

  <!-- MODAL PROJECT (CREATE / EDIT) -->
  <div id="modal-project" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" onclick="closeProjectModal()"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
      
      <div class="inline-block align-bottom bg-white rounded text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
        <form id="form-project" action="{{ route('admin.projects.store') }}" method="POST">
          @csrf
          <input type="hidden" name="_method" id="method-project" value="POST">
          
          <div class="bg-gov-900 px-6 py-4 text-white">
            <h3 class="text-base font-bold" id="title-modal-project">Tambah Paket Pekerjaan Baru</h3>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Paket Pekerjaan</label>
              <input type="text" name="nama_pekerjaan" id="p-name" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penyedia Jasa (Pelaksana)</label>
                <select name="contractor_id" id="p-contractor" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                  @foreach($approvedContractors as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pengawas Penanggung Jawab</label>
                <select name="pengawas_id" id="p-pengawas" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                  <option value="">— Belum Ditugaskan —</option>
                  @foreach($pengawas as $pw)
                    <option value="{{ $pw->id }}">{{ $pw->name }}@if($pw->bidang) &mdash; {{ $pw->bidang }}@endif</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nilai Kontrak (Rp)</label>
                <input type="number" name="nilai_kontrak" id="p-nilai" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tahun Anggaran</label>
                <input type="number" name="tahun_anggaran" id="p-tahun" value="2026" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Lapangan</label>
                <select name="status" id="p-status" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                  <option value="Persiapan">Persiapan</option>
                  <option value="Pelaksanaan">Pelaksanaan</option>
                  <option value="Selesai">Selesai</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Progress (%)</label>
                <input type="number" step="0.01" name="progress" id="p-progress" min="0" max="100" value="0.0" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
            </div>

            <!-- MONITORING TANGGAL -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Kontrak</label>
                <input type="date" name="tanggal_kontrak" id="p-tgl-kontrak" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal_pelaksanaan" id="p-tgl-pelaksanaan" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Pemeriksaan</label>
                <input type="date" name="tanggal_pemeriksaan" id="p-tgl-pemeriksaan" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Deadline</label>
                <input type="date" name="tanggal_deadline" id="p-tgl-deadline" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
            </div>

            <!-- PETA & KOORDINAT SELECTION -->
            <div class="space-y-2">
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Lokasi Proyek (Peta Spasial)</label>
              <div id="map-notice-box" class="text-[10px] text-slate-500 bg-slate-50 p-2 rounded border border-slate-200">
                <i class="ph-bold ph-info text-gov-600"></i> Klik lokasi pada peta di bawah ini untuk mengambil koordinat secara otomatis.
              </div>
              <div id="modal-map" class="modal-map rounded border border-slate-200"></div>
              
              <div class="grid grid-cols-2 gap-4 pt-2">
                <div>
                  <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Latitude</label>
                  <input type="text" name="latitude" id="p-lat" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                </div>
                <div>
                  <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Longitude</label>
                  <input type="text" name="longitude" id="p-lng" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                </div>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ruas Jalan (Lokasi Proyek)</label>
              <select name="ruas_jalan_id" id="p-ruas" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                <option value="">— Tidak terkait ruas jalan —</option>
                @foreach($ruasList as $r)
                  <option value="{{ $r['id'] }}">{{ $r['nomor_ruas'] ? $r['nomor_ruas'].' — ' : '' }}{{ $r['nama_ruas'] ?: 'Ruas #'.$r['id'] }} ({{ number_format($r['panjang_km'], 2) }} km)</option>
                @endforeach
              </select>
              <p id="ruas-notice" class="hidden mt-1 text-[11px] font-medium text-emerald-700"></p>
              <p class="mt-1 text-[11px] text-slate-500">Pilih ruas jalan tempat proyek berada, atau klik titik pada peta untuk deteksi otomatis ruas terdekat.</p>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Detail Keterangan Lokasi</label>
              <textarea name="detail_lokasi" id="p-detail" rows="2" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all" placeholder="Contoh: Desa Gumiwang RT 02 RW 01"></textarea>
            </div>
          </div>

          <div class="bg-slate-50 px-6 py-4 flex justify-end gap-2 border-t border-slate-200">
            <button type="button" onclick="closeProjectModal()" class="px-4 py-2 text-xs font-bold uppercase text-slate-700 border border-slate-300 bg-white rounded active:scale-[0.98]">Batal</button>
            <button type="submit" class="px-4 py-2 text-xs font-bold uppercase text-white bg-gov-900 hover:bg-gov-950 rounded shadow active:scale-[0.98]">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    const role = "{{ $currentRole }}";

    // Tab switching logic
    function switchTab(tabName) {
      const cTabBtn = document.getElementById('btn-tab-kontraktor');
      const pTabBtn = document.getElementById('btn-tab-pekerjaan');
      const pwTabBtn = document.getElementById('btn-tab-pengawas');
      const lTabBtn = document.getElementById('btn-tab-log');

      const cTabContent = document.getElementById('tab-content-kontraktor');
      const pTabContent = document.getElementById('tab-content-pekerjaan');
      const pwTabContent = document.getElementById('tab-content-pengawas');
      const lTabContent = document.getElementById('tab-content-log');

      const cAddBtn = document.getElementById('btn-add-kontraktor');
      const pAddBtn = document.getElementById('btn-add-pekerjaan');
      const pwAddBtn = document.getElementById('btn-add-pengawas');

      // Reset all buttons
      if(cTabBtn) cTabBtn.className = "py-4 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 focus:outline-none transition-all";
      if(pTabBtn) pTabBtn.className = "py-4 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 focus:outline-none transition-all";
      if(pwTabBtn) pwTabBtn.className = "py-4 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 focus:outline-none transition-all";
      if(lTabBtn) lTabBtn.className = "py-4 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 focus:outline-none transition-all flex items-center gap-1.5";

      // Reset all contents
      if(cTabContent) cTabContent.classList.add('hidden');
      if(pTabContent) pTabContent.classList.add('hidden');
      if(pwTabContent) pwTabContent.classList.add('hidden');
      if(lTabContent) lTabContent.classList.add('hidden');

      if(cAddBtn) cAddBtn.classList.add('hidden');
      if(pAddBtn) pAddBtn.classList.add('hidden');
      if(pwAddBtn) pwAddBtn.classList.add('hidden');

      if(tabName === 'kontraktor') {
        if(cTabBtn) cTabBtn.className = "py-4 text-sm font-bold border-b-2 border-gov-900 text-gov-900 focus:outline-none transition-all";
        if(cTabContent) cTabContent.classList.remove('hidden');
        if(cAddBtn) cAddBtn.classList.remove('hidden');
      } else if(tabName === 'pengawas') {
        if(pwTabBtn) pwTabBtn.className = "py-4 text-sm font-bold border-b-2 border-gov-900 text-gov-900 focus:outline-none transition-all";
        if(pwTabContent) pwTabContent.classList.remove('hidden');
        if(pwAddBtn) pwAddBtn.classList.remove('hidden');
      } else if(tabName === 'log') {
        if(lTabBtn) lTabBtn.className = "py-4 text-sm font-bold border-b-2 border-gov-900 text-gov-900 focus:outline-none transition-all flex items-center gap-1.5";
        if(lTabContent) lTabContent.classList.remove('hidden');
      } else {
        if(pTabBtn) pTabBtn.className = "py-4 text-sm font-bold border-b-2 border-gov-900 text-gov-900 focus:outline-none transition-all";
        if(pTabContent) pTabContent.classList.remove('hidden');
        if(pAddBtn) pAddBtn.classList.remove('hidden');
      }
    }

    // Auto-check URL parameter tab on page load
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const tabParam = urlParams.get('tab');
      if(tabParam) {
        switchTab(tabParam);
      }
    });

    // Modal Contractor Control
    function openContractorModal(c = null) {
      const form = document.getElementById("form-contractor");
      const title = document.getElementById("title-modal-contractor");
      const methodInput = document.getElementById("method-contractor");

      if (c) {
        title.textContent = "Edit Kontraktor: " + c.name;
        form.action = `/admin/contractors/${c.id}`;
        methodInput.value = "PUT";

        document.getElementById("c-name").value = c.name;
        document.getElementById("c-nib").value = c.nib;
        document.getElementById("c-rating").value = c.rating;
        document.getElementById("c-bidang").value = c.bidang;
        document.getElementById("c-kualifikasi").value = c.kualifikasi;
        document.getElementById("c-pj").value = c.pj;
        document.getElementById("c-email").value = c.email;
        document.getElementById("c-telepon").value = c.telepon;
        document.getElementById("c-alamat").value = c.alamat;
      } else {
        title.textContent = "Tambah Kontraktor Baru";
        form.action = "{{ route('admin.contractors.store') }}";
        methodInput.value = "POST";
        form.reset();
      }

      document.getElementById("modal-contractor").classList.remove("hidden");
    }

    function closeContractorModal() {
      document.getElementById("modal-contractor").classList.add("hidden");
    }

    // Modal Pengawas Control
    function openPengawasModal(pw = null) {
      const form = document.getElementById("form-pengawas");
      const title = document.getElementById("title-modal-pengawas");
      const methodInput = document.getElementById("method-pengawas");
      const passwordLabel = document.getElementById("pw-password-label");
      const passwordInput = document.getElementById("pw-password");

      if (pw) {
        title.textContent = "Edit Pengawas: " + pw.name;
        form.action = `/admin/pengawas/${pw.id}`;
        methodInput.value = "PUT";
        passwordLabel.textContent = "(kosongkan jika tidak diganti)";
        passwordInput.required = false;

        document.getElementById("pw-name").value = pw.name;
        document.getElementById("pw-nip").value = pw.nip || "";
        document.getElementById("pw-bidang").value = pw.bidang || "";
        document.getElementById("pw-email").value = pw.email;
        passwordInput.value = "";
      } else {
        title.textContent = "Tambah Pengawas Lapangan";
        form.action = "{{ route('admin.pengawas.store') }}";
        methodInput.value = "POST";
        passwordLabel.textContent = "(min. 6 karakter)";
        passwordInput.required = true;

        document.getElementById("pw-name").value = "";
        document.getElementById("pw-nip").value = "";
        document.getElementById("pw-bidang").value = "";
        document.getElementById("pw-email").value = "";
        passwordInput.value = "";
      }

      document.getElementById("modal-pengawas").classList.remove("hidden");
    }

    function closePengawasModal() {
      document.getElementById("modal-pengawas").classList.add("hidden");
    }

    // Modal Project Control
    let modalMap = null;
    let mapMarker = null;

    function openProjectModal(p = null) {
      const form = document.getElementById("form-project");
      const title = document.getElementById("title-modal-project");
      const methodInput = document.getElementById("method-project");

      let lat = -7.39675;
      let lng = 109.69724;

      if (p) {
        title.textContent = "Edit Paket Pekerjaan";
        form.action = `/admin/projects/${p.id}`;
        methodInput.value = "PUT";

        document.getElementById("p-name").value = p.nama_pekerjaan;
        document.getElementById("p-contractor").value = p.contractor_id;
        document.getElementById("p-nilai").value = p.nilai_kontrak;
        document.getElementById("p-tahun").value = p.tahun_anggaran;
        document.getElementById("p-status").value = p.status;
        document.getElementById("p-progress").value = p.progress;
        document.getElementById("p-lat").value = p.latitude;
        document.getElementById("p-lng").value = p.longitude;
        document.getElementById("p-detail").value = p.detail_lokasi || '';
        document.getElementById("p-ruas").value = p.ruas_jalan_id ? String(p.ruas_jalan_id) : '';
        document.getElementById("p-pengawas").value = p.pengawas_id ? String(p.pengawas_id) : '';
        document.getElementById("ruas-notice").classList.add('hidden');
        document.getElementById("p-tgl-kontrak").value = p.tanggal_kontrak || '';
        document.getElementById("p-tgl-pelaksanaan").value = p.tanggal_pelaksanaan || '';
        document.getElementById("p-tgl-pemeriksaan").value = p.tanggal_pemeriksaan || '';
        document.getElementById("p-tgl-deadline").value = p.tanggal_deadline || '';

        lat = p.latitude;
        lng = p.longitude;
      } else {
        title.textContent = "Tambah Paket Pekerjaan Baru";
        form.action = "{{ route('admin.projects.store') }}";
        methodInput.value = "POST";
        form.reset();
        
        document.getElementById("p-lat").value = lat;
        document.getElementById("p-lng").value = lng;
        document.getElementById("p-ruas").value = '';
        document.getElementById("p-pengawas").value = '';
        document.getElementById("ruas-notice").classList.add('hidden');
      }

      // STRICT FIELD LOCKOUT BY ROLE
      const generalFields = ["p-name", "p-contractor", "p-nilai", "p-tahun", "p-lat", "p-lng", "p-detail", "p-tgl-kontrak", "p-tgl-pelaksanaan", "p-tgl-deadline"];
      const auditFields = ["p-status", "p-progress", "p-tgl-pemeriksaan"];

      if (role === 'pemeriksa_lapangan') {
        generalFields.forEach(id => document.getElementById(id).disabled = true);
        auditFields.forEach(id => document.getElementById(id).disabled = false);
        // Hide map helper notice
        document.getElementById("map-notice-box").style.display = 'none';
      } else if (role === 'kontraktor') {
        generalFields.forEach(id => document.getElementById(id).disabled = false);
        document.getElementById("p-contractor").disabled = true; // Lock contractor selector to own firm
        auditFields.forEach(id => document.getElementById(id).disabled = true);
        document.getElementById("map-notice-box").style.display = 'block';
      } else {
        // Admin PUPR (Full Power)
        generalFields.forEach(id => document.getElementById(id).disabled = false);
        auditFields.forEach(id => document.getElementById(id).disabled = false);
        document.getElementById("map-notice-box").style.display = 'block';
      }

      // Ruas jalan & penugasan pengawas hanya boleh diatur oleh Admin PUPR
      document.getElementById("p-ruas").disabled = (role !== 'admin_pupr');
      document.getElementById("p-pengawas").disabled = (role !== 'admin_pupr');

      document.getElementById("modal-project").classList.remove("hidden");

      setTimeout(() => {
        if (!modalMap) {
          modalMap = L.map('modal-map').setView([lat, lng], 12);
          const streetMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri &mdash; Esri, HERE, Garmin, USGS, Intermap, INCREMENT P, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom',
            maxZoom: 19
          }).addTo(modalMap);

          const imageryMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri &mdash; Esri, Maxar, Earthstar Geographics, and the GIS User Community',
            maxZoom: 19
          });

          const topoMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri &mdash; Esri, HERE, Garmin, USGS, Intermap, INCREMENT P, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom',
            maxZoom: 19
          });

          L.control.layers({
            'Street Map': streetMap,
            'Citra Satelit': imageryMap,
            'Topografi': topoMap
          }, null, { position: 'topright' }).addTo(modalMap);

          modalMap.on('click', function(e) {
            if (role !== 'pemeriksa_lapangan') {
              updateCoords(e.latlng.lat, e.latlng.lng);
            }
          });
        } else {
          modalMap.setView([lat, lng], 12);
        }

        if (mapMarker) {
          mapMarker.setLatLng([lat, lng]);
        } else {
          mapMarker = L.marker([lat, lng]).addTo(modalMap);
        }
        
        modalMap.invalidateSize();
      }, 200);
    }

    function updateCoords(lat, lng) {
      document.getElementById("p-lat").value = parseFloat(lat).toFixed(6);
      document.getElementById("p-lng").value = parseFloat(lng).toFixed(6);
      if (mapMarker) {
        mapMarker.setLatLng([lat, lng]);
      }
      detectRuas(lat, lng);
    }

    async function detectRuas(lat, lng) {
      const sel = document.getElementById("p-ruas");
      const notice = document.getElementById("ruas-notice");
      if (!sel || role !== 'admin_pupr') return;

      try {
        const res = await fetch(`/admin/ruas-jalan/nearest?lat=${lat}&lng=${lng}`, {
          headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.found && data.ruas) {
          sel.value = String(data.ruas.id);
          if (notice) {
            const label = [data.ruas.nomor_ruas, data.ruas.nama_ruas].filter(Boolean).join(' — ') || ('Ruas #' + data.ruas.id);
            notice.textContent = `Ruas terdeteksi: ${label} (± ${Math.round(data.ruas.distance_m)} m dari titik)`;
            notice.classList.remove('hidden');
          }
        }
      } catch (e) {
        // Abaikan bila gagal; pilihan manual tetap tersedia.
      }
    }

    function closeProjectModal() {
      document.getElementById("modal-project").classList.add("hidden");
    }

    // Modal Admin Review Functions
    function openAdminReviewModal(id, title, contractor, oldProgress, newProgress, action) {
      const modal = document.getElementById('admReviewModal');
      const form = document.getElementById('admReviewForm');
      const badge = document.getElementById('admReviewTypeBadge');
      const modalTitle = document.getElementById('admReviewModalTitle');
      const projectName = document.getElementById('admReviewProjectName');
      const contractorName = document.getElementById('admReviewContractorName');
      const progressText = document.getElementById('admReviewProgressText');
      const actionInput = document.getElementById('admReviewActionInput');
      const noteInput = document.getElementById('admReviewNoteInput');
      const submitBtn = document.getElementById('admReviewSubmitBtn');

      form.action = '/mobile/pengawas/verify/' + id;
      actionInput.value = action;
      projectName.innerText = title;
      contractorName.innerText = contractor;
      progressText.innerText = oldProgress.toFixed(0) + '% → ' + newProgress.toFixed(0) + '%';
      noteInput.value = '';

      if (action === 'approve') {
        badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300';
        badge.innerText = 'SETUJUI PROGRES';
        modalTitle.innerText = 'Persetujuan Progres Fisik';
        submitBtn.className = 'flex-1 py-2.5 font-bold text-xs text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow transition-all flex items-center justify-center gap-2';
        submitBtn.querySelector('span').innerText = 'Konfirmasi & Setujui';
        noteInput.placeholder = 'Contoh: Progres fisik telah diperiksa di lokasi dan sesuai dengan spesifikasi teknis...';
      } else {
        badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-red-100 text-red-800 border border-red-300';
        badge.innerText = 'TOLAK PROGRES';
        modalTitle.innerText = 'Penolakan Progres Fisik';
        submitBtn.className = 'flex-1 py-2.5 font-bold text-xs text-white bg-red-600 hover:bg-red-700 rounded-lg shadow transition-all flex items-center justify-center gap-2';
        submitBtn.querySelector('span').innerText = 'Konfirmasi & Tolak';
        noteInput.placeholder = 'Contoh: Foto dokumentasi kurang jelas, mohon unggah ulang dari posisi tampak depan...';
      }

      modal.classList.remove('hidden');
    }

    function closeAdminReviewModal() {
      document.getElementById('admReviewModal').classList.add('hidden');
    }

    // Modal Final Approval (Lapisan Akhir Admin)
    function openAdminFinalModal(id, title, contractor, oldProgress, newProgress, action) {
      const modal = document.getElementById('admFinalModal');
      const form = document.getElementById('admFinalForm');
      const badge = document.getElementById('admFinalTypeBadge');
      const modalTitle = document.getElementById('admFinalModalTitle');
      const projectName = document.getElementById('admFinalProjectName');
      const contractorName = document.getElementById('admFinalContractorName');
      const progressText = document.getElementById('admFinalProgressText');
      const actionInput = document.getElementById('admFinalActionInput');
      const noteInput = document.getElementById('admFinalNoteInput');
      const submitBtn = document.getElementById('admFinalSubmitBtn');

      form.action = '/admin/projects/' + id + '/final-verify';
      actionInput.value = action;
      projectName.innerText = title;
      contractorName.innerText = contractor;
      progressText.innerText = oldProgress.toFixed(0) + '% -> ' + newProgress.toFixed(0) + '%';
      noteInput.value = '';

      if (action === 'approve') {
        badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300';
        badge.innerText = 'PERSETUJUAN AKHIR';
        modalTitle.innerText = 'Persetujuan Akhir Progres Fisik';
        submitBtn.className = 'flex-1 py-2.5 font-bold text-xs text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow transition-all flex items-center justify-center gap-2';
        submitBtn.querySelector('span').innerText = 'Konfirmasi & Setujui Final';
        noteInput.placeholder = 'Contoh: Progres fisik disetujui final sesuai rekomendasi pengawas lapangan...';
      } else {
        badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-red-100 text-red-800 border border-red-300';
        badge.innerText = 'TOLAK PERSETUJUAN';
        modalTitle.innerText = 'Penolakan Persetujuan Akhir';
        submitBtn.className = 'flex-1 py-2.5 font-bold text-xs text-white bg-red-600 hover:bg-red-700 rounded-lg shadow transition-all flex items-center justify-center gap-2';
        submitBtn.querySelector('span').innerText = 'Konfirmasi & Tolak';
        noteInput.placeholder = 'Contoh: Mohon lengkapi dokumen pendukung sebelum persetujuan akhir...';
      }

      modal.classList.remove('hidden');
    }

    function closeAdminFinalModal() {
      document.getElementById('admFinalModal').classList.add('hidden');
    }
  </script>

  <!-- MODAL REVIEW PENGAWAS ADMIN -->
  <div id="admReviewModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" onclick="closeAdminReviewModal()"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
        
        <!-- Header -->
        <div class="bg-gov-900 px-6 py-5 text-white flex justify-between items-start">
          <div>
            <span id="admReviewTypeBadge" class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase"></span>
            <h3 class="text-base font-bold tracking-tight text-white mt-1.5" id="admReviewModalTitle">Review Progres Pekerjaan</h3>
            <p class="text-xs text-slate-300 font-semibold leading-snug mt-0.5" id="admReviewProjectName"></p>
            <p class="text-[11px] text-slate-400 font-medium" id="admReviewContractorName"></p>
          </div>
          <button type="button" onclick="closeAdminReviewModal()" class="text-slate-300 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition-colors">
            <i class="ph-bold ph-x text-lg"></i>
          </button>
        </div>

        <!-- Body -->
        <form id="admReviewForm" action="" method="POST" class="p-6 space-y-4">
          @csrf
          <input type="hidden" name="action" id="admReviewActionInput">

          <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 flex justify-between items-center text-xs">
            <span class="text-slate-600 font-semibold">Usulan Perubahan Progres:</span>
            <span class="font-extrabold text-gov-800 font-mono text-sm" id="admReviewProgressText"></span>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
              Catatan / Keterangan Review Pengawas <span class="text-red-500">*</span>
            </label>
            <textarea name="verification_note" id="admReviewNoteInput" rows="3" required placeholder="Tuliskan catatan hasil pemeriksaan fisik lapangan di sini..." class="w-full p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gov-900 transition-all"></textarea>
          </div>

          <div class="flex gap-2 pt-2 border-t border-slate-100">
            <button type="button" onclick="closeAdminReviewModal()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-all">
              Batal
            </button>
            <button type="submit" id="admReviewSubmitBtn" class="flex-1 py-2.5 font-bold text-xs text-white rounded-lg shadow transition-all flex items-center justify-center gap-2">
              <span>Konfirmasi</span>
              <i class="ph-bold ph-check text-base"></i>
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- MODAL PERSETUJUAN AKHIR ADMIN (LAPISAN FINAL) -->
  <div id="admFinalModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" onclick="closeAdminFinalModal()"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">

        <!-- Header -->
        <div class="bg-emerald-800 px-6 py-5 text-white flex justify-between items-start">
          <div>
            <span id="admFinalTypeBadge" class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase"></span>
            <h3 class="text-base font-bold tracking-tight text-white mt-1.5" id="admFinalModalTitle">Persetujuan Akhir Pekerjaan</h3>
            <p class="text-xs text-slate-200 font-semibold leading-snug mt-0.5" id="admFinalProjectName"></p>
            <p class="text-[11px] text-slate-300 font-medium" id="admFinalContractorName"></p>
          </div>
          <button type="button" onclick="closeAdminFinalModal()" class="text-slate-300 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition-colors">
            <i class="ph-bold ph-x text-lg"></i>
          </button>
        </div>

        <!-- Body -->
        <form id="admFinalForm" action="" method="POST" class="p-6 space-y-4">
          @csrf
          <input type="hidden" name="action" id="admFinalActionInput">

          <div class="bg-emerald-50 p-3.5 rounded-xl border border-emerald-200 flex justify-between items-center text-xs">
            <span class="text-emerald-900 font-semibold">Persetujuan Progres Final:</span>
            <span class="font-extrabold text-emerald-800 font-mono text-sm" id="admFinalProgressText"></span>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
              Catatan Persetujuan Akhir Admin <span class="text-red-500">*</span>
            </label>
            <textarea name="final_note" id="admFinalNoteInput" rows="3" required placeholder="Tuliskan catatan persetujuan akhir admin di sini..." class="w-full p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-700 transition-all"></textarea>
          </div>

          <div class="flex gap-2 pt-2 border-t border-slate-100">
            <button type="button" onclick="closeAdminFinalModal()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-all">
              Batal
            </button>
            <button type="submit" id="admFinalSubmitBtn" class="flex-1 py-2.5 font-bold text-xs text-white rounded-lg shadow transition-all flex items-center justify-center gap-2">
              <span>Konfirmasi</span>
              <i class="ph-bold ph-seal-check text-base"></i>
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <script>
    function switchAdminMobileTab(tab) {
      ['beranda', 'pekerjaan', 'kontraktor', 'profil', 'log'].forEach(t => {
        const el = document.getElementById('adm-m-tab-' + t);
        const btn = document.getElementById('btn-adm-' + t);
        if (el && btn) {
          if (t === tab) {
            el.classList.remove('hidden');
            btn.className = 'flex flex-col items-center gap-1 text-gov-400 transition-all font-bold';
          } else {
            el.classList.add('hidden');
            btn.className = 'flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all';
          }
        }
      });
    }

    function filterAdminMobileProjects(status) {
      const cards = document.querySelectorAll('.adm-m-prj-card');
      cards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        if (status === 'all' || cardStatus === status) {
          card.classList.remove('hidden');
        } else {
          card.classList.add('hidden');
        }
      });

      // Tab styles
      ['all', 'pelaksanaan', 'selesai', 'persiapan'].forEach(st => {
        const btn = document.getElementById('flt-prj-' + st);
        if (btn) {
          if ((st === 'all' && status === 'all') || (st === 'pelaksanaan' && status === 'Pelaksanaan') || (st === 'selesai' && status === 'Selesai') || (st === 'persiapan' && status === 'Persiapan')) {
            btn.className = 'px-3 py-1.5 rounded-lg bg-gov-600 text-white whitespace-nowrap shadow-sm';
          } else {
            btn.className = 'px-3 py-1.5 rounded-lg bg-slate-900 text-slate-400 hover:text-white border border-slate-800 whitespace-nowrap';
          }
        }
      });
    }
  </script>
@endsection
