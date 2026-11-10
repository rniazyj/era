<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    public const LEVEL = ['Beginner', 'Intermediate', 'Advanced', 'Expert'];

    protected $table = 'skill';
    protected $primaryKey = 'id_skill';
    public $timestamps = false;

    // kategori_skill: hard | soft | tools
    protected $fillable = ['id_cv', 'kategori_skill', 'nama_skill', 'level_skill'];

    public function cv()
    {
        return $this->belongsTo(Cv::class, 'id_cv', 'id_cv');
    }
}