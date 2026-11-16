<?php

namespace App\Http\Middleware;

use App\Models\LogAktivitas;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CatatAktivitas
{
    /**
     * Pemakaian:
     *   CatatAktivitas::class            -> hanya mencatat akses ditolak (403) dan aksi di daftar()
     *   CatatAktivitas::class . ':Modul' -> sama seperti di atas, dan halaman lain yang dibuka
     *                                       di route itu juga dicatat dengan modul "Modul"
     */
    public function handle(Request $request, Closure $next, ?string $modul = null): Response
    {
        $response = $next($request);

        try {
            $this->catat($request, $response, $modul);
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }

    private function catat(Request $request, Response $response, ?string $modulUmum): void
    {
        $user = $request->user();

        if (! $user) {
            return;
        }

        $status = $response->getStatusCode();

        // 1. Akses ditolak (Authorization): pengguna login tapi rolenya tidak sesuai
        if ($status === 403) {
            LogAktivitas::catat(
                'Akses ditolak: ' . $request->method() . ' /' . ltrim($request->path(), '/'),
                'Otorisasi',
                'gagal'
            );

            return;
        }

        // Gagal kalau respons error, atau (untuk aksi non-GET) validasi menolak input
        $gagal = $status >= 400
            || (! $request->isMethod('GET')
                && $request->hasSession()
                && $request->session()->has('errors'));

        $hasil = $gagal ? 'gagal' : 'berhasil';

        // 2. Aksi penting yang terdaftar (Accounting)
        $nama   = $request->route()?->getName();
        $daftar = $this->daftar();

        if ($nama && isset($daftar[$nama])) {
            [$modul, $label] = $daftar[$nama];
            $aktivitas = is_callable($label) ? $label($request) : $label;

            LogAktivitas::catat($aktivitas, $modul, $hasil);

            return;
        }

        // 3. Perilaku lama: kalau route memberi parameter modul, catat halaman yang diakses
        if ($modulUmum !== null) {
            $aktivitas = $request->isMethod('GET')
                ? 'Akses halaman: ' . $request->path()
                : 'Aksi ' . $request->method() . ': ' . $request->path();

            LogAktivitas::catat($aktivitas, $modulUmum, $hasil);
        }
    }

    /**
     * Daftar aksi yang dicatat: nama route => [modul, label atau fungsi pembuat label].
     * Tambahkan baris baru di sini kalau ada route lain yang perlu dicatat.
     * Jangan pernah memasukkan password atau data sensitif ke dalam label.
     */
    private function daftar(): array
    {
        return [
            // Admin: lowongan & program
            'admin.lowongan.store' => ['Lowongan', fn (Request $r) =>
                'Menambah ' . ($r->input('jenis') === 'Magang' ? 'program magang' : 'lowongan kerja') . ': ' . $r->input('judul')],
            'admin.lowongan.update' => ['Lowongan', fn (Request $r) =>
                'Mengubah ' . ($r->route('jenis') === 'magang' ? 'program magang' : 'lowongan kerja') . ' #' . $r->route('id')],

            // Admin: seleksi pelamar
            'admin.pelamar.show' => ['Seleksi', fn (Request $r) =>
                'Melihat detail lamaran #' . $r->route('id')],
            'admin.pelamar.status' => ['Seleksi', fn (Request $r) =>
                'Mengubah status lamaran #' . $r->route('id') . ' menjadi ' . $r->input('status')],
            'admin.pelamar.jadwal' => ['Seleksi', fn (Request $r) =>
                'Mengatur jadwal interview lamaran #' . $r->route('id')],

            // Profil & akun
            'admin.profil.update'   => ['Profil', 'Memperbarui profil'],
            'admin.profil.password' => ['Profil', 'Mengganti password'],
            'pelamar.profil.update' => ['Profil', 'Memperbarui profil'],

            // Pelamar: lamaran
            'pelamar.lamaran.store' => ['Lamaran', 'Mengirim lamaran'],

            // Pimpinan: ekspor laporan (route-nya menyusul)
            'pimpinan.ekspor.pdf'   => ['Laporan', 'Mengekspor laporan PDF'],
            'pimpinan.ekspor.excel' => ['Laporan', 'Mengekspor laporan Excel'],
        ];
    }
}