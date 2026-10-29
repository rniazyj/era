<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    protected $table = 'cv';
    protected $primaryKey = 'id_cv';

    protected $fillable = [
        'id_profil',
        'sumber_cv',
        'file_cv_unggahan',
        'ringkasan_profil',
        'progres_cv',
    ];

    public function profil()
    {
        return $this->belongsTo(ProfilPelamar::class, 'id_profil', 'id_profil');
    }

    public function dataAkademik()
    {
        return $this->hasMany(DataAkademik::class, 'id_cv', 'id_cv');
    }

    public function pengalamanProfesional()
    {
        return $this->hasMany(PengalamanProfesional::class, 'id_cv', 'id_cv');
    }

    public function pengalamanOrganisasi()
    {
        return $this->hasMany(PengalamanOrganisasi::class, 'id_cv', 'id_cv');
    }

    public function proyek()
    {
        return $this->hasMany(Proyek::class, 'id_cv', 'id_cv');
    }

    public function skill()
    {
        return $this->hasMany(Skill::class, 'id_cv', 'id_cv');
    }
}