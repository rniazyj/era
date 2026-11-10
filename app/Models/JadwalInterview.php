<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalInterview extends Model
{
    protected $table = 'jadwal_interview';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'id_lamaran',
        'tgl_interview',
        'jam_mulai',
        'lokasi_atau_link',
        'status_jadwal',
        'catatan',
    ];

    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class, 'id_lamaran', 'id_lamaran');
    }
}