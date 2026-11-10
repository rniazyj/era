@php
  $str = \Illuminate\Support\Str::class;

  $nama    = $user->name;
  $inisial = mb_strtoupper(mb_substr($nama, 0, 1));
  $fotoUrl = filled($profil->foto) ? asset('storage/' . ltrim($profil->foto, '/')) : null;

  // Halaman "Data Diri & Profil" mengisi section 'editable' -> foto & bio bisa diubah di header
  $editable = $__env->hasSection('editable');

  $terisi = $kelengkapan['terisi'];
  $total  = $kelengkapan['total'];
  $persen = $kelengkapan['persen'];

  // Ring progress (r = 26 -> keliling ≈ 163.36)
  $keliling = 163.36;
  $offset   = $keliling - ($keliling * $persen / 100);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Profil Saya - ERA')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --nav-h: 5rem;
      --bg: #f5f5f7;
      --card: #ffffff;
      --ink: #0f0f11;
      --ink-soft: #3b3b44;
      --muted: #7a7a86;
      --orange: #f97316;
      --orange-dark: #ea580c;
      --orange-soft: #fff4ea;
      --line: rgba(15, 15, 17, 0.08);
      --radius: 22px;
      --shadow: 0 1px 2px rgba(15,15,17,.04), 0 8px 30px rgba(15,15,17,.04);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    body {
      min-height: 100vh;
      background: var(--bg);
      color: var(--ink);
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      font-size: 14px;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
    }

    svg { display: block; }
    .wrap { max-width: 1320px; margin: 0 auto; padding: 0 1.5rem; }

    /* ================= HEADER PROFIL (card rounded) ================= */
    .head-wrap { margin-top: calc(var(--nav-h) + 1.5rem); }

    .profile-head {
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    .banner {
      height: 200px;
      width: 100%;
      background: linear-gradient(115deg, #ffb366, #f97316, #ea580c, #7c2d12, #1a1a1d, #ea580c, #f97316, #ffb366);
      background-size: 400% 400%;
      animation: gradientMove 14s ease infinite;
    }
    @keyframes gradientMove {
      0%   { background-position: 0% 50%; }
      50%  { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .head-inner { display: flex; align-items: flex-start; gap: 1.75rem; padding: 0 1.75rem 1.75rem; }

    .avatar-wrap { position: relative; flex: none; margin-top: -4.5rem; width: 9.5rem; height: 9.5rem; }
    .avatar {
      width: 100%; height: 100%; border-radius: 50%;
      background: var(--orange); color: #fff;
      display: grid; place-items: center;
      font-size: 3.6rem; font-weight: 800;
      object-fit: cover;
      border: 5px solid #fff;
      box-shadow: 0 10px 30px rgba(15,15,17,.18);
    }
    .avatar-btn {
      position: absolute; right: 6px; bottom: 8px;
      width: 36px; height: 36px; border-radius: 50%;
      background: var(--ink); color: #fff; border: 3px solid #fff;
      display: grid; place-items: center; cursor: pointer;
      transition: background .2s, transform .2s;
    }
    .avatar-btn:hover { background: var(--orange); transform: scale(1.08); }
    .avatar-btn svg { width: 16px; height: 16px; }
    .avatar-btn input { display: none; }

    .hero-info { flex: 1; min-width: 0; padding-top: 1.1rem; }
    .hero-info h1 { font-size: clamp(1.5rem, 2.6vw, 2.1rem); font-weight: 800; letter-spacing: -.03em; line-height: 1.2; }
    .badges { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem 1rem; margin: .5rem 0 .8rem; }
    .badge { display: inline-flex; align-items: center; gap: .35rem; font-size: .78rem; font-weight: 600; color: var(--muted); }
    .badge svg { width: 14px; height: 14px; }

    .bio-input {
      width: 100%; max-width: 46rem; resize: vertical; min-height: 78px;
      border: 1.5px dashed transparent; background: transparent;
      border-radius: 14px; padding: .55rem .75rem; margin-left: -.75rem;
      font: inherit; font-size: .93rem; color: var(--ink-soft); line-height: 1.6;
      transition: border-color .2s, background .2s;
    }
    .bio-input:hover { border-color: #e2e2e8; }
    .bio-input:focus { outline: none; border-color: var(--orange); background: #fffaf5; border-style: solid; }
    .bio-text { max-width: 46rem; font-size: .93rem; color: var(--ink-soft); line-height: 1.6; white-space: pre-line; }
    .bio-text.kosong { color: #a9a9b4; }
    .bio-text a { color: var(--orange-dark); font-weight: 700; text-decoration: none; }

    /* ================= LAYOUT BAWAH ================= */
    .layout { display: grid; grid-template-columns: 300px minmax(0, 1fr); gap: 1.75rem; padding-top: 1.5rem; padding-bottom: 5rem; align-items: start; }

    /* ================= SIDEBAR ================= */
    .sidebar { position: sticky; top: calc(var(--nav-h) + 1rem); display: flex; flex-direction: column; gap: 1rem; }
    .side-card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow); padding: 1.1rem; }

    .side-user { display: flex; align-items: center; gap: .9rem; padding: .35rem; }
    .ring { position: relative; width: 62px; height: 62px; flex: none; }
    .ring svg { transform: rotate(-90deg); }
    .ring .track { stroke: #f0f0f3; }
    .ring .fill  { stroke: var(--orange); stroke-linecap: round; transition: stroke-dashoffset .8s ease; }
    .ring .pct { position: absolute; inset: 0; display: grid; place-items: center; font-weight: 800; font-size: .78rem; color: var(--ink); }
    .side-user h4 { font-size: .95rem; font-weight: 800; letter-spacing: -.01em; }
    .side-user p  { font-size: .75rem; color: var(--muted); }

    .nav-label { font-size: .65rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--muted); padding: .9rem .8rem .4rem; }
    .nav-label:first-child { padding-top: .2rem; }

    .nav-item {
      position: relative; display: flex; align-items: center; gap: .8rem;
      padding: .72rem .8rem; border-radius: 14px; color: var(--ink-soft);
      text-decoration: none; font-weight: 600; font-size: .88rem;
      transition: background .2s, color .2s, transform .2s;
    }
    .nav-item .ico { width: 34px; height: 34px; flex: none; border-radius: 11px; display: grid; place-items: center; background: #f4f4f6; color: var(--ink-soft); transition: all .2s; }
    .nav-item .ico svg { width: 18px; height: 18px; }
    .nav-item .tag { margin-left: auto; font-size: .66rem; font-weight: 700; background: var(--orange-soft); color: var(--orange-dark); padding: .15rem .5rem; border-radius: 99px; }
    .nav-item:hover { background: var(--orange-soft); color: var(--orange-dark); transform: translateX(2px); }
    .nav-item:hover .ico { background: #fff; color: var(--orange); }
    .nav-item.active { background: var(--ink); color: #fff; }
    .nav-item.active .ico { background: var(--orange); color: #fff; box-shadow: 0 6px 14px rgba(249,115,22,.4); }
    .nav-item.active .tag { background: rgba(255,255,255,.14); color: #fff; }
    .nav-item.active::before { content: ""; position: absolute; left: -1.1rem; top: 50%; width: 4px; height: 22px; transform: translateY(-50%); background: var(--orange); border-radius: 0 4px 4px 0; }

    .side-tip { background: linear-gradient(145deg, #17171a, #0a0a0b); color: #fff; border: none; position: relative; overflow: hidden; }
    .side-tip::after { content: ""; position: absolute; right: -40px; bottom: -40px; width: 140px; height: 140px; background: radial-gradient(circle, rgba(249,115,22,.55), transparent 70%); }
    .side-tip h5 { font-size: .9rem; font-weight: 800; margin-bottom: .25rem; position: relative; z-index: 1; }
    .side-tip p  { font-size: .76rem; color: #b9b9c3; position: relative; z-index: 1; }
    .side-tip a  { display: inline-block; margin-top: .75rem; position: relative; z-index: 1; background: var(--orange); color: #fff; text-decoration: none; font-weight: 700; font-size: .76rem; padding: .45rem .9rem; border-radius: 99px; }

    /* ================= KONTEN ================= */
    .main { min-width: 0; display: flex; flex-direction: column; gap: 1.5rem; }

    .card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow); padding: 1.75rem; scroll-margin-top: calc(var(--nav-h) + 1rem); }
    .card-head { display: flex; align-items: center; gap: .8rem; margin-bottom: 1.4rem; }
    .card-head .grow { flex: 1; min-width: 0; }
    .card-head .ico { width: 38px; height: 38px; flex: none; border-radius: 12px; display: grid; place-items: center; background: var(--orange-soft); color: var(--orange); }
    .card-head .ico svg { width: 19px; height: 19px; }
    .card-head h3 { font-size: 1rem; font-weight: 800; letter-spacing: -.01em; }
    .card-head p  { font-size: .76rem; color: var(--muted); }

    .fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem 1.1rem; }
    .field { display: flex; flex-direction: column; gap: .4rem; }
    .field.full { grid-column: 1 / -1; }
    .field label { font-size: .74rem; font-weight: 700; color: var(--ink-soft); }
    .field .hint { font-size: .7rem; color: var(--muted); }

    .input, .select, .textarea {
      width: 100%; font: inherit; font-size: .9rem; font-weight: 500; color: var(--ink);
      background: #fafafb; border: 1.5px solid #ececf0; border-radius: 13px; padding: .7rem .9rem;
      transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .textarea { resize: vertical; min-height: 88px; }
    .input::placeholder, .textarea::placeholder { color: #a9a9b4; font-weight: 400; }
    .input:hover, .select:hover, .textarea:hover { border-color: #dcdce2; }
    .input:focus, .select:focus, .textarea:focus { outline: none; background: #fff; border-color: var(--orange); box-shadow: 0 0 0 4px rgba(249,115,22,.14); }
    .is-invalid { border-color: #dc2626 !important; }
    .error { font-size: .72rem; color: #dc2626; font-weight: 600; }

    .input-group { position: relative; }
    .input-group .lead { position: absolute; left: .9rem; top: 50%; transform: translateY(-50%); color: var(--muted); pointer-events: none; }
    .input-group .lead svg { width: 16px; height: 16px; }
    .input-group .input { padding-left: 2.5rem; }

    .secure { display: inline-flex; align-items: center; gap: .35rem; font-size: .7rem; font-weight: 700; color: #15803d; background: #f0fdf4; padding: .15rem .55rem; border-radius: 99px; width: fit-content; }

    .progress-info { display: flex; justify-content: space-between; font-size: .85rem; font-weight: 700; margin-bottom: .5rem; }
    .bar { height: 10px; border-radius: 99px; background: #f1f1f4; overflow: hidden; }
    .bar i { display: block; height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--orange), #ffa24d); transition: width .8s ease; }
    .cv-note { margin-top: 1rem; font-size: .85rem; color: var(--muted); }
    .cv-note b { color: var(--ink); }
    .cv-note.done { color: #15803d; font-weight: 600; }

    .alert { border-radius: 16px; padding: .85rem 1.1rem; font-weight: 600; font-size: .85rem; display: flex; align-items: center; gap: .6rem; }
    .alert.block { display: block; }
    .alert.ok   { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .alert.warn { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
    .alert.err  { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .alert ul { margin: .35rem 0 0 1.1rem; font-weight: 500; }

    .btn { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; font: inherit; font-weight: 700; font-size: .85rem; cursor: pointer; padding: .7rem 1.3rem; border-radius: 99px; border: none; text-decoration: none; transition: transform .2s, box-shadow .2s, background .2s; }
    .btn svg { width: 16px; height: 16px; }
    .btn-primary { background: var(--orange); color: #fff; }
    .btn-primary:hover { background: var(--orange-dark); transform: translateY(-1px); }
    .btn-ghost { background: transparent; color: #fff; border: 1.5px solid rgba(255,255,255,.25); }
    .btn-ghost:hover { border-color: #fff; }
    .btn-sm { padding: .42rem .9rem; font-size: .76rem; }
    .btn-outline { background: #fff; color: var(--ink); border: 1.5px solid #e2e2e8; }
    .btn-outline:hover { border-color: var(--orange); color: var(--orange-dark); }
    .btn-danger { background: #fef2f2; color: #b91c1c; border: 1.5px solid #fecaca; }
    .btn-danger:hover { background: #fee2e2; }

    /* ===== daftar entri (akademik, pengalaman, dst) ===== */
    .item { border: 1px solid var(--line); border-radius: 18px; padding: 1.1rem 1.2rem; background: #fff; }
    .item + .item { margin-top: .8rem; }
    .item-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; }
    .item h4 { font-size: .95rem; font-weight: 800; letter-spacing: -.01em; }
    .item .sub  { font-size: .82rem; color: var(--ink-soft); font-weight: 600; }
    .item .meta { font-size: .76rem; color: var(--muted); margin-top: .15rem; }
    .item .desc { font-size: .84rem; color: var(--ink-soft); margin-top: .55rem; white-space: pre-line; }
    .item .desc a { color: var(--orange-dark); }
    .item-actions { display: flex; gap: .4rem; flex: none; }
    .item-actions form { display: inline; }

    .collapse { display: none; margin-top: 1rem; }
    .collapse.open { display: block; }
    .form-box { background: #fafafb; border: 1.5px dashed #e4e4ea; border-radius: 18px; padding: 1.2rem; }
    .form-actions { grid-column: 1 / -1; display: flex; gap: .5rem; justify-content: flex-end; margin-top: .25rem; }
    .check { display: flex; align-items: center; gap: .5rem; font-size: .82rem; font-weight: 600; color: var(--ink-soft); }
    .check input { width: 16px; height: 16px; accent-color: var(--orange); }

    .empty { text-align: center; color: var(--muted); padding: 1rem 0 .25rem; font-size: .86rem; }
    .stack > * + * { margin-top: 1.5rem; }

    .chips { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem; }
    .chip { display: inline-flex; align-items: center; gap: .45rem; background: var(--orange-soft); color: var(--orange-dark); padding: .3rem .4rem .3rem .85rem; border-radius: 99px; font-weight: 700; font-size: .8rem; }
    .chip small { color: var(--muted); font-weight: 600; }
    .chip form { display: inline; }
    .chip button { border: none; background: #fff; color: #b91c1c; width: 22px; height: 22px; border-radius: 50%; cursor: pointer; font-weight: 800; line-height: 1; }
    .chip button:hover { background: #fee2e2; }
    .add-row { display: flex; gap: .6rem; flex-wrap: wrap; }
    .add-row .input, .add-row .select { flex: 1; min-width: 160px; }

    .checklist { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .5rem 1rem; margin-top: 1.2rem; list-style: none; }
    .checklist li { display: flex; align-items: center; gap: .55rem; font-size: .84rem; font-weight: 600; color: var(--muted); }
    .checklist li::before { content: "○"; font-weight: 800; color: #c4c4cc; }
    .checklist li.ya { color: var(--ink); }
    .checklist li.ya::before { content: "✓"; color: #15803d; }
    .preview { width: 100%; height: 820px; border: 1px solid var(--line); border-radius: 18px; background: #f0f0f3; }
    .row-actions { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: 1.4rem; }

    /* bar simpan melayang */
    .savebar { position: fixed; left: 50%; bottom: 1.25rem; z-index: 50; width: min(640px, calc(100% - 2rem)); display: flex; align-items: center; justify-content: space-between; gap: 1rem; background: var(--ink); color: #fff; padding: .7rem .75rem .7rem 1.4rem; border-radius: 99px; box-shadow: 0 16px 40px rgba(0,0,0,.3); transform: translate(-50%, 160%); opacity: 0; pointer-events: none; transition: transform .35s cubic-bezier(.2,.9,.3,1.2), opacity .25s; }
    .savebar.show { transform: translate(-50%, 0); opacity: 1; pointer-events: auto; }
    .savebar span { font-size: .85rem; font-weight: 600; display: flex; align-items: center; gap: .55rem; }
    .savebar span::before { content: ""; width: 8px; height: 8px; border-radius: 50%; background: var(--orange); animation: pulse 1.4s infinite; }
    .savebar .actions { display: flex; gap: .5rem; }
    .savebar .btn { padding: .55rem 1.15rem; }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: .3; } }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 1050px) {
      .layout { grid-template-columns: 1fr; }
      .sidebar { position: static; }
      .side-tip { display: none; }
      .side-nav { display: flex; gap: .4rem; overflow-x: auto; padding: .6rem; scrollbar-width: none; }
      .side-nav::-webkit-scrollbar { display: none; }
      .nav-label { display: none; }
      .nav-item { flex: none; white-space: nowrap; }
      .nav-item .tag { display: none; }
      .nav-item.active::before { display: none; }
    }
    @media (max-width: 720px) {
      .fields, .checklist { grid-template-columns: 1fr; }
      .banner { height: 140px; }
      .wrap { padding: 0 1.1rem; }
      .head-inner { flex-direction: column; gap: .25rem; padding: 0 1.1rem 1.25rem; }
      .avatar-wrap { width: 7.5rem; height: 7.5rem; margin-top: -3.75rem; }
      .hero-info { padding-top: .5rem; width: 100%; }
      .card { padding: 1.25rem; }
      .item-top { flex-direction: column; }
      .preview { height: 520px; }
      .savebar { border-radius: 22px; flex-direction: column; align-items: stretch; text-align: center; padding: .9rem; }
      .savebar span { justify-content: center; }
      .savebar .actions { justify-content: center; }
    }
    @media (prefers-reduced-motion: reduce) { .banner { animation: none; } }
  </style>
</head>
<body>

@include('partials.navbar')

{{-- ================= HEADER PROFIL (card rounded) ================= --}}
<div class="wrap head-wrap">
  <header class="profile-head">
    <div class="banner"></div>

    <div class="head-inner">
     <div class="avatar-wrap">
  {{-- Selalu tampilkan tag img, jika $fotoUrl kosong maka src-nya kosong dan style display disembunyikan otomatis --}}
  <img id="avatarImg" class="avatar" src="{{ $fotoUrl ?? '' }}" alt="Foto {{ $nama }}" @if(!$fotoUrl) style="display: none;" @endif>
  
  {{-- Fallback inisial huruf hanya muncul jika memang belum ada fotoUrl --}}
  <div id="avatarFallback" class="avatar" aria-hidden="true" @if($fotoUrl) style="display: none;" @endif>{{ $inisial }}</div>

  @if($editable)
    <label class="avatar-btn" title="Ganti foto">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2Z"/><circle cx="12" cy="13" r="4"/></svg>
      <input type="file" name="foto" id="fotoInput" form="formProfil" accept="image/png,image/jpeg">
    </label>
  @else
    <a class="avatar-btn" href="{{ route('pelamar.profil') }}" title="Ubah foto di Data Diri &amp; Profil">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2Z"/><circle cx="12" cy="13" r="4"/></svg>
    </a>
  @endif
</div>

      <div class="hero-info">
        <h1 id="heroName">{{ $nama }}</h1>
        <div class="badges">
          <span class="badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>
            {{ $user->email }}
          </span>
          @if(filled($user->no_telepon))
            <span class="badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/></svg>
              {{ $user->no_telepon }}
            </span>
          @endif
          <span class="badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ filled($profil->kota) ? $profil->kota : 'Kota belum diatur' }}
          </span>
        </div>

        @if($editable)
          <textarea class="bio-input" name="bio" form="formProfil" maxlength="300" rows="3"
            placeholder="Klik di sini untuk menulis deskripsi singkat tentang dirimu...">{{ old('bio', $cv?->ringkasan_profil) }}</textarea>
          @error('bio')  <div class="error">{{ $message }}</div> @enderror
          @error('foto') <div class="error">{{ $message }}</div> @enderror
        @elseif(filled($cv?->ringkasan_profil))
          <p class="bio-text">{{ $cv->ringkasan_profil }}</p>
        @else
          <p class="bio-text kosong">Belum ada deskripsi diri. <a href="{{ route('pelamar.profil') }}">Tulis sekarang</a></p>
        @endif
      </div>
    </div>
  </header>
</div>

{{-- ================= SIDEBAR + KONTEN ================= --}}
<div class="wrap layout">

  <aside class="sidebar">

    <div class="side-card">
      <div class="side-user">
        <div class="ring" title="Kelengkapan profil / progres CV">
          <svg width="62" height="62" viewBox="0 0 62 62">
            <circle class="track" cx="31" cy="31" r="26" fill="none" stroke-width="6"/>
            <circle class="fill" cx="31" cy="31" r="26" fill="none" stroke-width="6"
                    stroke-dasharray="{{ $keliling }}" stroke-dashoffset="{{ $offset }}"/>
          </svg>
          <div class="pct">{{ $persen }}%</div>
        </div>
        <div style="min-width:0">
          <h4>{{ $str::limit($nama, 18) }}</h4>
          <p>Profil terisi {{ $terisi }}/{{ $total }} data</p>
        </div>
      </div>
    </div>

    <div class="side-card side-nav">
      <div class="nav-label">Menu Utama</div>

      <a href="{{ route('pelamar.profil') }}" class="nav-item {{ request()->routeIs('pelamar.profil', 'pelamar.profil.update') ? 'active' : '' }}">
        <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        Data Diri &amp; Profil
      </a>
      <a href="{{ route('pelamar.profil.akademik') }}" class="nav-item {{ request()->routeIs('pelamar.profil.akademik*') ? 'active' : '' }}">
        <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c3 2 9 2 12 0v-5"/></svg></span>
        Data Akademik
      </a>
      <a href="{{ route('pelamar.profil.pengalaman') }}" class="nav-item {{ request()->routeIs('pelamar.profil.pengalaman*') ? 'active' : '' }}">
        <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
        Pengalaman
      </a>
      <a href="{{ route('pelamar.profil.skill') }}" class="nav-item {{ request()->routeIs('pelamar.profil.skill*') ? 'active' : '' }}">
        <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8Z"/></svg></span>
        Skill &amp; Keahlian
      </a>

      <div class="nav-label">Dokumen</div>
      <a href="{{ route('pelamar.profil.cv') }}" class="nav-item {{ request()->routeIs('pelamar.profil.cv*') ? 'active' : '' }}">
        <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h5"/></svg></span>
        Curriculum Vitae
        <span class="tag">{{ $persen }}%</span>
      </a>

      <div class="nav-label">Sistem</div>
      <a href="#" class="nav-item">
        <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h0a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h0a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v0a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg></span>
        Pengaturan Akun
      </a>
    </div>

    @if($persen < 100)
      <div class="side-card side-tip">
        <h5>Profil lengkap, peluang lebih besar</h5>
        <p>
          @if(count($kelengkapan['kurang']))
            Belum diisi: {{ $str::limit(implode(', ', $kelengkapan['kurang']), 70) }}
          @else
            Rekruter lebih sering menghubungi pelamar dengan data yang terisi penuh.
          @endif
        </p>
        <a href="{{ route('pelamar.profil.cv') }}">Cek kelengkapan CV</a>
      </div>
    @endif
  </aside>

  <main class="main">

    @if (session('success'))
      <div class="alert ok">✓ {{ session('success') }}</div>
    @endif
    @if (session('warning'))
      <div class="alert warn">⚠ {{ session('warning') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert err block">
        Ada isian yang perlu diperbaiki:
        <ul>
          @foreach ($errors->all() as $pesan)
            <li>{{ $pesan }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @yield('konten')

  </main>
</div>

<script>
  // Tombol buka/tutup form (Tambah / Edit / Batal): data-toggle="id-elemen"
  document.addEventListener('click', function (e) {
    const t = e.target.closest('[data-toggle]');
    if (!t) return;
    const el = document.getElementById(t.dataset.toggle);
    if (el) el.classList.toggle('open');
  });
</script>
@stack('scripts')

</body>
</html>