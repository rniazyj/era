<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Menghitung seluruh lamaran baru yang masih diajukan
        $totalPelamarBaru = Lamaran::where('status_lamaran', 'diajukan')
            ->count();

        // 2. Mengambil maksimal 10 lamaran terbaru
        $pelamarBaruList = Lamaran::with([
                'pelamar',
                'lowongan',
                'program',
            ])
            ->where('status_lamaran', 'diajukan')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // 3. Mengambil akun pelamar yang profil/CV-nya belum lengkap
        // Kriteria: belum punya profil, belum punya CV,
        // atau progres CV masih di bawah 100%.
        $userBelumLengkapList = User::query()
            ->whereHas('role', function ($query) {
                $query->where('nama_role', 'pelamar');
            })
            ->leftJoin(
                'profil_pelamar as pp',
                'pp.id_pengguna',
                '=',
                'users.id'
            )
            ->leftJoin(
                'cv as c',
                'c.id_profil',
                '=',
                'pp.id_profil'
            )
            ->where(function ($query) {
                $query->whereNull('pp.id_profil')
                    ->orWhereNull('c.id_cv')
                    ->orWhere('c.progres_cv', '<', 100);
            })
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.no_telepon',
            ])
            ->distinct()
            ->orderBy('users.name')
            ->get();

        return view('admin.dashboard', compact(
            'totalPelamarBaru',
            'pelamarBaruList',
            'userBelumLengkapList'
        ));
    }
}
