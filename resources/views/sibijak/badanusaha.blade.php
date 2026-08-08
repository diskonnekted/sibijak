@extends('layouts.sibijak')

@section('title', 'SIBIJAK Banjarnegara - Badan Usaha')

@section('content')
  <!-- TOP BANNER -->
  <section class="bg-gov-900 text-white py-12 border-b border-gov-950">
    <div class="max-w-7xl mx-auto px-6 space-y-2">
      <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest block">PEMBINAAN USAHA</span>
      <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Direktori Badan Usaha Jasa Konstruksi</h1>
      <p class="text-xs text-slate-300 max-w-[60ch]">
        Sistem pemantauan kualifikasi dan legalitas badan usaha konstruksi (PT/CV) di Kabupaten Banjarnegara.
      </p>
    </div>
  </section>

  <!-- CONTENT SECTION -->
  <section class="py-16">
    <div class="max-w-7xl mx-auto px-6">
      
      @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded flex items-center gap-3 text-sm font-semibold mb-6">
          <i class="ph-bold ph-check-circle text-lg text-emerald-600"></i>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      <!-- Search & Filters -->
      <form action="{{ route('badanusaha') }}" method="GET" class="bg-white p-6 rounded border border-slate-200 shadow-sm mb-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
          <div class="md:col-span-5">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cari Nama Badan Usaha / PJ</label>
            <div class="relative">
              <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
              <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: PT Banjarnegara Prima" class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
            </div>
          </div>

          <div class="md:col-span-3">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Klasifikasi Bidang</label>
            <select name="bidang" class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              <option value="all">Semua Bidang</option>
              <option value="Sipil" {{ request('bidang') == 'Sipil' ? 'selected' : '' }}>Pekerjaan Sipil</option>
              <option value="Arsitektur" {{ request('bidang') == 'Arsitektur' ? 'selected' : '' }}>Arsitektur</option>
              <option value="Mekanikal" {{ request('bidang') == 'Mekanikal' ? 'selected' : '' }}>Mekanikal & Elektrikal</option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kualifikasi</label>
            <select name="kualifikasi" class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              <option value="all">Semua Kualifikasi</option>
              <option value="Besar" {{ request('kualifikasi') == 'Besar' ? 'selected' : '' }}>Besar (B)</option>
              <option value="Menengah" {{ request('kualifikasi') == 'Menengah' ? 'selected' : '' }}>Menengah (M)</option>
              <option value="Kecil" {{ request('kualifikasi') == 'Kecil' ? 'selected' : '' }}>Kecil (K)</option>
            </select>
          </div>

          <div class="md:col-span-2 flex gap-2">
            <button type="submit" class="flex-1 inline-flex items-center justify-center py-2.5 text-xs font-bold tracking-wider uppercase text-white bg-gov-900 hover:bg-gov-950 rounded shadow active:scale-[0.98] transition-all">
              Saring
            </button>
            @if(request()->hasAny(['search', 'bidang', 'kualifikasi']))
              <a href="{{ route('badanusaha') }}" class="inline-flex items-center justify-center p-2.5 text-xs font-bold uppercase text-slate-700 border border-slate-200 hover:bg-slate-50 rounded active:scale-[0.98] transition-all">
                Reset
              </a>
            @endif
          </div>
        </div>
      </form>

      <!-- List Table -->
      <div class="bg-white rounded border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-50">
                <th class="px-6 py-4 font-semibold">Nama Badan Usaha</th>
                <th class="px-6 py-4 font-semibold">NIB</th>
                <th class="px-6 py-4 font-semibold">Penanggung Jawab</th>
                <th class="px-6 py-4 font-semibold">Klasifikasi Bidang</th>
                <th class="px-6 py-4 font-semibold">Kualifikasi</th>
                <th class="px-6 py-4 font-semibold">Kinerja (Rating)</th>
                <th class="px-6 py-4 font-semibold text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
              @forelse($contractors as $c)
                <tr class="hover:bg-slate-50/50 transition-colors">
                  <td class="px-6 py-4 font-bold text-slate-900">
                    <a href="{{ route('badanusaha.show', $c) }}" class="hover:text-gov-600 transition-colors">
                      {{ $c->name }}
                    </a>
                  </td>
                  <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $c->nib }}</td>
                  <td class="px-6 py-4 text-slate-700">{{ $c->pj }}</td>
                  <td class="px-6 py-4 text-slate-700">{{ $c->bidang }}</td>
                  <td class="px-6 py-4">
                    <span class="inline-block px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-[10px] font-bold text-slate-600 uppercase">
                      {{ $c->kualifikasi }}
                    </span>
                  </td>
                  <td class="px-6 py-4 font-semibold text-amber-600">
                    <i class="ph-fill ph-star"></i> {{ number_format($c->rating, 1) }}
                  </td>
                  <td class="px-6 py-4 text-right">
                    <a href="{{ route('badanusaha.show', $c) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold tracking-wider uppercase text-slate-700 hover:text-gov-900 border border-slate-200 hover:border-slate-350 bg-slate-50 rounded active:scale-[0.98] transition-all">
                      Detail <i class="ph ph-arrow-right"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium bg-white">
                    <i class="ph-bold ph-warning text-3xl text-slate-300 mb-2 block"></i>
                    <span>Badan Usaha Tidak Ditemukan</span>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- DETAIL CONTRACTOR MODAL -->
  <div id="modal-container" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
        <!-- Header -->
        <div class="bg-gov-900 px-6 py-6 text-white flex justify-between items-start">
          <div>
            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-gov-850 text-[10px] uppercase font-bold tracking-wider mb-2" id="modal-badge">
              Kualifikasi
            </div>
            <h3 class="text-xl font-bold tracking-tight text-white" id="modal-title">Nama Kontraktor</h3>
            <span class="text-xs text-slate-300 font-mono" id="modal-nib">NIB: </span>
          </div>
          <button onclick="closeModal()" class="text-white/80 hover:text-white p-1 rounded hover:bg-white/10 transition-colors">
            <i class="ph-bold ph-x text-xl"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-6 space-y-6">
          <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded border border-slate-200 text-xs">
            <div>
              <span class="block text-slate-500 font-semibold uppercase tracking-wider mb-1">Klasifikasi Utama</span>
              <span class="text-sm font-bold text-slate-900" id="modal-bidang">Pekerjaan</span>
            </div>
            <div>
              <span class="block text-slate-500 font-semibold uppercase tracking-wider mb-1">Status Verifikasi</span>
              <span class="text-sm font-bold text-gov-600 flex items-center gap-1">
                <i class="ph-fill ph-check-circle"></i> Terverifikasi Dinas
              </span>
            </div>
            <div>
              <span class="block text-slate-500 font-semibold uppercase tracking-wider mb-1">Penanggung Jawab</span>
              <span class="text-sm font-bold text-slate-900" id="modal-pj">Nama PJ</span>
            </div>
            <div>
              <span class="block text-slate-500 font-semibold uppercase tracking-wider mb-1">Hubungi Kontak</span>
              <span class="text-sm font-bold text-slate-900" id="modal-kontak font-mono">Email / Telp</span>
            </div>
            <div class="col-span-2">
              <span class="block text-slate-500 font-semibold uppercase tracking-wider mb-1">Alamat Kantor</span>
              <span class="text-sm font-bold text-slate-900" id="modal-alamat">Alamat Lengkap</span>
            </div>
          </div>

          <div>
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Daftar Paket Pekerjaan Terlaksana</h4>
            <div class="divide-y divide-slate-200 border-y border-slate-200" id="modal-projects">
              <!-- JS Render -->
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-200">
          <button onclick="closeModal()" class="px-4 py-2 text-xs font-bold uppercase text-slate-700 border border-slate-300 bg-white rounded active:scale-[0.98]">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    function openModal(c) {
      document.getElementById("modal-badge").textContent = `Kualifikasi ${c.kualifikasi}`;
      document.getElementById("modal-title").textContent = c.name;
      document.getElementById("modal-nib").textContent = `NIB: ${c.nib}`;
      document.getElementById("modal-bidang").textContent = c.bidang;
      document.getElementById("modal-pj").textContent = c.pj;
      document.getElementById("modal-kontak").textContent = `${c.email || '-'} / ${c.telepon || '-'}`;
      document.getElementById("modal-alamat").textContent = c.alamat;

      const pContainer = document.getElementById("modal-projects");
      pContainer.innerHTML = "";

      if (!c.projects || c.projects.length === 0) {
        pContainer.innerHTML = `<div class="py-4 text-center text-slate-400 text-xs">Belum ada riwayat pekerjaan proyek di Banjarnegara.</div>`;
      } else {
        c.projects.forEach(p => {
          const row = document.createElement("div");
          row.className = "py-3 flex justify-between items-center text-xs";
          row.innerHTML = `
            <div>
              <span class="font-bold text-slate-800 block">${p.nama_pekerjaan}</span>
              <span class="text-slate-500 font-medium">Tahun Anggaran: ${p.tahun_anggaran} | Nilai: Rp ${new Intl.NumberFormat('id-ID').format(p.nilai_kontrak)}</span>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${
              p.status === 'Selesai' ? 'bg-gov-100 text-gov-800' : 'bg-amber-100 text-amber-800'
            }">
              ${p.status} (${parseFloat(p.progress).toFixed(0)}%)
            </span>
          `;
          pContainer.appendChild(row);
        });
      }

      document.getElementById("modal-container").classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }

    function closeModal() {
      document.getElementById("modal-container").classList.add("hidden");
      document.body.style.overflow = "auto";
    }
  </script>
@endsection
