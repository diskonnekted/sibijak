@extends('layouts.sibijak')

@section('title')
  Detail Kontraktor: {{ $contractor->name }} - SIBIJAK Banjarnegara
@endsection

@section('content')
  <!-- TOP HEADER -->
  <section class="bg-gov-900 text-white py-12 border-b border-gov-950">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
      <div class="space-y-2">
        <div class="flex items-center gap-2">
          <a href="{{ route('badanusaha') }}" class="text-xs font-bold text-slate-300 hover:text-white uppercase tracking-wider flex items-center gap-1">
            <i class="ph ph-arrow-left"></i> Kembali ke Direktori
          </a>
        </div>
        <span class="inline-block px-2.5 py-0.5 rounded bg-slate-100/10 border border-white/10 text-[10px] font-bold text-slate-300 uppercase tracking-wide">
          Kualifikasi {{ $contractor->kualifikasi }}
        </span>
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">{{ $contractor->name }}</h1>
        <span class="block font-mono text-xs text-slate-400">NIB: {{ $contractor->nib }}</span>
      </div>
      
      <div class="flex items-center gap-1.5 text-sm font-bold text-amber-600 bg-white px-3 py-1.5 rounded border border-amber-100 shadow-sm">
        <i class="ph-fill ph-star"></i> {{ number_format($contractor->rating, 1) }} / 5.0 Kinerja
      </div>
    </div>
  </section>

  <!-- CONTENT BODY -->
  <section class="py-16">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12">
      
      <!-- Left column: Company info -->
      <div class="lg:col-span-4 space-y-6">
        <div class="bg-white p-6 rounded border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2">Informasi Umum</h3>
          
          <div class="space-y-4 text-xs">
            <div>
              <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Klasifikasi Utama</span>
              <span class="text-sm font-bold text-slate-800">{{ $contractor->bidang }}</span>
            </div>
            <div>
              <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Penanggung Jawab</span>
              <span class="text-sm font-bold text-slate-800">{{ $contractor->pj }}</span>
            </div>
            <div>
              <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Kontak Resmi</span>
              <span class="text-sm font-bold text-slate-800">{{ $contractor->email ?? '-' }}<br>{{ $contractor->telepon ?? '-' }}</span>
            </div>
            <div>
              <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Alamat Kantor</span>
              <span class="text-sm font-bold text-slate-800 leading-relaxed">{{ $contractor->alamat }}</span>
            </div>
          </div>
        </div>

        <!-- Mini Stats -->
        <div class="grid grid-cols-2 gap-4">
          <div class="bg-white p-4 rounded border border-slate-200 shadow-sm text-center">
            <span class="block text-[9px] font-bold text-slate-400 uppercase">TOTAL PROYEK</span>
            <span class="text-xl font-extrabold text-slate-900">{{ $stats['total_projects'] }}</span>
          </div>
          <div class="bg-white p-4 rounded border border-slate-200 shadow-sm text-center">
            <span class="block text-[9px] font-bold text-slate-400 uppercase">SELESAI (100%)</span>
            <span class="text-xl font-extrabold text-gov-600">{{ $stats['completed_projects'] }}</span>
          </div>
        </div>

        <!-- Training Card -->
        <div class="bg-white p-6 rounded border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2">Riwayat Pembinaan & Pelatihan</h3>
          <div class="space-y-3">
            @forelse($contractor->trainings as $tr)
              <div class="p-3 bg-slate-50 border border-slate-150 rounded space-y-1">
                <span class="inline-block px-1.5 py-0.5 rounded bg-emerald-100 border border-emerald-200 text-[8px] font-bold text-emerald-800 uppercase">
                  Tersertifikasi
                </span>
                <span class="block text-xs font-bold text-slate-800">{{ $tr->nama_pelatihan }}</span>
                <span class="block text-[10px] text-slate-400 font-semibold font-mono">{{ date('d M Y', strtotime($tr->tanggal)) }}</span>
              </div>
            @empty
              <div class="text-xs text-slate-400 text-center py-4">Belum mengikuti program pembinaan/pelatihan dinas.</div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- Right column: Annual archives -->
      <div class="lg:col-span-8 space-y-8">
        <h2 class="text-lg font-bold text-slate-900 uppercase tracking-wider border-b border-slate-200 pb-3">Arsip Pekerjaan & Dokumentasi Fisik</h2>

        @php
          $pendingContractorProjects = $contractor->projects->where('verification_status', 'pending');
        @endphp

        @if($pendingContractorProjects->count() > 0)
          <div class="bg-amber-50 border border-amber-300 text-amber-900 p-4 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-400/30 flex items-center justify-center text-amber-700 flex-shrink-0">
                <i class="ph-bold ph-hourglass-high text-xl"></i>
              </div>
              <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wide text-amber-900">Progres Terbaru Menunggu Review Pengawas</h3>
                <p class="text-xs text-amber-800 mt-0.5">Terdapat <strong>{{ $pendingContractorProjects->count() }} pengajuan progres fisik</strong> dari kontraktor ini yang sedang diajukan dan dalam tahap verifikasi pengawas lapangan.</p>
              </div>
            </div>
            <a href="/login" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 flex-shrink-0">
              <span>Review Pengawas</span>
              <i class="ph-bold ph-arrow-right"></i>
            </a>
          </div>
        @endif

        @forelse($annualArchive as $tahun => $projects)
          <div class="space-y-4">
            <div class="flex items-center gap-3">
              <span class="px-3 py-1 bg-slate-900 text-white rounded text-xs font-bold font-mono">{{ $tahun }}</span>
              <div class="flex-1 h-px bg-slate-200"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              @foreach($projects as $p)
                <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                  <!-- Visual documentation (photo matching project ID) -->
                  <div class="relative aspect-video bg-slate-100">
                    <img src="{{ asset(ltrim($p->reported_photo ?: 'storage/projects/sample_jembatan.jpg', '/')) }}" alt="{{ $p->nama_pekerjaan }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('storage/projects/sample_default.jpg') }}';">
                    <div class="absolute top-3 right-3 flex flex-col gap-1 items-end">
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-white/90 text-slate-800 shadow-sm border border-slate-200">
                        {{ $p->status }}
                      </span>
                      @if($p->verification_status === 'pending')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-amber-500 text-white shadow-sm border border-amber-400 animate-pulse">
                          <i class="ph-bold ph-hourglass-high"></i> Review Pengawas
                        </span>
                      @endif
                    </div>
                  </div>

                  <!-- Details -->
                  <div class="p-6 space-y-4">
                    <div class="space-y-1">
                      <h4 class="text-sm font-extrabold text-slate-900 leading-snug line-clamp-2 hover:text-gov-600" title="{{ $p->nama_pekerjaan }}">
                        <a href="{{ route('pekerjaan.show', $p) }}">
                          {{ $p->nama_pekerjaan }}
                        </a>
                      </h4>
                      <span class="block text-[10px] text-slate-400 font-bold uppercase">{{ $p->detail_lokasi ?: 'Lokasi Banjarnegara' }}</span>
                    </div>

                    <!-- Progress bar -->
                    <div class="space-y-1">
                      <div class="flex justify-between text-[10px] font-bold text-slate-500 uppercase">
                        <span>KEMAJUAN FISIK TERVERIFIKASI</span>
                        <span>{{ number_format($p->progress, 0) }}%</span>
                      </div>
                      <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-gov-600 h-1.5" style="width: {{ $p->progress }}%"></div>
                      </div>

                      @if($p->verification_status === 'pending')
                        <div class="bg-amber-50 border border-amber-200 p-2 rounded-lg flex items-center justify-between text-[10px] mt-2">
                          <span class="text-amber-800 font-semibold flex items-center gap-1">
                            <i class="ph-bold ph-arrow-up-right text-amber-600"></i> Diajukan Baru:
                          </span>
                          <span class="font-extrabold text-amber-900 bg-amber-200/80 px-2 py-0.5 rounded border border-amber-300">
                            {{ number_format($p->progress, 0) }}% → {{ number_format($p->reported_progress, 0) }}%
                          </span>
                        </div>
                      @endif
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-between items-center text-[11px] font-bold text-slate-700">
                      <span>NILAI KONTRAK</span>
                      <span class="text-gov-800">Rp {{ number_format($p->nilai_kontrak, 0, ',', '.') }}</span>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @empty
          <div class="py-12 text-center bg-white border border-slate-200 rounded">
            <i class="ph-bold ph-folder-open text-3xl text-slate-300 mb-2 block"></i>
            <span class="text-sm font-semibold text-slate-400 uppercase tracking-wider block">Belum ada arsip pekerjaan terdaftar.</span>
          </div>
        @endforelse
      </div>

    </div>
  </section>
@endsection
