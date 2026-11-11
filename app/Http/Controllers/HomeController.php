<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganKerja;
use App\Models\ProgramMagang;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil data lowongan & program yang aktif untuk ditampilkan di beranda
        $lowongan = LowonganKerja::whereIn('status', ['aktif', 'dibuka'])->take(6)->get();
        $program = ProgramMagang::whereIn('status', ['aktif', 'dibuka'])->take(6)->get();

        return view('home', compact('lowongan', 'program'));
    }
}