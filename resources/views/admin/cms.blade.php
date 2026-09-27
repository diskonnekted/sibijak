@extends('layouts.admin')

@section('title', 'Kelola Konten & Publikasi | SIBIJAK')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Kelola Konten & Publikasi</h1>
      <p class="text-sm text-slate-500">Kelola pelatihan, regulasi, dan berita yang tampil di portal publik.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-gov-900 hover:text-gov-700">
      <i class="ph-bold ph-arrow-left"></i> Kembali ke Dashboard
    </a>
  </div>

  @if(session('success'))
  <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm font-semibold">
    <i class="ph-bold ph-check-circle"></i> {{ session('success') }}
  </div>
  @endif
  @if(session('error'))
  <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm font-semibold">
    <i class="ph-bold ph-warning-circle"></i> {{ session('error') }}
  </div>
  @endif

  {{-- ===================== PELATIHAN ===================== --}}
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
      <div>
        <h2 class="font-bold text-slate-900">Pelatihan</h2>
        <p class="text-xs text-slate-500">{{ $trainings->count() }} pelatihan</p>
      </div>
      <button onclick="openPelatihanModal()" class="px-3 py-2 rounded bg-gov-900 text-white text-sm font-bold hover:bg-gov-700 flex items-center gap-1">
        <i class="ph-bold ph-plus"></i> Tambah
      </button>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs font-bold text-slate-400 uppercase tracking-wider">
          <tr>
            <th class="px-6 py-3">Nama Pelatihan</th>
            <th class="px-4 py-3">Tanggal</th>
            <th class="px-4 py-3">Kuota</th>
            <th class="px-4 py-3">Pendaftar</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($trainings as $t)
          <tr class="hover:bg-slate-50/60">
            <td class="px-6 py-3 font-semibold text-slate-900">{{ $t->nama_pelatihan }}</td>
            <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
            <td class="px-4 py-3 text-slate-600">{{ $t->kuota }}</td>
            <td class="px-4 py-3 text-slate-600">{{ $t->pendaftar_count }}</td>
            <td class="px-4 py-3">
              <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $t->status === 'Selesai' ? 'bg-slate-100 text-slate-500' : 'bg-emerald-100 text-emerald-700' }}">{{ $t->status }}</span>
            </td>
            <td class="px-4 py-3 text-right whitespace-nowrap">
              <button onclick='openPelatihanModal(@json($t))' class="px-2.5 py-1.5 rounded text-xs font-semibold text-gov-900 hover:bg-slate-100">Edit</button>
              <form action="{{ route('admin.cms.pelatihan.delete', $t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pelatihan ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-2.5 py-1.5 rounded text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data pelatihan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ===================== REGULASI ===================== --}}
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
      <div>
        <h2 class="font-bold text-slate-900">Regulasi</h2>
        <p class="text-xs text-slate-500">{{ $regulations->count() }} regulasi</p>
      </div>
      <button onclick="openRegulasiModal()" class="px-3 py-2 rounded bg-gov-900 text-white text-sm font-bold hover:bg-gov-700 flex items-center gap-1">
        <i class="ph-bold ph-plus"></i> Tambah
      </button>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs font-bold text-slate-400 uppercase tracking-wider">
          <tr>
            <th class="px-6 py-3">Judul</th>
            <th class="px-4 py-3">Nomor</th>
            <th class="px-4 py-3">Tahun</th>
            <th class="px-4 py-3">Kategori</th>
            <th class="px-4 py-3">File</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($regulations as $r)
          <tr class="hover:bg-slate-50/60">
            <td class="px-6 py-3 font-semibold text-slate-900">{{ $r->judul }}</td>
            <td class="px-4 py-3 text-slate-600">{{ $r->nomor }}</td>
            <td class="px-4 py-3 text-slate-600">{{ $r->tahun }}</td>
            <td class="px-4 py-3 text-slate-600">{{ $r->kategori }}</td>
            <td class="px-4 py-3">
              @if($r->file)
                <a href="{{ route('regulasi.download', $r) }}" class="inline-flex items-center gap-1 text-xs font-bold text-gov-600 hover:text-gov-800" title="Unduh PDF">
                  <i class="ph-bold ph-file-pdf text-base text-red-500"></i> PDF
                </a>
              @else
                <span class="text-xs text-slate-300">—</span>
              @endif
            </td>
            <td class="px-4 py-3 text-right whitespace-nowrap">
              <button onclick='openRegulasiModal(@json($r))' class="px-2.5 py-1.5 rounded text-xs font-semibold text-gov-900 hover:bg-slate-100">Edit</button>
              <form action="{{ route('admin.cms.regulasi.delete', $r) }}" method="POST" class="inline" onsubmit="return confirm('Hapus regulasi ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-2.5 py-1.5 rounded text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data regulasi.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ===================== BERITA ===================== --}}
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
      <div>
        <h2 class="font-bold text-slate-900">Berita</h2>
        <p class="text-xs text-slate-500">{{ $news->count() }} berita</p>
      </div>
      <button onclick="openBeritaModal()" class="px-3 py-2 rounded bg-gov-900 text-white text-sm font-bold hover:bg-gov-700 flex items-center gap-1">
        <i class="ph-bold ph-plus"></i> Tambah
      </button>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs font-bold text-slate-400 uppercase tracking-wider">
          <tr>
            <th class="px-6 py-3">Judul</th>
            <th class="px-4 py-3">Tanggal</th>
            <th class="px-4 py-3">Kategori</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($news as $n)
          <tr class="hover:bg-slate-50/60">
            <td class="px-6 py-3 font-semibold text-slate-900">{{ $n->judul }}</td>
            <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($n->tanggal)->format('d/m/Y') }}</td>
            <td class="px-4 py-3 text-slate-600">{{ $n->kategori }}</td>
            <td class="px-4 py-3">
              @if($n->status === 'publish')
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">Terbit</span>
              @else
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-700">Draft</span>
              @endif
            </td>
            <td class="px-4 py-3 text-right whitespace-nowrap">
              <button onclick='openBeritaModal(@json($n))' class="px-2.5 py-1.5 rounded text-xs font-semibold text-gov-900 hover:bg-slate-100">Edit</button>
              <form action="{{ route('admin.cms.berita.delete', $n) }}" method="POST" class="inline" onsubmit="return confirm('Hapus berita ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-2.5 py-1.5 rounded text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada data berita.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- ============ MODAL PELATIHAN ============ --}}
<div id="modal-pelatihan" class="hidden fixed inset-0 z-50 overflow-y-auto">
  <div class="flex items-end sm:items-center justify-center min-h-full p-4 text-center">
    <div class="fixed inset-0 bg-slate-950/60" onclick="closePelatihanModal()"></div>
    <div class="relative inline-block align-bottom bg-white rounded-xl shadow-xl text-left overflow-hidden sm:max-w-lg w-full">
      <form id="form-pelatihan" method="POST" action="{{ route('admin.cms.pelatihan.store') }}">
        @csrf
        <input type="hidden" name="_method" id="pelatihan-method" value="">
        <div class="bg-gov-900 px-6 py-4 flex items-center justify-between">
          <h3 id="pelatihan-modal-title" class="text-white font-bold text-lg">Tambah Pelatihan</h3>
          <button type="button" onclick="closePelatihanModal()" class="text-white/70 hover:text-white"><i class="ph-bold ph-x text-xl"></i></button>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Pelatihan</label>
            <input type="text" name="nama_pelatihan" id="p-nama" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal</label>
              <input type="date" name="tanggal" id="p-tanggal" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kuota</label>
              <input type="number" name="kuota" id="p-kuota" required min="1" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
            <select name="status" id="p-status" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
              <option value="Mendatang">Mendatang</option>
              <option value="Berlangsung">Berlangsung</option>
              <option value="Selesai">Selesai</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Detail (opsional)</label>
            <textarea name="detail" id="p-detail" rows="3" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900"></textarea>
          </div>
        </div>
        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-2">
          <button type="button" onclick="closePelatihanModal()" class="px-4 py-2 rounded border border-slate-300 text-sm font-semibold text-slate-600 hover:bg-white">Batal</button>
          <button type="submit" class="px-4 py-2 rounded bg-gov-900 text-white text-sm font-bold hover:bg-gov-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ============ MODAL REGULASI ============ --}}
<div id="modal-regulasi" class="hidden fixed inset-0 z-50 overflow-y-auto">
  <div class="flex items-end sm:items-center justify-center min-h-full p-4 text-center">
    <div class="fixed inset-0 bg-slate-950/60" onclick="closeRegulasiModal()"></div>
    <div class="relative inline-block align-bottom bg-white rounded-xl shadow-xl text-left overflow-hidden sm:max-w-lg w-full">
      <form id="form-regulasi" method="POST" action="{{ route('admin.cms.regulasi.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" id="regulasi-method" value="">
        <div class="bg-gov-900 px-6 py-4 flex items-center justify-between">
          <h3 id="regulasi-modal-title" class="text-white font-bold text-lg">Tambah Regulasi</h3>
          <button type="button" onclick="closeRegulasiModal()" class="text-white/70 hover:text-white"><i class="ph-bold ph-x text-xl"></i></button>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Judul</label>
            <input type="text" name="judul" id="r-judul" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor</label>
              <input type="text" name="nomor" id="r-nomor" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tahun</label>
              <input type="number" name="tahun" id="r-tahun" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kategori</label>
            <select name="kategori" id="r-kategori" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
              <option value="">— Pilih Kategori —</option>
              @foreach($kategoriList as $kategori)
                <option value="{{ $kategori }}">{{ $kategori }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">File PDF Regulasi</label>
            <input type="file" name="file" id="r-file" accept=".pdf,application/pdf"
                   class="w-full text-sm file:mr-3 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-gov-900 file:text-white file:font-bold hover:file:bg-gov-700 text-slate-500">
            <p class="text-[11px] text-slate-400 mt-1">PDF max 20 MB. Dapat diunduh oleh kontraktor &amp; masyarakat umum.</p>
            <p id="r-file-hint" class="text-[11px] text-gov-600 font-semibold mt-1"></p>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Deskripsi (opsional)</label>
            <textarea name="deskripsi" id="r-deskripsi" rows="3" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900"></textarea>
          </div>
        </div>
        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-2">
          <button type="button" onclick="closeRegulasiModal()" class="px-4 py-2 rounded border border-slate-300 text-sm font-semibold text-slate-600 hover:bg-white">Batal</button>
          <button type="submit" class="px-4 py-2 rounded bg-gov-900 text-white text-sm font-bold hover:bg-gov-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ============ MODAL BERITA ============ --}}
<div id="modal-berita" class="hidden fixed inset-0 z-50 overflow-y-auto">
  <div class="flex items-end sm:items-center justify-center min-h-full p-4 text-center">
    <div class="fixed inset-0 bg-slate-950/60" onclick="closeBeritaModal()"></div>
    <div class="relative inline-block align-bottom bg-white rounded-xl shadow-xl text-left overflow-hidden sm:max-w-lg w-full">
      <form id="form-berita" method="POST" action="{{ route('admin.cms.berita.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" id="berita-method" value="">
        <div class="bg-gov-900 px-6 py-4 flex items-center justify-between">
          <h3 id="berita-modal-title" class="text-white font-bold text-lg">Tambah Berita</h3>
          <button type="button" onclick="closeBeritaModal()" class="text-white/70 hover:text-white"><i class="ph-bold ph-x text-xl"></i></button>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Judul</label>
            <input type="text" name="judul" id="b-judul" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Terbit</label>
              <input type="date" name="tanggal" id="b-tanggal" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kategori</label>
              <select name="kategori" id="b-kategori" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
                <option value="">— Pilih Kategori —</option>
                @foreach($kategoriBerita as $kategori)
                  <option value="{{ $kategori }}">{{ $kategori }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Foto Cover (opsional)</label>
              <input type="file" name="cover_image" id="b-cover" accept=".jpg,.jpeg,.png,.webp,image/*"
                     class="w-full text-sm file:mr-3 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-gov-900 file:text-white file:font-bold hover:file:bg-gov-700 text-slate-500">
              <p class="text-[11px] text-slate-400 mt-1">JPG/PNG/WebP, maks 2 MB.</p>
              <p id="b-cover-hint" class="text-[11px] text-gov-600 font-semibold mt-1"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
              <select name="status" id="b-status" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900">
                <option value="draft">Draft</option>
                <option value="publish">Terbit</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Konten</label>
            <textarea name="konten" id="b-konten" rows="5" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-900"></textarea>
          </div>
        </div>
        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-2">
          <button type="button" onclick="closeBeritaModal()" class="px-4 py-2 rounded border border-slate-300 text-sm font-semibold text-slate-600 hover:bg-white">Batal</button>
          <button type="submit" class="px-4 py-2 rounded bg-gov-900 text-white text-sm font-bold hover:bg-gov-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function openPelatihanModal(data = null) {
  const isEdit = !!data;
  document.getElementById('pelatihan-modal-title').textContent = isEdit ? 'Edit Pelatihan' : 'Tambah Pelatihan';
  document.getElementById('p-nama').value = isEdit ? data.nama_pelatihan : '';
  document.getElementById('p-tanggal').value = isEdit ? data.tanggal : '';
  document.getElementById('p-kuota').value = isEdit ? data.kuota : '';
  document.getElementById('p-status').value = isEdit ? data.status : 'Mendatang';
  document.getElementById('p-detail').value = isEdit ? (data.detail || '') : '';
  document.getElementById('pelatihan-method').value = isEdit ? 'PUT' : '';
  document.getElementById('form-pelatihan').action = isEdit
    ? "{{ url('admin/cms/pelatihan') }}/" + data.id
    : "{{ route('admin.cms.pelatihan.store') }}";
  document.getElementById('modal-pelatihan').classList.remove('hidden');
}
function closePelatihanModal() { document.getElementById('modal-pelatihan').classList.add('hidden'); }

function openRegulasiModal(data = null) {
  const isEdit = !!data;
  document.getElementById('regulasi-modal-title').textContent = isEdit ? 'Edit Regulasi' : 'Tambah Regulasi';
  document.getElementById('r-judul').value = isEdit ? data.judul : '';
  document.getElementById('r-nomor').value = isEdit ? (data.nomor || '') : '';
  document.getElementById('r-tahun').value = isEdit ? (data.tahun || '') : '';
  document.getElementById('r-kategori').value = isEdit ? (data.kategori || '') : '';
  document.getElementById('r-deskripsi').value = isEdit ? (data.deskripsi || '') : '';
  document.getElementById('r-file').value = '';
  document.getElementById('r-file-hint').textContent = (isEdit && data.file)
    ? ('File terpasang: ' + data.file.split('/').pop() + ' — unggah PDF baru untuk menggantinya')
    : '';
  document.getElementById('regulasi-method').value = isEdit ? 'PUT' : '';
  document.getElementById('form-regulasi').action = isEdit
    ? "{{ url('admin/cms/regulasi') }}/" + data.id
    : "{{ route('admin.cms.regulasi.store') }}";
  document.getElementById('modal-regulasi').classList.remove('hidden');
}
function closeRegulasiModal() { document.getElementById('modal-regulasi').classList.add('hidden'); }

function openBeritaModal(data = null) {
  const isEdit = !!data;
  document.getElementById('berita-modal-title').textContent = isEdit ? 'Edit Berita' : 'Tambah Berita';
  document.getElementById('b-judul').value = isEdit ? data.judul : '';
  document.getElementById('b-tanggal').value = isEdit ? data.tanggal : '';
  document.getElementById('b-kategori').value = isEdit ? (data.kategori || '') : '';
  document.getElementById('b-cover').value = '';
  document.getElementById('b-cover-hint').textContent = (isEdit && data.cover_image)
    ? ('Foto terpasang: ' + data.cover_image.split('/').pop() + ' — unggah foto baru untuk menggantinya')
    : '';
  document.getElementById('b-status').value = isEdit ? data.status : 'draft';
  document.getElementById('b-konten').value = isEdit ? (data.konten || '') : '';
  document.getElementById('berita-method').value = isEdit ? 'PUT' : '';
  document.getElementById('form-berita').action = isEdit
    ? "{{ url('admin/cms/berita') }}/" + data.id
    : "{{ route('admin.cms.berita.store') }}";
  document.getElementById('modal-berita').classList.remove('hidden');
}
function closeBeritaModal() { document.getElementById('modal-berita').classList.add('hidden'); }
</script>
@endsection