<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pelamar\Concerns\MemuatProfil;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AkademikController extends Controller
{
    use MemuatProfil;

    public const JENJANG = ['SMA/SMK', 'D3', 'D4', 'S1', 'S2', 'S3'];

    public function index(Request $request)
    {
        $dasar = $this->dataDasar($request);

        $daftar = $dasar['cv']
            ? $dasar['cv']->dataAkademik()->orderByDesc('tahun_masuk')->get()
            : collect();

        return view('pelamar.profil.akademik', $dasar + ['daftar' => $daftar]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan());

        $this->cvPelamar($request)->dataAkademik()->create($data);
        $this->sinkronProgres($request);

        return back()->with('success', 'Data pendidikan ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate($this->aturan());

        $row = $this->cvPelamar($request)->dataAkademik()->where('id_akademik', $id)->firstOrFail();
        $row->update($data);
        $this->sinkronProgres($request);

        return back()->with('success', 'Data pendidikan diperbarui.');
    }

    public function destroy(Request $request, int $id)
    {
        $this->cvPelamar($request)->dataAkademik()->where('id_akademik', $id)->firstOrFail()->delete();
        $this->sinkronProgres($request);

        return back()->with('success', 'Data pendidikan dihapus.');
    }

    private function aturan(): array
    {
        return [
            'jenjang'     => ['required', Rule::in(self::JENJANG)],
            'institusi'   => 'required|string|max:150',
            'jurusan'     => 'nullable|string|max:150',
            'ipk'         => 'nullable|numeric|between:0,4',
            'tahun_masuk' => 'required|integer|between:1950,2100',
            'tahun_lulus' => 'nullable|integer|between:1950,2100|gte:tahun_masuk',
            'deskripsi'   => 'nullable|string|max:500',
        ];
    }
}