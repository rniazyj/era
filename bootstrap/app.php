<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan alias middleware role di sini
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'catat.aktivitas' => \App\Http\Middleware\CatatAktivitas::class,
        ]);

        // User yang sudah login lalu membuka /login, /daftar, dst:
        // admin & pimpinan ke dashboard masing-masing, pelamar/user biasa ke home.
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();

            if ($user && $user->isAdmin()) {
                return route('admin.dashboard');
            }
            if ($user && $user->isPimpinan()) {
                return route('pimpinan.dashboard');
            }

            return route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();