<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pelamar\Concerns\MemuatProfil;
use App\Models\ProfilPelamar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\CvImportParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    public function impor(Request $request, CvImportParser $parser)
    {
        $request->validate([
            'cv_pdf' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'cv_pdf.required' => 'Pilih file CV terlebih dahulu.',
            'cv_pdf.mimes' => 'CV harus berupa PDF, JPG, atau PNG.',
            'cv_pdf.max' => 'Ukuran CV maksimal 10 MB.',
        ]);

        $path = $request->file('cv_pdf')->store('cv-import', 'public');
        $absolutePath = Storage::disk('public')->path($path);

        try {
            $hasil = $parser->parse($absolutePath);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);
            return back()->with('error', 'CV tidak dapat dibaca. Gunakan file PDF, JPG, atau PNG dengan teks yang cukup jelas.');
        }

        if (blank($hasil['teks_cv'] ?? null)) {
            Storage::disk('public')->delete($path);
            return back()->with('error', 'Teks pada CV tidak ditemukan. Pastikan dokumen atau foto memiliki resolusi dan pencahayaan yang jelas.');
        }

        $cv = $this->cvPelamar($request);
        $cv->forceFill([
            'sumber_cv' => 'unggahan',
            'file_cv_unggahan' => $path,
        ])->save();

        $request->session()->flash('cv_import', $hasil);

        $this->terapkanHasil($request, $hasil);

        return redirect()->route('pelamar.profil.cv')->with(
            'success',
            'CV berhasil dibaca dan data profil otomatis diterapkan. Periksa kembali hasilnya.'
        );
    }

    public function terapkan(Request $request)
    {
        $hasil = $request->session()->get('cv_import');

        if (! is_array($hasil)) {
            return back()->with('error', 'Data hasil pembacaan CV sudah tidak tersedia. Unggah kembali CV Anda.');
        }

        $this->terapkanHasil($request, $hasil);

        $request->session()->forget('cv_import');
        return redirect()->route('pelamar.profil.cv')->with('success', 'Data profil berhasil diperbarui.');
    }

    private function terapkanHasil(Request $request, array $hasil): void
    {
        $user = $request->user();
        $profil = ProfilPelamar::firstOrCreate(['id_pengguna' => $user->id]);
        $cv = $this->cvPelamar($request);

        DB::transaction(function () use ($user, $profil, $cv, $hasil) {
            if (filled($hasil['name'] ?? null)) {
                $user->forceFill(['name' => mb_substr($hasil['name'], 0, 100)])->save();
            }

            if (filled($hasil['no_telepon'] ?? null)) {
                $user->forceFill(['no_telepon' => mb_substr($hasil['no_telepon'], 0, 20)])->save();
            }

            foreach ([
                'jenis_kelamin' => $hasil['jenis_kelamin'] ?? null,
                'tempat_lahir' => $hasil['tempat_lahir'] ?? null,
                'tgl_lahir' => $hasil['tgl_lahir'] ?? null,
                'kota' => $hasil['kota'] ?? null,
                'alamat' => $hasil['alamat'] ?? null,
                'linkedin_url' => $hasil['linkedin_url'] ?? null,
                'instagram_url' => $hasil['instagram_url'] ?? null,
                'portofolio_url' => $hasil['portofolio_url'] ?? null,
            ] as $kolom => $nilai) {
                if (filled($nilai) && Schema::hasColumn($profil->getTable(), $kolom)) {
                    $profil->{$kolom} = mb_substr((string) $nilai, 0, $kolom === 'alamat' ? 500 : 100);
                }
            }
            $profil->save();

            $ringkasan = $hasil['ringkasan'] ?? null;
            if (filled($ringkasan)) {
                $cv->forceFill(['ringkasan_profil' => mb_substr($ringkasan, 0, 300)])->save();
            }

            foreach ($hasil['skills'] ?? [] as $skill) {
                $cv->skill()->updateOrCreate(
                    ['kategori_skill' => 'hard', 'nama_skill' => mb_substr((string) $skill, 0, 100)],
                    ['level_skill' => 'Intermediate']
                );
            }

            foreach ($hasil['pendidikan'] ?? [] as $pendidikan) {
                $institusi = trim((string) ($pendidikan['institusi'] ?? ''));
                if (! filled($institusi)) continue;

                $tahunMasuk = 1950;
                $tahunLulus = null;
                $rentang = (string) ($pendidikan['tahun'] ?? '');
                if (preg_match_all('/(19|20)\d{2}/', $rentang, $tahun)) {
                    $tahunMasuk = (int) ($tahun[0][0] ?? 1950);
                    $tahunLulus = count($tahun[0]) > 1 ? (int) $tahun[0][1] : null;
                }

                $cv->dataAkademik()->updateOrCreate(
                    ['institusi' => mb_substr($institusi, 0, 150)],
                    [
                        'jenjang' => $pendidikan['jenjang'] ?: 'S1',
                        'tahun_masuk' => $tahunMasuk,
                        'tahun_lulus' => $tahunLulus,
                    ]
                );
            }
        });

        $this->sinkronProgres($request);
    }

    // Foto jadi data-URI supaya dompdf tidak bergantung pada URL/symlink storage
    private function fotoBase64(ProfilPelamar $profil): ?string
    {
        if (blank($profil->foto) || ! Storage::disk('public')->exists($profil->foto)) {
            return null;
        }

        $path = Storage::disk('public')->path($profil->foto);
        $mime = mime_content_type($path);

        if (! in_array($mime, ['image/jpeg', 'image/png'], true)) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }
}