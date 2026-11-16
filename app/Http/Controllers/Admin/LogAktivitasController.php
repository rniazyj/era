<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LogAktivitasController extends Controller
{
    private const PER_HALAMAN = 20;

    private const AMBANG_GAGAL = 5;
    private const MENIT_GAGAL  = 15;

    /**
     * Validasi filter yang digunakan bersama.
     */
    private function validasiFilter(Request $request): array
    {
        return $request->validate([
            'q'      => ['nullable', 'string', 'max:100'],
            'role'   => ['nullable', Rule::in(['admin_hr', 'pimpinan', 'pelamar', 'tamu'])],
            'modul'  => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['berhasil', 'gagal'])],
            'dari'   => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ], [
            'sampai.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
            'date' => ':attribute tidak valid.',
        ]);
    }

    /**
     * Query log dengan seluruh filter.
     */
    private function queryLog(array $filter): Builder
    {
        return LogAktivitas::query()
            ->with('pengguna.role')

            ->when($filter['q'] ?? null, function ($q, $cari) {
                $q->where(function ($w) use ($cari) {
                    $w->where('aktivitas', 'like', "%{$cari}%")
                      ->orWhere('modul', 'like', "%{$cari}%")
                      ->orWhere('alamat_ip', 'like', "%{$cari}%")
                      ->orWhereHas('pengguna', fn ($u) => $u
                          ->where('name', 'like', "%{$cari}%")
                          ->orWhere('email', 'like', "%{$cari}%"));
                });
            })

            ->when($filter['role'] ?? null, function ($q, $role) {
                if ($role === 'tamu') {
                    $q->whereNull('id_pengguna');
                } else {
                    $q->whereHas('pengguna.role', fn ($r) =>
                        $r->where('nama_role', $role)
                    );
                }
            })

            ->when($filter['modul'] ?? null,
                fn ($q, $m) => $q->where('modul', $m)
            )

            ->when($filter['status'] ?? null,
                fn ($q, $s) => $q->where('status_aksi', $s)
            )

            ->when($filter['dari'] ?? null,
                fn ($q, $d) => $q->where(
                    'waktu',
                    '>=',
                    Carbon::parse($d)->startOfDay()
                )
            )

            ->when($filter['sampai'] ?? null,
                fn ($q, $d) => $q->where(
                    'waktu',
                    '<=',
                    Carbon::parse($d)->endOfDay()
                )
            )

            ->orderByDesc('waktu')
            ->orderByDesc('id_log');
    }

    /**
     * Halaman utama log aktivitas.
     */
    public function index(Request $request)
    {
        $filter = $this->validasiFilter($request);

        $logs = $this->queryLog($filter)
            ->paginate(self::PER_HALAMAN)
            ->withQueryString();

        $hariIni = now()->startOfDay();

        $stat = [
            'hariIni' => LogAktivitas::where('waktu', '>=', $hariIni)->count(),

            'penggunaAktif' => LogAktivitas::where('waktu', '>=', $hariIni)
                ->whereNotNull('id_pengguna')
                ->distinct()
                ->count('id_pengguna'),

            'gagalLogin' => LogAktivitas::where('modul', 'Autentikasi')
                ->where('status_aksi', 'gagal')
                ->where('waktu', '>=', now()->subDay())
                ->count(),

            'terkunci' => User::where('terkunci_sampai', '>', now())->count(),
        ];

        $ipMencurigakan = LogAktivitas::query()
            ->where('modul', 'Autentikasi')
            ->where('status_aksi', 'gagal')
            ->where('waktu', '>=', now()->subMinutes(self::MENIT_GAGAL))
            ->whereNotNull('alamat_ip')
            ->select('alamat_ip', DB::raw('count(*) as jumlah'))
            ->groupBy('alamat_ip')
            ->havingRaw('count(*) >= ?', [self::AMBANG_GAGAL])
            ->orderByDesc('jumlah')
            ->get();

        $daftarModul = LogAktivitas::query()
            ->distinct()
            ->orderBy('modul')
            ->pluck('modul');

        return view('admin.log', [
            'logs'           => $logs,
            'stat'           => $stat,
            'ipMencurigakan' => $ipMencurigakan,
            'daftarModul'    => $daftarModul,
            'filter'         => $filter,
            'ambang'         => self::AMBANG_GAGAL,
            'menit'          => self::MENIT_GAGAL,
        ]);
    }

    /**
     * Ekspor CSV yang dapat dibuka dengan Microsoft Excel.
     */
    public function exportExcel(Request $request)
    {
        $filter = $this->validasiFilter($request);

        $namaFile = 'log-aktivitas-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($filter) {
            $output = fopen('php://output', 'w');

            // BOM agar karakter UTF-8 terbaca dengan benar oleh Excel.
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'No',
                'Waktu',
                'Nama Pengguna',
                'Email',
                'Role',
                'Aktivitas',
                'Modul',
                'Hasil',
                'Alamat IP',
            ], ';');

            $no = 1;

            $this->queryLog($filter)
                ->chunk(500, function ($logs) use ($output, &$no) {
                    foreach ($logs as $log) {
                        $pengguna = $log->pengguna;

                        fputcsv($output, [
                            $no++,
                            $log->waktu?->format('d-m-Y H:i:s'),
                            $pengguna?->name ?? 'Tidak dikenal',
                            $pengguna?->email ?? '-',
                            $pengguna?->role?->nama_role ?? 'Tamu',
                            $log->aktivitas,
                            $log->modul,
                            $log->status_aksi,
                            $log->alamat_ip ?? '-',
                        ], ';');
                    }
                });

            fclose($output);
        }, $namaFile, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Ekspor PDF.
     */
    public function exportPdf(Request $request)
    {
        $filter = $this->validasiFilter($request);

        $logs = $this->queryLog($filter)->get();

        $pdf = Pdf::loadView('admin.log-pdf', [
            'logs' => $logs,
            'filter' => $filter,
            'tanggalEkspor' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download(
            'log-aktivitas-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }
}
