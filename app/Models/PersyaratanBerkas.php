<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersyaratanBerkas extends Model
{
    protected $table = 'persyaratan_berkas';
    protected $primaryKey = 'id_persyaratan';

    // Tabel tidak punya created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'id_lowongan',
        'id_program',
        'nama_berkas',
        'format_file',
        'wajib',
    ];

    protected $casts = [
        'wajib' => 'boolean',
    ];

    public function lowongan()
    {
        return $this->belongsTo(LowonganKerja::class, 'id_lowongan', 'id_lowongan');
    }

    public function program()
    {
        return $this->belongsTo(ProgramMagang::class, 'id_program', 'id_program');
    }

    // Daftar format yang diizinkan (dipakai untuk validasi upload pelamar)
    public function getFormatListAttribute(): string
    {
        return filled($this->format_file) ? $this->format_file : 'pdf';
    }

    // Kolom maks_kb tidak ada di tabel, jadi pakai batas bawaan 2 MB
    public function getMaksKbAttrAttribute(): int
    {
        return 2048;
    }
}