<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramMagang extends Model
{
    protected $table = 'program_magang';
    protected $primaryKey = 'id_program';

    protected $fillable = [
        'id_admin', 'nama_program', 'deskripsi', 'silabus', 'tgl_buka', 'tgl_tutup', 'tgl_mulai', 'tgl_selesai', 'durasi_hari', 'kuota', 'status'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'id');
    }

    public function persyaratan()
    {
        return $this->hasMany(PersyaratanBerkas::class, 'id_program', 'id_program');
    }

    public function lamaran()
    {
        return $this->hasMany(Lamaran::class, 'id_program', 'id_program');
    }
}