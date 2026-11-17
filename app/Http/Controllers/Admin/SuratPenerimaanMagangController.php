<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\TemplateSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SuratPenerimaanMagangController extends Controller
{
    /**
     * Menampilkan form penerbitan LOA.
     */
    public function form(int $id)
    {
        $lamaran = Lamaran::with([
            'pelamar',
            'program',
            'surat',
        ])->findOrFail($id);

        $this->pastikanLayakMenerimaLoa($lamaran);

        $template = TemplateSurat::where(
            'nama_template',
            'Surat Penerimaan Magang (LOA)'
        )->firstOrFail();

        return view('admin.surat.form-loa-magang', compact(
            'lamaran',
            'template'
        ));
    }

    /**
     * Menyimpan data dan menerbitkan PDF LOA.
     */
    public function terbitkan(Request $request, int $id)
    {
        $lamaran = Lamaran::with([
            'pelamar',
            'program',
            'surat',
        ])->findOrFail($id);

        $this->pastikanLayakMenerimaLoa($lamaran);

        $template = TemplateSurat::where(
            'nama_template',
            'Surat Penerimaan Magang (LOA)'
        )->firstOrFail();

        $validated = $request->validate([
            'nomor_surat' => [
                'required',
                'string',
                'max:100',
                Rule::unique('surat_penerimaan', 'nomor_surat')
                    ->ignore(
                        $lamaran->surat?->id_surat,
                        'id_surat'
                    ),
            ],
            'tgl_terbit' => [
                'required',
                'date',
            ],
            'tgl_mulai_magang' => [
                'required',
                'date',
            ],
            'tgl_selesai_magang' => [
                'required',
                'date',
                'after_or_equal:tgl_mulai_magang',
            ],
            'nama_penandatangan' => [
                'required',
                'string',
                'max:255',
            ],
            'jabatan_penandatangan' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        // Perbarui periode magang sesuai input admin.
        $lamaran->tgl_mulai_magang = $validated['tgl_mulai_magang'];
        $lamaran->tgl_selesai_magang = $validated['tgl_selesai_magang'];

        $lamaran->durasi_hari = \Carbon\Carbon::parse(
            $validated['tgl_mulai_magang']
        )->diffInDays(
            \Carbon\Carbon::parse($validated['tgl_selesai_magang'])
        ) + 1;

        $lamaran->save();

        // Pastikan gambar tanda tangan dan stempel tersedia.
        $gambarTtdStempel = public_path(
            'storage/surat/ttd-stempel.png'
        );

        if (! file_exists($gambarTtdStempel)) {
            return back()
                ->withInput()
                ->withErrors([
                    'gambar' => 'File tanda tangan dan stempel tidak ditemukan di public/images/surat/ttd-stempel.png.',
                ]);
        }

        $data = [
            'lamaran' => $lamaran,
            'template' => $template,
            'nomorSurat' => $validated['nomor_surat'],
            'tanggalTerbit' => $validated['tgl_terbit'],
            'namaPenandatangan' => $validated['nama_penandatangan'],
            'jabatanPenandatangan' => $validated['jabatan_penandatangan'],
            'gambarTtdStempel' => 'data:image/png;base64,' .
                base64_encode(file_get_contents($gambarTtdStempel)),
        ];

        // Buat PDF menggunakan DomPDF.
        $pdf = Pdf::loadView('admin.surat.loa-magang', $data)
            ->setPaper('a4', 'portrait');

        $namaFile = 'LOA-Magang-' . $lamaran->id_lamaran . '.pdf';
        $pathFile = 'surat/loa-magang/' . $namaFile;

        Storage::disk('public')->put(
            $pathFile,
            $pdf->output()
        );

        // Simpan atau perbarui data surat untuk lamaran ini.
        $surat = $lamaran->surat()->updateOrCreate(
            [
                'id_lamaran' => $lamaran->id_lamaran,
            ],
            [
                'id_template' => $template->id_template,
                'nomor_surat' => $validated['nomor_surat'],
                'tgl_terbit' => $validated['tgl_terbit'],
                'file_surat' => $pathFile,
                'status_penerbitan' => 'terbit',
            ]
        );

        return redirect()
            ->route('admin.pelamar.show', $lamaran->id_lamaran)
            ->with('success', 'LOA magang berhasil diterbitkan.');
    }

    /**
     * Memastikan LOA hanya untuk pelamar magang yang diterima.
     */
    private function pastikanLayakMenerimaLoa(Lamaran $lamaran): void
    {
        abort_unless(
            $lamaran->id_program &&
            $lamaran->status_lamaran === 'diterima',
            403,
            'LOA hanya dapat diterbitkan untuk pelamar magang yang sudah diterima.'
        );
    }
}
