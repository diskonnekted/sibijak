@extends('layouts.sibijak')

@section('title', 'SIBIJAK Banjarnegara - Peraturan Jasa Konstruksi')

@section('content')
  <!-- TOP BANNER -->
  <section class="bg-gov-900 text-white py-12 border-b border-gov-950">
    <div class="max-w-7xl mx-auto px-6 space-y-2">
      <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest block">ASPEK HUKUM</span>
      <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Peraturan & Kebijakan Jasa Konstruksi</h1>
      <p class="text-xs text-slate-300 max-w-[60ch]">
        Landasan regulasi nasional dan peraturan daerah terkait tertib usaha jasa konstruksi Indonesia.
      </p>
    </div>
  </section>

  <!-- CONTENT -->
  <section class="py-16">
    <div class="max-w-7xl mx-auto px-6">
      
      <!-- Filter Kategori -->
      <div class="flex flex-wrap gap-3 mb-8">
        <a href="{{ route('regulasi') }}?kategori=all" class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider {{ !request('kategori') || request('kategori') == 'all' ? 'bg-gov-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }} transition-all">Semua Kategori</a>
        <a href="{{ route('regulasi') }}?kategori=Undang-Undang" class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider {{ request('kategori') == 'Undang-Undang' ? 'bg-gov-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }} transition-all">Undang-Undang</a>
        <a href="{{ route('regulasi') }}?kategori=Peraturan Pemerintah" class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider {{ request('kategori') == 'Peraturan Pemerintah' ? 'bg-gov-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }} transition-all">Peraturan Pemerintah</a>
      </div>

      <!-- Regulations List -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($regulations as $r)
          <div class="bg-white p-6 rounded border border-slate-200 shadow-sm flex flex-col justify-between min-h-[180px]">
            <div class="space-y-3">
              <div class="flex justify-between items-center text-[10px] font-bold text-gov-600 uppercase">
                <span>{{ $r->kategori }}</span>
                <span class="font-mono">{{ $r->tahun }}</span>
              </div>
              <h3 class="text-base font-extrabold text-slate-900">{{ $r->judul }}</h3>
              <p class="text-xs text-slate-500 font-semibold font-mono">{{ $r->nomor }}</p>
              <p class="text-xs text-slate-500 leading-relaxed">{{ $r->deskripsi }}</p>
            </div>
            
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-400">
              <span>Status: Berlaku</span>
              <a href="#" class="inline-flex items-center gap-1.5 text-gov-600 hover:text-gov-800 transition-colors uppercase font-bold tracking-wider">
                <i class="ph ph-download"></i> Unduh PDF
              </a>
            </div>
          </div>
        @empty
          <div class="col-span-full py-16 text-center bg-white border border-slate-200 rounded">
            <i class="ph-bold ph-warning text-3xl text-slate-300 mb-2 block"></i>
            <span class="text-sm font-semibold text-slate-400 uppercase tracking-wider block">Regulasi tidak ditemukan.</span>
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection
