<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengalamanOrganisasi extends Model
{
    protected $table = 'pengalaman_organisasi';
    protected $primaryKey = 'id_organisasi';
    public $timestamps = false;

    protected $fillable = [
        'id_cv', 'nama_organisasi', 'jabatan', 'kategori',
        'tgl_mulai', 'tgl_selesai', 'masih_berjalan', 'deskripsi',
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