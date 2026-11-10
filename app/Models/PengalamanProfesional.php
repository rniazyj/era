<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengalamanProfesional extends Model
{
    protected $table = 'pengalaman_profesional';
    protected $primaryKey = 'id_pengalaman';
    public $timestamps = false;

    protected $fillable = [
        'id_cv', 'nama_perusahaan', 'posisi', 'jenis_kerja', 'lokasi',
        'tgl_mulai', 'tgl_selesai', 'masih_berjalan', 'deskripsi_tugas',
    ];

    protected $casts = [
        'tgl_mulai'      => 'date',
        'tgl_selesai'    => 'date',
        'masih_berjalan' => 'boolean',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'id_cv', 'id_cv');
    }
}