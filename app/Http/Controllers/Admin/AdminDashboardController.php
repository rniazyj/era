<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalInterview;
use App\Models\Lamaran;
use App\Models\LowonganKerja;
use App\Models\ProgramMagang;
use App\Models\User;
use Illuminate\Support\Carbon;

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

        $ringkasanStatus = Lamaran::query()
            ->selectRaw("COALESCE(status_lamaran, 'diajukan') as status, COUNT(*) as jumlah")
            ->groupBy('status_lamaran')
            ->pluck('jumlah', 'status')
            ->toArray();

        $lowonganAktif = LowonganKerja::where('status', 'dibuka')->count()
            + ProgramMagang::where('status', 'dibuka')->count();

        $jadwalHariIni = JadwalInterview::whereDate('tgl_interview', today())
            ->where('status_jadwal', JadwalInterview::TERJADWAL)
            ->count();

        $jadwalPekanIni = JadwalInterview::whereBetween('tgl_interview', [today(), today()->addDays(7)])
            ->where('status_jadwal', JadwalInterview::TERJADWAL)
            ->count();

        $jadwalPerluTindakLanjut = JadwalInterview::whereDate('tgl_interview', '<', today())
            ->where('status_jadwal', JadwalInterview::TERJADWAL)
            ->count();

        $trenLamaran = collect(range(6, 0))->map(function (int $offset) {
            $tanggal = today()->subDays($offset);

            return [
                'label' => $tanggal->locale('id')->isoFormat('ddd'),
                'jumlah' => Lamaran::whereDate('tgl_pengajuan', $tanggal)->count(),
            ];
        })->values();

        $jadwalTerbaru = JadwalInterview::with(['lamaran.pelamar', 'lamaran.lowongan', 'lamaran.program'])
            ->whereBetween('tgl_interview', [today(), today()->addDays(14)])
            ->where('status_jadwal', JadwalInterview::TERJADWAL)
            ->orderBy('tgl_interview')
            ->orderBy('jam_mulai')
            ->limit(4)
            ->get();

        $pelamarDiproses = Lamaran::where('status_lamaran', 'diproses')->count();
        $pelamarDiterima = Lamaran::where('status_lamaran', 'diterima')->count();

        return view('admin.dashboard', compact(
            'totalPelamarBaru',
            'pelamarBaruList',
            'userBelumLengkapList',
            'ringkasanStatus',
            'lowonganAktif',
            'jadwalHariIni',
            'jadwalPekanIni',
            'jadwalPerluTindakLanjut',
            'trenLamaran',
            'jadwalTerbaru',
            'pelamarDiproses',
            'pelamarDiterima'
        ));
    }
}
