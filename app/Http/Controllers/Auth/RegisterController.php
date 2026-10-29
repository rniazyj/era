<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\ProfilPelamar;
use App\Models\Cv;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.daftar');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Cari ID role untuk pelamar
        $rolePelamar = Role::where('nama_role', 'pelamar')->first();

        DB::beginTransaction();
        try {
            // 1. Buat User baru
            $user = User::create([
                'id_role' => $rolePelamar ? $rolePelamar->id_role : 1,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status_akun' => 'aktif',
            ]);

            // 2. Buat otomatis baris profil_pelamar kosong
            $profil = ProfilPelamar::create([
                'id_pengguna' => $user->id,
            ]);

            // 3. Buat otomatis baris cv kosong terkait profil tersebut
            Cv::create([
                'id_profil' => $profil->id_profil,
                'sumber_cv' => 'manual',
                'progres_cv' => 0,
            ]);

            DB::commit();

            // Auto login setelah register
            auth()->login($user);

            return redirect()->route('pelamar.dashboard');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat registrasi: ' . $e->getMessage()])->withInput();
        }
    }
}