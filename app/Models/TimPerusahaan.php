<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TimPerusahaan extends Model
{
    protected $table = 'tim_perusahaan';
    protected $primaryKey = 'id_tim';

    protected $fillable = ['nama', 'jabatan', 'divisi', 'foto', 'bio', 'linkedin_url', 'urutan', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true)->orderBy('urutan')->orderBy('id_tim');
    }
}