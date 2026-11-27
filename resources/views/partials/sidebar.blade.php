@php
  $user = auth()->user();
  $role = $user->role ?? null;
  $has  = fn ($name) => \Illuminate\Support\Facades\Route::has($name);
  $href = fn ($name) => $has($name) ? route($name) : '#';

  // Cek peran: pimpinan memakai menu sendiri, selain itu memakai menu admin
  $isPimpinan = $user && method_exists($user, 'isPimpinan') && $user->isPimpinan();

  // Daftar Ikon (path heroicons outline)
  $icons = [
    'home'      => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10',
    'briefcase' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'users'     => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    'document'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'calendar'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    'globe'     => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
    'clock'     => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    'profile'   => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM4 21a8 8 0 0116 0',
    'shield'    => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zm0-10l3-3m-6 0l3 3',
    'chart'     => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'calculator' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
    'logout'    => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
  ];

  // Struktur Menu: [label, nama route, pola route aktif, ikon]
  if ($isPimpinan) {
    // Menu Pimpinan
    $groups = [
      ['title' => null, 'roles' => null, 'items' => [
        ['Dashboard', 'pimpinan.dashboard', 'pimpinan.dashboard*', 'home'],
        ['Analisis',  'pimpinan.analisis.index', 'pimpinan.analisis*', 'chart'],
        ['Profil',    'pimpinan.profil', 'pimpinan.profil*', 'profile'],
      ]],
    ];
  } else {
    // Menu Admin
    $groups = [
      ['title' => null, 'roles' => null, 'items' => [
        ['Home', 'admin.dashboard', 'admin.dashboard*', 'home'],
        ['Kelola Admin', 'admin.admin.index', 'admin.admin*', 'shield'],
      ]],
      ['title' => 'Rekrutmen', 'roles' => null, 'items' => [
        ['Kelola Lowongan', 'admin.lowongan.index', 'admin.lowongan*', 'briefcase'],
        ['Kelola Lamaran',  'admin.pelamar.index',  'admin.pelamar*',  'users'],
        ['Kelola Jadwal Interview', 'admin.jadwal.index',  'admin.jadwal*',   'calendar'],
      ]],
      ['title' => 'Pengaturan & Sistem', 'roles' => null, 'items' => [
        ['Kelola Isi Website', 'admin.konten.index', 'admin.konten*', 'globe'],
        ['Log Aktivitas',  'admin.log',    'admin.log*',    'clock'],
      ]],
    ];
  }

  $show = fn ($roles) => !$roles || !$role || in_array($role, $roles, true);
@endphp

{{-- Wrapper "contents" supaya tidak merusak layout flex/sticky di parent --}}
<div
  class="contents"
  x-data="{
    open: false,
    mobileOpen: false,
    isDesktop: window.matchMedia('(min-width: 1024px)').matches,

    // Label tampil: di desktop ikut 'open', di mobile selalu tampil
    get expanded() { return this.isDesktop ? this.open : true; },

    init() {
      const mq = window.matchMedia('(min-width: 1024px)');
      mq.addEventListener('change', (e) => {
        this.isDesktop = e.matches;
        if (e.matches) this.mobileOpen = false;
      });
      // Kunci scroll body saat drawer mobile terbuka
      this.$watch('mobileOpen', (v) => {
        document.body.classList.toggle('overflow-hidden', v && !this.isDesktop);
      });
    },

    toggleLogo() {
      if (this.isDesktop) { this.open = !this.open; }
      else { this.mobileOpen = false; }
    },

    onNavClick(e) {
      if (!this.isDesktop) {
        this.mobileOpen = false;
      } else if (!this.open) {
        e.preventDefault();
        this.open = true;
      }
    }
  }"
  @keydown.escape.window="mobileOpen = false">

  @if(!$isPimpinan)
    @php
      $namaAdmin = $user->name ?? 'Admin';
      $inisialAdmin = mb_strtoupper(mb_substr(trim($namaAdmin), 0, 1));
    @endphp
    <div class="fixed top-4 right-4 z-[60] flex items-center gap-2 lg:top-6"
         x-data="{ chatOpen: false, notificationOpen: false, profileOpen: false }"
         @click.outside="chatOpen = false; notificationOpen = false; profileOpen = false"
         @keydown.escape.window="chatOpen = false; notificationOpen = false; profileOpen = false">

      {{-- Chat admin --}}
      <div class="relative">
        <button type="button"
                @click="chatOpen = !chatOpen; notificationOpen = false; profileOpen = false"
                class="relative grid h-11 w-11 place-items-center rounded-full border border-black/10 bg-white/90 text-zinc-700 shadow-sm backdrop-blur-xl transition hover:border-orange-200 hover:bg-white hover:text-orange-600"
                aria-label="Buka chat"
                :aria-expanded="chatOpen">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25A2.25 2.25 0 016 3h12a2.25 2.25 0 012.25 2.25v9A2.25 2.25 0 0118 16.5H9l-5.25 4.5v-15.75z"/>
          </svg>
          <span class="absolute right-0.5 top-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-orange-500"></span>
        </button>

        <div x-show="chatOpen"
             x-transition
             class="absolute right-0 mt-2 w-[19rem] max-w-[calc(100vw-2rem)] rounded-2xl border border-black/10 bg-white p-1.5 shadow-lg shadow-black/10"
             style="display:none">
          <div class="flex items-center justify-between px-3 py-2.5">
            <div>
              <p class="text-sm font-bold text-zinc-900">Pesan</p>
              <p class="text-[11px] text-zinc-400">Percakapan dengan pelamar</p>
            </div>
            @if($has('admin.pelamar.index'))
              <a href="{{ route('admin.pelamar.index') }}" class="text-[11px] font-bold text-orange-600 hover:text-orange-700">Lihat semua</a>
            @endif
          </div>
          <div class="border-t border-zinc-100 px-3 py-7 text-center">
            <span class="mx-auto grid h-10 w-10 place-items-center rounded-full bg-orange-50 text-orange-500">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a3.375 3.375 0 106.75 0 3.375 3.375 0 00-6.75 0zm-5.25 9.75l1.2-3.6A9 9 0 113.375 21.75z"/>
              </svg>
            </span>
            <p class="mt-2 text-xs font-bold text-zinc-700">Belum ada pesan baru</p>
            <p class="mt-1 text-[11px] text-zinc-400">Pesan terbaru akan tampil di sini.</p>
          </div>
        </div>
      </div>

      {{-- Notifikasi admin --}}
      <div class="relative">
        <button type="button"
                @click="notificationOpen = !notificationOpen; chatOpen = false; profileOpen = false"
                class="relative grid h-11 w-11 place-items-center rounded-full border border-black/10 bg-white/90 text-zinc-700 shadow-sm backdrop-blur-xl transition hover:border-orange-200 hover:bg-white hover:text-orange-600"
                aria-label="Buka notifikasi"
                :aria-expanded="notificationOpen">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9a6 6 0 10-12 0v.75a8.967 8.967 0 01-2.311 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
          </svg>
          <span class="absolute -right-0.5 -top-0.5 grid h-4 min-w-4 place-items-center rounded-full border-2 border-white bg-orange-500 px-1 text-[8px] font-bold leading-none text-white">3</span>
        </button>

        <div x-show="notificationOpen"
             x-transition
             class="absolute right-0 mt-2 w-[21rem] max-w-[calc(100vw-2rem)] rounded-2xl border border-black/10 bg-white p-1.5 shadow-lg shadow-black/10"
             style="display:none">
          <div class="flex items-center justify-between px-3 py-2.5">
            <div>
              <p class="text-sm font-bold text-zinc-900">Notifikasi</p>
              <p class="text-[11px] text-zinc-400">Aktivitas terbaru</p>
            </div>
            @if($has('admin.log'))
              <a href="{{ route('admin.log') }}" class="text-[11px] font-bold text-orange-600 hover:text-orange-700">Lihat log</a>
            @endif
          </div>
          <div class="space-y-1 border-t border-zinc-100 pt-1.5">
            @if($has('admin.jadwal.index'))
              <a href="{{ route('admin.jadwal.index') }}" class="flex gap-3 rounded-xl px-3 py-2.5 transition hover:bg-orange-50">
                <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-orange-500"></span>
                <span>
                  <span class="block text-xs font-bold text-zinc-800">Jadwal interview perlu diperiksa</span>
                  <span class="mt-0.5 block text-[11px] text-zinc-400">Buka halaman jadwal interview</span>
                </span>
              </a>
            @endif
            @if($has('admin.pelamar.index'))
              <a href="{{ route('admin.pelamar.index') }}" class="flex gap-3 rounded-xl px-3 py-2.5 transition hover:bg-orange-50">
                <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-orange-500"></span>
                <span>
                  <span class="block text-xs font-bold text-zinc-800">Lamaran baru menunggu seleksi</span>
                  <span class="mt-0.5 block text-[11px] text-zinc-400">Lihat daftar pelamar</span>
                </span>
              </a>
            @endif
            @if($has('admin.log'))
              <a href="{{ route('admin.log') }}" class="flex gap-3 rounded-xl px-3 py-2.5 transition hover:bg-orange-50">
                <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-zinc-300"></span>
                <span>
                  <span class="block text-xs font-bold text-zinc-800">Aktivitas sistem tercatat</span>
                  <span class="mt-0.5 block text-[11px] text-zinc-400">Periksa riwayat log aktivitas</span>
                </span>
              </a>
            @endif
          </div>
        </div>
      </div>

      {{-- Profil admin --}}
      <div class="relative">
      <button type="button"
              @click="profileOpen = !profileOpen; chatOpen = false; notificationOpen = false"
              class="inline-flex h-11 items-center gap-2 rounded-full border border-black/10 bg-white/90 py-1 pl-1 pr-3 text-zinc-900 shadow-sm backdrop-blur-xl transition hover:bg-white"
              aria-label="Menu profil admin"
              :aria-expanded="profileOpen">
        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-500 text-sm font-extrabold text-white shadow-sm">{{ $inisialAdmin }}</span>
        <span class="hidden max-w-32 truncate text-sm font-semibold sm:block">{{ $namaAdmin }}</span>
        <svg class="h-3 w-3 transition-transform" :class="profileOpen && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 9l7 7 7-7"/></svg>
      </button>

      <div x-show="profileOpen"
           x-transition
           class="absolute right-0 mt-2 w-60 rounded-2xl border border-black/10 bg-white p-1.5 shadow-lg shadow-black/10"
           style="display:none">
        <div class="mb-1 border-b border-zinc-100 px-3 py-2.5">
          <p class="truncate text-sm font-bold text-zinc-900">{{ $namaAdmin }}</p>
          <p class="truncate text-xs text-zinc-500">{{ $user->email ?? '' }}</p>
        </div>
        @if(Route::has('home'))
          <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
            Website Utama
          </a>
        @endif
        @if($has('admin.profil'))
          <a href="{{ route('admin.profil') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-orange-50 hover:text-orange-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['profile'] }}"/></svg>
            Profil Saya
          </a>
        @endif
        @if($has('logout'))
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['logout'] }}"/></svg>
              Keluar
            </button>
          </form>
        @endif
      </div>
      </div>
    </div>
  @endif

  <!-- Overlay Mobile -->
  <div
    x-show="mobileOpen"
    x-transition.opacity
    @click="mobileOpen = false"
    class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
    style="display: none;">
  </div>

  <!-- Tombol Hamburger Mobile (hilang saat drawer terbuka) -->
  <button
    type="button"
    x-show="!mobileOpen"
    x-transition.opacity
    @click="mobileOpen = true"
    aria-label="Buka menu"
    class="fixed top-4 left-4 z-[60] lg:hidden
           w-12 h-12 rounded-2xl
           flex items-center justify-center
           text-white bg-[#1e1e24]/80 backdrop-blur-xl
           border border-white/20 shadow-lg">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
  </button>

  <aside
    id="sidebar"
    :class="[
      mobileOpen ? 'translate-x-0' : '-translate-x-[120%]',
      open ? 'lg:w-64' : 'lg:w-20'
    ]"
    class="fixed top-0 left-0 z-50 flex flex-col
           m-4 py-2 h-[calc(100vh-32px)] h-[calc(100dvh-32px)]
           w-64 max-w-[calc(100vw-32px)]
           text-white rounded-3xl
           bg-[#1e1e24]/40 backdrop-blur-2xl
           border border-white/20
           shadow-[0_8px_32px_0_rgba(0,0,0,0.37)]
           transition-all duration-500 ease-in-out

           lg:sticky lg:top-4 lg:left-auto lg:ml-4 lg:mr-0
           lg:shrink-0 lg:translate-x-0">

    <!-- TOP SECTION: Logo & Toggle -->
    <div class="shrink-0">
      <div class="px-5 py-2.5 flex items-center justify-between border-b border-white/10 cursor-pointer group"
           @click="toggleLogo()">

        <div class="flex items-center gap-4 overflow-hidden">
    <div class="w-9 h-9 rounded-full bg-orange-500 flex items-center justify-center shrink-0 shadow-md overflow-hidden">
        <img src="{{ asset('storage/logoGaleri.png') }}" alt="Logo" class="w-full h-full object-cover">
    </div>

          <span x-show="expanded"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 -translate-x-2"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-2"
                class="font-bold tracking-tight text-sm whitespace-nowrap">
            Galeri Magang<i class="inline-block w-1.5 h-1.5 rounded-full bg-orange-500 ml-0.5"></i>
          </span>
        </div>

        <button x-show="expanded"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 -translate-x-1"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-1"
                type="button"
                aria-label="Tutup menu"
                class="text-white/40 group-hover:text-white transition focus:outline-none">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 16 16">
            <path d="M11.5 15l-7-7 7-7v14z"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Menu Navigasi (scroll hanya di mobile agar tooltip desktop tidak terpotong) -->
    <nav class="flex-1 min-h-0 mt-3 relative max-lg:overflow-y-auto">

      @foreach($groups as $group)
        @if($show($group['roles']))

          <div class="mb-3">

            @if($group['title'])
              <p x-show="expanded"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 -translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 -translate-x-2"
                 class="px-6 mb-1.5 text-[10px] tracking-[.2em] text-white/40 font-semibold uppercase">
                {{ $group['title'] }}
              </p>
            @endif

            <ul class="space-y-1">

              @foreach($group['items'] as $item)

                @php
                  $label  = $item[0];
                  $route  = $item[1];
                  $match  = $item[2];
                  $icon   = $item[3] ?? 'home';
                  $active = request()->routeIs($match);
                @endphp

                <li class="relative group/menu">

                  <a href="{{ $href($route) }}"
                     @click="onNavClick($event)"
                     @if($active)
                       class="flex items-center gap-4 pl-5 pr-3 py-1.5 rounded-l-full bg-white/15 text-white font-semibold transition-all border-y border-l border-white/10 shadow-inner"
                     @else
                       class="flex items-center gap-4 pl-5 pr-3 py-1.5 rounded-l-full text-white/70 hover:text-white hover:bg-white/10 transition-all"
                     @endif>

                    <div class="shrink-0 flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300 {{ $active ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30' : '' }}">
                      <svg class="w-5 h-5 {{ $active ? 'text-white' : 'text-white/85 group-hover/menu:text-white' }}"
                           fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="{{ $icons[$icon] ?? $icons['home'] }}"/>
                      </svg>
                    </div>

                    <span x-show="expanded"
                          x-transition:enter="transition ease-out duration-500"
                          x-transition:enter-start="opacity-0 -translate-x-2"
                          x-transition:enter-end="opacity-100 translate-x-0"
                          x-transition:leave="transition ease-in duration-300"
                          x-transition:leave-start="opacity-100 translate-x-0"
                          x-transition:leave-end="opacity-0 -translate-x-2"
                          class="text-sm whitespace-nowrap {{ $active ? 'text-white font-bold' : 'text-white/90' }}">
                      {{ $label }}
                    </span>
                  </a>

                  <!-- Tooltip Menu (hanya desktop saat sidebar collapse) -->
                  <div x-show="!expanded"
                       class="absolute left-full ml-4 top-1/2 -translate-y-1/2 bg-orange-500/80 backdrop-blur-xl text-white text-xs font-semibold px-4 py-2.5 rounded-full opacity-0 translate-x-[-10px] group-hover/menu:opacity-100 group-hover/menu:translate-x-0 transition-all duration-300 pointer-events-none whitespace-nowrap z-[999] shadow-2xl border border-white/20">
                    {{ $label }}
                  </div>

                </li>

              @endforeach

            </ul>

          </div>

        @endif
      @endforeach

    </nav>

  </aside>
</div>