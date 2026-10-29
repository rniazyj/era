<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganKerja;

class LowonganController extends Controller
{
    // Menampilkan daftar seluruh lowongan pekerjaan
    public function index()
    {
        $lowongan = LowonganKerja::where('status', 'aktif')->latest()->paginate(9);
        return view('pages.lowongan.index', compact('lowongan'));
    }

    // Menampilkan detail lowongan tertentu berdasarkan ID
    public function show($id)
    {
        $lowongan = LowonganKerja::with('persyaratan')->findOrFail($id);
        return view('pages.lowongan.show', compact('lowongan'));
    }
}