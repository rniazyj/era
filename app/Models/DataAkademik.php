<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataAkademik extends Model
{
    protected $table = 'data_akademik';
    protected $primaryKey = 'id_akademik';
    public $timestamps = false; // Karena tabel ini tidak punya kolom created_at/updated_at di SQL kamu

    protected $fillable = [
        'id_cv', 'jenjang', 'institusi', 'jurusan', 'ipk', 'tahun_masuk', 'tahun_lulus', 'deskripsi'
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'id_cv', 'id_cv');
    }
}