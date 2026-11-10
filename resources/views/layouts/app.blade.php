<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'ERA - Membangun Masa Depan Karier & Talenta')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  {{-- Jika project memakai Vite + Tailwind, ganti baris CDN di bawah dengan @vite(['resources/css/app.css','resources/js/app.js']) --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter','system-ui','sans-serif'] }, colors: { paper: '#faf9fc' } } } };
  </script>

  @stack('styles')
</head>
<body class="bg-paper text-zinc-900 font-sans antialiased min-h-screen flex flex-col overflow-x-hidden" id="top">

  @include('partials.navbar')

  <main class="flex-1 pt-16">
    {{-- Pesan flash --}}
    @if(session('success') || session('status') || session('error'))
      <div class="max-w-7xl mx-auto px-6 pt-6">
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

  @include('partials.footer')

  @stack('scripts')
</body>
</html>