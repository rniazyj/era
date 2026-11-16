<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalInterview;
use App\Models\Lamaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminJadwalController extends Controller
{
    private const TAMPIL = ['mendatang', 'hariini', 'terlewat', 'selesai', 'dibatalkan', 'semua'];

    // Halaman kelola jadwal interview
    public function index(Request $request)
    {
        $tampil = $request->query('tampil', 'mendatang');
        if (! in_array($tampil, self::TAMPIL, true)) {
            $tampil = 'mendatang';
        }

        $cari    = trim((string) $request->query('q', ''));
        $hariIni = today()->toDateString();

        $query = JadwalInterview::with(['lamaran.pelamar', 'lamaran.lowongan', 'lamaran.program']);

        switch ($tampil) {
            case 'hariini':
                $query->terjadwal()->whereDate('tgl_interview', $hariIni);
                break;
            case 'terlewat':
                $query->terjadwal()->whereDate('tgl_interview', '<', $hariIni);
                break;
            case 'selesai':
                $query->where('status_jadwal', JadwalInterview::SELESAI);
                break;
            case 'dibatalkan':
                $query->where('status_jadwal', JadwalInterview::DIBATALKAN);
                break;
            case 'semua':
                break;
            default: // mendatang (termasuk hari ini)
                $query->terjadwal()->whereDate('tgl_interview', '>=', $hariIni);
        }

        if ($cari !== '') {
            $query->whereHas('lamaran.pelamar', fn ($w) => $w->where('name', 'like', '%' . $cari . '%'));
        }

        $naik = in_array($tampil, ['mendatang', 'hariini'], true);
        $query->orderBy('tgl_interview', $naik ? 'asc' : 'desc')
              ->orderBy('jam_mulai', $naik ? 'asc' : 'desc');

        $daftar     = $query->get();
        $perTanggal = $daftar->groupBy(fn ($j) => $j->tgl_interview->toDateString());

        $stat = [
            'hariini'   => JadwalInterview::terjadwal()->whereDate('tgl_interview', $hariIni)->count(),
            'pekan'     => JadwalInterview::terjadwal()
                                ->whereDate('tgl_interview', '>', $hariIni)
                                ->whereDate('tgl_interview', '<=', today()->addDays(7)->toDateString())
                                ->count(),
            'terlewat'  => JadwalInterview::terjadwal()->whereDate('tgl_interview', '<', $hariIni)->count(),
        ];

        // Lamaran yang belum punya jadwal aktif (untuk pilihan di form "Atur Jadwal Baru")
        $lamaranOpsi = Lamaran::with(['pelamar', 'lowongan', 'program'])
            ->whereDoesntHave('jadwal', fn ($j) => $j->terjadwal())
            ->latest('id_lamaran')
            ->limit(300)
            ->get();

        return view('admin.jadwal.index', compact('perTanggal', 'daftar', 'stat', 'tampil', 'cari', 'lamaranOpsi'));
    }

    // Dari halaman Jadwal: pelamar dipilih lewat form (field id_lamaran)
    public function simpan(Request $request)
    {
        $request->validate(
            ['id_lamaran' => ['required', 'integer', 'exists:lamaran,id_lamaran']],
            ['id_lamaran.required' => 'Pilih pelamar terlebih dahulu.']
        );

        return $this->proses($request, (int) $request->input('id_lamaran'));
    }

    // Dari halaman detail pelamar: lamaran sudah pasti dari URL
    public function simpanUntukLamaran(Request $request, int $id)
    {
        return $this->proses($request, $id);
    }

    // Tandai selesai / batalkan / aktifkan kembali
    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([JadwalInterview::TERJADWAL, JadwalInterview::SELESAI, JadwalInterview::DIBATALKAN])],
        ]);

        JadwalInterview::findOrFail($id)->update(['status_jadwal' => $data['status']]);

        $pesan = [
            JadwalInterview::SELESAI    => 'Interview ditandai selesai.',
            JadwalInterview::DIBATALKAN => 'Jadwal interview dibatalkan.',
            JadwalInterview::TERJADWAL  => 'Jadwal interview diaktifkan kembali.',
        ][$data['status']];

        return back()->with('success', $pesan);
    }

    public function destroy(int $id)
    {
        JadwalInterview::findOrFail($id)->delete();

        return back()->with('success', 'Jadwal interview dihapus.');
    }

    // Satu lamaran hanya punya satu jadwal: menyimpan lagi = menjadwal ulang
    private function proses(Request $request, int $idLamaran)
    {
        $lamaran = Lamaran::with('pelamar')->findOrFail($idLamaran);

        $data = $request->validate([
            'tgl_interview'    => ['required', 'date', 'after_or_equal:today'],
            'jam_mulai'        => ['required', 'date_format:H:i'],
            'lokasi_atau_link' => ['required', 'string', 'max:255'],
            'catatan'          => ['nullable', 'string', 'max:1000'],
        ], [
            'tgl_interview.required'       => 'Tanggal interview wajib diisi.',
            'tgl_interview.after_or_equal' => 'Tanggal interview tidak boleh di masa lalu.',
            'jam_mulai.required'           => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format'        => 'Format jam tidak valid (contoh 09:30).',
            'lokasi_atau_link.required'    => 'Lokasi atau tautan meeting wajib diisi.',
        ]);

        $jadwal = JadwalInterview::updateOrCreate(
            ['id_lamaran' => $lamaran->id_lamaran],
            $data + ['status_jadwal' => JadwalInterview::TERJADWAL]
        );

        // Peringatan lunak kalau ada jadwal lain di tanggal & jam yang sama
        $bentrok = JadwalInterview::terjadwal()
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->whereDate('tgl_interview', $data['tgl_interview'])
            ->where('jam_mulai', $data['jam_mulai'] . ':00')
            ->count();

        $redirect = back()->with('success', 'Jadwal interview untuk ' . ($lamaran->pelamar->name ?? 'pelamar') . ' berhasil disimpan.');

        if ($bentrok > 0) {
            $redirect->with('warning', "Perhatian: ada {$bentrok} jadwal lain di tanggal dan jam yang sama.");
        }

        return $redirect;
    }
}