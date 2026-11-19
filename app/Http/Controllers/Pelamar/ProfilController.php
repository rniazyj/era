<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pelamar\Concerns\MemuatProfil;
use App\Models\ProfilPelamar;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    use MemuatProfil;

    // Data Diri & Profil
    public function show(Request $request)
    {
        return view('pelamar.profil.index', $this->dataDasar($request));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'no_telepon'     => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'bio'            => 'nullable|string|max:300',
            'jenis_kelamin'  => 'nullable|in:L,P',
            'tempat_lahir'   => 'nullable|string|max:100',
            'tgl_lahir'      => 'nullable|date|before:today',
            'kota'           => 'nullable|string|max:100',
            'alamat'         => 'nullable|string|max:500',
            'nik'            => 'nullable|digits:16',
            'linkedin_url'   => 'nullable|url|max:255',
            'instagram_url'  => 'nullable|url|max:255',
            'portofolio_url' => 'nullable|url|max:255',
            // dompdf tidak bisa membaca webp, jadi foto dibatasi jpg/png
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'no_telepon.regex' => 'No. telepon hanya boleh berisi angka, spasi, +, - dan tanda kurung.',
            'foto.mimes'       => 'Foto harus berformat JPG atau PNG (supaya bisa tampil di CV PDF).',
        ]);

        // users: name & no_telepon (forceFill supaya tidak bergantung pada $fillable)
        $user->forceFill([
            'name'       => $data['name'],
            'no_telepon' => $data['no_telepon'] ?? null,
        ])->save();

        $profil = ProfilPelamar::where('id_pengguna', $user->id)->first() ?? new ProfilPelamar();
        $profil->id_pengguna = $user->id;

        $tabel    = $profil->getTable();
        $dilewati = [];

        $kolomProfil = [
            'jenis_kelamin', 'tempat_lahir', 'tgl_lahir', 'kota', 'alamat',
            'linkedin_url', 'instagram_url', 'portofolio_url',
        ];

        foreach ($kolomProfil as $kolom) {
            if (! array_key_exists($kolom, $data)) {
                continue;
            }

            if (Schema::hasColumn($tabel, $kolom)) {
                $profil->setAttribute($kolom, $data[$kolom]);
            } else {
                $dilewati[] = $kolom;
            }
        }

        // NIK hanya ditimpa kalau diisi
        if (! empty($data['nik'])) {
            if (Schema::hasColumn($tabel, 'nik_terenkripsi')) {
                $profil->nik_terenkripsi = Crypt::encryptString($data['nik']);
            } else {
                $dilewati[] = 'nik_terenkripsi';
            }
        }

        // Foto: hapus yang lama, simpan yang baru
        if ($request->hasFile('foto')) {
            if (filled($profil->foto)) {
                Storage::disk('public')->delete($profil->foto);
            }
            $profil->foto = $request->file('foto')->store('foto-profil', 'public');
        }

        $profil->save();

        // Ringkasan diri disimpan di cv.ringkasan_profil (CV dibuat otomatis kalau belum ada)
        $this->cvPelamar($request)
            ->forceFill(['ringkasan_profil' => $data['bio'] ?? null])
            ->save();

        $this->sinkronProgres($request);

        $redirect = back()->with('success', 'Profil berhasil diperbarui.');

        if (! empty($dilewati)) {
            $redirect->with(
                'warning',
                'Kolom ini tidak ada di tabel ' . $tabel . ' sehingga tidak tersimpan: ' . implode(', ', $dilewati) . '. Jalankan php artisan migrate.'
            );
        }

        return $redirect;
    }

    // Menampilkan halaman Pengaturan Akun
public function pengaturanAkun(Request $request)
{
    $data = $this->dataDasar($request);

    return view('pelamar.profil.pengaturan-akun', $data);
}

// Mengubah email akun pelamar
public function updateEmail(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user->id),
        ],
        'password_lama' => [
            'required',
            'current_password',
        ],
    ], [
        'email.required' => 'Email baru wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email tersebut sudah digunakan akun lain.',
        'password_lama.required' => 'Password saat ini wajib diisi.',
        'password_lama.current_password' => 'Password saat ini tidak sesuai.',
    ]);

    // Jika email tidak berubah, tidak perlu mengirim verifikasi ulang.
    if ($user->email === $validated['email']) {
        return back()->with('info', 'Email yang dimasukkan sama dengan email saat ini.');
    }

    // Email baru belum dianggap terverifikasi.
    $user->forceFill([
        'email' => $validated['email'],
        'email_verified_at' => null,
    ])->save();

    // Kirim tautan verifikasi ke email baru.
    $user->sendEmailVerificationNotification();

    return redirect()
        ->route('verification.notice', ['email' => $user->email])
        ->with('status', 'Email berhasil diperbarui. Silakan periksa inbox email baru untuk melakukan verifikasi.');
}

// Mengubah password akun pelamar
public function updatePassword(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'password_lama' => [
            'required',
            'current_password',
        ],
        'password_baru' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'different:password_lama',
        ],
    ], [
        'password_lama.required' => 'Password saat ini wajib diisi.',
        'password_lama.current_password' => 'Password saat ini tidak sesuai.',
        'password_baru.required' => 'Password baru wajib diisi.',
        'password_baru.min' => 'Password baru minimal 8 karakter.',
        'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
        'password_baru.different' => 'Password baru harus berbeda dari password saat ini.',
    ]);

    $user->forceFill([
        'password' => $validated['password_baru'],
    ])->save();

    return back()->with(
        'success_password',
        'Password berhasil diperbarui.'
    );
}
}