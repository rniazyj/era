<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pelamar\Concerns\MemuatProfil;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    use MemuatProfil;

    public function index(Request $request)
    {
        $dasar = $this->dataDasar($request);
        $cv    = $dasar['cv'];

        $ambil = fn (string $jenis) => $cv
            ? $cv->skill()->where('kategori_skill', $jenis)->orderBy('nama_skill')->get()
            : collect();

        return view('pelamar.profil.skill', $dasar + [
            'hard'  => $ambil('hard'),
            'soft'  => $ambil('soft'),
            'tools' => $ambil('tools'),
            'level' => Skill::LEVEL,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis' => ['required', Rule::in(['hard', 'soft', 'tools'])],
            'nama'  => 'required|string|max:255',
            'level' => ['required_if:jenis,hard', 'nullable', Rule::in(Skill::LEVEL)],
        ], [
            'level.required_if' => 'Pilih level untuk hard skill.',
        ]);

        // Hard skill satu per satu (punya level). Soft skill & tools boleh banyak, pisahkan dengan koma.
        $daftar = $data['jenis'] === 'hard'
            ? [trim($data['nama'])]
            : collect(preg_split('/[,;\n]+/', $data['nama']))
                ->map(fn ($n) => trim($n))
                ->filter()
                ->unique()
                ->values()
                ->all();

        $cv = $this->cvPelamar($request);

        foreach ($daftar as $nama) {
            $cv->skill()->updateOrCreate(
                ['kategori_skill' => $data['jenis'], 'nama_skill' => Str::limit($nama, 100, '')],
                ['level_skill' => $data['jenis'] === 'hard' ? $data['level'] : null]
            );
        }

        $this->sinkronProgres($request);

        return back()->with('success', 'Skill ditambahkan.');
    }

    public function destroy(Request $request, int $id)
    {
        $this->cvPelamar($request)->skill()->where('id_skill', $id)->firstOrFail()->delete();
        $this->sinkronProgres($request);

        return back()->with('success', 'Skill dihapus.');
    }
}