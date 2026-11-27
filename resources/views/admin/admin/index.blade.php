<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Admin - Galeri Magang</title>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter','system-ui','sans-serif'] }, colors: { paper:'#faf9fc', ewforange:'#f97316' } } } };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">
<div class="flex min-h-screen flex-col lg:flex-row" x-data="{ tambah: {{ session('open_create_admin') ? 'true' : 'false' }}, mode: 'existing' }">
  @include('partials.sidebar')

  <main class="min-w-0 w-full flex-1 px-4 sm:px-6 lg:px-10 pt-20 sm:pt-24 lg:pt-10 pb-20">
    <div class="w-full max-w-6xl mx-auto">
      <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
          <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">Panel Administrator</span>
          <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-2">Kelola Admin</h1>
          <p class="text-sm text-zinc-500 mt-1">Tambah dan kelola akun dengan akses administrator.</p>
        </div>
      </div>

      @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ session('error') }}</div>
      @endif

      @if($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          <p class="font-bold mb-1">Periksa kembali data:</p>
          <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
      @endif

      <div class="mb-4 rounded-2xl border border-zinc-200 bg-white p-3 shadow-sm">
        <form method="GET" action="{{ route('admin.admin.index') }}" class="flex flex-col gap-2 sm:flex-row">
          <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 110-15 7.5 7.5 0 010 15z"/></svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau nomor telepon" class="w-full rounded-xl border border-zinc-200 py-2.5 pl-10 pr-3 text-sm outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
          </div>
          <button class="rounded-xl bg-zinc-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-zinc-800">Cari</button>
          <button type="button" @click="tambah = true" class="shrink-0 inline-flex items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-orange-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Admin
          </button>
        </form>
      </div>

      <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-100 bg-zinc-50/70 text-xs uppercase tracking-wider text-zinc-500">
              <tr>
                <th class="px-5 py-4">Admin</th>
                <th class="px-5 py-4">Kontak</th>
                <th class="px-5 py-4">Status</th>
                <th class="px-5 py-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
              @forelse($admins as $admin)
                @php $inisial = mb_strtoupper(mb_substr(trim($admin->name), 0, 1)); @endphp
                <tr class="hover:bg-zinc-50/60">
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                      <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-500 text-sm font-extrabold text-white">{{ $inisial }}</div>
                      <div><p class="font-bold text-zinc-900">{{ $admin->name }}</p><p class="text-xs text-zinc-400">Admin HR</p></div>
                    </div>
                  </td>
                  <td class="px-5 py-4">
                    <p class="font-medium text-zinc-700">{{ $admin->email }}</p>
                    <p class="text-xs text-zinc-400">{{ $admin->no_telepon ?: '-' }}</p>
                  </td>
                  <td class="px-5 py-4">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $admin->status_akun === 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                      {{ ucfirst($admin->status_akun ?? 'aktif') }}
                    </span>
                  </td>
                  <td class="px-5 py-4">
                    <div class="flex justify-end gap-2" x-data="{ edit: {{ (int) session('edit_admin_id') === (int) $admin->id ? 'true' : 'false' }} }">
                      <button type="button" @click="edit = true" class="rounded-lg border border-zinc-200 px-3 py-2 text-xs font-bold text-zinc-700 hover:border-orange-300 hover:text-orange-600">Edit</button>
                      @if($admin->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.admin.destroy', $admin) }}" onsubmit="return confirm('Cabut hak admin {{ addslashes($admin->name) }}? Akun tetap ada dan menjadi akun pelamar.')">
                          @csrf @method('DELETE')
                          <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50">Cabut Hak</button>
                        </form>
                      @endif

                      <div x-show="edit" x-transition.opacity class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40 p-4" @keydown.escape.window="edit = false">
                        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl" @click.outside="edit = false">
                          <h2 class="text-lg font-bold">Edit Admin</h2>
                          <form method="POST" action="{{ route('admin.admin.update', $admin) }}" class="mt-5 space-y-4">
                            @csrf @method('PUT')
                            <label class="block"><span class="text-xs font-bold text-zinc-600">Nama</span><input name="name" value="{{ $admin->name }}" required class="mt-1 w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm outline-none focus:border-orange-400"></label>
                            <label class="block"><span class="text-xs font-bold text-zinc-600">Email</span><input type="email" name="email" value="{{ $admin->email }}" required class="mt-1 w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm outline-none focus:border-orange-400"></label>
                            <label class="block"><span class="text-xs font-bold text-zinc-600">No. Telepon</span><input name="no_telepon" value="{{ $admin->no_telepon }}" class="mt-1 w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm outline-none focus:border-orange-400"></label>
                            <div class="flex justify-end gap-2 pt-2"><button type="button" @click="edit = false" class="rounded-xl border border-zinc-200 px-4 py-2.5 text-sm font-bold">Batal</button><button class="rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-600">Simpan</button></div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="px-5 py-12 text-center text-zinc-400">Belum ada akun admin yang ditemukan.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($admins->hasPages())<div class="border-t border-zinc-100 px-5 py-4">{{ $admins->links() }}</div>@endif
      </div>
    </div>
  </main>

  <div x-show="tambah" x-transition.opacity class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40 p-4" @keydown.escape.window="tambah = false">
    <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl" @click.outside="tambah = false">
      <h2 class="text-lg font-bold">Tambahkan Hak Admin</h2>
      <p class="mt-1 text-sm text-zinc-500">Pilih akun yang sudah terdaftar atau buat akun baru.</p>

      <div class="mt-5 grid grid-cols-2 rounded-xl bg-zinc-100 p-1">
        <button type="button" @click="mode = 'existing'" :class="mode === 'existing' ? 'bg-white text-zinc-900 shadow-sm' : 'text-zinc-500'" class="rounded-lg px-3 py-2 text-sm font-bold transition">Akun Terdaftar</button>
        <button type="button" @click="mode = 'new'" :class="mode === 'new' ? 'bg-white text-zinc-900 shadow-sm' : 'text-zinc-500'" class="rounded-lg px-3 py-2 text-sm font-bold transition">Akun Baru</button>
      </div>

      <form x-show="mode === 'existing'" method="POST" action="{{ route('admin.admin.store') }}" class="mt-5 space-y-4">
        @csrf
        <input type="hidden" name="mode" value="existing">
        <label class="block">
          <span class="text-xs font-bold text-zinc-600">Pilih Akun</span>
          <select name="user_id" required class="mt-1 w-full rounded-xl border border-zinc-200 px-3 py-3 text-sm outline-none focus:border-orange-400">
            <option value="">-- Pilih akun user --</option>
            @foreach($eligibleUsers as $user)
              <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
            @endforeach
          </select>
        </label>
        @if($eligibleUsers->isEmpty())
          <p class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-medium text-amber-700">Tidak ada akun terdaftar yang belum menjadi admin.</p>
        @endif
        <div class="flex justify-end gap-2 pt-2"><button type="button" @click="tambah = false" class="rounded-xl border border-zinc-200 px-4 py-2.5 text-sm font-bold">Batal</button><button class="rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-600">Jadikan Admin</button></div>
      </form>

      <form x-show="mode === 'new'" method="POST" action="{{ route('admin.admin.store') }}" class="mt-5 space-y-4" style="display:none">
        @csrf
        <input type="hidden" name="mode" value="new">
        <label class="block"><span class="text-xs font-bold text-zinc-600">Nama</span><input name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm outline-none focus:border-orange-400"></label>
        <label class="block"><span class="text-xs font-bold text-zinc-600">Email</span><input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm outline-none focus:border-orange-400"></label>
        <label class="block"><span class="text-xs font-bold text-zinc-600">No. Telepon</span><input name="no_telepon" value="{{ old('no_telepon') }}" class="mt-1 w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm outline-none focus:border-orange-400"></label>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <label class="block"><span class="text-xs font-bold text-zinc-600">Password</span><input type="password" name="password" required minlength="8" class="mt-1 w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm outline-none focus:border-orange-400"></label>
          <label class="block"><span class="text-xs font-bold text-zinc-600">Konfirmasi Password</span><input type="password" name="password_confirmation" required minlength="8" class="mt-1 w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm outline-none focus:border-orange-400"></label>
        </div>
        <div class="flex justify-end gap-2 pt-2"><button type="button" @click="tambah = false" class="rounded-xl border border-zinc-200 px-4 py-2.5 text-sm font-bold">Batal</button><button class="rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-600">Tambah Admin</button></div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
