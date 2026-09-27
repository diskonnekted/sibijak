<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Reset Kata Sandi — SIBIJAK</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.0.3/src/regular/style.css">
  <style>
    body{font-family:'Inter',sans-serif}
  </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <div class="h-14 w-14 bg-amber-500 rounded-xl flex items-center justify-center mx-auto mb-4">
        <i class="ph-fill ph-buildings text-2xl text-slate-900"></i>
      </div>
      <h1 class="text-2xl font-extrabold text-white">SIBIJAK</h1>
      <p class="text-xs text-slate-400 mt-1">Sistem Informasi Pembina Jasa Konstruksi</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-2xl">
      <h2 class="text-lg font-bold text-white mb-1">Reset Kata Sandi</h2>
      <p class="text-xs text-slate-400 mb-5">Masukkan kata sandi baru untuk akun Anda.</p>

      @if ($errors->any())
        <div class="mb-4 p-3 bg-red-950 border border-red-800 rounded text-sm text-red-300">
          {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $email) }}" required
               class="w-full px-3 py-2.5 text-sm bg-slate-800 border border-slate-700 rounded text-white focus:border-amber-500 focus:outline-none transition-all mb-4"
               placeholder="nama@instansi.go.id">

        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kata Sandi Baru</label>
        <input type="password" name="password" required
               class="w-full px-3 py-2.5 text-sm bg-slate-800 border border-slate-700 rounded text-white focus:border-amber-500 focus:outline-none transition-all mb-4"
               placeholder="Minimal 8 karakter">

        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Konfirmasi Kata Sandi</label>
        <input type="password" name="password_confirmation" required
               class="w-full px-3 py-2.5 text-sm bg-slate-800 border border-slate-700 rounded text-white focus:border-amber-500 focus:outline-none transition-all"
               placeholder="Ulangi kata sandi baru">

        <button type="submit"
                class="mt-5 w-full py-2.5 rounded text-sm font-bold bg-amber-500 text-slate-900 hover:bg-amber-400 transition-colors">
          Simpan Kata Sandi Baru
        </button>
      </form>
    </div>

    <div class="pt-4 text-center">
      <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
        <i class="ph-bold ph-arrow-left"></i> Kembali ke halaman login
      </a>
    </div>
  </div>
</body>
</html>