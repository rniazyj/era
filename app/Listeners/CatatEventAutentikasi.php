<?php

namespace App\Listeners;

use App\Models\LogAktivitas;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class CatatEventAutentikasi
{
    public function handleLogin(Login $event): void
    {
        LogAktivitas::catat(
            'Login berhasil',
            'Autentikasi',
            'berhasil',
            $event->user->getAuthIdentifier()
        );
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            LogAktivitas::catat(
                'Logout',
                'Autentikasi',
                'berhasil',
                $event->user->getAuthIdentifier()
            );
        }
    }

    public function handleFailed(Failed $event): void
    {
        $email = $event->credentials['email'] ?? null;

        LogAktivitas::catat(
            'Percobaan login gagal' . ($email ? ' untuk ' . LogAktivitas::samarkanEmail($email) : ''),
            'Autentikasi',
            'gagal',
            $event->user?->getAuthIdentifier()
        );
    }

    public function handleLockout(Lockout $event): void
    {
        LogAktivitas::catat(
            'Login diblokir karena terlalu banyak percobaan',
            'Autentikasi',
            'gagal',
            null
        );
    }
}