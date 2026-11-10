<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pelamar\Concerns\MemuatProfil;
use App\Models\ProfilPelamar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    use MemuatProfil;

    // Halaman status CV + pratinjau
    public function show(Request $request)
    {
        return view('pelamar.profil.cv', $this->dataDasar($request));
    }

    // ?mode=lihat -> tampil di browser (pratinjau), selain itu -> unduh
    public function pdf(Request $request)
    {
        $dasar = $this->dataDasar($request);
        $cv    = $dasar['cv'];

        $data = $dasar + [
            'akademik'    => $cv ? $cv->dataAkademik()->orderByDesc('tahun_masuk')->get() : collect(),
            'profesional' => $cv ? $cv->pengalamanProfesional()->orderByDesc('tgl_mulai')->get() : collect(),
            'organisasi'  => $cv ? $cv->pengalamanOrganisasi()->orderByDesc('tgl_mulai')->get() : collect(),
            'proyek'      => $cv ? $cv->proyek()->orderByDesc('id_proyek')->get() : collect(),
            'hard'        => $cv ? $cv->skill()->where('kategori_skill', 'hard')->orderBy('nama_skill')->get() : collect(),
            'soft'        => $cv ? $cv->skill()->where('kategori_skill', 'soft')->orderBy('nama_skill')->get() : collect(),
            'tools'       => $cv ? $cv->skill()->where('kategori_skill', 'tools')->orderBy('nama_skill')->get() : collect(),
            'fotoData'    => $this->fotoBase64($dasar['profil']),
        ];

        $pdf = Pdf::loadView('pelamar.profil.cv-pdf', $data)->setPaper('a4', 'portrait');

        $namaFile = 'CV_' . str_replace(' ', '_', trim(preg_replace('/[^A-Za-z0-9 ]/', '', $dasar['user']->name))) . '.pdf';

        return $request->query('mode') === 'lihat'
            ? $pdf->stream($namaFile)
            : $pdf->download($namaFile);
    }

    // Foto jadi data-URI supaya dompdf tidak bergantung pada URL/symlink storage
    private function fotoBase64(ProfilPelamar $profil): ?string
    {
        if (blank($profil->foto) || ! Storage::disk('public')->exists($profil->foto)) {
            return null;
        }

        $path = Storage::disk('public')->path($profil->foto);
        $mime = mime_content_type($path);

        // dompdf hanya mendukung jpeg/png
        if (! in_array($mime, ['image/jpeg', 'image/png'], true)) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }
}