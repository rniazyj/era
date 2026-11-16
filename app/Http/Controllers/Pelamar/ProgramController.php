<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\ProgramMagang;
use App\Models\BerkasLamaran;
use App\Models\ProfilPelamar;
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

        // Periksa kelengkapan profil pelamar.
        $profil = $user->profilPelamar
            ?? new ProfilPelamar(['id_pengguna' => $user->id]);

        $profil->setRelation('user', $user);

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
            'kelayakan',
            'linkProfil'
        ));
    }

    /**
     * Menyimpan pendaftaran program magang.
     */
    public function store(Request $request)
    {
        $idProgram = $request->input('id_program');

        $program = ProgramMagang::with('persyaratan')
            ->findOrFail($idProgram);

        $user = Auth::user();

        // Pastikan program masih dibuka.
        // Pastikan status dan tanggal pendaftaran masih berlaku.
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

        // Aturan validasi pendaftaran.
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
        ];

        $names = [
            'tanggal_mulai' => 'tanggal mulai magang',
            'tanggal_selesai' => 'tanggal selesai magang',
            'pernyataan' => 'pernyataan',
        ];

        // Validasi berkas maksimal 5 MB.
        foreach ($program->persyaratan as $item) {
            $field = 'berkas.' . $item->id_persyaratan;

            $rules[$field] = [
                $item->wajib ? 'required' : 'nullable',
                'file',
                'mimes:' . implode(',', $item->format_list),
                'max:' . self::MAX_BERKAS_KB,
            ];

            $names[$field] = $item->nama_berkas;
        }

        $validated = $request->validate($rules, [
            'tanggal_mulai.required' => 'Tanggal mulai magang wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai magang wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'pernyataan.accepted' => 'Kamu harus menyetujui pernyataan kebenaran data.',
            'berkas.*.required' => ':attribute wajib diunggah.',
            'berkas.*.file' => ':attribute harus berupa file.',
            'berkas.*.mimes' => 'Format :attribute tidak sesuai.',
            'berkas.*.max' => 'Ukuran :attribute maksimal 5 MB.',
        ], $names);

        $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = Carbon::parse($validated['tanggal_selesai']);

        $durasiHari = (int) $tanggalMulai->diffInDays($tanggalSelesai);

        $fileTersimpan = [];

        try {
            DB::transaction(function () use (
                $request,
                $program,
                $user,
                $validated,
                $durasiHari,
                &$fileTersimpan
            ) {

            // Periksa kembali status dan tanggal pendaftaran.
$programTerkini = ProgramMagang::where('id_program', $program->id_program)
    ->lockForUpdate()
    ->firstOrFail();

if (!$this->pendaftaranMasihDibuka($programTerkini)) {
    abort(409, 'Masa pendaftaran program ini sudah berakhir atau belum dimulai.');
}

                // Periksa kembali pendaftaran ganda.
                $sudahMelamar = Lamaran::where('id_pelamar', $user->id)
                    ->where('id_program', $program->id_program)
                    ->lockForUpdate()
                    ->exists();

                if ($sudahMelamar) {
                    abort(409, 'Kamu sudah pernah mendaftar program ini.');
                }

                // Simpan data pendaftaran magang.
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

                // Simpan berkas persyaratan.
                foreach ($program->persyaratan as $item) {
                    $file = $request->file(
                        'berkas.' . $item->id_persyaratan
                    );

                    if (!$file) {
                        continue;
                    }

                    $path = $file->store(
                        "berkas_lamaran/{$lamaran->id_lamaran}",
                        'public'
                    );

                    $fileTersimpan[] = $path;

                    BerkasLamaran::create([
                        'id_lamaran' => $lamaran->id_lamaran,
                        'id_persyaratan' => $item->id_persyaratan,
                        'nama_file' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'status_verifikasi' => 'menunggu',
                    ]);
                }
            });

        } catch (\Throwable $e) {
            // Hapus file jika proses penyimpanan gagal.
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
            ->with('success', 'Pendaftaran magang berhasil dikirim!');
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
