<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataAkademik extends Model
{
    protected $table = 'data_akademik';
    protected $primaryKey = 'id_akademik';

    // Tabel data_akademik tidak punya kolom created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'id_cv',
        'jenjang',
        'institusi',
        'jurusan',
        'ipk',
        'tahun_masuk',
        'tahun_lulus',
        'deskripsi',
    ];
}