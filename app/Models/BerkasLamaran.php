<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BerkasLamaran extends Model
{
    protected $table = 'berkas_lamaran';
    protected $primaryKey = 'id_berkas';

    protected $fillable = [
        'id_lamaran',
        'id_persyaratan',
        'nama_file',
        'path_file',
        'status_verifikasi',
        'catatan_verifikasi',
        'algoritma_enkripsi',
        'iv_nonce',
    ];

    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class, 'id_lamaran', 'id_lamaran');
    }

    public function persyaratan()
    {
        return $this->belongsTo(PersyaratanBerkas::class, 'id_persyaratan', 'id_persyaratan');
    }
}