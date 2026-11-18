<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\ProgramMagang;
use App\Models\BerkasLamaran;
use App\Models\ProfilPelamar;
use App\Models\AnggotaTimLamaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProgramController extends Controller
{
    private const MAX_BERKAS_KB = 5120;

    /**
     * Menampilkan form pendaftaran program magang.
     */
    
/**
 * Menampilkan form pendaftaran program magang.
 */
public function create(Request $request)
{
    $idProgram = $request->query('id_program');

    if (!$idProgram) {
        return redirect()
            ->route('internship.index')
            ->with('error', 'Silakan pilih program magang terlebih dahulu.');
    }

    $program = ProgramMagang::with('persyaratan')
        ->findOrFail($idProgram);

    $user = Auth::user();

    if (!$this->pendaftaranMasihDibuka($program)) {
        return redirect()
            ->route('program.show', $program->id_program)
            ->with('error', 'Pendaftaran program ini belum dibuka atau sudah ditutup.');
    }

    // Periksa apakah pelamar sudah pernah mendaftar.
    $sudahMelamar = Lamaran::where('id_pelamar', $user->id)
        ->where('id_program', $program->id_program)
        ->exists();

    if ($sudahMelamar) {
        return redirect()
            ->route('program.show', $program->id_program)
            ->with('error', 'Kamu sudah mengajukan lamaran untuk program ini.');
    }

    // Variabel untuk form gabungan kerja dan magang.
    $jenisLamaran = 'magang';
    $itemLamaran = $program;
    $persyaratan = $program->persyaratan;

    // Ambil profil pelamar.
    $profil = $user->profilPelamar
        ?? new ProfilPelamar(['id_pengguna' => $user->id]);

    $profil->setRelation('user', $user);

    // Ambil data CV dan riwayat pendidikan.
    $profil->loadMissing('cv.dataAkademik');

    // Ambil pendidikan yang ditandai sebagai pendidikan utama.
    $pendidikanUtama = $profil->cv
        ? $profil->cv->dataAkademik
            ->firstWhere('is_utama', true)
        : null;

    // Periksa kelengkapan profil pelamar.
    $kelengkapan = $profil->kelengkapan();

    $syaratProfil = [
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
    ];

    $profilItem = collect($kelengkapan['item'])
        ->only($syaratProfil);

    $profilKurang = $profilItem->reject()->keys()->all();

    $kelayakan = [
        'profil_item' => $profilItem->all(),
        'profil_kurang' => $profilKurang,
        'profil_lengkap' => count($profilKurang) === 0,
        'persen_profil' => $kelengkapan['persen'],
        'sudah_melamar' => false,
        'boleh_melamar' => count($profilKurang) === 0,
    ];

    $linkProfil = route('pelamar.profil');

    return view('pages.apply', compact(
        'jenisLamaran',
        'itemLamaran',
        'persyaratan',
        'user',
        'profil',
        'pendidikanUtama',
        'kelayakan',
        'linkProfil'
    ));
}


    /**
     * Menyimpan pendaftaran program magang.
     */
    
/**
 * Menyimpan pendaftaran program magang beserta anggota tim
 * dan berkas persyaratan masing-masing anggota.
 */
public function store(Request $request)
{
    $idProgram = $request->input('id_program');

    $program = ProgramMagang::with('persyaratan')
        ->findOrFail($idProgram);

    $user = Auth::user();

    // Pastikan program masih dibuka.
    if (!$this->pendaftaranMasihDibuka($program)) {
        return redirect()
            ->route('program.show', $program->id_program)
            ->with('error', 'Pendaftaran program ini belum dibuka atau sudah ditutup.');
    }

    // Periksa pendaftaran ganda.
    $sudahMelamar = Lamaran::where('id_pelamar', $user->id)
        ->where('id_program', $program->id_program)
        ->exists();

    if ($sudahMelamar) {
        return redirect()
            ->route('program.show', $program->id_program)
            ->with('error', 'Kamu sudah mengajukan lamaran untuk program ini.');
    }

    /*
    |--------------------------------------------------------------------------
    | Ambil data ketua tim dari database
    |--------------------------------------------------------------------------
    */

    $profil = $user->profilPelamar;

    if (!$profil) {
        return back()
            ->withInput()
            ->with('error', 'Profil pelamar tidak ditemukan. Silakan lengkapi profil terlebih dahulu.');
    }

    $profil->loadMissing('cv.dataAkademik');

    $pendidikanUtama = $profil->cv
        ? $profil->cv->dataAkademik->firstWhere('is_utama', true)
        : null;

    if (!$pendidikanUtama) {
        return back()
            ->withInput()
            ->with('error', 'Data pendidikan utama belum tersedia. Silakan tandai salah satu pendidikan utama di profilmu.');
    }

    /*
    |--------------------------------------------------------------------------
    | Identifikasi anggota tambahan
    |--------------------------------------------------------------------------
    */

    // Menggunakan all() agar data input dan file sama-sama terbaca.
    $semuaInput = $request->all();

    $dataAnggota = $semuaInput['anggota'] ?? [];

    $indeksAnggota = array_filter(
        array_keys($dataAnggota),
        fn ($index) => is_numeric($index) && (int) $index > 0
    );

    $indeksAnggota = array_map('intval', $indeksAnggota);

    sort($indeksAnggota);

    // Maksimal 3 anggota tambahan di luar ketua.
    if (count($indeksAnggota) > 3) {
        return back()
            ->withInput()
            ->with('error', 'Jumlah anggota maksimal 4 orang termasuk ketua tim.');
    }

    // Pastikan indeks anggota berurutan: 1, 2, 3.
    $indeksSeharusnya = $indeksAnggota
        ? range(1, count($indeksAnggota))
        : [];

    if ($indeksAnggota !== $indeksSeharusnya) {
        return back()
            ->withInput()
            ->with('error', 'Data anggota tim tidak valid. Silakan periksa kembali anggota yang ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | Aturan validasi
    |--------------------------------------------------------------------------
    */

    $rules = [
        'id_program' => [
            'required',
            'exists:program_magang,id_program',
        ],

        'tanggal_mulai' => [
            'required',
            'date',
        ],

        'tanggal_selesai' => [
            'required',
            'date',
            'after_or_equal:tanggal_mulai',
        ],

        'pernyataan' => [
            'accepted',
        ],

        'anggota' => [
            'sometimes',
            'array',
        ],
    ];

    $names = [
        'tanggal_mulai' => 'tanggal mulai magang',
        'tanggal_selesai' => 'tanggal selesai magang',
        'pernyataan' => 'pernyataan',
    ];


/*
|--------------------------------------------------------------------------
| Validasi data ketua tim
|--------------------------------------------------------------------------
*/

$rules['anggota.0'] = [
    'required',
    'array',
];

$fieldKetua = [
    'nama' => 'nama lengkap ketua',
    'email' => 'email ketua',
    'no_telepon' => 'nomor telepon ketua',
    'npm' => 'NPM ketua',
    'jenjang' => 'jenjang pendidikan ketua',
    'institusi' => 'institusi ketua',
    'fakultas' => 'fakultas ketua',
    'program_studi' => 'program studi ketua',
];

foreach ($fieldKetua as $field => $label) {
    $rules["anggota.0.$field"] = [
        'required',
        'string',
        'max:255',
    ];

    $names["anggota.0.$field"] = $label;
}

$rules['anggota.0.email'] = [
    'required',
    'email',
    'max:255',
];

$rules['anggota.0.no_telepon'] = [
    'required',
    'string',
    'max:30',
];

$rules['anggota.0.npm'] = [
    'required',
    'string',
    'max:30',
];

$rules['anggota.0.jenjang'] = [
    'required',
    'in:D3,D4,S1,S2,S3',
];

    // Validasi data anggota tambahan.
    foreach ($indeksAnggota as $index) {

        $rules["anggota.$index"] = [
            'required',
            'array',
        ];

        $fieldAnggota = [
            'nama' => 'nama lengkap',
            'email' => 'email',
            'no_telepon' => 'nomor telepon',
            'npm' => 'NPM',
            'jenjang' => 'jenjang pendidikan',
            'institusi' => 'institusi',
            'fakultas' => 'fakultas',
            'program_studi' => 'program studi',
        ];

        foreach ($fieldAnggota as $field => $label) {

            $rules["anggota.$index.$field"] = [
                'required',
                'string',
                'max:255',
            ];

            $names["anggota.$index.$field"] =
                "$label anggota " . ($index + 1);
        }

        $rules["anggota.$index.email"] = [
            'required',
            'email',
            'max:255',
        ];

        $rules["anggota.$index.no_telepon"] = [
            'required',
            'string',
            'max:30',
        ];

        $rules["anggota.$index.npm"] = [
            'required',
            'string',
            'max:30',
        ];

        $rules["anggota.$index.jenjang"] = [
    'required',
    'in:SMA,SMK,D3,D4,S1,S2,S3',
];
    }

    /*
    |--------------------------------------------------------------------------
    | Validasi berkas setiap anggota
    |--------------------------------------------------------------------------
    */

    // Indeks 0 adalah ketua, sedangkan indeks 1-3 anggota tambahan.
    $semuaIndeks = array_merge([0], $indeksAnggota);

    foreach ($semuaIndeks as $index) {

        foreach ($program->persyaratan as $item) {

            $field = "anggota.$index.berkas." . $item->id_persyaratan;

            $rules[$field] = [
                $item->wajib ? 'required' : 'nullable',
                'file',
                'mimes:' . implode(',', $item->format_list),
                'max:' . self::MAX_BERKAS_KB,
            ];

            $namaAnggota = $index === 0
                ? 'ketua tim'
                : 'anggota ' . ($index + 1);

            $names[$field] = $item->nama_berkas . ' (' . $namaAnggota . ')';
        }
    }

    $validated = $request->validate($rules, [
        'tanggal_mulai.required' => 'Tanggal mulai magang wajib diisi.',
        'tanggal_selesai.required' => 'Tanggal selesai magang wajib diisi.',
        'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        'pernyataan.accepted' => 'Kamu harus menyetujui pernyataan kebenaran data.',


'anggota.0.nama.required' => 'Nama ketua wajib diisi.',
'anggota.0.email.required' => 'Email ketua wajib diisi.',
'anggota.0.email.email' => 'Format email ketua tidak valid.',
'anggota.0.no_telepon.required' => 'Nomor telepon ketua wajib diisi.',
'anggota.0.npm.required' => 'NPM ketua wajib diisi.',
'anggota.0.jenjang.required' => 'Jenjang pendidikan ketua wajib dipilih.',
'anggota.0.institusi.required' => 'Institusi ketua wajib diisi.',
'anggota.0.fakultas.required' => 'Fakultas ketua wajib diisi.',
'anggota.0.program_studi.required' => 'Program studi ketua wajib diisi.',


        'anggota.*.nama.required' => 'Nama anggota wajib diisi.',
        'anggota.*.email.required' => 'Email anggota wajib diisi.',
        'anggota.*.email.email' => 'Format email anggota tidak valid.',
        'anggota.*.no_telepon.required' => 'Nomor telepon anggota wajib diisi.',
        'anggota.*.npm.required' => 'NPM anggota wajib diisi.',
        'anggota.*.jenjang.required' => 'Jenjang pendidikan anggota wajib dipilih.',
        'anggota.*.institusi.required' => 'Institusi anggota wajib diisi.',
        'anggota.*.fakultas.required' => 'Fakultas anggota wajib diisi.',
        'anggota.*.program_studi.required' => 'Program studi anggota wajib diisi.',

        'anggota.*.berkas.*.required' => ':attribute wajib diunggah.',
        'anggota.*.berkas.*.file' => ':attribute harus berupa file.',
        'anggota.*.berkas.*.mimes' => 'Format :attribute tidak sesuai.',
        'anggota.*.berkas.*.max' => 'Ukuran :attribute maksimal 5 MB.',
    ], $names);

    /*
    |--------------------------------------------------------------------------
    | Hitung durasi magang
    |--------------------------------------------------------------------------
    */

    $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
    $tanggalSelesai = Carbon::parse($validated['tanggal_selesai']);

    $durasiHari = (int) $tanggalMulai->diffInDays($tanggalSelesai);

    $fileTersimpan = [];

    try {

        DB::transaction(function () use (
            $request,
            $program,
            $user,
            $profil,
            $pendidikanUtama,
            $validated,
            $indeksAnggota,
            $durasiHari,
            &$fileTersimpan
        ) {

            /*
            |--------------------------------------------------------------------------
            | Periksa ulang program dan pendaftaran
            |--------------------------------------------------------------------------
            */

            $programTerkini = ProgramMagang::where(
                'id_program',
                $program->id_program
            )
                ->lockForUpdate()
                ->firstOrFail();

            if (!$this->pendaftaranMasihDibuka($programTerkini)) {
                abort(409, 'Masa pendaftaran program ini sudah berakhir atau belum dimulai.');
            }

            $sudahMelamar = Lamaran::where('id_pelamar', $user->id)
                ->where('id_program', $program->id_program)
                ->lockForUpdate()
                ->exists();

            if ($sudahMelamar) {
                abort(409, 'Kamu sudah pernah mendaftar program ini.');
            }

            /*
            |--------------------------------------------------------------------------
            | Buat satu data lamaran untuk seluruh tim
            |--------------------------------------------------------------------------
            */

            $lamaran = Lamaran::create([
                'id_pelamar' => $user->id,
                'id_program' => $program->id_program,
                'id_lowongan' => null,
                'tgl_pengajuan' => now()->toDateString(),
                'tgl_mulai_magang' => $validated['tanggal_mulai'],
                'tgl_selesai_magang' => $validated['tanggal_selesai'],
                'durasi_hari' => $durasiHari,
                'status_lamaran' => 'diajukan',
            ]);

            
/*
|--------------------------------------------------------------------------
| Simpan data ketua tim
|--------------------------------------------------------------------------
*/

// Ambil data ketua yang sudah divalidasi dari formulir.
$dataKetua = $validated['anggota'][0];

$ketua = AnggotaTimLamaran::create([
    'id_lamaran' => $lamaran->id_lamaran,
    'id_pengguna' => $user->id,

    // Identitas ketua dari formulir.
    'nama' => $dataKetua['nama'],
    'email' => $dataKetua['email'],
    'no_telepon' => $dataKetua['no_telepon'],

    // Data akademik ketua dari formulir.
    'npm' => $dataKetua['npm'],
    'institusi' => $dataKetua['institusi'],
    'fakultas' => $dataKetua['fakultas'],
    'program_studi' => $dataKetua['program_studi'],
    'jenjang' => $dataKetua['jenjang'],

    'is_ketua' => true,
]);


            /*
            |--------------------------------------------------------------------------
            | Simpan berkas ketua
            |--------------------------------------------------------------------------
            */

            foreach ($program->persyaratan as $item) {

                $file = $request->file(
                    'anggota.0.berkas.' . $item->id_persyaratan
                );

                if (!$file) {
                    continue;
                }

                $path = $file->store(
                    "berkas_lamaran/{$lamaran->id_lamaran}/anggota/{$ketua->id_anggota}",
                    'public'
                );

                $fileTersimpan[] = $path;

                BerkasLamaran::create([
                    'id_lamaran' => $lamaran->id_lamaran,
                    'id_anggota' => $ketua->id_anggota,
                    'id_persyaratan' => $item->id_persyaratan,
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'status_verifikasi' => 'menunggu',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan anggota tambahan dan berkasnya
            |--------------------------------------------------------------------------
            */

            foreach ($indeksAnggota as $index) {

                $data = $validated['anggota'][$index];

                $anggota = AnggotaTimLamaran::create([
                    'id_lamaran' => $lamaran->id_lamaran,
                    'id_pengguna' => null,

                    'nama' => $data['nama'],
                    'email' => $data['email'],
                    'no_telepon' => $data['no_telepon'],

                    'npm' => $data['npm'],
                    'institusi' => $data['institusi'],
                    'fakultas' => $data['fakultas'],
                    'program_studi' => $data['program_studi'],
                    'jenjang' => $data['jenjang'],

                    'is_ketua' => false,
                ]);

                // Simpan berkas anggota.
                foreach ($program->persyaratan as $item) {

                    $file = $request->file(
                        "anggota.$index.berkas." . $item->id_persyaratan
                    );

                    if (!$file) {
                        continue;
                    }

                    $path = $file->store(
                        "berkas_lamaran/{$lamaran->id_lamaran}/anggota/{$anggota->id_anggota}",
                        'public'
                    );

                    $fileTersimpan[] = $path;

                    BerkasLamaran::create([
                        'id_lamaran' => $lamaran->id_lamaran,
                        'id_anggota' => $anggota->id_anggota,
                        'id_persyaratan' => $item->id_persyaratan,
                        'nama_file' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'status_verifikasi' => 'menunggu',
                    ]);
                }
            }
        });

    } catch (\Throwable $e) {

        // Bersihkan file jika proses database gagal.
        foreach ($fileTersimpan as $path) {
            Storage::disk('public')->delete($path);
        }

        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            return redirect()
                ->route('program.show', $program->id_program)
                ->with('error', $e->getMessage());
        }

        report($e);

        return back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan saat menyimpan pendaftaran. Silakan coba kembali.');
    }

    return redirect()
        ->route('pelamar.dashboard')
        ->with('success', 'Pendaftaran magang beserta seluruh anggota tim berhasil dikirim!');
}


    /**
 * Memeriksa apakah pendaftaran program masih berlangsung.
 */
private function pendaftaranMasihDibuka(ProgramMagang $program): bool
{
    if ($program->status !== 'dibuka') {
        return false;
    }

    $hariIni = Carbon::today();

    // Belum memasuki tanggal pembukaan.
    if (
        $program->tgl_buka &&
        $hariIni->lt(Carbon::parse($program->tgl_buka)->startOfDay())
    ) {
        return false;
    }

    // Tanggal penutupan sudah terlewati.
    if (
        $program->tgl_tutup &&
        $hariIni->gt(Carbon::parse($program->tgl_tutup)->startOfDay())
    ) {
        return false;
    }

    return true;
}
}
