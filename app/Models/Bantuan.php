<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bantuan extends Model
{
    protected $table = 'bantuan';
    protected $primaryKey = 'id_bantuan';

    protected $fillable = [
        'kategori',
        'pertanyaan',
        'jawaban',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'urutan' => 'integer',
    ];

    /** Hanya entri yang ditampilkan ke publik */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}