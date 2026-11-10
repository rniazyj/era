<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TonggakSejarah extends Model
{
    protected $table = 'tonggak_sejarah';
    protected $primaryKey = 'id_tonggak';

    protected $fillable = ['tahun', 'judul', 'deskripsi', 'urutan'];
}