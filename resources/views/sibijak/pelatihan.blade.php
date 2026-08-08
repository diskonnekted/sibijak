@extends('layouts.sibijak')

@section('title', 'SIBIJAK Banjarnegara - Pelatihan Jasa Konstruksi')

@section('content')
  <!-- TOP BANNER -->
  <section class="bg-gov-900 text-white py-12 border-b border-gov-950">
    <div class="max-w-7xl mx-auto px-6 space-y-2">
      <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest block">KOMPETENSI TENAGA KERJA</span>
      <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Pelatihan & Bimtek Tenaga Konstruksi</h1>
      <p class="text-xs text-slate-300 max-w-[60ch]">
        Program sertifikasi resmi peningkatan keahlian tenaga kerja terampil konstruksi di Kabupaten Banjarnegara.
      </p>
    </div>
  </section>

  <!-- CONTENT -->
  <section class="py-16">
    <div class="max-w-7xl mx-auto px-6">
      
      @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded flex items-center gap-3 text-sm font-semibold mb-8">
          <i class="ph-bold ph-check-circle text-lg text-emerald-600"></i>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      <div class="grid grid-cols-1 gap-6">
        @forelse($trainings as $t)
          <div class="bg-white p-6 rounded border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-3 max-w-[70ch]">
              <div class="flex flex-wrap items-center gap-2.5">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${
                  $t->status === 'Berjalan' ? 'bg-amber-100 text-amber-800' : ($t->status === 'Mendatang' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-600')
                }">
                  {{ $t->status }}
                </span>
                <span class="text-xs text-slate-500 font-medium">
                  <i class="ph ph-calendar"></i> {{ date('d F Y', strtotime($t->tanggal)) }}
                </span>
              </div>
              <h3 class="text-lg font-bold text-slate-900">{{ $t->nama_pelatihan }}</h3>
              <p class="text-xs text-slate-500 leading-relaxed">{{ $t->detail }}</p>
              
              <!-- Progress kuota -->
              <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
                <div class="flex items-center gap-1.5">
                  <i class="ph ph-users"></i> Pendaftar: {{ $t->pendaftar_count }} / {{ $t->kuota }} Kuota
                </div>
                <!-- Mini Progress Bar -->
                <div class="w-32 bg-slate-200 h-1.5 rounded-full overflow-hidden">
                  <div class="bg-gov-600 h-1.5" style="width: {{ ($t->pendaftar_count / $t->kuota) * 100 }}%"></div>
                </div>
              </div>
            </div>

            <div class="flex-shrink-0">
              @if($t->status === 'Mendatang' && $t->pendaftar_count < $t->kuota)
                <a href="{{ route('daftar') }}?training_id={{ $t->id }}" class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold tracking-wider uppercase text-white bg-gov-900 hover:bg-gov-950 rounded shadow active:scale-[0.98] transition-all">
                  Daftar Sekarang
                </a>
              @else
                <button disabled class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold tracking-wider uppercase text-slate-400 bg-slate-100 border border-slate-200 rounded cursor-not-allowed">
                  Pendaftaran Tutup
                </button>
              @endif
            </div>
          </div>
        @empty
          <div class="py-16 text-center bg-white border border-slate-200 rounded">
            <i class="ph-bold ph-warning text-3xl text-slate-300 mb-2 block"></i>
            <span class="text-sm font-semibold text-slate-400 uppercase tracking-wider block">Belum ada agenda pelatihan.</span>
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection
