<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersyaratanBerkas extends Model
{
    protected $table = 'persyaratan_berkas';
    protected $primaryKey = 'id_persyaratan';

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
        return $this->belongsTo(
            LowonganKerja::class,
            'id_lowongan',
            'id_lowongan'
        );
    }

    public function program()
    {
        return $this->belongsTo(
            ProgramMagang::class,
            'id_program',
            'id_program'
        );
    }

    public function berkasLamaran()
    {
        return $this->hasMany(
            BerkasLamaran::class,
            'id_persyaratan',
            'id_persyaratan'
        );
    }

    public function getFormatListAttribute()
    {
        return collect(explode(',', $this->format_file ?? 'pdf'))
            ->map(fn ($format) => strtolower(trim($format)))
            ->filter()
            ->values()
            ->all();
    }
}
