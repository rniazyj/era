<?php

namespace App\Http\Controllers\Pelamar\Concerns;

use App\Models\Cv;
use App\Models\ProfilPelamar;
use Illuminate\Http\Request;

/**
 * Dipakai semua controller halaman profil supaya layout (header + sidebar)
 * selalu menerima data yang sama.
 */
trait MemuatProfil
{
    /** Data dasar untuk layout. Tidak membuat record baru. */
    protected function dataDasar(Request $request): array
    {
        $user = $request->user();

        $profil = ProfilPelamar::where('id_pengguna', $user->id)->first();

        if (! $profil) {
            $profil = new ProfilPelamar();
            $profil->id_pengguna = $user->id;
        }

        $profil->setRelation('user', $user);

        return [
            'user'        => $user,
            'profil'      => $profil,
            'cv'          => $profil->exists ? $profil->cv : null,
            'kelengkapan' => $profil->kelengkapan(),
        ];
    }

    /** Ambil CV milik pelamar; profil dan CV dibuat otomatis kalau belum ada. */
    protected function cvPelamar(Request $request): Cv
    {
        $profil = ProfilPelamar::firstOrCreate(['id_pengguna' => $request->user()->id]);

        return Cv::firstOrCreate(
            ['id_profil' => $profil->id_profil],
            ['sumber_cv' => Cv::SUMBER_DEFAULT, 'progres_cv' => 0]
        );
    }

    /** Simpan persentase terbaru ke cv.progres_cv. Panggil setiap data CV berubah. */
    protected function sinkronProgres(Request $request): void
    {
        $profil = ProfilPelamar::with(['user', 'cv'])
            ->where('id_pengguna', $request->user()->id)
            ->first();

        if ($profil && $profil->cv) {
            $profil->cv->forceFill([
                'progres_cv' => $profil->kelengkapan()['persen'],
            ])->save();
        }
    }
}