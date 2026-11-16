<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Analisis Rekrutmen - ERA</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        paper: '#faf9fc',
                        ewforange: '#f97316'
                    }
                }
            }
        };
    </script>

    <style>
        [x-cloak] { display: none !important; }

        .panel {
            background: white;
            border: 1px solid #f1f1f3;
            border-radius: 22px;
            box-shadow: 0 5px 20px rgba(24,24,27,.035);
        }

        .field {
    width: 100%;
    height: 44px;
    box-sizing: border-box;
    border: 1px solid #e4e4e7;
    border-radius: 12px;
    padding: 0 13px;
    font-size: 13px;
    outline: none;
    background: white;
    display: flex;
    align-items: center;
}

.field:focus {
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249,115,22,.10);
}

.btn-orange {
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f97316;
    color: white;
    border-radius: 12px;
    padding: 0 16px;
    font-size: 12px;
    font-weight: 700;
    transition: .2s;
    white-space: nowrap;
}

.btn-orange:hover {
    background: #ea580c;
    transform: translateY(-1px);
}

        .chart-wrap {
            position: relative;
            height: 280px;
        }

        @media(max-width:640px) {
            .chart-wrap { height: 240px; }
        }



/* RANGE SLIDER - ORANGE WHITE GLOW */
.range-slider {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 7px;
    border-radius: 999px;
    outline: none;
    cursor: pointer;

    background: linear-gradient(
        90deg,
        #FF9E0F 0%,
        #FFB84D calc(var(--range-progress) * 0.65),
        #FFE0A3 calc(var(--range-progress) * 0.85),
        #ffffff var(--range-progress),
        #34303c var(--range-progress),
        #34303c 100%
    );

    transition: background 0.15s ease;
}


/* THUMB - SOFT ORANGE GLOW */
.range-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;

    width: 17px;
    height: 17px;
    border-radius: 50%;

    background: #ffffff;
    border: 2px solid #FFF0D6;

    box-shadow:
        0 0 3px 1px rgba(255, 224, 163, 0.6),
        0 0 7px 3px rgba(255, 158, 15, 0.3);

    cursor: grab;

    transition:
        transform 0.2s ease,
        box-shadow 0.25s ease;
}

.range-slider:hover::-webkit-slider-thumb {
    transform: scale(1.08);

    box-shadow:
        0 0 4px 2px rgba(255, 224, 163, 0.7),
        0 0 10px 4px rgba(255, 158, 15, 0.4);
}

.range-slider:active::-webkit-slider-thumb {
    transform: scale(1.15);
    cursor: grabbing;

    box-shadow:
        0 0 5px 2px rgba(255, 255, 255, 0.8),
        0 0 12px 5px rgba(255, 158, 15, 0.45);
}

/* FIREFOX */
.range-slider::-moz-range-thumb {
    width: 13px;
    height: 13px;
    border-radius: 50%;

    background: #ffffff;
    border: 2px solid #FFF0D6;

    box-shadow:
        0 0 3px 1px rgba(255, 224, 163, 0.6),
        0 0 7px 3px rgba(255, 158, 15, 0.3);

    cursor: grab;
}

.range-slider:active::-moz-range-thumb {
    transform: scale(1.15);

    box-shadow:
        0 0 5px 2px rgba(255, 255, 255, 0.8),
        0 0 12px 5px rgba(255, 158, 15, 0.45);
}

    </style>
</head>

<body class="bg-paper text-zinc-800 font-sans antialiased overflow-x-hidden" style="font-family: 'Inter', sans-serif;">

<div class="flex min-h-screen flex-col lg:flex-row">

    @include('partials.sidebar')

    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10
                 pt-20 sm:pt-24 lg:pt-10 pb-12">

        <div class="w-full max-w-6xl mx-auto space-y-7">

            {{-- HEADER --}}
            <div>
                <span class="text-[10px] tracking-[.2em] font-bold text-ewforange
                             bg-orange-50 px-3 py-1 rounded-full uppercase">
                    PANEL PIMPINAN
                </span>

                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight mt-3">
                    Analisis Rekrutmen
                </h1>

                <p class="text-xs text-zinc-400 mt-1">
                    Pantau tren pendaftaran dan lakukan simulasi kebutuhan kuota rekrutmen.
                </p>
            </div>

            {{-- GRAFIK PER BULAN --}}
            <section class="panel p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">
                    <div>
                        <h2 class="font-bold text-sm">Grafik Pendaftar per Bulan</h2>
                        <p class="text-[11px] text-zinc-400 mt-1">
                            Tren jumlah lamaran selama 12 bulan terakhir.
                        </p>
                    </div>

                    <span class="text-[10px] font-semibold text-orange-600 bg-orange-50
                                 rounded-full px-3 py-1.5 w-fit">
                        12 BULAN TERAKHIR
                    </span>
                </div>

                <div class="chart-wrap">
                    <canvas id="chartBulan"></canvas>
                </div>
            </section>

            {{-- GRAFIK PER POSISI --}}
            <section class="panel p-5 sm:p-6">
                <div class="mb-5">
                    <h2 class="font-bold text-sm">Grafik Pendaftar per Posisi</h2>
                    <p class="text-[11px] text-zinc-400 mt-1">
                        Perbandingan jumlah pendaftar pada setiap lowongan kerja dan program magang.
                    </p>
                </div>

                @if($grafikPosisi->isNotEmpty())
                    <div class="chart-wrap">
                        <canvas id="chartPosisi"></canvas>
                    </div>
                @else
                    <div class="py-12 text-center text-xs text-zinc-400">
                        Belum ada data pendaftaran.
                    </div>
                @endif
            </section>

            {{-- DRILLDOWN DAFTAR PENDAFTAR --}}
            <section class="panel p-5 sm:p-6" id="daftar-pendaftar">

                <div class="mb-5">
                    <h2 class="font-bold text-sm">Daftar Pendaftar</h2>
                    <p class="text-[11px] text-zinc-400 mt-1">
                        Telusuri detail pendaftar berdasarkan bulan dan posisi yang dilamar.
                    </p>
                </div>

                
{{-- FILTER --}}
<form method="GET"
      action="{{ route('pimpinan.analisis.index') }}"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] gap-3 items-end mb-5">

    {{-- BULAN PENDAFTARAN --}}
    <div class="min-w-0">
        <label class="block text-[11px] font-semibold text-zinc-500 mb-2">
            Bulan Pendaftaran
        </label>

        <input
            type="month"
            name="bulan"
            value="{{ $bulan }}"
            class="field"
        >
    </div>

    {{-- POSISI --}}
    <div
        x-data="{
            open: false,
            selected: @js($posisi),
            options: @js($opsiSimulasi)
        }"
        class="relative min-w-0"
        @click.outside="open = false"
        @keydown.escape.window="open = false"
    >
        <label class="block text-[11px] font-semibold text-zinc-500 mb-2">
            Posisi
        </label>

        <input type="hidden" name="posisi" :value="selected">

        {{-- BUTTON DROPDOWN --}}
        <button
            type="button"
            @click="open = !open"
            class="field justify-between gap-3 text-left cursor-pointer transition-all duration-200"
            :class="open
                ? 'border-orange-400 ring-4 ring-orange-500/10'
                : 'hover:border-orange-300'"
        >
            <span
                class="text-xs font-medium truncate"
                :class="selected ? 'text-zinc-700' : 'text-zinc-400'"
                x-text="options.find(item => item.key === selected)?.label || 'Semua Posisi'"
            ></span>

            <svg
                class="w-4 h-4 text-zinc-400 shrink-0 transition-transform duration-300"
                :class="open ? 'rotate-180 text-orange-500' : ''"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m6 9 6 6 6-6"
                />
            </svg>
        </button>

        {{-- DROPDOWN MENU --}}
        <div
            x-cloak
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.98]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.98]"
            class="absolute z-50 top-full left-0 right-0 mt-2
                   bg-white border border-zinc-100 rounded-xl
                   shadow-[0_10px_35px_rgba(0,0,0,0.09)]
                   p-1.5 max-h-60 overflow-y-auto"
        >

            {{-- SEMUA POSISI --}}
            <button
                type="button"
                @click="selected = ''; open = false"
                class="w-full flex items-center justify-between gap-3
                       px-3 py-2.5 rounded-lg text-left text-xs
                       transition-all duration-200"
                :class="selected === ''
                    ? 'bg-orange-50 text-orange-600 font-semibold'
                    : 'text-zinc-600 hover:bg-orange-50 hover:text-orange-600'"
            >
                <span>Semua Posisi</span>

                <svg
                    x-show="selected === ''"
                    class="w-4 h-4 text-orange-500 shrink-0"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m5 12 4 4L19 6"
                    />
                </svg>
            </button>

            {{-- DAFTAR POSISI --}}
            <template x-for="item in options" :key="item.key">
                <button
                    type="button"
                    @click="selected = item.key; open = false"
                    class="w-full flex items-center justify-between gap-3
                           px-3 py-2.5 rounded-lg text-left text-xs
                           transition-all duration-200"
                    :class="selected === item.key
                        ? 'bg-orange-50 text-orange-600 font-semibold'
                        : 'text-zinc-600 hover:bg-orange-50 hover:text-orange-600'"
                >
                    <span x-text="item.label"></span>

                    <svg
                        x-show="selected === item.key"
                        class="w-4 h-4 text-orange-500 shrink-0"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m5 12 4 4L19 6"
                        />
                    </svg>
                </button>
            </template>
        </div>
    </div>

    {{-- TOMBOL FILTER DAN RESET --}}
    <div class="flex items-center gap-2 h-11 w-full lg:w-auto">

        <button
            type="submit"
            class="btn-orange flex-1 lg:min-w-[170px]"
        >
            Terapkan Filter
        </button>

        <a
            href="{{ route('pimpinan.analisis.index') }}"
            class="h-11 inline-flex items-center justify-center
                   rounded-xl border border-zinc-200
                   px-4 text-xs font-semibold text-zinc-500
                   hover:bg-zinc-50 transition whitespace-nowrap"
        >
            Reset
        </a>

    </div>

</form>


                {{-- TABEL --}}
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px] text-left">
                        <thead>
                            <tr class="border-y border-zinc-100 text-[10px]
                                       uppercase tracking-wider text-zinc-400">
                                <th class="py-3 px-3">Pendaftar</th>
                                <th class="py-3 px-3">Posisi</th>
                                <th class="py-3 px-3">Jenis</th>
                                <th class="py-3 px-3">Tanggal</th>
                                <th class="py-3 px-3">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-100">
                            @forelse($daftarPendaftar as $lamaran)
                                @php
                                    $statusWarna = [
                                        'diajukan' => 'bg-amber-50 text-amber-600',
                                        'diproses' => 'bg-sky-50 text-sky-600',
                                        'diterima' => 'bg-emerald-50 text-emerald-600',
                                        'ditolak' => 'bg-rose-50 text-rose-600',
                                    ];

                                    $jenis = $lamaran->id_program ? 'Magang' : 'Kerja';

                                    $namaPosisi = $lamaran->id_program
                                        ? ($lamaran->program?->nama_program ?? '-')
                                        : ($lamaran->lowongan?->judul ?? '-');
                                @endphp

                                <tr class="hover:bg-orange-50/40 transition">
                                    <td class="py-3 px-3">
                                        <p class="text-xs font-bold text-zinc-700">
                                            {{ $lamaran->pelamar?->name ?? 'Pelamar tidak ditemukan' }}
                                        </p>
                                        <p class="text-[10px] text-zinc-400 mt-1">
                                            {{ $lamaran->pelamar?->email ?? '-' }}
                                        </p>
                                    </td>

                                    <td class="py-3 px-3 text-xs text-zinc-600">
                                        {{ $namaPosisi }}
                                    </td>

                                    <td class="py-3 px-3">
                                        <span class="text-[10px] font-semibold
                                            {{ $jenis === 'Magang'
                                                ? 'bg-violet-50 text-violet-600'
                                                : 'bg-blue-50 text-blue-600' }}
                                            px-2.5 py-1 rounded-full">
                                            {{ $jenis }}
                                        </span>
                                    </td>

                                    <td class="py-3 px-3 text-xs text-zinc-500">
                                        {{ $lamaran->tgl_pengajuan
                                            ? \Carbon\Carbon::parse($lamaran->tgl_pengajuan)->format('d/m/Y')
                                            : '-' }}
                                    </td>

                                    <td class="py-3 px-3">
                                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full
                                            {{ $statusWarna[$lamaran->status_lamaran] ?? 'bg-zinc-100 text-zinc-500' }}">
                                            {{ ucfirst($lamaran->status_lamaran ?? '-') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-xs text-zinc-400">
                                        Tidak ada pendaftar yang sesuai dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $daftarPendaftar->links() }}
                </div>
            </section>

            {{-- SIMULASI KUOTA --}}
            <section class="panel p-5 sm:p-6"
         x-data="simulasiKuota(@js($opsiSimulasi))">

                <div class="flex items-start gap-3 mb-6">
                    <div class="w-11 h-11 rounded-2xl bg-orange-50 text-orange-600
                                flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4 19V5m0 14h16M8 15l3-4 3 2 5-7"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-bold text-sm">Simulasi Kuota Rekrutmen</h2>
                        <p class="text-[11px] text-zinc-400 mt-1">
                            Uji skenario kuota dan persentase kelulusan tanpa mengubah data asli.
                        </p>
                    </div>
                </div>

                @if(count($opsiSimulasi))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div class="space-y-4">

                            <div class="relative"
     @click.outside="posisiOpen = false"
     @keydown.escape.window="posisiOpen = false">

    <label class="block text-[11px] font-semibold text-zinc-500 mb-2">
        Pilih Posisi
    </label>

    {{-- BUTTON DROPDOWN --}}
    <button
        type="button"
        @click="posisiOpen = !posisiOpen"
        class="field flex items-center justify-between gap-3 text-left
               cursor-pointer transition-all duration-200"
        :class="posisiOpen
            ? 'border-orange-400 ring-4 ring-orange-500/10'
            : 'hover:border-orange-300'"
    >
        <span
            class="text-xs font-medium text-zinc-700 truncate"
            x-text="options.find(item => item.key === selectedKey)?.label || 'Pilih Posisi'"
        ></span>

        <svg
            class="w-4 h-4 text-zinc-400 shrink-0 transition-transform duration-300"
            :class="posisiOpen ? 'rotate-180 text-orange-500' : ''"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="m6 9 6 6 6-6"/>
        </svg>
    </button>

    {{-- DROPDOWN MENU --}}
    <div
        x-cloak
        x-show="posisiOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.98]"
        class="absolute z-50 top-full left-0 right-0 mt-2
               bg-white border border-zinc-100 rounded-xl
               shadow-[0_10px_35px_rgba(0,0,0,0.09)]
               p-1.5 max-h-60 overflow-y-auto"
    >
        <template x-for="item in options" :key="item.key">
            <button
                type="button"
                @click="
                    selectedKey = item.key;
                    pilihPosisi();
                    posisiOpen = false;
                "
                class="w-full flex items-center justify-between gap-3
                       px-3 py-2.5 rounded-lg text-left text-xs
                       transition-all duration-200"
                :class="selectedKey === item.key
                    ? 'bg-orange-50 text-orange-600 font-semibold'
                    : 'text-zinc-600 hover:bg-orange-50 hover:text-orange-600'"
            >
                <span x-text="item.label"></span>

                <svg
                    x-show="selectedKey === item.key"
                    class="w-4 h-4 text-orange-500 shrink-0"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m5 12 4 4L19 6"/>
                </svg>
            </button>
        </template>
    </div>
</div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <label class="text-[11px] font-semibold text-zinc-500">
                                        Kuota yang Disimulasikan
                                    </label>
                                    <span class="text-xs font-bold text-orange-600"
                                          x-text="kuota + ' orang'"></span>
                                </div>

                                
<input
    type="range"
    min="0"
    max="200"
    step="1"
    x-model.number="kuota"
    :style="'--range-progress: ' + (Number(kuota) / 200 * 100) + '%'"
    class="range-slider"
>


                                <input type="number" min="0" max="10000"
                                       x-model.number="kuota"
                                       class="field mt-2">
                            </div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <label class="text-[11px] font-semibold text-zinc-500">
                                        Persentase Kelulusan
                                    </label>
                                    <span class="text-xs font-bold text-orange-600"
                                          x-text="persen + '%'"></span>
                                </div>
                                   
<input
    type="range"
    min="0"
    max="100"
    step="1"
    x-model.number="persen"
    :style="'--range-progress: ' + Number(persen) + '%'"
    class="range-slider"
>

                            </div>

                            <div class="rounded-xl bg-zinc-50 p-4">
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-500">Jumlah pendaftar saat ini</span>
                                    <strong x-text="pendaftar + ' orang'"></strong>
                                </div>
                                <div class="flex justify-between text-xs mt-3">
                                    <span class="text-zinc-500">Kuota asli database</span>
                                    <strong x-text="kuotaAsli + ' orang'"></strong>
                                </div>
                            </div>
                        </div>

                        {{-- HASIL SIMULASI --}}
                        <div class="rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600
                                    p-5 text-white flex flex-col justify-between">

                            <div>
                                <p class="text-xs text-white/75 font-medium">
                                    Perkiraan peserta diterima
                                </p>

                                <p class="text-4xl font-bold mt-3"
                                   x-text="diterima.toLocaleString('id-ID')">
                                </p>

                                <p class="text-[11px] text-white/75 mt-1">
                                    orang berdasarkan persentase kelulusan
                                </p>
                            </div>

                            <div class="border-t border-white/20 mt-7 pt-5 space-y-3">
                                <div class="flex justify-between text-xs">
                                    <span class="text-white/75">Kuota simulasi</span>
                                    <strong x-text="kuota + ' orang'"></strong>
                                </div>

                                <div class="flex justify-between text-xs">
                                    <span class="text-white/75">Sisa / kekurangan kuota</span>
                                    <strong x-text="selisihLabel"></strong>
                                </div>

                                <div class="rounded-xl bg-white/15 p-3">
                                    <p class="text-[10px] uppercase tracking-wider
                                              text-white/70 font-bold">
                                        Hasil simulasi
                                    </p>
                                    <p class="text-sm font-bold mt-1" x-text="kesimpulan"></p>
                                    <p class="text-[10px] text-white/75 mt-1">
                                        Hasil ini hanya perkiraan, bukan keputusan seleksi aktual.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="py-10 text-center text-xs text-zinc-400">
                        Belum tersedia lowongan atau program magang untuk disimulasikan.
                    </div>
                @endif
            </section>

            <p class="text-[10px] text-zinc-400 text-center pb-3">
                Data analisis berdasarkan informasi lamaran yang tersimpan di sistem.
            </p>

        </div>
    </main>
</div>

<script>
    // ---------- DATA GRAFIK ----------
    const dataBulan = @json($grafikBulan);
    const dataPosisi = @json($grafikPosisi);

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                grid: {
                    color: '#f4f4f5'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    };

    new Chart(document.getElementById('chartBulan'), {
        type: 'line',
        data: {
            labels: dataBulan.map(item => item.label),
            datasets: [{
                label: 'Jumlah Pendaftar',
                data: dataBulan.map(item => item.total),
                borderColor: '#f97316',
                backgroundColor: 'rgba(249,115,22,.10)',
                fill: true,
                tension: .35,
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#f97316'
            }]
        },
        options: commonOptions
    });

    @if($grafikPosisi->isNotEmpty())
        new Chart(document.getElementById('chartPosisi'), {
            type: 'bar',
            data: {
                labels: dataPosisi.map(item => item.label),
                datasets: [{
                    label: 'Jumlah Pendaftar',
                    data: dataPosisi.map(item => item.total),
                    backgroundColor: '#fb923c',
                    borderRadius: 8,
                    maxBarThickness: 45
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: '#f4f4f5' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    @endif

    // ---------- SIMULASI KUOTA ----------
    function simulasiKuota(options = []) {
    return {
        options: options,
        selectedKey: '',
        posisiOpen: false,

        kuotaAsli: 0,
        kuota: 0,
        pendaftar: 0,
        persen: 50,

        init() {
            this.selectedKey = this.options[0]?.key ?? '';
            this.pilihPosisi();
        },

        pilihPosisi() {
            const option = this.options.find(
                item => item.key === this.selectedKey
            );

            if (!option) return;

            this.kuotaAsli = Number(option.kuota || 0);
            this.pendaftar = Number(option.pendaftar || 0);
            this.kuota = this.kuotaAsli;
        },

        get diterima() {
            return Math.round(this.pendaftar * this.persen / 100);
        },

        get selisih() {
            return this.kuota - this.diterima;
        },

        get selisihLabel() {
            if (this.selisih > 0) {
                return this.selisih + ' orang tersisa';
            }

            if (this.selisih < 0) {
                return Math.abs(this.selisih) + ' orang kurang';
            }

            return 'Kuota terpenuhi';
        },

        get kesimpulan() {
            if (this.diterima === 0) {
                return 'Belum ada peserta yang diperkirakan lolos.';
            }

            if (this.selisih > 0) {
                return 'Kuota mencukupi untuk skenario kelulusan ini.';
            }

            if (this.selisih < 0) {
                return 'Kuota belum mencukupi. Pertimbangkan penambahan kuota.';
            }

            return 'Kuota tepat sesuai perkiraan peserta diterima.';
        }
    };
}
</script>

</body>
</html>
