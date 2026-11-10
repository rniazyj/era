<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TentangController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\BantuanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
<<<<<<< HEAD
use App\Http\Controllers\Pelamar\ProfilController as PelamarProfilController;
use App\Http\Controllers\Pelamar\ProgramMagangController;
=======
use App\Http\Controllers\Pelamar\ProfilController;
use App\Http\Controllers\Pelamar\AkademikController;
use App\Http\Controllers\Pelamar\PengalamanController;
use App\Http\Controllers\Pelamar\SkillController;
use App\Http\Controllers\Pelamar\CvController;
>>>>>>> vina
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLowonganController;
use App\Http\Controllers\Admin\SeleksiController;
use App\Http\Controllers\Admin\AdminProfilController;
use App\Http\Controllers\Pimpinan\PimpinanDashboardController;

// 1. RUTE PUBLIK
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [TentangController::class, 'perusahaan'])->name('tentang');
Route::get('/tentang/tim', [TentangController::class, 'tim'])->name('tentang.tim');
Route::get('/tentang/legalitas', [TentangController::class, 'legalitas'])->name('tentang.legalitas');
Route::get('/tentang/cabang', [TentangController::class, 'cabang'])->name('tentang.cabang');
Route::get('/tentang/produk', [TentangController::class, 'produk'])->name('tentang.produk');
Route::get('/lowongan', [LowonganController::class, 'index'])->name('lowongan.index');
Route::get('/lowongan/{id}', [LowonganController::class, 'show'])->name('lowongan.show');
Route::get('/internship', [ProgramController::class, 'index'])
    ->name('internship.index');
Route::get('/internship/{id}', [ProgramController::class, 'show'])
    ->name('internship.show');
Route::get('/bantuan', [BantuanController::class, 'index'])->name('bantuan');

// 2. RUTE AUTHENTICATION & PASSWORD RESET (Guest Only)
Route::middleware('guest')->group(function () {
    // Login & Register
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/daftar', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'register']);

    // Lupa & Reset Password
    Route::get('/lupa-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Logout (Auth Only)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// 3. RUTE ROLE: ADMIN / HR
Route::middleware(['auth', 'role:admin_hr'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [AdminProfilController::class, 'show'])
        ->name('profil');

    Route::put('/profil', [AdminProfilController::class, 'update'])
        ->name('profil.update');

    Route::put('/profil/password', [AdminProfilController::class, 'updatePassword'])
        ->name('profil.password');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/lowongan', [AdminLowonganController::class, 'index'])->name('lowongan.index');
    Route::get('/lowongan/create', [AdminLowonganController::class, 'create'])->name('lowongan.create');
    Route::post('/lowongan', [AdminLowonganController::class, 'store'])->name('lowongan.store');
    Route::get('/lowongan/{jenis}/{id}/edit', [AdminLowonganController::class, 'edit'])
        ->whereIn('jenis', ['kerja', 'magang'])
        ->whereNumber('id')
        ->name('lowongan.edit');
    Route::put('/lowongan/{jenis}/{id}', [AdminLowonganController::class, 'update'])
        ->whereIn('jenis', ['kerja', 'magang'])
        ->whereNumber('id')
        ->name('lowongan.update');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/pelamar', [SeleksiController::class, 'index'])->name('pelamar.index');
    Route::get('/pelamar/{id}', [SeleksiController::class, 'show'])->whereNumber('id')->name('pelamar.show');
    Route::patch('/pelamar/{id}/status', [SeleksiController::class, 'updateStatus'])->whereNumber('id')->name('pelamar.status');
    Route::post('/pelamar/{id}/jadwal', [SeleksiController::class, 'simpanJadwal'])->whereNumber('id')->name('pelamar.jadwal');
});

// 4. RUTE ROLE: PIMPINAN
Route::middleware(['auth', 'role:pimpinan'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    Route::get('/dashboard', [PimpinanDashboardController::class, 'index'])->name('dashboard');
});

// 5. RUTE ROLE: PELAMAR
Route::middleware(['auth', 'role:pelamar'])->group(function () {
<<<<<<< HEAD
    // Tambahkan baris rute dashboard ini:
    Route::get('/dashboard', [\App\Http\Controllers\Pelamar\PelamarDashboardController::class, 'index'])->name('pelamar.dashboard');
    
    Route::get('/lamaran/create', [\App\Http\Controllers\Pelamar\LamaranController::class, 'create'])->name('pelamar.lamaran.create');
    Route::post('/lamaran', [\App\Http\Controllers\Pelamar\LamaranController::class, 'store'])->name('pelamar.lamaran.store');

    // Rute profil yang sudah ada sebelumnya biarkan saja:
    Route::get('/profil', [PelamarProfilController::class, 'show'])->name('pelamar.profil');
    Route::put('/profil', [PelamarProfilController::class, 'update'])->name('pelamar.profil.update');
=======

    Route::prefix('profil')->group(function () {
        // Data diri & profil (index.blade.php)
        Route::get('/', [ProfilController::class, 'show'])->name('pelamar.profil');
        Route::put('/', [ProfilController::class, 'update'])->name('pelamar.profil.update');

        // Data akademik
        Route::get('/akademik', [AkademikController::class, 'index'])->name('pelamar.profil.akademik');
        Route::post('/akademik', [AkademikController::class, 'store'])->name('pelamar.profil.akademik.store');
        Route::put('/akademik/{id}', [AkademikController::class, 'update'])->whereNumber('id')->name('pelamar.profil.akademik.update');
        Route::delete('/akademik/{id}', [AkademikController::class, 'destroy'])->whereNumber('id')->name('pelamar.profil.akademik.destroy');

        // Pengalaman: organisasi | profesional | proyek
        Route::get('/pengalaman', [PengalamanController::class, 'index'])->name('pelamar.profil.pengalaman');
        Route::post('/pengalaman/{jenis}', [PengalamanController::class, 'store'])
            ->where('jenis', 'organisasi|profesional|proyek')->name('pelamar.profil.pengalaman.store');
        Route::put('/pengalaman/{jenis}/{id}', [PengalamanController::class, 'update'])
            ->where(['jenis' => 'organisasi|profesional|proyek', 'id' => '[0-9]+'])->name('pelamar.profil.pengalaman.update');
        Route::delete('/pengalaman/{jenis}/{id}', [PengalamanController::class, 'destroy'])
            ->where(['jenis' => 'organisasi|profesional|proyek', 'id' => '[0-9]+'])->name('pelamar.profil.pengalaman.destroy');

        // Skill & keahlian
        Route::get('/skill', [SkillController::class, 'index'])->name('pelamar.profil.skill');
        Route::post('/skill', [SkillController::class, 'store'])->name('pelamar.profil.skill.store');
        Route::delete('/skill/{id}', [SkillController::class, 'destroy'])->whereNumber('id')->name('pelamar.profil.skill.destroy');

        // Curriculum Vitae + PDF
        Route::get('/cv', [CvController::class, 'show'])->name('pelamar.profil.cv');
        Route::get('/cv/pdf', [CvController::class, 'pdf'])->name('pelamar.profil.cv.pdf');
    });
>>>>>>> vina

});