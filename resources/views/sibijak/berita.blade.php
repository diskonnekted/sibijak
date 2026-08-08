@extends('layouts.sibijak')

@section('title', 'SIBIJAK Banjarnegara - Berita & Pengumuman')

@section('content')
  <!-- TOP BANNER -->
  <section class="bg-gov-900 text-white py-12 border-b border-gov-950">
    <div class="max-w-7xl mx-auto px-6 space-y-2">
      <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest block">PORTAL INFORMASI</span>
      <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Kabar Konstruksi & Pengumuman</h1>
      <p class="text-xs text-slate-300 max-w-[60ch]">
        Berita berkala terkait kebijakan dinas, agenda sertifikasi dan informasi konstruksi Kabupaten Banjarnegara.
      </p>
    </div>
  </section>

  <!-- CONTENT -->
  <section class="py-16">
    <div class="max-w-4xl mx-auto px-6 space-y-12">
      @forelse($news as $n)
        <article class="bg-white p-8 rounded border border-slate-200 shadow-sm space-y-4">
          <div class="flex items-center justify-between text-xs text-slate-400 font-bold uppercase tracking-wider">
            <span>{{ $n->kategori }}</span>
            <span>{{ date('d F Y', strtotime($n->tanggal)) }}</span>
          </div>
          <h2 class="text-xl font-extrabold text-slate-900 hover:text-gov-900 leading-snug">
            <a href="#">{{ $n->judul }}</a>
          </h2>
          <div class="prose max-w-none text-sm text-slate-600 leading-relaxed space-y-3">
            <p>{{ $n->konten }}</p>
            <p>Diharapkan para pelaku usaha dan pembina terus memantau portal ini untuk memperoleh update terpercaya seputar aturan main, program pelatihan, sertifikasi kompetensi, serta berita pengadaan di lingkup dinas PUPR Banjarnegara.</p>
          </div>
        </article>
      @empty
        <div class="py-16 text-center bg-white border border-slate-200 rounded">
          <i class="ph-bold ph-warning text-3xl text-slate-300 mb-2 block"></i>
          <span class="text-sm font-semibold text-slate-400 uppercase tracking-wider block">Belum ada berita diterbitkan.</span>
        </div>
      @endforelse
    </div>
  </section>
@endsection
