<?php

namespace App\Http\Controllers;

use App\Models\ProgramMagang;

class ProgramController extends Controller
{
    // Menampilkan daftar program magang
    public function index()
    {
        $programs = ProgramMagang::whereIn('status', ['aktif', 'dibuka'])
            ->latest()
            ->paginate(9);

        return view('pages.program.index', compact('programs'));
    }

    // Menampilkan detail program magang
    public function show($id)
    {
        $program = ProgramMagang::with('persyaratan')
            ->findOrFail($id);

        $silabusAda = ! empty($program->silabus)
            && \Illuminate\Support\Facades\Storage::disk('public')
                ->exists($program->silabus);

        $silabusUrl = $silabusAda
            ? \Illuminate\Support\Facades\Storage::disk('public')
                ->url($program->silabus)
            : null;

        return view('pages.program.show', compact(
            'program',
            'silabusAda',
            'silabusUrl'
        ));
    }
}