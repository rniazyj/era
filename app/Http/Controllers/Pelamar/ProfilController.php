<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Models\ProfilPelamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    // Halaman profil pelamar (terhubung dengan CV lewat relasi cv())
    public function show(Request $request)
    {
        $user = $request->user();

        $profil = ProfilPelamar::with('cv')
            ->where('id_pengguna', $user->id)
            ->first();

        // Kalau belum punya profil, pakai objek kosong (tidak disimpan)
        // supaya view tidak perlu mengecek null di mana-mana.
        if (!$profil) {
            $profil = new ProfilPelamar();
            $profil->id_pengguna = $user->id;
        }

        $profil->setRelation('user', $user);

        return view('pelamar.profil', [
            'user'        => $user,
            'profil'      => $profil,
            'cv'          => $profil->exists ? $profil->cv : null,
            'kelengkapan' => $profil->kelengkapan(),
        ]);
    }

    // Simpan perubahan profil
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'bio'            => 'nullable|string|max:300',
            'jenis_kelamin'  => 'nullable|in:L,P',
            'tempat_lahir'   => 'nullable|string|max:100',
            'tgl_lahir'      => 'nullable|date|before:today',
            'kota'           => 'nullable|string|max:100',
            'alamat'         => 'nullable|string|max:500',
            'nik'            => 'nullable|digits:16',
            'linkedin_url'   => 'nullable|url|max:255',
            'portofolio_url' => 'nullable|url|max:255',
            'foto'           => 'nullable|image|max:2048',
        ]);

        // Nama: forceFill supaya tidak bergantung pada $fillable di model User
        $user->forceFill(['name' => $data['name']])->save();

        // Ambil profil lama atau buat baru
        $profil = ProfilPelamar::where('id_pengguna', $user->id)->first() ?? new ProfilPelamar();
        $profil->id_pengguna = $user->id;

        // Isi tiap kolom langsung. Kolom yang tidak ada di tabel dilewati dan dilaporkan.
        $tabel    = $profil->getTable();
        $dilewati = [];

        $kolomProfil = [
            'bio', 'jenis_kelamin', 'tempat_lahir', 'tgl_lahir',
            'kota', 'alamat', 'linkedin_url', 'portofolio_url',
        ];

        foreach ($kolomProfil as $kolom) {
            if (!array_key_exists($kolom, $data)) {
                continue;
            }

            if (Schema::hasColumn($tabel, $kolom)) {
                $profil->setAttribute($kolom, $data[$kolom]);
            } else {
                $dilewati[] = $kolom;
            }
        }

        // NIK hanya ditimpa kalau diisi
        if (!empty($data['nik'])) {
            if (Schema::hasColumn($tabel, 'nik_terenkripsi')) {
                $profil->nik_terenkripsi = Crypt::encryptString($data['nik']);
            } else {
                $dilewati[] = 'nik_terenkripsi';
            }
        }

        // Foto: hapus yang lama, simpan yang baru
        if ($request->hasFile('foto')) {
            if (filled($profil->foto)) {
                Storage::disk('public')->delete($profil->foto);
            }
            $profil->foto = $request->file('foto')->store('foto-profil', 'public');
        }

        $profil->save();

        // Sinkronkan progres CV dengan kelengkapan profil
        // (satu sumber hitungan yang sama dengan isi CV yang dicetak ke PDF)
        $profil->setRelation('user', $user);
        $profil->load('cv');

        if ($profil->cv) {
            $profil->cv->forceFill([
                'progres_cv' => $profil->kelengkapan()['persen'],
            ])->save();
        }

        $redirect = back()->with('success', 'Profil berhasil diperbarui.');

        if (!empty($dilewati)) {
            $redirect->with(
                'warning',
                'Kolom ini tidak ada di tabel ' . $tabel . ' sehingga tidak tersimpan: ' . implode(', ', $dilewati) . '.'
            );
        }

        return $redirect;
    }
}