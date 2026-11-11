<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ASUMSI kolom tabel persyaratan_berkas:
 *   id_persyaratan (PK), id_lowongan, nama_berkas, keterangan (nullable),
 *   wajib (boolean), format (mis. "pdf,jpg,png"), maks_kb (integer)
 * Kalau modelmu sudah ada, cukup samakan nama kolom di LowonganController.
 */
class PersyaratanBerkas extends Model
{
    protected $table = 'persyaratan_berkas';
    protected $primaryKey = 'id_persyaratan';
    public $timestamps = false;

    protected $fillable = [
        'id_lowongan',
        'id_program',
        'nama_berkas',
        'keterangan',
        'wajib',
        'format_file',
        'maks_kb',
    ];
    protected $casts = ['wajib' => 'boolean', 'maks_kb' => 'integer'];

    public function lowongan()
    {
        return $this->belongsTo(LowonganKerja::class, 'id_lowongan', 'id_lowongan');
    }

    public function getFormatListAttribute(): string
    {
        return filled($this->format_file) ? $this->format_file : 'pdf,jpg,jpeg,png';
    }

    public function getMaksKbAttrAttribute(): int
    {
        return $this->maks_kb ?: 2048;
    }
}