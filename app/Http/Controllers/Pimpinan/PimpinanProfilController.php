<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PimpinanProfilController extends Controller
{
    // Menampilkan halaman profil pimpinan
    public function show()
    {
        $user = Auth::user();

        return view('pimpinan.profil', compact('user'));
    }

    // Mengubah informasi akun pimpinan
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'no_telepon' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);

        $user->update($validated);

        return redirect()
            ->route('pimpinan.profil')
            ->with('success', 'Informasi akun berhasil diperbarui.');
    }

    // Mengubah password pimpinan
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

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
            ],
        ], [
            'password_lama.current_password' =>
                'Password saat ini tidak sesuai.',

            'password_baru.min' =>
                'Password baru minimal 8 karakter.',

            'password_baru.confirmed' =>
                'Konfirmasi password baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password_baru']),
        ]);

        return redirect()
            ->route('pimpinan.profil')
            ->with('success_password', 'Password berhasil diperbarui.');
    }
}