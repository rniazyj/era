<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProdukLayanan extends Model
{
    protected $table = 'produk_layanan';
    protected $primaryKey = 'id_produk';

    protected $fillable = ['kode', 'nama', 'kategori', 'deskripsi', 'urutan', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true)->orderBy('urutan')->orderBy('id_produk');
    }
}