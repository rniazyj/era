<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = optional($user->role)->nama_role;

        // Jika role user tidak ada dalam daftar izin, abort 403
        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized action. Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}