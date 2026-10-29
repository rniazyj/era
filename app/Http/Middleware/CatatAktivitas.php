<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LogAktivitas;
use Symfony\Component\HttpFoundation\Response;

class CatatAktivitas
{
    public function handle(Request $request, Closure $next, $modul = 'Umum'): Response
    {
        $response = $next($request);

        if (auth()->check()) {
            LogAktivitas::create([
                'id_pengguna' => auth()->id(),
                'aktivitas' => 'Akses halaman: ' . $request->path(),
                'modul' => $modul,
                'status_aksi' => 'berhasil',
                'alamat_ip' => $request->ip(),
            ]);
        }

        return $response;
    }
}