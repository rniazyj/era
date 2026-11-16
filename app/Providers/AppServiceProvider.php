<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use App\Listeners\CatatEventAutentikasi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    Event::listen(Login::class, [CatatEventAutentikasi::class, 'handleLogin']);

    Event::listen(Logout::class, [CatatEventAutentikasi::class, 'handleLogout']);

    Event::listen(Failed::class, [CatatEventAutentikasi::class, 'handleFailed']);

    Event::listen(Lockout::class, [CatatEventAutentikasi::class, 'handleLockout']);
}
}
