<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\LogKirim;
use App\Models\SuratPenerimaan;
use Barryvdh\DomPDF\Facade\Pdf;
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


        // ---------- 5. INSIGHT STRATEGIS OTOMATIS ----------
        $insight = [];

        // A. Lamaran yang melewati batas waktu seleksi
if ($totalLamaran === 0) {
    $insight[] = [
        'jenis' => 'info',
        'judul' => 'Belum Ada Data Pendaftaran',
        'pesan' => 'Belum terdapat data lamaran pada periode ini. Insight strategis akan tersedia setelah data pendaftaran tercatat.'
    ];
} elseif ($lewatSla > 0) {
    $insight[] = [
        'jenis' => 'warning',
        'judul' => 'Lamaran Melewati Batas Waktu Seleksi',
        'pesan' => "{$lewatSla} lamaran belum memperoleh keputusan dan telah melewati batas {$data['slaHari']} hari. Perlu dilakukan evaluasi terhadap lamaran tersebut."
    ];
} elseif ($belumDiputuskan > 0) {
    $insight[] = [
        'jenis' => 'info',
        'judul' => 'Lamaran Masih Dalam Proses',
        'pesan' => "Terdapat {$belumDiputuskan} lamaran yang belum memperoleh keputusan. Pantau proses seleksi agar tidak menumpuk."
    ];
} else {
    $insight[] = [
        'jenis' => 'success',
        'judul' => 'Status Seleksi Terkendali',
        'pesan' => 'Seluruh lamaran pada periode ini telah memiliki keputusan seleksi.'
    ];
}

        // B. Tingkat penerimaan
        if ($diputuskan > 0) {
            $insight[] = [
                'jenis' => 'info',
                'judul' => 'Evaluasi Tingkat Penerimaan',
                'pesan' => "Tingkat penerimaan sebesar {$persenDiterima}% dari {$diputuskan} lamaran yang telah diputuskan. Data ini dapat digunakan untuk mengevaluasi hasil seleksi."
            ];
        } else {
            $insight[] = [
                'jenis' => 'info',
                'judul' => 'Belum Tersedia Hasil Seleksi',
                'pesan' => 'Belum terdapat lamaran yang diputuskan pada periode ini sehingga tingkat penerimaan belum dapat dianalisis.'
            ];
        }

        // C. Waktu proses seleksi
        if ($waktu['rata'] !== null) {
            if ($waktu['rata'] > self::SLA_HARI) {
                $insight[] = [
                    'jenis' => 'warning',
                    'judul' => 'Waktu Seleksi Perlu Dievaluasi',
                    'pesan' => "Rata-rata waktu penyelesaian seleksi {$waktu['rata']} hari, melebihi acuan {$data['slaHari']} hari. Evaluasi proses seleksi dapat dilakukan untuk meningkatkan efisiensi."
                ];
            } else {
                $insight[] = [
                    'jenis' => 'success',
                    'judul' => 'Waktu Seleksi Relatif Baik',
                    'pesan' => "Rata-rata waktu penyelesaian seleksi {$waktu['rata']} hari, masih dalam acuan {$data['slaHari']} hari."
                ];
            }
        }

        // D. Perbandingan tren pendaftaran dua bulan terakhir
        $trenValid = collect($tren)->values();
        $bulanTerakhir = $trenValid->last();
        $bulanSebelumnya = $trenValid->get($trenValid->count() - 2);

        if ($bulanTerakhir && $bulanSebelumnya) {
            $selisih = $bulanTerakhir['total'] - $bulanSebelumnya['total'];

            if ($bulanSebelumnya['total'] > 0) {
                $persenPerubahan = round(
                    ($selisih / $bulanSebelumnya['total']) * 100,
                    1
                );

                if ($selisih > 0) {
                    $insight[] = [
                        'jenis' => 'success',
                        'judul' => 'Minat Pendaftaran Meningkat',
                        'pesan' => "Jumlah pendaftaran bulan {$bulanTerakhir['bulan']} meningkat {$persenPerubahan}% dibanding bulan {$bulanSebelumnya['bulan']}. Hal ini menunjukkan peningkatan minat pendaftar."
                    ];
                } elseif ($selisih < 0) {
                    $insight[] = [
                        'jenis' => 'warning',
                        'judul' => 'Minat Pendaftaran Menurun',
                        'pesan' => "Jumlah pendaftaran bulan {$bulanTerakhir['bulan']} menurun " . abs($persenPerubahan) . "% dibanding bulan {$bulanSebelumnya['bulan']}. Evaluasi daya tarik lowongan dapat dipertimbangkan."
                    ];
                } else {
                    $insight[] = [
                        'jenis' => 'info',
                        'judul' => 'Minat Pendaftaran Stabil',
                        'pesan' => "Jumlah pendaftaran bulan {$bulanTerakhir['bulan']} sama dengan bulan {$bulanSebelumnya['bulan']}. "
                    ];
                }
            } elseif ($bulanTerakhir['total'] > 0) {
                $insight[] = [
                    'jenis' => 'success',
                    'judul' => 'Pendaftaran Mulai Tercatat',
                    'pesan' => "Terdapat {$bulanTerakhir['total']} pendaftaran pada bulan {$bulanTerakhir['bulan']}, sedangkan bulan sebelumnya belum terdapat pendaftaran."
                ];
            }
        }


        return view('pimpinan.dashboard', [
    'd'           => $data,
    'periode'      => $periode,
    'opsiPeriode'  => self::OPSI_PERIODE,
    'insight'      => $insight,
]);
    }

    // ==========================================
// EKSPOR LAPORAN DASHBOARD KE EXCEL (CSV)
// ==========================================
public function exportExcel(Request $request)
{
    // Ambil data dashboard sesuai periode yang dipilih
    $view = $this->index($request);
    $viewData = $view->getData();

    $d = $viewData['d'];
    $periode = $viewData['periode'];
    $opsiPeriode = self::OPSI_PERIODE;

    $namaFile = 'laporan-dashboard-pimpinan-' . now()->format('Ymd-His') . '.csv';

    return response()->streamDownload(function () use ($d, $periode, $opsiPeriode) {

        $handle = fopen('php://output', 'w');

        // BOM agar karakter UTF-8 terbaca dengan benar di Excel
        fwrite($handle, "\xEF\xBB\xBF");

        // Informasi laporan
        fputcsv($handle, ['LAPORAN DASHBOARD PIMPINAN'], ';');
        fputcsv($handle, ['PT Equityworld Futures'], ';');
        fputcsv($handle, ['Periode', $opsiPeriode[$periode]], ';');
        fputcsv($handle, ['Tanggal Ekspor', now()->format('d-m-Y H:i:s')], ';');
        fputcsv($handle, [], ';');

        // 1. RINGKASAN PENDAFTAR
        fputcsv($handle, ['RINGKASAN PENDAFTAR'], ';');
        fputcsv($handle, ['Keterangan', 'Jumlah'], ';');
        fputcsv($handle, ['Total Lamaran', $d['totalLamaran']], ';');
        fputcsv($handle, ['Pelamar Unik', $d['pelamarUnik']], ';');
        fputcsv($handle, ['Pendaftar Magang', $d['totalMagang']], ';');
        fputcsv($handle, ['Pendaftar Kerja', $d['totalKerja']], ';');
        fputcsv($handle, [], ';');

        // 2. STATUS LAMARAN
        fputcsv($handle, ['STATUS LAMARAN'], ';');
        fputcsv($handle, ['Status', 'Jumlah'], ';');

        foreach ($d['perStatus'] as $status => $jumlah) {
            fputcsv($handle, [ucfirst($status), $jumlah], ';');
        }

        fputcsv($handle, [], ';');

        // 3. PERSENTASE DITERIMA
        fputcsv($handle, ['PERSENTASE DITERIMA'], ';');
        fputcsv($handle, ['Keterangan', 'Jumlah'], ';');
        fputcsv($handle, ['Diterima', $d['diterima']], ';');
        fputcsv($handle, ['Ditolak', $d['ditolak']], ';');
        fputcsv($handle, ['Sudah Diputuskan', $d['diputuskan']], ';');
        fputcsv($handle, [
            'Persentase Diterima',
            $d['persenDiterima'] !== null ? $d['persenDiterima'] . '%' : '-'
        ], ';');
        fputcsv($handle, ['Belum Diputuskan', $d['belumDiputuskan']], ';');
        fputcsv($handle, ['Melewati SLA', $d['lewatSla']], ';');
        fputcsv($handle, [], ';');

        // 4. WAKTU PROSES
        fputcsv($handle, ['WAKTU PROSES'], ';');
        fputcsv($handle, ['Keterangan', 'Nilai'], ';');
        fputcsv($handle, ['Jumlah Lamaran Diputuskan', $d['waktu']['jumlah']], ';');
        fputcsv($handle, ['Rata-rata (hari)', $d['waktu']['rata'] ?? '-'], ';');
        fputcsv($handle, ['Median (hari)', $d['waktu']['median'] ?? '-'], ';');
        fputcsv($handle, ['Tercepat (hari)', $d['waktu']['min'] ?? '-'], ';');
        fputcsv($handle, ['Terlama (hari)', $d['waktu']['max'] ?? '-'], ';');
        fputcsv($handle, [], ';');

        // 5. TREN PENDAFTAR
        fputcsv($handle, ['TREN PENDAFTAR - 6 BULAN TERAKHIR'], ';');
        fputcsv($handle, ['Bulan', 'Total', 'Magang', 'Kerja'], ';');

        foreach ($d['tren'] as $tren) {
            fputcsv($handle, [
                $tren['bulan'],
                $tren['total'],
                $tren['magang'],
                $tren['kerja'],
            ], ';');
        }

        fputcsv($handle, [], ';');

        // 6. SURAT PENERIMAAN
        fputcsv($handle, ['SURAT PENERIMAAN'], ';');
        fputcsv($handle, ['Keterangan', 'Jumlah'], ';');
        fputcsv($handle, ['Total Surat', $d['surat']['total']], ';');
        fputcsv($handle, ['Surat Terbit', $d['surat']['terbit']], ';');
        fputcsv($handle, ['Surat Draft', $d['surat']['draft']], ';');
        fputcsv($handle, [
            'Pelamar Diterima Belum Memiliki Surat Terbit',
            $d['surat']['diterimaBelumSurat'],
        ], ';');

        fputcsv($handle, [], ';');
        fputcsv($handle, ['Rincian Status Surat'], ';');
        fputcsv($handle, ['Status', 'Jumlah'], ';');

        foreach ($d['surat']['perStatus'] as $surat) {
            fputcsv($handle, [$surat['status'], $surat['n']], ';');
        }

        fputcsv($handle, [], ';');

        // 7. PENGIRIMAN
        fputcsv($handle, ['PENGIRIMAN'], ';');
        fputcsv($handle, ['Keterangan', 'Jumlah'], ';');
        fputcsv($handle, ['Total Pengiriman', $d['kirim']['total']], ';');
        fputcsv($handle, ['Berhasil', $d['kirim']['berhasil']], ';');
        fputcsv($handle, ['Gagal', $d['kirim']['gagal']], ';');
        fputcsv($handle, ['Menunggu', $d['kirim']['menunggu']], ';');
        fputcsv($handle, [
            'Persentase Berhasil',
            $d['kirim']['persenBerhasil'] !== null
                ? $d['kirim']['persenBerhasil'] . '%'
                : '-',
        ], ';');

        fputcsv($handle, [], ';');
        fputcsv($handle, ['Rincian Status Pengiriman'], ';');
        fputcsv($handle, ['Status', 'Kategori', 'Jumlah'], ';');

        foreach ($d['kirim']['perStatus'] as $kirim) {
            fputcsv($handle, [
                $kirim['status'],
                ucfirst($kirim['kategori']),
                $kirim['n'],
            ], ';');
        }

        fputcsv($handle, [], ';');
        fputcsv($handle, ['Rincian Kanal Pengiriman'], ';');
        fputcsv($handle, ['Kanal', 'Jumlah'], ';');

        foreach ($d['kirim']['perKanal'] as $kanal) {
            fputcsv($handle, [$kanal['kanal'], $kanal['n']], ';');
        }

        fclose($handle);

    }, $namaFile, [
        'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
}


// ==========================================
// EKSPOR LAPORAN DASHBOARD KE PDF
// ==========================================
public function exportPdf(Request $request)
{
    // Ambil data dashboard sesuai periode yang dipilih
    $view = $this->index($request);
    $viewData = $view->getData();

    $pdf = Pdf::loadView('pimpinan.laporan-pdf', [
        'd' => $viewData['d'],
        'periodeLabel' => self::OPSI_PERIODE[$viewData['periode']],
        'tanggalEkspor' => now(),
    ])->setPaper('a4', 'portrait');

    return $pdf->download(
        'laporan-dashboard-pimpinan-' . now()->format('Ymd-His') . '.pdf'
    );
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