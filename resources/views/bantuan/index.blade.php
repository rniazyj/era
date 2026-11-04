<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pusat Bantuan - ERA</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{ --paper:#faf9fc; --ink:#111; --orange:#f97316; --orange-d:#ea580c; --line:rgba(0,0,0,.12); --field:#f2f2f2; }
    *{ box-sizing:border-box; margin:0; padding:0; }
    body{ min-height:100vh; background:var(--paper); color:#18181b; font-family:'Inter',system-ui,sans-serif; font-size:14px; line-height:1.6; -webkit-font-smoothing:antialiased; }
    ::selection{ background:var(--ink); color:#fff; }
    .wrap{ max-width:52rem; margin:0 auto; padding:7rem 1.5rem 5rem; }
    .eyebrow{ font-size:10px; font-weight:600; letter-spacing:.2em; text-transform:uppercase; }
    .eyebrow .o{ color:var(--orange); }
    h1{ margin-top:.75rem; font-size:clamp(1.9rem,4vw,2.8rem); font-weight:600; letter-spacing:-.03em; line-height:1.1; }
    h1 .dim{ color:rgba(0,0,0,.27); }
    .lead{ margin-top:.9rem; font-size:.85rem; color:rgba(0,0,0,.6); max-width:30rem; }

    .search{ display:flex; gap:.5rem; margin-top:1.75rem; }
    .search input{ flex:1; min-width:0; height:3.25rem; padding:0 1rem; border:1px solid var(--line); border-radius:6px; background:var(--field); font:inherit; font-size:.875rem; color:var(--ink); outline:none; transition:border-color .3s,box-shadow .3s,background .3s; }
    .search input:hover{ border-color:rgba(0,0,0,.3); }
    .search input:focus{ background:#fff; border-color:var(--ink); box-shadow:0 0 0 3px rgba(249,115,22,.18); }
    .search button{ position:relative; height:3.25rem; padding:0 1.5rem; border:0; border-radius:6px; background:var(--ink); color:#fff; font:inherit; font-size:.875rem; font-weight:500; cursor:pointer; overflow:hidden; }
    .search button::before{ content:''; position:absolute; inset:0; background:var(--orange); transform:translateX(-101%); transition:transform .65s cubic-bezier(.16,1,.3,1); }
    .search button:hover::before{ transform:none; }
    .search button span{ position:relative; z-index:1; }

    .info{ margin-top:1rem; font-size:.78rem; color:rgba(0,0,0,.55); }
    .info a{ color:var(--orange-d); }

    .grp{ margin-top:2.5rem; }
    .grp h2{ font-size:10px; font-weight:600; letter-spacing:.2em; text-transform:uppercase; color:var(--orange); padding-bottom:.6rem; border-bottom:1px solid var(--ink); }
    details{ border-bottom:1px solid var(--line); }
    summary{ list-style:none; cursor:pointer; display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1.1rem 0; font-size:.95rem; font-weight:500; transition:color .2s; }
    summary::-webkit-details-marker{ display:none; }
    summary:hover{ color:var(--orange-d); }
    summary svg{ flex:none; width:16px; height:16px; transition:transform .3s; }
    details[open] summary svg{ transform:rotate(180deg); }
    .ans{ padding:0 2rem 1.25rem 0; color:rgba(0,0,0,.65); font-size:.875rem; }

    .empty{ margin-top:3rem; padding:2.5rem 1rem; text-align:center; border:1px dashed var(--line); border-radius:10px; color:rgba(0,0,0,.55); }

    .contact{ margin-top:3.5rem; padding:1.5rem; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; background:#fff; border:1px solid var(--line); border-radius:10px; }
    .contact b{ display:block; font-size:.95rem; font-weight:600; }
    .contact span{ font-size:.8rem; color:rgba(0,0,0,.55); }
    .contact a{ padding:.6rem 1.25rem; border-radius:6px; background:var(--ink); color:#fff; font-size:.8rem; font-weight:500; text-decoration:none; transition:background .3s; }
    .contact a:hover{ background:var(--orange); }
  </style>
</head>
<body>

@include('partials.navbar')

<main class="wrap">
  <p class="eyebrow">PUSAT <span class="o">BANTUAN</span></p>
  <h1>Ada yang bisa kami bantu?<br><span class="dim">Cari jawabannya di sini.</span></h1>
  <p class="lead">Pertanyaan yang paling sering diajukan seputar akun, lamaran, dan program magang.</p>

  <form class="search" action="{{ route('bantuan') }}" method="GET" role="search">
    <input type="search" name="q" value="{{ $q }}" placeholder="Cari pertanyaan, misalnya: lupa password" aria-label="Cari bantuan">
    <button type="submit"><span>Cari</span></button>
  </form>

  @if($q !== '')
    <p class="info">
      Hasil untuk <b>“{{ $q }}”</b> ({{ $grup->flatten()->count() }} ditemukan).
      <a href="{{ route('bantuan') }}">Hapus pencarian</a>
    </p>
  @endif

  @forelse($grup as $kategori => $items)
    <section class="grp">
      <h2>{{ $kategori }}</h2>
      @foreach($items as $item)
        <details>
          <summary>
            {{ $item->pertanyaan }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 9l7 7 7-7"/></svg>
          </summary>
          <div class="ans">{!! nl2br(e($item->jawaban)) !!}</div>
        </details>
      @endforeach
    </section>
  @empty
    <div class="empty">
      @if($q !== '')
        Tidak ada hasil untuk “{{ $q }}”. Coba kata kunci lain.
      @else
        Belum ada pertanyaan yang ditampilkan.
      @endif
    </div>
  @endforelse

  @if($email)
    <div class="contact">
      <div>
        <b>Belum menemukan jawabannya?</b>
        <span>Tim kami siap membantu lewat e-mail.</span>
      </div>
      <a href="mailto:{{ $email }}">Hubungi kami</a>
    </div>
  @endif
</main>

</body>
</html>