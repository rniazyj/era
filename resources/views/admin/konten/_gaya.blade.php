{{-- resources/views/admin/konten/_gaya.blade.php
     Gaya halaman Konten Website. Semua kelas berawalan "kw-" supaya tidak bentrok dengan layout dashboard. --}}
<style>
  .kw, .kw * { box-sizing: border-box; }
  .kw { color: #141414; max-width: 78rem; }
  .kw a { text-decoration: none; }
  .kw h1, .kw h2, .kw h3, .kw p, .kw ul, .kw ol { margin: 0; padding: 0; }
  .kw ul, .kw ol { list-style: none; }

  /* ---------- header ---------- */
  .kw-crumb { display: flex; flex-wrap: wrap; align-items: center; gap: .4rem; margin-bottom: .6rem; font-size: .78rem; color: #71717a; }
  .kw-crumb a { color: #71717a; }
  .kw-crumb a:hover { color: #c2570c; }
  .kw-crumb b { color: #141414; font-weight: 600; }
  .kw-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; }
  .kw-head h1 { font-size: 1.6rem; font-weight: 700; letter-spacing: -.02em; }
  .kw-head p { margin-top: .3rem; font-size: .88rem; color: #71717a; max-width: 38rem; }

  /* ---------- tata letak: menu kiri + isi kanan ---------- */
  .kw-layout { display: grid; grid-template-columns: 16.5rem minmax(0, 1fr); gap: 1.5rem; align-items: start; }
  .kw-side { position: sticky; top: 1rem; display: grid; gap: .75rem; }
  .kw-home { display: flex; align-items: center; gap: .5rem; padding: .65rem .9rem; border: 1px solid rgba(20,20,20,.1); border-radius: .85rem; background: #fff; font-size: .82rem; font-weight: 600; color: #3f3f46; }
  .kw-home:hover { border-color: #f97316; color: #c2570c; }

  .kw-grp { border: 1px solid rgba(20,20,20,.1); border-radius: .95rem; background: #fff; overflow: hidden; }
  .kw-grp > summary { display: flex; align-items: center; justify-content: space-between; gap: .5rem; padding: .8rem .95rem; font-size: .84rem; font-weight: 700; cursor: pointer; list-style: none; }
  .kw-grp > summary::-webkit-details-marker { display: none; }
  .kw-grp > summary::after { content: "▾"; font-size: .8rem; color: #a1a1aa; transition: transform .2s; }
  .kw-grp:not([open]) > summary::after { transform: rotate(-90deg); }
  .kw-grp[open] > summary { background: #fff1e4; color: #c2570c; }
  .kw-grp ul { padding: .35rem; }
  .kw-grp li a { display: flex; align-items: center; gap: .6rem; padding: .5rem .6rem; border-radius: .6rem; font-size: .82rem; font-weight: 500; color: #3f3f46; }
  .kw-grp li a i { flex: none; display: grid; place-items: center; width: 1.4rem; height: 1.4rem; border-radius: 50%; background: #f6f5f3; font-size: .66rem; font-style: normal; font-weight: 700; color: #71717a; }
  .kw-grp li a:hover { background: #f6f5f3; }
  .kw-grp li a.on { background: #141414; color: #fff; font-weight: 600; }
  .kw-grp li a.on i { background: #f97316; color: #141414; }
  .kw-grp li a .dot { margin-left: auto; width: .5rem; height: .5rem; border-radius: 50%; background: #d4d4d8; }
  .kw-grp li a .dot.ok { background: #22c55e; }

  /* ---------- keterangan "tampil di mana" ---------- */
  .kw-where { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .8rem; margin-bottom: 1rem; padding: .85rem 1.1rem; border-radius: .95rem; background: #fff1e4; font-size: .85rem; color: #7c3a0a; }
  .kw-where strong { color: #141414; }

  /* ---------- pesan ---------- */
  .kw-flash { display: flex; gap: .6rem; align-items: flex-start; margin-bottom: 1rem; padding: .75rem 1rem; border-radius: .8rem; font-size: .85rem; }
  .kw-flash.ok  { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
  .kw-flash.err { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
  .kw-flash ul { padding-left: 1.1rem; list-style: disc; }

  /* ---------- kartu & tabel ---------- */
  .kw-card { border: 1px solid rgba(20,20,20,.1); border-radius: 1rem; background: #fff; overflow: hidden; }
  .kw-card + .kw-card { margin-top: 1rem; }
  .kw-card-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.1rem 1.25rem; border-bottom: 1px solid rgba(20,20,20,.08); }
  .kw-card-head h2 { font-size: 1.05rem; font-weight: 700; }
  .kw-card-head p { margin-top: .2rem; font-size: .8rem; color: #71717a; }
  .kw-table-wrap { overflow-x: auto; }
  .kw-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
  .kw-table th { padding: .7rem 1rem; background: #f6f5f3; font-size: .66rem; font-weight: 700; letter-spacing: .08em; text-align: left; text-transform: uppercase; color: #71717a; white-space: nowrap; }
  .kw-table td { padding: .8rem 1rem; border-top: 1px solid rgba(20,20,20,.07); vertical-align: middle; }
  .kw-table tr:hover td { background: #fcfcfb; }
  .kw-table .utama { font-weight: 600; }
  .kw-table .sub { display: block; max-width: 26rem; margin-top: .1rem; overflow: hidden; font-size: .78rem; color: #71717a; text-overflow: ellipsis; white-space: nowrap; }
  .kw-aksi { display: flex; gap: .4rem; justify-content: flex-end; }
  .kw-empty { padding: 3rem 1rem; text-align: center; font-size: .9rem; color: #71717a; }
  .kw-empty strong { display: block; margin-bottom: .3rem; color: #141414; font-size: 1rem; }

  .kw-thumb { display: grid; place-items: center; width: 3.2rem; height: 3.2rem; border-radius: .7rem; background: #ecebe8; font-size: .75rem; font-weight: 700; color: #a1a1aa; object-fit: cover; }
  .kw-thumb.logo { object-fit: contain; background: #fff; border: 1px solid rgba(20,20,20,.1); padding: .25rem; }
  .kw-badge { display: inline-block; padding: .15rem .6rem; border-radius: 99px; background: #f6f5f3; font-size: .7rem; font-weight: 600; color: #3f3f46; white-space: nowrap; }
  .kw-badge.ok { background: #f0fdf4; color: #15803d; }
  .kw-badge.off { background: #f4f4f5; color: #71717a; }
  .kw-badge.or { background: #fff1e4; color: #c2570c; }

  /* ---------- tombol ---------- */
  .kw-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; padding: .55rem 1.05rem; border: 1px solid transparent; border-radius: 99px; background: #f97316; font: inherit; font-size: .8rem; font-weight: 600; color: #141414; cursor: pointer; transition: background .2s, transform .35s cubic-bezier(.34,1.56,.64,1), box-shadow .25s; }
  .kw-btn:hover { background: #ea580c; transform: scale(.97); box-shadow: 0 8px 18px -8px rgba(234,88,12,.7); }
  .kw-btn:active { transform: scale(.93); }
  .kw-btn.ghost { background: #fff; border-color: rgba(20,20,20,.15); }
  .kw-btn.ghost:hover { background: #fff; border-color: #141414; box-shadow: 0 8px 18px -8px rgba(20,20,20,.4); }
  .kw-btn.dark { background: #141414; color: #fff; }
  .kw-btn.dark:hover { background: #000; }
  .kw-btn.danger { background: #fff; border-color: #fecaca; color: #b91c1c; }
  .kw-btn.danger:hover { background: #fef2f2; border-color: #b91c1c; box-shadow: none; }
  .kw-btn.sm { padding: .35rem .8rem; font-size: .75rem; }

  /* ---------- halaman depan admin ---------- */
  .kw-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem; margin-bottom: 1.25rem; }
  .kw-step { display: flex; gap: .75rem; align-items: flex-start; padding: .95rem 1.05rem; border: 1px solid rgba(20,20,20,.1); border-radius: 1rem; background: #fff; }
  .kw-step > b { flex: none; display: grid; place-items: center; width: 1.9rem; height: 1.9rem; border-radius: 50%; background: #f97316; font-size: .8rem; }
  .kw-step strong { display: block; font-size: .88rem; }
  .kw-step span { font-size: .78rem; color: #71717a; }

  .kw-pages { display: grid; gap: 1rem; }
  .kw-pcard { border: 1px solid rgba(20,20,20,.1); border-radius: 1.1rem; background: #fff; overflow: hidden; }
  .kw-pcard-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.15rem 1.3rem; background: #141414; color: #fff; }
  .kw-pcard-head h2 { font-size: 1.1rem; font-weight: 700; }
  .kw-pcard-head p { margin-top: .2rem; font-size: .8rem; color: rgba(255,255,255,.65); }
  .kw-pcard-head code { display: inline-block; margin-top: .45rem; padding: .1rem .55rem; border-radius: 99px; background: rgba(255,255,255,.12); font-size: .72rem; color: #fdba74; }
  .kw-pcard-head .kw-btn.ghost { background: transparent; border-color: rgba(255,255,255,.3); color: #fff; }
  .kw-pcard-head .kw-btn.ghost:hover { border-color: #fff; box-shadow: none; }
  .kw-list li + li { border-top: 1px solid rgba(20,20,20,.07); }
  .kw-item { display: flex; align-items: center; gap: .9rem; padding: .9rem 1.3rem; color: inherit; transition: background .2s; }
  .kw-item:hover { background: #fff7ed; }
  .kw-no { flex: none; display: grid; place-items: center; width: 1.9rem; height: 1.9rem; border-radius: 50%; background: #f6f5f3; font-size: .75rem; font-weight: 700; color: #71717a; }
  .kw-item:hover .kw-no { background: #f97316; color: #141414; }
  .kw-item-t { flex: 1; min-width: 0; }
  .kw-item-t strong { display: block; font-size: .92rem; }
  .kw-item-t small { display: block; overflow: hidden; font-size: .78rem; color: #71717a; text-overflow: ellipsis; white-space: nowrap; }
  .kw-edit { flex: none; font-size: .78rem; font-weight: 700; color: #c2570c; }

  /* ---------- formulir ---------- */
  .kw-sec { padding: 1.25rem; }
  .kw-sec + .kw-sec { border-top: 1px solid rgba(20,20,20,.08); }
  .kw-sec-t { display: flex; align-items: center; gap: .6rem; margin-bottom: 1rem; font-size: .72rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #c2570c; }
  .kw-sec-t::after { content: ""; flex: 1; height: 1px; background: rgba(20,20,20,.08); }
  .kw-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.1rem 1.25rem; }
  .kw-field { display: flex; flex-direction: column; gap: .35rem; min-width: 0; }
  .kw-field.full { grid-column: 1 / -1; }
  .kw-field > label { font-size: .82rem; font-weight: 600; }
  .kw-field > label b { color: #dc2626; }
  .kw-field input[type=text], .kw-field input[type=url], .kw-field input[type=number], .kw-field textarea {
    width: 100%; padding: .65rem .85rem; border: 1px solid rgba(20,20,20,.18); border-radius: .7rem; background: #fff; font: inherit; font-size: .88rem; color: #141414; transition: border-color .2s, box-shadow .2s;
  }
  .kw-field textarea { min-height: 7rem; resize: vertical; line-height: 1.55; }
  .kw-field input:focus, .kw-field textarea:focus { outline: none; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.18); }
  .kw-field.bad input, .kw-field.bad textarea { border-color: #dc2626; }
  .kw-hint { display: flex; justify-content: space-between; gap: .6rem; font-size: .75rem; color: #71717a; }
  .kw-count { flex: none; font-variant-numeric: tabular-nums; }
  .kw-err { font-size: .76rem; font-weight: 600; color: #dc2626; }
  .kw-check { display: inline-flex; align-items: center; gap: .55rem; font-size: .85rem; font-weight: 600; cursor: pointer; }
  .kw-check input { width: 1.05rem; height: 1.05rem; accent-color: #f97316; }
  .kw-switch { display: flex; gap: .7rem; align-items: flex-start; padding: .85rem 1rem; border: 1px solid rgba(20,20,20,.12); border-radius: .85rem; cursor: pointer; }
  .kw-switch input { margin-top: .15rem; width: 1.1rem; height: 1.1rem; accent-color: #f97316; }
  .kw-switch strong { display: block; font-size: .85rem; }
  .kw-switch span { font-size: .76rem; color: #71717a; }

  /* unggah foto */
  .kw-foto { display: flex; flex-wrap: wrap; gap: 1.1rem; align-items: center; }
  .kw-foto-prev { display: grid; place-items: center; width: 17rem; max-width: 100%; aspect-ratio: 16 / 10; border: 2px dashed rgba(20,20,20,.18); border-radius: 1rem; background: #fafafa; overflow: hidden; font-size: .78rem; color: #a1a1aa; }
  .kw-foto-prev img { width: 100%; height: 100%; object-fit: cover; }
  .kw-foto.logo .kw-foto-prev { width: 9rem; aspect-ratio: 1; }
  .kw-foto.logo .kw-foto-prev img { object-fit: contain; padding: .8rem; background: #fff; }
  .kw-foto-ctl { display: grid; gap: .55rem; justify-items: start; }
  .kw-foto-ctl .nama { max-width: 16rem; overflow: hidden; font-size: .76rem; color: #71717a; text-overflow: ellipsis; white-space: nowrap; }

  /* bar simpan menempel di bawah */
  .kw-savebar { position: sticky; bottom: 0; z-index: 5; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .8rem; padding: .9rem 1.25rem; border-top: 1px solid rgba(20,20,20,.1); background: rgba(255,255,255,.94); backdrop-filter: blur(6px); }
  .kw-savebar .info { font-size: .78rem; color: #71717a; }
  .kw-savebar .info.dirty { color: #c2570c; font-weight: 600; }
  .kw-savebar .tombol { display: flex; gap: .6rem; margin-left: auto; }

  @media (max-width: 960px) {
    .kw-layout { grid-template-columns: 1fr; }
    .kw-side { position: static; }
    .kw-steps { grid-template-columns: 1fr; }
  }
  @media (max-width: 720px) { .kw-grid { grid-template-columns: 1fr; } .kw-edit { display: none; } }
</style>