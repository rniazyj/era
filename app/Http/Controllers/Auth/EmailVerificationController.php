<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    // Halaman pemberitahuan verifikasi.
    public function notice(Request $request)
    {
        return view('auth.verify-email', [
            'email' => $request->query('email'),
        ]);
    }

    // Memproses tautan verifikasi dari email.
    public function verify(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        // Pastikan hash sesuai dengan email pengguna.
        abort_unless(
            hash_equals(
                sha1($user->getEmailForVerification()),
                $hash
            ),
            403,
            'Tautan verifikasi tidak valid.'
        );

        // Hanya akun pelamar yang menggunakan verifikasi publik.
        abort_unless(
    $user->isPelamar() ||
    $user->isAdmin() ||
    $user->isPimpinan(),
    403,
    'Role akun tidak diizinkan melakukan verifikasi.'
);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()
            ->route('login')
            ->with('status', 'Email berhasil diverifikasi! Sekarang Anda dapat login ke akun ERA.');
    }

    // Mengirim ulang email verifikasi.
    public function resend(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (
    $user &&
    ($user->isPelamar() || $user->isAdmin() || $user->isPimpinan()) &&
    ! $user->hasVerifiedEmail()
) {
    $user->sendEmailVerificationNotification();
}

        // Pesan dibuat umum untuk mencegah pengungkapan email terdaftar.
        return back()->with(
            'status',
            'Jika email tersebut terdaftar dan belum diverifikasi, tautan verifikasi akan dikirimkan.'
        );
    }
}
