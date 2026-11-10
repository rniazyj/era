<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pelamar\Concerns\MemuatProfil;
use Illuminate\Http\Request;

class PengalamanController extends Controller
{
    use MemuatProfil;

    public function index(Request $request)
    {
        $dasar = $this->dataDasar($request);
        $cv    = $dasar['cv'];

        return view('pelamar.profil.pengalaman', $dasar + [
            'organisasi'  => $cv ? $cv->pengalamanOrganisasi()->orderByDesc('tgl_mulai')->get() : collect(),
            'profesional' => $cv ? $cv->pengalamanProfesional()->orderByDesc('tgl_mulai')->get() : collect(),
            'proyek'      => $cv ? $cv->proyek()->orderByDesc('id_proyek')->get() : collect(),
        ]);
    }

    public function store(Request $request, string $jenis)
    {
        $k    = $this->konfigurasi($jenis);
        $data = $this->bersihkan($request, $request->validate($k['aturan']));

        $this->cvPelamar($request)->{$k['relasi']}()->create($data);
        $this->sinkronProgres($request);

        return back()->with('success', $k['nama'] . ' ditambahkan.');
    }

    public function update(Request $request, string $jenis, int $id)
    {
        $k    = $this->konfigurasi($jenis);
        $data = $this->bersihkan($request, $request->validate($k['aturan']));

        $row = $this->cvPelamar($request)->{$k['relasi']}()->where($k['pk'], $id)->firstOrFail();
        $row->update($data);
        $this->sinkronProgres($request);

        return back()->with('success', $k['nama'] . ' diperbarui.');
    }

    public function destroy(Request $request, string $jenis, int $id)
    {
        $k = $this->konfigurasi($jenis);

        $this->cvPelamar($request)->{$k['relasi']}()->where($k['pk'], $id)->firstOrFail()->delete();
        $this->sinkronProgres($request);

        return back()->with('success', $k['nama'] . ' dihapus.');
    }

    /** Checkbox "masih berjalan" -> boolean, dan kosongkan tanggal selesai bila dicentang. */
    private function bersihkan(Request $request, array $data): array
    {
        if (array_key_exists('tgl_mulai', $data)) {
            $data['masih_berjalan'] = $request->boolean('masih_berjalan');

            if ($data['masih_berjalan']) {
                $data['tgl_selesai'] = null;
            }
        }

        return $data;
    }

    private function konfigurasi(string $jenis): array
    {
        $tanggal = [
            'tgl_mulai'      => 'required|date',
            'tgl_selesai'    => 'nullable|date|after_or_equal:tgl_mulai',
            'masih_berjalan' => 'nullable|boolean',
        ];

        return match ($jenis) {
            'organisasi' => [
                'relasi' => 'pengalamanOrganisasi',
                'pk'     => 'id_organisasi',
                'nama'   => 'Pengalaman organisasi',
                'aturan' => [
                    'jabatan'         => 'required|string|max:255',
                    'nama_organisasi' => 'required|string|max:255',
                    'kategori'        => 'nullable|string|max:100',
                    'deskripsi'       => 'nullable|string|max:1000',
                ] + $tanggal,
            ],
            'profesional' => [
                'relasi' => 'pengalamanProfesional',
                'pk'     => 'id_pengalaman',
                'nama'   => 'Pengalaman profesional',
                'aturan' => [
                    'posisi'          => 'required|string|max:255',
                    'nama_perusahaan' => 'required|string|max:255',
                    'jenis_kerja'     => 'nullable|in:Full-time,Part-time,Magang,Freelance,Kontrak',
                    'lokasi'          => 'nullable|string|max:255',
                    'deskripsi_tugas' => 'nullable|string|max:1000',
                ] + $tanggal,
            ],
            'proyek' => [
                'relasi' => 'proyek',
                'pk'     => 'id_proyek',
                'nama'   => 'Pengalaman proyek',
                'aturan' => [
                    'nama_proyek'   => 'required|string|max:255',
                    'peran'         => 'nullable|string|max:255',
                    'teknologi'     => 'nullable|string|max:255',
                    'tautan_proyek' => 'nullable|url|max:255',
                    'deskripsi'     => 'nullable|string|max:1000',
                ],
            ],
            default => abort(404),
        };
    }
}