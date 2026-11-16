<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\LowonganKerja;
use App\Models\ProgramMagang;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PimpinanAnalisisController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua lamaran beserta data pelamar dan posisi yang dilamar.
        $semuaLamaran = Lamaran::with([
            'pelamar',
            'lowongan',
            'program',
        ])->get();

        // ---------- 1. GRAFIK PENDAFTAR PER BULAN ----------
        $awal = now()->startOfMonth()->subMonths(11);

        $kelompokBulan = $semuaLamaran
            ->filter(fn ($l) => $l->tgl_pengajuan
                && Carbon::parse($l->tgl_pengajuan)->gte($awal))
            ->groupBy(fn ($l) => Carbon::parse($l->tgl_pengajuan)->format('Y-m'));

        $grafikBulan = collect(range(0, 11))->map(function ($i) use ($awal, $kelompokBulan) {
            $bulan = $awal->copy()->addMonths($i);

            return [
                'kode'  => $bulan->format('Y-m'),
                'label' => $bulan->locale('id')->translatedFormat('M Y'),
                'total' => $kelompokBulan->get($bulan->format('Y-m'), collect())->count(),
            ];
        })->values();

        // ---------- 2. GRAFIK PENDAFTAR PER POSISI ----------
        $kelompokPosisi = $semuaLamaran->groupBy(function ($l) {
            if ($l->id_program) {
                return 'magang:' . $l->id_program;
            }

            return 'kerja:' . $l->id_lowongan;
        });

        $grafikPosisi = $kelompokPosisi->map(function ($items, $key) {
            $pertama = $items->first();

            $nama = $pertama->id_program
                ? ($pertama->program?->nama_program ?? 'Program Magang')
                : ($pertama->lowongan?->judul ?? 'Lowongan Kerja');

            return [
                'key'   => $key,
                'label' => $nama,
                'jenis' => $pertama->id_program ? 'Magang' : 'Kerja',
                'total' => $items->count(),
            ];
        })->sortByDesc('total')->values();

        // ---------- 3. FILTER DAFTAR PENDAFTAR ----------
        $query = Lamaran::with(['pelamar', 'lowongan', 'program']);

        $bulan = $request->query('bulan');
        $posisi = $request->query('posisi');

        if ($bulan && preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            [$tahun, $nomorBulan] = explode('-', $bulan);

            if ((int) $nomorBulan >= 1 && (int) $nomorBulan <= 12) {
                $query->whereYear('tgl_pengajuan', $tahun)
                    ->whereMonth('tgl_pengajuan', $nomorBulan);
            }
        }

        if ($posisi && preg_match('/^(kerja|magang):\d+$/', $posisi)) {
            [$jenis, $id] = explode(':', $posisi);

            if ($jenis === 'magang') {
                $query->where('id_program', $id);
            } else {
                $query->whereNull('id_program')
                    ->where('id_lowongan', $id);
            }
        }

        $daftarPendaftar = $query
            ->orderByDesc('tgl_pengajuan')
            ->orderByDesc('id_lamaran')
            ->paginate(10)
            ->withQueryString();

        // ---------- 4. OPSI SIMULASI KUOTA ----------
        $opsiSimulasi = [];

        $lowongan = LowonganKerja::orderBy('judul')->get();

        foreach ($lowongan as $item) {
            $opsiSimulasi[] = [
                'key'       => 'kerja:' . $item->id_lowongan,
                'label'     => $item->judul . ' (Kerja)',
                'kuota'     => (int) $item->kuota,
                'pendaftar' => $semuaLamaran->where('id_lowongan', $item->id_lowongan)
                    ->whereNull('id_program')->count(),
            ];
        }

        $program = ProgramMagang::orderBy('nama_program')->get();

        foreach ($program as $item) {
            $opsiSimulasi[] = [
                'key'       => 'magang:' . $item->id_program,
                'label'     => $item->nama_program . ' (Magang)',
                'kuota'     => (int) $item->kuota,
                'pendaftar' => $semuaLamaran->where('id_program', $item->id_program)->count(),
            ];
        }

        return view('pimpinan.analisis', [
            'grafikBulan'   => $grafikBulan,
            'grafikPosisi'  => $grafikPosisi,
            'daftarPendaftar' => $daftarPendaftar,
            'opsiSimulasi'  => $opsiSimulasi,
            'bulan'         => $bulan,
            'posisi'        => $posisi,
        ]);
    }
}
