<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KantorCabang extends Model
{
    protected $table = 'kantor_cabang';
    protected $primaryKey = 'id_cabang';

    protected $fillable = ['nama_kantor', 'kota', 'alamat', 'telepon', 'tautan_peta', 'is_pusat', 'urutan', 'aktif'];
    protected $casts = ['is_pusat' => 'boolean', 'aktif' => 'boolean'];

    // Kantor pusat selalu paling atas
    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true)->orderByDesc('is_pusat')->orderBy('urutan')->orderBy('id_cabang');
    }

    // Pakai tautan_peta kalau diisi, kalau tidak buat otomatis dari alamat
    public function getLinkPetaAttribute(): string
    {
        return filled($this->tautan_peta)
            ? $this->tautan_peta
            : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($this->nama_kantor . ' ' . $this->alamat);
    }
}