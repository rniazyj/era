@php
  $has  = fn ($name) => \Illuminate\Support\Facades\Route::has($name);
  $link = fn ($name, $fallback = '#') => $has($name) ? route($name) : $fallback;

  // Sesuaikan nama route di sini
  $columns = [
    'Navigasi Cepat' => [
      ['Tentang KarierHub', $link('about')],
      ['Pusat Akun Kandidat', $link('dashboard')],
      ['Panduan Seleksi & Asesmen', '#'],
      ['Kebijakan Privasi', '#'],
    ],
    'Peluang Karir' => [
      ['Lowongan Kerja Profesional', $link('job')],
      ['Program Magang Bersertifikat', $link('internship')],
      ['Eksplorasi Perusahaan Mitra', '#'],
      ['Jalur Fresh Graduate', '#'],
    ],
    'Hubungi Kami' => [
      ['karir@karierhub.co.id', 'mailto:karir@karierhub.co.id'],
      ['+62 (021) 5088-7200', 'tel:+622150887200'],
      ['Helpdesk Talenta', '#'],
      ['Kirim Email', 'mailto:karir@karierhub.co.id'],
    ],
  ];
@endphp

<footer class="bg-[#161616] text-white">
  <div class="max-w-7xl mx-auto px-6 md:px-10 py-10 md:py-12 grid md:grid-cols-[1.1fr_2fr] gap-10 md:gap-14">

    <!-- Brand -->
    <div class="flex flex-col justify-between gap-6">
      <div>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xl font-semibold tracking-tight">ERA<i class="w-1.5 h-1.5 rounded-full bg-orange-500"></i></a>
        <p class="mt-3 text-xs text-white/45 leading-relaxed max-w-xs">Portal karir korporat resmi dan inkubasi talenta magang nasional. Menghubungkan potensi terbaik Indonesia dengan peluang industri terpercaya.</p>
      </div>
      <p class="text-sm font-semibold leading-snug tracking-tight">Pilih. Kirim. Seleksi. <span class="text-white/45">Berkarya.</span></p>
    </div>

    <!-- Kolom tautan -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-8 text-xs">
      @foreach($columns as $title => $items)
        <div>
          <p class="text-[9px] tracking-[.18em] text-white/40 mb-3">{{ strtoupper($title) }}</p>
          <ul class="space-y-2">
            @foreach($items as [$label, $href])
              <li><a href="{{ $href }}" class="inline-block text-white/80 hover:text-orange-400 hover:translate-x-1 transition">{{ $label }}</a></li>
            @endforeach
          </ul>
        </div>
      @endforeach
    </div>

    <!-- Bar bawah -->
    <div class="md:col-span-2 pt-5 border-t border-white/10 flex flex-wrap items-center justify-between gap-2 text-[11px] text-white/40">
      <span>&copy; {{ date('Y') }} KarierHub Nusantara. Seluruh hak cipta dilindungi.</span>
      <a href="#top" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;" class="hover:text-orange-400 transition">Kembali ke atas ↑</a>
    </div>
  </div>
</footer>