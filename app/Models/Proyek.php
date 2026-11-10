<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    protected $table = 'proyek';
    protected $primaryKey = 'id_proyek';
    public $timestamps = false;

    protected $fillable = [
        'id_cv', 'nama_proyek', 'peran', 'teknologi',
        'tgl_mulai', 'tgl_selesai', 'tautan_proyek', 'deskripsi',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'id_cv', 'id_cv');
    }
}