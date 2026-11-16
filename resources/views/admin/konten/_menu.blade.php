{{--
  resources/views/admin/konten/_menu.blade.php  (menggantikan _tab.blade.php; file _tab boleh dihapus)
  Menu kiri: 3 halaman situs -> bagian-bagiannya, urut seperti tampil di halaman.
  Butuh variabel: $tipe, $halamanKini
--}}
@php $menu = \App\Models\ProfilPerusahaan::navigasi(); @endphp

<aside class="kw-side" aria-label="Menu konten website">
  <a class="kw-home" href="{{ route('admin.konten.index') }}">← Semua halaman</a>

  @foreach($menu as $kode => $h)
    <details class="kw-grp" @if($kode === $halamanKini) open @endif>
      <summary>{{ $h['nama'] }}</summary>
      <ul>
        @foreach($h['bagian'] as $b)
          @php $aktif = $b['tipe'] === $tipe && ($tipe !== 'hero' || $kode === $halamanKini); @endphp
          <li>
            <a href="{{ $b['url'] }}" class="{{ $aktif ? 'on' : '' }}" @if($aktif) aria-current="page" @endif>
              <i>{{ $b['nomor'] }}</i>{{ $b['nama'] }}
              <em class="dot {{ $b['ok'] ? 'ok' : '' }}" title="{{ $b['status'] }}"></em>
            </a>
          </li>
        @endforeach
      </ul>
    </details>
  @endforeach
</aside>