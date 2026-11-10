<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Legalitas extends Model
{
    protected $table = 'legalitas';
    protected $primaryKey = 'id_legalitas';

    protected $fillable = ['lembaga', 'singkatan', 'nomor_izin', 'deskripsi', 'urutan', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true)->orderBy('urutan')->orderBy('id_legalitas');
    }
}