<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilPelamar extends Model
{
    protected $table = 'profil_pelamar';
    protected $primaryKey = 'id_profil';

    protected $fillable = [
        'id_pengguna',
        'foto',
        'bio',
        'jenis_kelamin',
        'tempat_lahir',
        'tgl_lahir',
        'alamat',
        'kota',
        'linkedin_url',
        'portofolio_url',
        'nik_terenkripsi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id');
    }

    public function cv()
    {
        return $this->hasOne(Cv::class, 'id_profil', 'id_profil');
    }

    /**
     * Kelengkapan data yang tampil di CV (PDF).
     * NIK sengaja tidak dihitung karena tidak dicetak di CV.
     * Kalau template CV bertambah bagian (pendidikan, pengalaman, skill),
     * cukup tambah satu baris di $cek dan persentase ikut menyesuaikan.
     */
    public function kelengkapan(): array
    {
        $cek = [
            'Nama lengkap'   => filled($this->user?->name),
            'Foto'           => filled($this->foto),
            'Ringkasan diri' => filled($this->bio),
            'Jenis kelamin'  => filled($this->jenis_kelamin),
            'Tempat lahir'   => filled($this->tempat_lahir),
            'Tanggal lahir'  => filled($this->tgl_lahir),
            'Kota'           => filled($this->kota),
            'Alamat'         => filled($this->alamat),
            'LinkedIn'       => filled($this->linkedin_url),
            'Portofolio'     => filled($this->portofolio_url),
            // Aktifkan kalau relasinya sudah ada (sesuaikan nama relasi):
            // 'Pendidikan'  => $this->exists && $this->pendidikan()->exists(),
            // 'Pengalaman'  => $this->exists && $this->pengalaman()->exists(),
            // 'Skill'       => $this->exists && $this->skill()->exists(),
        ];

        $terisi = collect($cek)->filter()->count();
        $total  = count($cek);

        return [
            'terisi' => $terisi,
            'total'  => $total,
            'persen' => (int) round($terisi / $total * 100),
            'kurang' => collect($cek)->reject()->keys()->all(),
        ];
    }
}