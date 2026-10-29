<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class LoginController extends Controller
{
    // Tampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Cek apakah akun sedang dikunci karena terlalu banyak percobaan gagal
            if ($user->terkunci_sampai && Carbon::now()->lt($user->terkunci_sampai)) {
                $sisaMenit = Carbon::now()->diffInMinutes($user->terkunci_sampai) + 1;
                return back()->withErrors(['email' => "Akun Anda terkunci sementara karena salah password berkali-kali. Coba lagi dalam {$sisaMenit} menit."]);
            }
        }

        // Coba autentikasi Laravel
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Reset percobaan gagal jika login sukses
            $authUser = Auth::user();
            $authUser->update([
                'percobaan_gagal' => 0,
                'terkunci_sampai' => null
            ]);

            // Arahkan berdasarkan role
            if ($authUser->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($authUser->isPimpinan()) {
                return redirect()->route('pimpinan.dashboard');
            } else {
                return redirect()->route('pelamar.dashboard');
            }
        }

        // Jika login GAGAL, tangani hitungan percobaan gagal
        if ($user) {
            $user->increment('percobaan_gagal');

            // Jika sudah 5 kali gagal, kunci akun selama 15 menit
            if ($user->percobaan_gagal >= 5) {
                $user->update([
                    'terkunci_sampai' => Carbon::now()->addMinutes(15)
                ]);
                return back()->withErrors(['email' => 'Akun Anda dikunci selama 15 menit akibat 5x salah password.']);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}