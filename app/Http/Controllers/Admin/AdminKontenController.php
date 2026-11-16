<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilPerusahaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Pengelola konten website (halaman Tentang) di dashboard admin.
 * Satu controller melayani semua jenis konten; bentuk form & aturan validasinya dibaca dari
 * ProfilPerusahaan::TIPE, jadi menambah jenis konten baru cukup menambah satu blok di model itu.
 */
class AdminKontenController extends Controller
{
    // /admin/konten -> halaman depan: 3 halaman situs beserta bagian-bagiannya
    public function index()
    {
        return view('admin.konten.index', [
            'halaman' => ProfilPerusahaan::navigasi(),
        ]);
    }

    public function daftar(string $tipe)
    {
        $cfg = $this->cfg($tipe);

        // Banner atas diedit dari menu tiap halaman, bukan dari daftar
        if ($cfg['mode'] === 'tetap') {
            return redirect()->route('admin.konten.index');
        }

        // Konten tunggal (profil, visi, misi): langsung buka form edit
        if ($cfg['mode'] === 'tunggal') {
            $row = ProfilPerusahaan::firstOrCreate(['tipe' => $tipe], ['aktif' => true]);

            return redirect()->route('admin.konten.edit', [$tipe, $row->getKey()]);
        }

        $rows = ProfilPerusahaan::tipe($tipe)->orderBy('urutan')->orderBy('id_profil')->get();

        return view('admin.konten.daftar', $this->konteks($tipe, $cfg) + ['rows' => $rows]);
    }

    public function create(string $tipe)
    {
        $cfg = $this->cfg($tipe);
        abort_unless($cfg['mode'] === 'daftar', 404);

        return view('admin.konten.form', $this->konteks($tipe, $cfg) + [
            'row'        => null,
            'urutanBaru' => (int) ProfilPerusahaan::tipe($tipe)->max('urutan') + 1,
        ]);
    }

    public function store(Request $request, string $tipe): RedirectResponse
    {
        $cfg = $this->cfg($tipe);
        abort_unless($cfg['mode'] === 'daftar', 404);

        $this->simpan($request, $cfg, $tipe, null);

        return redirect()->route('admin.konten.daftar', $tipe)
            ->with('success', $cfg['nama'] . ' berhasil ditambahkan.');
    }

    public function edit(string $tipe, int $id)
    {
        $cfg = $this->cfg($tipe);
        $row = $this->baris($tipe, $id);

        return view('admin.konten.form', $this->konteks($tipe, $cfg, $tipe === 'hero' ? $row->kategori : null) + [
            'row'        => $row,
            'urutanBaru' => null,
        ]);
    }

    public function update(Request $request, string $tipe, int $id): RedirectResponse
    {
        $cfg = $this->cfg($tipe);
        $row = $this->baris($tipe, $id);

        $this->simpan($request, $cfg, $tipe, $row);

        // Konten tunggal & banner tetap di halaman edit; konten daftar kembali ke daftar
        $tujuan = $cfg['mode'] === 'daftar'
            ? route('admin.konten.daftar', $tipe)
            : route('admin.konten.edit', [$tipe, $row->getKey()]);

        return redirect($tujuan)->with('success', 'Perubahan ' . Str::lower($cfg['nama']) . ' berhasil disimpan.');
    }

    public function destroy(string $tipe, int $id): RedirectResponse
    {
        $cfg = $this->cfg($tipe);
        abort_unless($cfg['mode'] === 'daftar', 404);

        $row = $this->baris($tipe, $id);
        $this->hapusFile($row->foto);
        $row->delete();

        return redirect()->route('admin.konten.daftar', $tipe)->with('success', 'Data berhasil dihapus.');
    }

    /* ------------------------------------------------------------------ */

    private function cfg(string $tipe): array
    {
        return ProfilPerusahaan::TIPE[$tipe] ?? abort(404);
    }

    private function baris(string $tipe, int $id): ProfilPerusahaan
    {
        return ProfilPerusahaan::tipe($tipe)->where('id_profil', $id)->firstOrFail();
    }

    // Variabel yang dibutuhkan menu, header, dan tombol "Lihat di situs"
    private function konteks(string $tipe, array $cfg, ?string $halamanKini = null): array
    {
        $halamanKini ??= $cfg['halaman'];
        $h = ProfilPerusahaan::HALAMAN[$halamanKini] ?? abort(404);

        return [
            'tipe'        => $tipe,
            'cfg'         => $cfg,
            'halamanKini' => $halamanKini,
            'halaman'     => $h,
            'urlSitus'    => route($h['route']) . (! empty($cfg['anchor']) ? '#' . $cfg['anchor'] : ''),
        ];
    }

    // Validasi + simpan (dipakai tambah & ubah)
    private function simpan(Request $request, array $cfg, string $tipe, ?ProfilPerusahaan $row): ProfilPerusahaan
    {
        $data = $request->validate($this->aturan($cfg), $this->pesan(), $this->namaAtribut($cfg));

        $row ??= new ProfilPerusahaan(['tipe' => $tipe]);

        foreach ($cfg['field'] as $kolom => $f) {
            if ($f['tipe'] === 'foto') {
                continue;
            }
            if ($f['tipe'] === 'centang') {
                $row->{$kolom} = $request->boolean($kolom);
                continue;
            }
            $nilai = $data[$kolom] ?? null;
            $row->{$kolom} = ($nilai === null || trim((string) $nilai) === '') ? null : trim((string) $nilai);
        }

        if ($cfg['mode'] === 'daftar') {
            $row->urutan = (int) ($data['urutan'] ?? 0);
            $row->aktif  = $request->boolean('aktif');
        }

        // Foto: unggahan baru menggantikan yang lama; centang "hapus foto" mengosongkan
        if (isset($cfg['field']['foto'])) {
            if ($request->hasFile('foto')) {
                $this->hapusFile($row->foto);
                $row->foto = $request->file('foto')->store('profil', 'public');
            } elseif ($request->boolean('hapus_foto')) {
                $this->hapusFile($row->foto);
                $row->foto = null;
            }
        }

        $row->save();

        // Hanya satu kantor pusat
        if ($tipe === 'cabang' && $row->is_utama) {
            ProfilPerusahaan::tipe('cabang')->where('id_profil', '!=', $row->getKey())->update(['is_utama' => false]);
        }

        return $row;
    }

    private function hapusFile(?string $path): void
    {
        if (filled($path) && ! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('public')->delete($path);
        }
    }

    private function aturan(array $cfg): array
    {
        $aturan = [];

        foreach ($cfg['field'] as $kolom => $f) {
            $aturan[$kolom] = match ($f['tipe']) {
                'foto'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                'centang' => 'nullable|boolean',
                default   => $f['aturan'] ?? 'nullable|string|max:255',
            };
        }

        if ($cfg['mode'] === 'daftar') {
            $aturan['urutan'] = 'nullable|integer|min:0|max:9999';
            $aturan['aktif']  = 'nullable|boolean';
        }
        $aturan['hapus_foto'] = 'nullable|boolean';

        return $aturan;
    }

    private function namaAtribut(array $cfg): array
    {
        return collect($cfg['field'])->map(fn ($f) => $f['label'])->all()
            + ['urutan' => 'Urutan', 'aktif' => 'Status tayang'];
    }

    // Pesan kesalahan berbahasa Indonesia (tidak bergantung pada folder lang/id)
    private function pesan(): array
    {
        return [
            'required'    => ':attribute wajib diisi.',
            'string'      => ':attribute harus berupa teks.',
            'max.string'  => ':attribute maksimal :max karakter.',
            'max.file'    => 'Ukuran :attribute maksimal :max KB.',
            'image'       => ':attribute harus berupa gambar.',
            'mimes'       => ':attribute harus berformat jpg, png, atau webp.',
            'uploaded'    => ':attribute gagal diunggah. Coba file yang lebih kecil.',
            'url'         => 'Format :attribute tidak valid, awali dengan https://',
            'integer'     => ':attribute harus berupa angka.',
            'digits'      => ':attribute harus :digits digit (contoh: 2015).',
            'min.numeric' => ':attribute minimal :min.',
            'max.numeric' => ':attribute maksimal :max.',
            'boolean'     => ':attribute tidak valid.',
        ];
    }
}