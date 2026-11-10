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
}