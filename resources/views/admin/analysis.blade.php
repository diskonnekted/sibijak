@extends('layouts.admin')

@section('title', 'Analisa Kegiatan Fisik - SIKAP Admin')
@section('page_title', 'Analisis Data & Rekomendasi Kebijakan')

@section('content')
  <div class="space-y-8">
    
    <!-- STATS OVERVIEW CARD -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Budget Summary Card -->
      <div class="bg-white p-6 rounded border border-slate-200 shadow-sm flex flex-col justify-between">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Anggaran Dialokasikan</span>
          <span class="text-2xl font-extrabold text-slate-900 leading-none">Rp {{ number_format($stats['total_contract_value'], 0, ',', '.') }}</span>
        </div>
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-semibold mt-4">
          <span>Rata-rata Nilai Kontrak</span>
          <span>Rp {{ number_format($stats['total_projects'] ? ($stats['total_contract_value'] / $stats['total_projects']) : 0, 0, ',', '.') }}</span>
        </div>
      </div>

      <!-- Progress Meter Card -->
      <div class="bg-white p-6 rounded border border-slate-200 shadow-sm flex flex-col justify-between">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Rata-rata Progress Fisik Wilayah</span>
          <span class="text-3xl font-extrabold text-gov-600 leading-none">{{ number_format($stats['average_progress'], 1) }}%</span>
        </div>
        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-4">
          <div class="bg-gov-600 h-2 rounded-full" style="width: {{ $stats['average_progress'] }}%"></div>
        </div>
      </div>

      <!-- Project Status Breakdown -->
      <div class="bg-white p-6 rounded border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2">Status Kegiatan Fisik</h3>
        
        <div class="grid grid-cols-3 gap-2 text-center">
          <div class="p-2 bg-slate-50 rounded border border-slate-100">
            <span class="block text-lg font-bold text-slate-700 leading-none">{{ $stats['status_persiapan'] }}</span>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Persiapan</span>
          </div>
          <div class="p-2 bg-amber-50 rounded border border-amber-100">
            <span class="block text-lg font-bold text-amber-600 leading-none">{{ $stats['status_pelaksanaan'] }}</span>
            <span class="text-[9px] font-bold text-amber-500 uppercase tracking-wider">Pelaksanaan</span>
          </div>
          <div class="p-2 bg-emerald-50 rounded border border-emerald-100">
            <span class="block text-lg font-bold text-emerald-600 leading-none">{{ $stats['status_selesai'] }}</span>
            <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-wider">Selesai</span>
          </div>
        </div>
      </div>

    </div>

    <!-- MAIN GRID SECTION -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
      
      <!-- Recommendations Section -->
      <div class="xl:col-span-7 space-y-6">
        <div class="bg-white p-6 rounded border border-slate-200 shadow-sm space-y-4">
          <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
            <div class="w-8 h-8 rounded bg-gov-50 border border-gov-100 flex items-center justify-center text-gov-800">
              <i class="ph-bold ph-lightbulb text-lg"></i>
            </div>
            <div>
              <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Rekomendasi Tindakan Strategis</h2>
              <span class="text-[10px] text-slate-400 font-semibold uppercase">Hasil Evaluasi Audit Sistem</span>
            </div>
          </div>

          <div class="space-y-4">
            @forelse($recommendations as $rec)
              <div class="p-4 rounded border flex items-start gap-4 {{
                $rec['tipe'] === 'Danger' ? 'bg-red-50/50 border-red-200 text-red-800' : (
                  $rec['tipe'] === 'Warning' ? 'bg-amber-50/50 border-amber-200 text-amber-800' : 'bg-blue-50/50 border-blue-200 text-blue-800'
                )
              }}">
                <div class="pt-0.5">
                  @if($rec['tipe'] === 'Danger')
                    <i class="ph-bold ph-warning-circle text-xl text-red-600"></i>
                  @elseif($rec['tipe'] === 'Warning')
                    <i class="ph-bold ph-warning text-xl text-amber-600"></i>
                  @else
                    <i class="ph-bold ph-info text-xl text-blue-600"></i>
                  @endif
                </div>
                <div class="space-y-1 text-xs">
                  <p class="font-bold leading-normal text-slate-900">{{ $rec['pesan'] }}</p>
                  <p class="text-slate-500 font-semibold leading-relaxed pt-1">
                    <span class="text-[10px] font-bold uppercase block tracking-wider {{
                      $rec['tipe'] === 'Danger' ? 'text-red-700' : (
                        $rec['tipe'] === 'Warning' ? 'text-amber-700' : 'text-blue-700'
                      )
                    }}">Rekomendasi Solusi:</span>
                    {{ $rec['solusi'] }}
                  </p>
                </div>
              </div>
            @empty
              <div class="py-12 text-center text-slate-400 font-medium">
                <i class="ph-bold ph-check-square text-3xl text-slate-200 block mb-2"></i>
                <span>Sistem tidak mendeteksi deviasi/masalah kritis pada data kegiatan fisik saat ini.</span>
              </div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- High Risk Projects List -->
      <div class="xl:col-span-5 space-y-6">
        <div class="bg-white p-6 rounded border border-slate-200 shadow-sm space-y-4">
          <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
            <div class="w-8 h-8 rounded bg-red-50 border border-red-100 flex items-center justify-center text-red-800">
              <i class="ph-bold ph-clock-countdown text-lg"></i>
            </div>
            <div>
              <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Deteksi Risiko Keterlambatan</h2>
              <span class="text-[10px] text-red-500 font-bold uppercase">Proyek Belum Selesai</span>
            </div>
          </div>

          <div class="space-y-4">
            @forelse($highRiskProjects as $hp)
              <div class="p-4 bg-slate-50 border border-slate-200 rounded space-y-3">
                <div class="flex justify-between items-start gap-2">
                  <h4 class="text-xs font-bold text-slate-900 leading-snug line-clamp-2" title="{{ $hp->nama_pekerjaan }}">
                    {{ $hp->nama_pekerjaan }}
                  </h4>
                  <span class="flex-shrink-0 inline-block px-1.5 py-0.5 rounded-full text-[8px] font-bold uppercase tracking-wider {{
                    $hp->progress < 40 ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-amber-100 text-amber-800 border border-amber-200'
                  }}">
                    {{ $hp->progress < 40 ? 'Kritis' : 'Waspada' }}
                  </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-[10px] text-slate-500 border-t border-b border-slate-150 py-2">
                  <div>
                    <span class="block text-[8px] font-bold text-slate-400 uppercase mb-0.5">Pelaksana</span>
                    <span class="font-bold text-slate-700 truncate block">{{ $hp->contractor->name }}</span>
                  </div>
                  <div>
                    <span class="block text-[8px] font-bold text-slate-400 uppercase mb-0.5">Sisa Waktu</span>
                    <span class="font-bold font-mono {{ $hp->days_remaining < 30 ? 'text-red-600' : 'text-amber-600' }}">
                      {{ $hp->days_remaining }} Hari Lagi
                    </span>
                  </div>
                </div>

                <div class="space-y-1">
                  <div class="flex justify-between text-[9px] font-bold text-slate-500">
                    <span>PROGRESS FISIK</span>
                    <span>{{ number_format($hp->progress, 1) }}%</span>
                  </div>
                  <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                    <div class="h-1.5 rounded-full {{ $hp->progress < 40 ? 'bg-red-500' : 'bg-amber-500' }}" style="width: {{ $hp->progress }}%"></div>
                  </div>
                </div>
              </div>
            @empty
              <div class="py-16 text-center text-slate-400 font-medium">
                <i class="ph-bold ph-shield text-3xl text-slate-200 block mb-2"></i>
                <span>Seluruh proyek berjalan tepat waktu sesuai target linimasa.</span>
              </div>
            @endforelse
          </div>
        </div>
      </div>

    </div>

  </div>
@endsection
