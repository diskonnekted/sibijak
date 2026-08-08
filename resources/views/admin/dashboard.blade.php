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

  <div class="space-y-8">
    
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
        </div>
        
        <div class="py-3 flex gap-2">
          @if($currentRole === 'admin_pupr')
            <button onclick="openContractorModal()" id="btn-add-kontraktor" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold tracking-wider uppercase text-white bg-gov-900 hover:bg-gov-950 rounded transition-all active:scale-[0.98]">
              <i class="ph-bold ph-plus"></i> Kontraktor Baru
            </button>
            <button onclick="openProjectModal()" id="btn-add-pekerjaan" class="hidden inline-flex items-center gap-2 px-4 py-2 text-xs font-bold tracking-wider uppercase text-white bg-gov-900 hover:bg-gov-950 rounded transition-all active:scale-[0.98]">
              <i class="ph-bold ph-plus"></i> Pekerjaan Baru
            </button>
          @endif
        </div>
      </div>

      <!-- TAB KONTRAKTOR CONTENT -->
      @if($currentRole !== 'kontraktor')
        <div id="tab-content-kontraktor" class="p-6">
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
                    <td colspan="7" class="py-8 text-center text-slate-400 font-medium">Belum ada data kontraktor.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      @endif

      <!-- TAB PEKERJAAN CONTENT -->
      <div id="tab-content-pekerjaan" class="@if($currentRole !== 'kontraktor') hidden @endif p-6">
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
                  <td class="py-4 text-xs font-medium text-slate-600">{{ $p->contractor->name }}</td>
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
    </div>
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
                  @foreach($contractors as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
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
      const cTabContent = document.getElementById('tab-content-kontraktor');
      const pTabContent = document.getElementById('tab-content-pekerjaan');
      const cAddBtn = document.getElementById('btn-add-kontraktor');
      const pAddBtn = document.getElementById('btn-add-pekerjaan');

      if(tabName === 'kontraktor') {
        if(cTabBtn) cTabBtn.className = "py-4 text-sm font-bold border-b-2 border-gov-900 text-gov-900 focus:outline-none transition-all";
        pTabBtn.className = "py-4 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 focus:outline-none transition-all";
        if(cTabContent) cTabContent.classList.remove('hidden');
        pTabContent.classList.add('hidden');
        if(cAddBtn) cAddBtn.classList.remove('hidden');
        if(pAddBtn) pAddBtn.classList.add('hidden');
      } else {
        pTabBtn.className = "py-4 text-sm font-bold border-b-2 border-gov-900 text-gov-900 focus:outline-none transition-all";
        if(cTabBtn) cTabBtn.className = "py-4 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 focus:outline-none transition-all";
        pTabContent.classList.remove('hidden');
        if(cTabContent) cTabContent.classList.add('hidden');
        if(pAddBtn) pAddBtn.classList.remove('hidden');
        if(cAddBtn) cAddBtn.classList.add('hidden');
      }
    }

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

      document.getElementById("modal-project").classList.remove("hidden");

      setTimeout(() => {
        if (!modalMap) {
          modalMap = L.map('modal-map').setView([lat, lng], 12);
          L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19
          }).addTo(modalMap);

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
    }

    function closeProjectModal() {
      document.getElementById("modal-project").classList.add("hidden");
    }
  </script>
@endsection
