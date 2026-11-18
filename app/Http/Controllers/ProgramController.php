<?php

namespace App\Http\Controllers;

use App\Models\ProgramMagang;

class ProgramController extends Controller
{
    // Menampilkan daftar program magang
    public function index()
{
    $programs = ProgramMagang::where('status', 'dibuka')
        ->with([
            'lamaran' => function ($query) {
                $query->where('status_lamaran', 'diterima')
                    ->withCount('anggotaTim');
            },
        ])
        ->latest()
        ->get();

    $programs->each(function ($program) {
        $kuotaTerpakai = $program->lamaran->sum(function ($lamaran) {
            return $lamaran->anggota_tim_count > 0
                ? $lamaran->anggota_tim_count
                : 1;
        });

        $program->setAttribute(
            'sisa_kuota',
            max(0, (int) ($program->kuota ?? 0) - $kuotaTerpakai)
        );
    });

    return view('pages.program.index', compact('programs'));
}

    // Menampilkan detail program magang
    public function show($id)
{
    $program = ProgramMagang::with([
        'persyaratan',
        'lamaran' => function ($query) {
            $query->where('status_lamaran', 'diterima')
                ->withCount('anggotaTim');
        },
    ])->findOrFail($id);

    $kuotaTerpakai = $program->lamaran->sum(function ($lamaran) {
        return $lamaran->anggota_tim_count > 0
            ? $lamaran->anggota_tim_count
            : 1;
    });

    $program->setAttribute(
        'sisa_kuota',
        max(0, (int) ($program->kuota ?? 0) - $kuotaTerpakai)
    );

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