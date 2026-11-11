<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogKirim extends Model
{
    protected $table = 'log_kirim';
    protected $primaryKey = 'id_kirim';

    // Tabel log_kirim tidak punya kolom created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'id_lamaran',
        'id_surat',
        'kanal',
        'tujuan',
        'jenis_pesan',
        'status_kirim',
        'waktu_kirim',
    ];

    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class, 'id_lamaran', 'id_lamaran');
    }

    public function surat()
    {
        return $this->belongsTo(SuratPenerimaan::class, 'id_surat', 'id_surat');
    }
}