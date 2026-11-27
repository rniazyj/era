<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard - Galeri Magang')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  {{-- Jika project memakai Vite + Tailwind, ganti baris CDN di bawah dengan @vite(['resources/css/app.css','resources/js/app.js']) --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
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

      <!-- Konten -->
      <main class="flex-1 px-4 md:px-8 pt-6 pb-8">
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
        <span>&copy; {{ date('Y') }} Galeri Magang Platform. All rights reserved.</span>
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