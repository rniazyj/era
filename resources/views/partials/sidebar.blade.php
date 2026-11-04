@php
  $user = auth()->user();
  $role = $user->role ?? null;                       // sesuaikan: kolom role di tabel users (mis. 'pelamar', 'pimpinan')
  $has  = fn ($name) => \Illuminate\Support\Facades\Route::has($name);
  $href = fn ($name) => $has($name) ? route($name) : '#';

  // Ikon (path heroicons outline)
  $icons = [
    'home'     => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10',
    'briefcase'=> 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'user'     => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    'check'    => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'chart'    => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'mail'     => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'cog'      => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
    'logout'   => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
  ];

  /*
   | MENU: sesuaikan 'route' (nama route di web.php), 'match' (pola routeIs untuk state aktif)
   | dan 'roles' (null = semua role). Route yang belum ada akan tampil dengan href "#".
   */
  $groups = [
    ['title' => 'Utama', 'roles' => null, 'items' => [
      ['Dashboard',      'dashboard',            'dashboard',        'home'],
    ]],
    ['title' => 'Pelamar', 'roles' => ['pelamar'], 'items' => [
      ['Lowongan',       'pelamar.lowongan.index', 'pelamar.lowongan.*', 'briefcase'],
      ['Lamaran Saya',   'pelamar.lamaran.index',  'pelamar.lamaran.*',  'document'],
      ['Profil & Berkas','pelamar.profil',         'pelamar.profil*',    'user'],
    ]],
    ['title' => 'Pimpinan', 'roles' => ['pimpinan'], 'items' => [
      ['Data Pelamar',   'pimpinan.pelamar.index',     'pimpinan.pelamar.*',     'user'],
      ['Persetujuan',    'pimpinan.persetujuan.index', 'pimpinan.persetujuan.*', 'check'],
      ['Laporan',        'pimpinan.laporan',           'pimpinan.laporan*',      'chart'],
    ]],
    ['title' => 'Surat', 'roles' => null, 'items' => [
      ['Daftar Surat',   'surat.index',  'surat.index',  'mail'],
      ['Buat Surat',     'surat.create', 'surat.create', 'document'],
    ]],
  ];
  $show = fn ($roles) => !$roles || !$role || in_array($role, $roles, true);   // role kosong = tampilkan semua
@endphp

<!-- Overlay mobile -->
<div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity lg:hidden"></div>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen shrink-0 flex flex-col bg-[#111] text-white transition-transform duration-300">

  <!-- Logo -->
  <div class="h-16 px-6 flex items-center justify-between border-b border-white/10">
    <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-lg font-semibold tracking-tight">ERA<i class="w-1.5 h-1.5 rounded-full bg-orange-500"></i></a>
    <button type="button" id="sidebar-close" class="lg:hidden text-white/60 hover:text-white" aria-label="Tutup menu">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>

  <!-- Kartu pengguna -->
  @if($user)
    <div class="px-4 pt-5">
      <div class="flex items-center gap-3 rounded-2xl bg-white/5 border border-white/10 p-3">
        <span class="w-10 h-10 rounded-xl bg-orange-500 text-white font-semibold flex items-center justify-center">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
        <div class="min-w-0">
          <p class="text-sm font-semibold truncate">{{ $user->name }}</p>
          <p class="text-[11px] text-white/45 truncate">{{ $role ? ucfirst($role) : 'Pengguna' }}</p>
        </div>
      </div>
    </div>
  @endif

  <!-- Menu -->
  <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
    @foreach($groups as $group)
      @if($show($group['roles']))
        <div>
          <p class="px-3 mb-2 text-[9px] tracking-[.2em] text-white/35">{{ strtoupper($group['title']) }}</p>
          <ul class="space-y-1">
            @foreach($group['items'] as [$label, $route, $match, $icon])
              @php $active = request()->routeIs($match); @endphp
              <li>
                <a href="{{ $href($route) }}" class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition {{ $active ? 'bg-white/10 text-white font-medium' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                  <span class="absolute left-0 top-2 bottom-2 w-[3px] rounded-full bg-orange-500 transition-transform origin-center {{ $active ? 'scale-y-100' : 'scale-y-0 group-hover:scale-y-50' }}"></span>
                  <svg class="w-5 h-5 shrink-0 {{ $active ? 'text-orange-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="{{ $icons[$icon] }}"/></svg>
                  <span class="truncate">{{ $label }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      @endif
    @endforeach
  </nav>

  <!-- Bawah: pengaturan & keluar -->
  <div class="p-4 border-t border-white/10 space-y-1">
    <a href="{{ $href('profile.edit') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-white/60 hover:text-white hover:bg-white/5 transition">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="{{ $icons['cog'] }}"/></svg>Pengaturan
    </a>
    @if($has('logout'))
      <form method="POST" action="{{ route('logout') }}">@csrf
        <button type="submit" class="w-full flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-white/60 hover:text-orange-400 hover:bg-white/5 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="{{ $icons['logout'] }}"/></svg>Keluar
        </button>
      </form>
    @endif
  </div>
</aside>