<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class LoginController extends Controller
{
    // Halaman yang tidak boleh dijadikan tujuan setelah login
    private const ABAIKAN = ['/login', '/daftar', '/lupa-password', '/reset-password', '/logout'];

    // Tampilkan halaman login
    public function showLoginForm(Request $request)
    {
        $this->simpanHalamanSebelumnya($request);

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
                $sisaMenit = (int) ceil(Carbon::now()->diffInMinutes($user->terkunci_sampai, true));
                return back()->withErrors(['email' => "Akun Anda terkunci sementara karena salah password berkali-kali. Coba lagi dalam {$sisaMenit} menit."]);
            }
        }

        // Coba autentikasi Laravel
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            // regenerate() mempertahankan isi session, jadi 'url.intended' tidak hilang
            $request->session()->regenerate();

            // Reset percobaan gagal jika login sukses
            $authUser = Auth::user();
            $authUser->update([
                'percobaan_gagal' => 0,
                'terkunci_sampai' => null
            ]);

            // Tujuan bawaan per role, dipakai kalau tidak ada halaman sebelumnya
            $default = match (true) {
                $authUser->isAdmin()    => route('admin.dashboard'),
                $authUser->isPimpinan() => route('pimpinan.dashboard'),
                $authUser->isPelamar()  => route('pelamar.dashboard'),
                default                 => route('home'),
            };

            // Kembali ke halaman yang tadi dibuka (mis. form lamar), kalau tidak ada baru ke $default
            return redirect()->intended($default);
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

    /**
     * Kalau pengunjung membuka login lewat tombol "Login" biasa (bukan lewat halaman yang
     * dilindungi middleware auth), Laravel belum menyimpan tujuan. Di sini kita simpan
     * halaman sebelumnya supaya setelah login ia kembali ke sana.
     */
    private function simpanHalamanSebelumnya(Request $request): void
    {
        // Sudah ada tujuan dari middleware auth (mis. klik "Lamar") -> jangan ditimpa
        if ($request->session()->has('url.intended')) {
            return;
        }

        $sebelumnya = url()->previous();
        $bagian = parse_url($sebelumnya);

        // Hanya halaman di situs ini sendiri (cegah open redirect)
        if (($bagian['host'] ?? null) !== $request->getHost()) {
            return;
        }

        $path = rtrim($bagian['path'] ?? '/', '/') ?: '/';

        foreach (self::ABAIKAN as $p) {
            if (str_starts_with($path, $p)) {
                return;
            }
        }

        $request->session()->put('url.intended', $sebelumnya);
    }
}