<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgramMagang;

class ProgramController extends Controller
{
    // Menampilkan daftar program magang
    public function index()
    {
        $program = ProgramMagang::where('status', 'aktif')->latest()->paginate(9);
        return view('pages.program.index', compact('program'));
    }

    // Menampilkan detail program magang tertentu berdasarkan ID
    public function show($id)
    {
        $program = ProgramMagang::with('persyaratan')->findOrFail($id);
        return view('pages.program.show', compact('program'));
    }
}