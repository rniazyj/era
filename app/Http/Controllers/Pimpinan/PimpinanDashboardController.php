<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\LogKirim;
use App\Models\SuratPenerimaan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PimpinanDashboardController extends Controller
{
    private const STATUS_LAMARAN = ['diajukan', 'diproses', 'diterima', 'ditolak'];

    // Lamaran yang belum diputuskan lebih dari ini (hari) dianggap menunggu terlalu lama
    private const SLA_HARI = 7;

    // Sesuaikan dengan nilai status_kirim yang kamu pakai di database
    private const KIRIM_BERHASIL = ['terkirim', 'berhasil', 'sukses', 'delivered', 'sent', 'dibaca'];
    private const KIRIM_GAGAL    = ['gagal', 'error', 'failed', 'ditolak'];

    private const OPSI_PERIODE = [
        'semua'     => 'Semua Waktu',
        '30-hari'   => '30 Hari Terakhir',
        '90-hari'   => '90 Hari Terakhir',
        'tahun-ini' => 'Tahun Ini',
    ];

    public function index(Request $request)
    {
        $periode = $request->query('periode', 'semua');

        if (! array_key_exists($periode, self::OPSI_PERIODE)) {
            $periode = 'semua';
        }

        // Batas awal periode (berdasarkan tanggal pengajuan lamaran)
        $mulai = match ($periode) {
            '30-hari'   => now()->subDays(30)->toDateString(),
            '90-hari'   => now()->subDays(90)->toDateString(),
            'tahun-ini' => now()->startOfYear()->toDateString(),
            default     => null,
        };

        $lamaran = fn () => Lamaran::query()
            ->when($mulai, fn ($q) => $q->whereDate('tgl_pengajuan', '>=', $mulai));

        $lewatLamaran = fn ($q) => $q->whereHas(
            'lamaran',
            fn ($l) => $l->whereDate('tgl_pengajuan', '>=', $mulai)
        );

        // ---------- 1. TOTAL PENDAFTAR ----------
        $totalLamaran = $lamaran()->count();
        $pelamarUnik  = $lamaran()->distinct()->count('id_pelamar');
        $totalMagang  = $lamaran()->whereNotNull('id_program')->count();
        $totalKerja   = $lamaran()->whereNull('id_program')->count();

        $statusRaw = $lamaran()
            ->select('status_lamaran', DB::raw('count(*) as n'))
            ->groupBy('status_lamaran')
            ->pluck('n', 'status_lamaran');

        $perStatus = [];
        foreach (self::STATUS_LAMARAN as $st) {
            $perStatus[$st] = (int) ($statusRaw[$st] ?? 0);
        }

        // ---------- 2. PERSENTASE DITERIMA ----------
        $diterima       = $perStatus['diterima'];
        $ditolak        = $perStatus['ditolak'];
        $diputuskan     = $diterima + $ditolak;
        $persenDiterima = $diputuskan > 0 ? round($diterima / $diputuskan * 100, 1) : null;

        // ---------- 3. WAKTU PROSES ----------
        $durasi = $lamaran()
            ->whereNotNull('tgl_keputusan')
            ->get(['tgl_pengajuan', 'tgl_keputusan'])
            ->map(fn ($l) => max(0, (int) Carbon::parse($l->tgl_pengajuan)->startOfDay()
                ->diffInDays(Carbon::parse($l->tgl_keputusan)->startOfDay())))
            ->values();

        $waktu = [
            'jumlah' => $durasi->count(),
            'rata'   => $durasi->isNotEmpty() ? round($durasi->avg(), 1) : null,
            'median' => $durasi->isNotEmpty() ? round($durasi->median(), 1) : null,
            'min'    => $durasi->isNotEmpty() ? $durasi->min() : null,
            'max'    => $durasi->isNotEmpty() ? $durasi->max() : null,
        ];

        $belumDiputuskan = $perStatus['diajukan'] + $perStatus['diproses'];
        $lewatSla = $lamaran()
            ->whereIn('status_lamaran', ['diajukan', 'diproses'])
            ->whereDate('tgl_pengajuan', '<', now()->subDays(self::SLA_HARI)->toDateString())
            ->count();

        // ---------- TREN PENDAFTAR 6 BULAN TERAKHIR (tidak mengikuti filter periode) ----------
        $awalTren = now()->startOfMonth()->subMonths(5);

        $perBulan = Lamaran::query()
            ->whereDate('tgl_pengajuan', '>=', $awalTren->toDateString())
            ->get(['tgl_pengajuan', 'id_program'])
            ->groupBy(fn ($l) => Carbon::parse($l->tgl_pengajuan)->format('Y-m'));

        $tren = collect(range(0, 5))->map(function ($i) use ($awalTren, $perBulan) {
            $bulan = $awalTren->copy()->addMonths($i);
            $grup  = $perBulan->get($bulan->format('Y-m'), collect());

            return [
                'label'  => $bulan->copy()->locale('id')->translatedFormat('M'),
                'bulan'  => $bulan->copy()->locale('id')->translatedFormat('F Y'),
                'total'  => $grup->count(),
                'magang' => $grup->whereNotNull('id_program')->count(),
                'kerja'  => $grup->whereNull('id_program')->count(),
            ];
        })->all();

        // ---------- 4. SURAT TERBIT ----------
        $suratQ = fn () => SuratPenerimaan::query()->when($mulai, $lewatLamaran);

        $suratRaw = $suratQ()
            ->select('status_penerbitan', DB::raw('count(*) as n'))
            ->groupBy('status_penerbitan')
            ->pluck('n', 'status_penerbitan');

        $suratTotal = (int) $suratRaw->sum();
        $suratTerbit = (int) $suratRaw
            ->filter(fn ($n, $st) => strtolower((string) $st) !== 'draft')
            ->sum();

        $suratPerStatus = $suratRaw
            ->map(fn ($n, $st) => ['status' => (string) $st, 'n' => (int) $n])
            ->values()
            ->all();

        // Pelamar yang sudah diterima tapi belum punya surat yang terbit
        $diterimaBelumSurat = $lamaran()
            ->where('status_lamaran', 'diterima')
            ->whereDoesntHave('surat', fn ($q) => $q->where('status_penerbitan', '!=', 'draft'))
            ->count();

        // ---------- 4b. PENGIRIMAN ----------
        $kirimQ = fn () => LogKirim::query()->when($mulai, $lewatLamaran);

        $kirimRaw = $kirimQ()
            ->select('status_kirim', DB::raw('count(*) as n'))
            ->groupBy('status_kirim')
            ->pluck('n', 'status_kirim');

        $kirimPerKanal = $kirimQ()
            ->select('kanal', DB::raw('count(*) as n'))
            ->groupBy('kanal')
            ->pluck('n', 'kanal')
            ->map(fn ($n, $kanal) => ['kanal' => (string) $kanal, 'n' => (int) $n])
            ->values()
            ->all();

        $kirimPerStatus = $kirimRaw
            ->map(fn ($n, $st) => [
                'status'   => (string) $st,
                'n'        => (int) $n,
                'kategori' => $this->kategoriKirim((string) $st),
            ])
            ->values();

        $kirimTotal    = (int) $kirimPerStatus->sum('n');
        $kirimBerhasil = (int) $kirimPerStatus->where('kategori', 'berhasil')->sum('n');
        $kirimGagal    = (int) $kirimPerStatus->where('kategori', 'gagal')->sum('n');

        $data = [
            'totalLamaran'   => $totalLamaran,
            'pelamarUnik'    => $pelamarUnik,
            'totalMagang'    => $totalMagang,
            'totalKerja'     => $totalKerja,
            'perStatus'      => $perStatus,

            'diterima'       => $diterima,
            'ditolak'        => $ditolak,
            'diputuskan'     => $diputuskan,
            'persenDiterima' => $persenDiterima,

            'waktu'          => $waktu,
            'belumDiputuskan' => $belumDiputuskan,
            'lewatSla'       => $lewatSla,
            'slaHari'        => self::SLA_HARI,

            'tren'           => $tren,

            'surat' => [
                'total'              => $suratTotal,
                'terbit'             => $suratTerbit,
                'draft'              => $suratTotal - $suratTerbit,
                'perStatus'          => $suratPerStatus,
                'diterimaBelumSurat' => $diterimaBelumSurat,
            ],

            'kirim' => [
                'total'         => $kirimTotal,
                'berhasil'      => $kirimBerhasil,
                'gagal'         => $kirimGagal,
                'menunggu'      => $kirimTotal - $kirimBerhasil - $kirimGagal,
                'persenBerhasil' => $kirimTotal > 0 ? round($kirimBerhasil / $kirimTotal * 100, 1) : null,
                'perStatus'     => $kirimPerStatus->all(),
                'perKanal'      => $kirimPerKanal,
            ],
        ];

        return view('pimpinan.dashboard', [
            'd'           => $data,
            'periode'     => $periode,
            'opsiPeriode' => self::OPSI_PERIODE,
        ]);
    }

    // Kelompokkan status_kirim menjadi berhasil / gagal / menunggu
    private function kategoriKirim(string $status): string
    {
        $s = strtolower(trim($status));

        if (in_array($s, self::KIRIM_BERHASIL, true)) {
            return 'berhasil';
        }

        if (in_array($s, self::KIRIM_GAGAL, true)) {
            return 'gagal';
        }

        return 'menunggu';
    }
}