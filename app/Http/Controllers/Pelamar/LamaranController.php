<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pelamar\Concerns\MemuatProfil;
use App\Models\Lamaran;
use Illuminate\Http\Request;

class LamaranController extends Controller
{
    use MemuatProfil;

    /**
     * Menampilkan seluruh riwayat lamaran milik pelamar.
     */
    public function index(Request $request)
    {
        $jenis = $request->query('jenis', 'semua');

        // Batasi pilihan filter yang diperbolehkan.
        if (! in_array($jenis, ['semua', 'kerja', 'magang'], true)) {
            $jenis = 'semua';
        }

        $query = Lamaran::with([
    'lowongan',
    'program',
    'jadwal',
    'surat',
])
    ->where('id_pelamar', $request->user()->id);

        // Filter berdasarkan jenis lamaran.
        if ($jenis === 'kerja') {
            $query->whereNull('id_program');
        } elseif ($jenis === 'magang') {
            $query->whereNotNull('id_program');
        }

        $daftar = $query
            ->orderByDesc('tgl_pengajuan')
            ->orderByDesc('id_lamaran')
            ->paginate(10)
            ->withQueryString();

        return view('pelamar.profil.lamaran-index', array_merge(
            $this->dataDasar($request),
            [
                'daftar' => $daftar,
                'jenis'  => $jenis,
            ]
        ));
    }

    /**
     * Menampilkan detail salah satu lamaran milik pelamar.
     */
    public function show(Request $request, $id)
    {
        $lamaran = Lamaran::with([
            'lowongan',
            'program',
            'berkas',
            'jadwal',
            'surat',
        ])
            ->where('id_pelamar', $request->user()->id)
            ->findOrFail($id);

        return view('pelamar.profil.lamaran-show', array_merge(
            $this->dataDasar($request),
            [
                'lamaran' => $lamaran,
            ]
        ));
    }
}
