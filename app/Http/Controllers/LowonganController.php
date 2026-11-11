<?php

namespace App\Http\Controllers;

use App\Models\Lamaran;
use App\Models\LowonganKerja;
use App\Models\ProfilPelamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class LowonganController extends Controller
{
    /** Menampilkan daftar seluruh lowongan pekerjaan */
    public function index()
    {
        $daftarLowongan = LowonganKerja::whereIn('status', ['aktif', 'dibuka'])
            ->latest('id_lowongan')
            ->get()
            ->map(function (LowonganKerja $lowongan) {
                $gajiMin = (int) ($lowongan->gaji_min ?? 0);
                $gajiMax = (int) ($lowongan->gaji_max ?? 0);

                $gaji = 'Negosiasi';
                if ($gajiMin > 0 && $gajiMax > 0 && $gajiMin !== $gajiMax) {
                    $gaji = 'Rp ' . number_format($gajiMin, 0, ',', '.') . ' - ' . number_format($gajiMax, 0, ',', '.');
                } elseif ($gajiMax > 0) {
                    $gaji = 'Rp ' . number_format($gajiMax, 0, ',', '.');
                } elseif ($gajiMin > 0) {
                    $gaji = 'Rp ' . number_format($gajiMin, 0, ',', '.');
                }

                return [
                    'id' => $lowongan->id_lowongan,
                    'judul' => $lowongan->judul,
                    'huruf' => strtoupper(mb_substr($lowongan->judul, 0, 1)),
                    'kategori' => $lowongan->kategori ?: 'Umum',
                    'posisi' => (int) ($lowongan->kuota ?? 1),
                    'urgent' => (bool) ($lowongan->urgent ?? false),
                    'status' => in_array($lowongan->status, ['aktif', 'dibuka'], true) ? 'Aktif' : ucfirst((string) $lowongan->status),
                    'gaji' => $gaji,
                    'gaji_max' => round((($gajiMax ?: $gajiMin ?: 0) / 1_000_000), 1),
                    'deskripsi' => Str::limit(strip_tags((string) $lowongan->deskripsi), 160),
                ];
            })
            ->all();

        return view('pages.lowongan.index', compact('daftarLowongan'));
    }

    // Menampilkan detail lowongan tertentu berdasarkan ID
    public function show($id)
    {
        $lowongan = LowonganKerja::with('persyaratan')->findOrFail($id);

        $user = Auth::user();
        $kelayakan = null;
        $linkProfil = Route::has('pelamar.profil') ? route('pelamar.profil') : url('/profil');

        if ($user && $user->isPelamar()) {
            $profil = $user->profilPelamar ?? new ProfilPelamar(['id_pengguna' => $user->id]);
            $profil->setRelation('user', $user);

            $k = $profil->kelengkapan();
            $item = collect($k['item'])->only([
                'Nama lengkap',
                'No. telepon',
                'Foto',
                'Ringkasan diri',
                'Jenis kelamin',
                'Tempat lahir',
                'Tanggal lahir',
                'Kota',
                'Alamat',
                'Pendidikan',
            ]);
            $kurang = $item->reject()->keys()->all();

            $sudah = Lamaran::where('id_pelamar', $user->id)
                ->where('id_lowongan', $lowongan->id_lowongan)
                ->exists();

            $kelayakan = [
                'profil_item'    => $item->all(),
                'profil_kurang'  => $kurang,
                'profil_lengkap' => count($kurang) === 0,
                'persen_profil'  => $k['persen'],
                'sudah_melamar'  => $sudah,
                'boleh_melamar'  => $lowongan->sedang_buka && ! $sudah && count($kurang) === 0,
            ];
        }

        return view('pages.lowongan.show', compact('lowongan', 'kelayakan', 'linkProfil'));
    }
}