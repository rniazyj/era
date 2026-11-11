<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPenerimaan extends Model
{
    protected $table = 'surat_penerimaan';
    protected $primaryKey = 'id_surat';

    protected $fillable = [
        'id_lamaran',
        'id_template',
        'nomor_surat',
        'tgl_terbit',
        'file_surat',
        'status_penerbitan',
    ];

    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class, 'id_lamaran', 'id_lamaran');
    }

    public function kirim()
    {
        return $this->hasMany(LogKirim::class, 'id_surat', 'id_surat');
    }
}