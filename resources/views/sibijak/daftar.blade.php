@extends('layouts.sibijak')

@section('title', 'SIBIJAK Banjarnegara - Pendaftaran Online')

@section('content')
  <!-- TOP BANNER -->
  <section class="bg-gov-900 text-white py-12 border-b border-gov-950">
    <div class="max-w-7xl mx-auto px-6 space-y-2">
      <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest block">REGISTRASI ONLINE</span>
      <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Portal Pendaftaran SIBIJAK</h1>
      <p class="text-xs text-slate-300 max-w-[60ch]">
        Form pengajuan pendaftaran Badan Usaha Jasa Konstruksi baru serta pendaftaran bimbingan teknis kompetensi.
      </p>
    </div>
  </section>

  <!-- CONTENT -->
  <section class="py-16">
    <div class="max-w-3xl mx-auto px-6">
      
      @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded flex items-center gap-3 text-sm font-semibold mb-6">
          <i class="ph-bold ph-warning-circle text-lg text-red-600"></i>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded flex items-center gap-3 text-sm font-semibold mb-6">
          <i class="ph-bold ph-check-circle text-lg text-emerald-600"></i>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      <!-- Tab Controller -->
      <div class="bg-white rounded border border-slate-200 overflow-hidden shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 flex">
          <button onclick="switchDaftarTab('badanusaha')" id="btn-tab-bu" class="flex-1 py-4 text-center text-xs font-bold uppercase tracking-wider border-b-2 border-gov-900 text-gov-900 focus:outline-none transition-all">
            Pendaftaran Badan Usaha
          </button>
          <button onclick="switchDaftarTab('pelatihan')" id="btn-tab-pl" class="flex-1 py-4 text-center text-xs font-bold uppercase tracking-wider border-b-2 border-transparent text-slate-500 hover:text-slate-800 focus:outline-none transition-all">
            Pendaftaran Pelatihan
          </button>
        </div>

        <!-- FORM BADAN USAHA -->
        <div id="form-content-bu" class="p-8">
          <form action="{{ route('daftar.badanusaha') }}" method="POST" class="space-y-4">
            @csrf
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2 mb-4">Data Perusahaan</h3>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Perusahaan / Kontraktor</label>
              <input type="text" name="name" required placeholder="Contoh: PT Banjar Karya Abadi" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIB</label>
                <input type="text" name="nib" required placeholder="Masukkan NIB resmi" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penanggung Jawab (Direktur)</label>
                <input type="text" name="pj" required placeholder="Nama Lengkap & Gelar" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Klasifikasi Bidang</label>
                <select name="bidang" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                  <option value="Sipil">Pekerjaan Sipil</option>
                  <option value="Arsitektur">Arsitektur</option>
                  <option value="Mekanikal">Mekanikal</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kualifikasi Usaha</label>
                <select name="kualifikasi" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                  <option value="Besar">Besar (B)</option>
                  <option value="Menengah">Menengah (M)</option>
                  <option value="Kecil">Kecil (K)</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Perusahaan</label>
                <input type="email" name="email" required placeholder="example@perusahaan.co.id" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Telepon</label>
                <input type="text" name="telepon" placeholder="Nomor Telepon Kantor" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Kantor Lengkap</label>
              <textarea name="alamat" required rows="3" placeholder="Alamat lengkap beserta kabupaten/provinsi" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all"></textarea>
            </div>

            <div class="pt-4 flex justify-end">
              <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 text-xs font-bold tracking-wider uppercase text-white bg-gov-900 hover:bg-gov-950 rounded shadow active:scale-[0.98] transition-all">
                Daftarkan Badan Usaha
              </button>
            </div>
          </form>
        </div>

        <!-- FORM PELATIHAN -->
        <div id="form-content-pl" class="hidden p-8">
          <form action="{{ route('daftar.pelatihan') }}" method="POST" class="space-y-4">
            @csrf
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2 mb-4">Form Keikutsertaan Bimtek</h3>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Paket Pelatihan / Bimtek</label>
              <select name="training_id" id="pl-select" required class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
                @forelse($trainings as $tr)
                  <option value="{{ $tr->id }}" {{ request('training_id') == $tr->id ? 'selected' : '' }}>
                    {{ $tr->nama_pelatihan }} (Tanggal: {{ date('d M Y', strtotime($tr->tanggal)) }})
                  </option>
                @empty
                  <option value="" disabled>Tidak ada kelas pelatihan yang sedang membuka registrasi</option>
                @endforelse
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Peserta</label>
              <input type="text" name="nama_peserta" required placeholder="Nama Lengkap Beserta Gelar jika ada" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIK (Nomor Induk Kependudukan)</label>
                <input type="text" name="nik" required maxlength="16" placeholder="Masukkan 16 digit NIK" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. HP / WhatsApp</label>
                <input type="text" name="kontak" required placeholder="Contoh: 081234567890" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Asal Instansi / Perusahaan</label>
              <input type="text" name="instansi" required placeholder="Contoh: CV Banjar Mandiri / Umum" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900 transition-all">
            </div>

            <div class="pt-4 flex justify-end">
              <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 text-xs font-bold tracking-wider uppercase text-white bg-gov-900 hover:bg-gov-950 rounded shadow active:scale-[0.98] transition-all">
                Kirim Pendaftaran Pelatihan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
@endsection

@section('scripts')
  <script>
    // Tab switching logic for registration forms
    function switchDaftarTab(formName) {
      const buTabBtn = document.getElementById('btn-tab-bu');
      const plTabBtn = document.getElementById('btn-tab-pl');
      const buForm = document.getElementById('form-content-bu');
      const plForm = document.getElementById('form-content-pl');

      if(formName === 'badanusaha') {
        buTabBtn.className = "flex-1 py-4 text-center text-xs font-bold uppercase tracking-wider border-b-2 border-gov-900 text-gov-900 focus:outline-none transition-all";
        plTabBtn.className = "flex-1 py-4 text-center text-xs font-bold uppercase tracking-wider border-b-2 border-transparent text-slate-500 hover:text-slate-800 focus:outline-none transition-all";
        buForm.classList.remove('hidden');
        plForm.classList.add('hidden');
      } else {
        plTabBtn.className = "flex-1 py-4 text-center text-xs font-bold uppercase tracking-wider border-b-2 border-gov-900 text-gov-900 focus:outline-none transition-all";
        buTabBtn.className = "flex-1 py-4 text-center text-xs font-bold uppercase tracking-wider border-b-2 border-transparent text-slate-500 hover:text-slate-800 focus:outline-none transition-all";
        plForm.classList.remove('hidden');
        buForm.classList.add('hidden');
      }
    }

    // Auto-detect training ID from URL query
    document.addEventListener("DOMContentLoaded", () => {
      const urlParams = new URLSearchParams(window.location.search);
      const trainingId = urlParams.get('training_id');
      if(trainingId) {
        switchDaftarTab('pelatihan');
        const select = document.getElementById('pl-select');
        select.value = trainingId;
      }
    });
  </script>
@endsection
