<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilPelamar extends Model
{
    protected $table = 'profil_pelamar';
    protected $primaryKey = 'id_profil';

    protected $fillable = [
        'id_pengguna',
        'foto',
        'jenis_kelamin',
        'tempat_lahir',
        'tgl_lahir',
        'alamat',
        'kota',
        'linkedin_url',
        'portofolio_url',
        'nik_terenkripsi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id');
    }

    public function cv()
    {
        return $this->hasOne(Cv::class, 'id_profil', 'id_profil');
    }
}