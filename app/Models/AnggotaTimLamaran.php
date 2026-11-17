<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggotaTimLamaran extends Model
{
    protected $table = 'anggota_tim_lamaran';

    protected $primaryKey = 'id_anggota';

    protected $fillable = [
        'id_lamaran',
        'id_pengguna',
        'nama',
        'email',
        'no_telepon',
        'npm',
        'institusi',
        'fakultas',
        'program_studi',
        'jenjang',
        'is_ketua',
    ];

    protected $casts = [
        'is_ketua' => 'boolean',
    ];

    public function lamaran()
    {
        return $this->belongsTo(
            Lamaran::class,
            'id_lamaran',
            'id_lamaran'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_pengguna',
            'id'
        );
    }

    public function berkas()
    {
        return $this->hasMany(
            BerkasLamaran::class,
            'id_anggota',
            'id_anggota'
        );
    }
}
