<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ASUMSI kolom tabel berkas_lamaran:
 *   id_berkas (PK), id_lamaran, id_persyaratan, nama_file, path_file
 */
class BerkasLamaran extends Model
{
    protected $table = 'berkas_lamaran';
    protected $primaryKey = 'id_berkas';

    protected $fillable = ['id_lamaran', 'id_persyaratan', 'nama_file', 'path_file'];

    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class, 'id_lamaran', 'id_lamaran');
    }

    public function persyaratan()
    {
        return $this->belongsTo(PersyaratanBerkas::class, 'id_persyaratan', 'id_persyaratan');
    }
}