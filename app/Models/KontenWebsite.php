<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KontenWebsite extends Model
{
    // Nilai kolom status_tayang untuk konten yang ditampilkan.
    // Sesuaikan kalau di databasemu memakai kata lain (mis. 'published', 'aktif').
    public const STATUS_TAYANG = 'tayang';

    protected $table = 'konten_website';
    protected $primaryKey = 'id_konten';

    protected $fillable = ['id_admin', 'judul', 'jenis_konten', 'isi_konten', 'status_tayang'];

    public function scopeTayang(Builder $q): Builder
    {
        return $q->where('status_tayang', self::STATUS_TAYANG);
    }
}