@php
  $user = auth()->user();
  $role = $user->role ?? null;
  $has  = fn ($name) => \Illuminate\Support\Facades\Route::has($name);
  $href = fn ($name) => $has($name) ? route($name) : '#';

  // Daftar Ikon (path heroicons outline)
  $icons = [
    'home'      => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10',
    'briefcase' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'users'     => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    'document'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'globe'     => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
    'clock'     => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    'profile'   => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM4 21a8 8 0 0116 0',
    'logout'    => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
  ];

  // Struktur Menu Admin
  $groups = [
    ['title' => null, 'roles' => null, 'items' => [
      ['Home',   'admin.dashboard',   'admin.dashboard*', 'home'],
      ['Profil', 'admin.profil','admin.profil*',    'profile'],
    ]],
    ['title' => 'Rekrutmen', 'roles' => null, 'items' => [
      ['Kelola Lowongan', 'admin.lowongan.index', 'admin.lowongan*', 'briefcase'],
      ['Daftar Pelamar',  'admin.pelamar.index',  'admin.pelamar*',  'users'],
      ['Buat Surat LoA',  'admin.surat.create',   'admin.surat*',    'document'],
    ]],
    ['title' => 'Pengaturan & Sistem', 'roles' => null, 'items' => [
      ['Konten Perusahaan', 'admin.konten.index', 'admin.konten*', 'globe'],
      ['Log Aktivitas',     'admin.log.index',    'admin.log*',    'clock'],
    ]],
  ];

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
          <div class="w-9 h-9 rounded-full bg-orange-500 text-white font-bold flex items-center justify-center shrink-0 shadow-md">
            <span>E</span>
          </div>

          <span x-show="expanded"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 -translate-x-2"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-2"
                class="font-bold tracking-tight text-sm whitespace-nowrap">
            ERA<i class="inline-block w-1.5 h-1.5 rounded-full bg-orange-500 ml-0.5"></i>
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
    <nav class="flex-1 min-h-0 mt-2 relative max-lg:overflow-y-auto">

      @foreach($groups as $group)
        @if($show($group['roles']))

          <div class="mb-2">

            @if($group['title'])
              <p x-show="expanded"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 -translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 -translate-x-2"
                 class="px-6 mb-1 text-[10px] tracking-[.2em] text-white/40 font-semibold uppercase">
                {{ $group['title'] }}
              </p>
            @endif

            <ul class="space-y-0">

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
                       class="flex items-center gap-4 pl-5 py-2 rounded-l-full bg-white/15 text-white font-semibold transition-all border-y border-l border-white/10 shadow-inner"
                     @else
                       class="flex items-center gap-4 pl-5 py-2 rounded-l-full text-white/70 hover:text-white hover:bg-white/10 transition-all"
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

    <!-- BOTTOM SECTION: Keluar (Logout) -->
    <div class="shrink-0 border-t border-white/10 pt-1">

      @if($has('logout'))

        <form method="POST" action="{{ route('logout') }}">
          @csrf

          <button type="submit"
                  @click="
                    if (isDesktop && !open) {
                      $event.preventDefault();
                      open = true;
                    }
                  "
                  class="relative group/logout w-full flex items-center gap-4 pl-5 py-2 rounded-l-full text-red-600 hover:bg-red-500/20 transition">

            <div class="shrink-0 flex items-center justify-center w-10 h-10 rounded-full">
              <svg class="w-5 h-5 text-red-600"
                   fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['logout'] }}"/>
              </svg>
            </div>

            <span x-show="expanded"
                  x-transition:enter="transition ease-out duration-500"
                  x-transition:enter-start="opacity-0 -translate-x-2"
                  x-transition:enter-end="opacity-100 translate-x-0"
                  x-transition:leave="transition ease-in duration-300"
                  x-transition:leave-start="opacity-100 translate-x-0"
                  x-transition:leave-end="opacity-0 -translate-x-2"
                  class="text-sm whitespace-nowrap">
              Keluar
            </span>

            <!-- Tooltip Logout -->
            <div x-show="!expanded"
                 class="absolute left-full ml-4 top-1/2 -translate-y-1/2 bg-red-600/80 backdrop-blur-xl text-white text-xs font-semibold px-4 py-2.5 rounded-full opacity-0 translate-x-[-10px] group-hover/logout:opacity-100 group-hover/logout:translate-x-0 transition-all duration-300 pointer-events-none whitespace-nowrap z-[999] shadow-2xl">
              Keluar
            </div>

          </button>
        </form>

      @endif

    </div>

  </aside>
</div>