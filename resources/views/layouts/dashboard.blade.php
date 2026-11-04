<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard - ERA')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  {{-- Jika project memakai Vite + Tailwind, ganti baris CDN di bawah dengan @vite(['resources/css/app.css','resources/js/app.js']) --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter','system-ui','sans-serif'] }, colors: { paper: '#faf9fc' } } } };
  </script>
  @stack('styles')
</head>
<body class="bg-paper text-zinc-900 font-sans antialiased">
  @php
    $user = auth()->user();
    $has  = fn ($name) => \Illuminate\Support\Facades\Route::has($name);
  @endphp

  <div class="min-h-screen lg:flex">

    @include('partials.sidebar')

    <div class="flex-1 min-w-0 flex flex-col">

      <!-- Top bar -->
      <header class="sticky top-0 z-30 h-16 px-4 md:px-8 flex items-center justify-between gap-4 backdrop-blur-md bg-[#faf9fc]/80 border-b border-black/5">
        <div class="flex items-center gap-3 min-w-0">
          <button type="button" id="sidebar-open" class="lg:hidden w-9 h-9 flex items-center justify-center rounded-full border border-black/15" aria-label="Buka menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h10"/></svg>
          </button>
          <div class="min-w-0">
            <p class="text-[10px] tracking-[.18em] text-black/40 leading-none">@yield('page_label', 'PORTAL ERA')</p>
            <h1 class="text-base md:text-lg font-semibold tracking-tight truncate">@yield('page_title', 'Dashboard')</h1>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <a href="{{ url('/') }}" class="hidden sm:inline text-xs font-medium text-black/50 hover:text-orange-600 transition">Lihat situs</a>

          @if($user)
            <div class="relative group">
              <button type="button" class="flex items-center gap-2.5 rounded-full border border-black/10 bg-white pl-1.5 pr-3.5 py-1.5 hover:border-black/30 transition" aria-haspopup="true">
                <span class="w-7 h-7 rounded-full bg-orange-500 text-white text-xs font-semibold flex items-center justify-center">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                <span class="hidden sm:block text-xs font-medium max-w-[8rem] truncate">{{ $user->name }}</span>
                <span class="text-[10px] text-black/40">▾</span>
              </button>
              <div class="absolute right-0 top-full pt-2 hidden group-hover:block group-focus-within:block">
                <div class="w-48 bg-white rounded-2xl shadow-xl border border-black/5 p-1.5 text-sm">
                  <a href="{{ $has('profile.edit') ? route('profile.edit') : '#' }}" class="block px-3 py-2 rounded-xl hover:bg-black/5">Profil saya</a>
                  @if($has('logout'))
                    <form method="POST" action="{{ route('logout') }}">@csrf
                      <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-red-600 hover:bg-red-50">Keluar</button>
                    </form>
                  @endif
                </div>
              </div>
            </div>
          @endif
        </div>
      </header>

      <!-- Konten -->
      <main class="flex-1 p-4 md:p-8">
        @if(session('success') || session('status') || session('error'))
          <div class="mb-6 space-y-3">
            @if(session('success') || session('status'))
              <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                <span class="mt-1 w-2 h-2 rounded-full bg-green-500 shrink-0"></span><span>{{ session('success') ?? session('status') }}</span>
              </div>
            @endif
            @if(session('error'))
              <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
                <span class="mt-1 w-2 h-2 rounded-full bg-red-500 shrink-0"></span><span>{{ session('error') }}</span>
              </div>
            @endif
          </div>
        @endif

        @yield('content')
      </main>

      <footer class="px-4 md:px-8 py-5 border-t border-black/5 text-[11px] text-black/40 flex flex-wrap items-center justify-between gap-2">
        <span>&copy; {{ date('Y') }} ERA Platform. All rights reserved.</span>
        <span>Equityworld Recruitment Access</span>
      </footer>
    </div>
  </div>

  <script>
    /* Sidebar mobile: buka / tutup */
    (function () {
      var side = document.getElementById('sidebar'), overlay = document.getElementById('sidebar-overlay');
      var openBtn = document.getElementById('sidebar-open'), closeBtn = document.getElementById('sidebar-close');
      if (!side) return;
      function open()  { side.classList.remove('-translate-x-full'); overlay.classList.remove('opacity-0', 'pointer-events-none'); document.body.classList.add('overflow-hidden'); }
      function close() { side.classList.add('-translate-x-full'); overlay.classList.add('opacity-0', 'pointer-events-none'); document.body.classList.remove('overflow-hidden'); }
      if (openBtn) openBtn.addEventListener('click', open);
      if (closeBtn) closeBtn.addEventListener('click', close);
      overlay.addEventListener('click', close);
      document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    })();
  </script>
  @stack('scripts')
</body>
</html>