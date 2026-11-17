<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    protected $table = 'template_surat';
    protected $primaryKey = 'id_template';

    protected $fillable = [
        'nama_template',
        'isi_template',
        'instruksi_penerbitan',
    ];

    public function suratPenerimaan()
    {
        return $this->hasMany(
            SuratPenerimaan::class,
            'id_template',
            'id_template'
        );
    }
}
