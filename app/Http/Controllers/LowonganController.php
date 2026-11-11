<?php

namespace App\Http\Controllers;

use App\Models\LowonganKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LowonganController extends Controller
{
    /** Menampilkan daftar seluruh lowongan pekerjaan */
    public function index()
    {
        $daftarLowongan = LowonganKerja::where('status', 'aktif')
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
                    'status' => $lowongan->status === 'aktif' ? 'Aktif' : ucfirst((string) $lowongan->status),
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
        return view('pages.lowongan.show', compact('lowongan'));
    }
}