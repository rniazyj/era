{{-- resources/views/pages/lowongan/_head.blade.php  |  dipakai show & apply: @include('pages.lowongan._head', ['title' => '...']) --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }} - PT Equityworld Futures</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: { extend: {
      fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'], head: ['Outfit', '"Plus Jakarta Sans"', 'sans-serif'] },
      colors: { ink: '#141414', sand: '#f6f5f3', ewforange: '#f97316' }
    } }
  };
</script>
<style>
  :root { --spring: cubic-bezier(.34, 1.56, .64, 1); }
  .press { --sh: 234, 88, 12; transition: background .2s, color .2s, border-color .2s, box-shadow .25s, transform .45s var(--spring); -webkit-tap-highlight-color: transparent; }
  .press.dk { --sh: 20, 20, 20; }
  .press:hover { transform: scale(.97); box-shadow: 0 10px 22px -8px rgba(var(--sh), .55); }
  .press:active { transform: scale(.92); transition-duration: .1s; }
  .press:focus-visible { outline: 2px solid #141414; outline-offset: 3px; }
  .field-file::file-selector-button { margin-right: .75rem; border: 0; border-radius: 99px; background: #141414; color: #fff; padding: .45rem 1rem; font-weight: 600; font-size: .75rem; cursor: pointer; }
  @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } }
</style>