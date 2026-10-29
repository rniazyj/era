<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LowonganKerja extends Model
{
    protected $table = 'lowongan_kerja';
    protected $primaryKey = 'id_lowongan';

    protected $fillable = [
        'id_admin', 'judul', 'deskripsi', 'kualifikasi', 'kuota', 'tgl_buka', 'tgl_tutup', 'status'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'id');
    }

    public function persyaratan()
    {
        return $this->hasMany(PersyaratanBerkas::class, 'id_lowongan', 'id_lowongan');
    }

    public function lamaran()
    {
        return $this->hasMany(Lamaran::class, 'id_lowongan', 'id_lowongan');
    }
}