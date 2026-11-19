<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Role;
use App\Models\ProfilPelamar;
use App\Models\Cv;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.daftar');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        // Role pelamar wajib tersedia.
        // Jangan menggunakan fallback ID role.
        $rolePelamar = Role::where('nama_role', 'pelamar')->firstOrFail();

        DB::transaction(function () use ($validated, $rolePelamar, &$user) {

            // Membuat akun pelamar.
            // Password otomatis di-hash melalui cast pada model User.
            $user = User::create([
                'id_role' => $rolePelamar->id_role,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'status_akun' => 'aktif',
                'email_verified_at' => null,
                'percobaan_gagal' => 0,
                'terkunci_sampai' => null,
            ]);

            // Membuat profil pelamar kosong.
            $profil = ProfilPelamar::create([
                'id_pengguna' => $user->id,
            ]);

            // Membuat CV kosong.
            Cv::create([
                'id_profil' => $profil->id_profil,
                'sumber_cv' => 'manual',
                'progres_cv' => 0,
            ]);
        });

        // Mengirim email verifikasi melalui notifikasi bawaan Laravel.
        event(new Registered($user));

        return redirect()
            ->route('verification.notice', ['email' => $user->email])
            ->with('status', 'Registrasi berhasil! Silakan periksa email Anda untuk melakukan verifikasi akun.');
    }
}
