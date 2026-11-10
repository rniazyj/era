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
        'jenis_kelamin',
        'tempat_lahir',
        'tgl_lahir',
        'alamat',
        'kota',
        'linkedin_url',
        'instagram_url',
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
     * Kelengkapan = apa saja yang benar-benar tampil di CV (PDF).
     * NIK tidak dihitung karena tidak dicetak di CV.
     * Mau menambah bagian CV baru? Tambah satu baris di $cek.
     */
    public function kelengkapan(): array
    {
        $cv = $this->exists ? $this->cv : null;

        $ada   = fn (string $relasi): bool => $cv !== null && $cv->{$relasi}()->exists();
        $skill = fn (string $jenis): bool => $cv !== null && $cv->skill()->where('kategori_skill', $jenis)->exists();

        $cek = [
            'Nama lengkap'        => filled($this->user?->name),
            'No. telepon'         => filled($this->user?->no_telepon),
            'Foto'                => filled($this->foto),
            'Ringkasan diri'      => filled($cv?->ringkasan_profil),
            'Jenis kelamin'       => filled($this->jenis_kelamin),
            'Tempat lahir'        => filled($this->tempat_lahir),
            'Tanggal lahir'       => filled($this->tgl_lahir),
            'Kota'                => filled($this->kota),
            'Alamat'              => filled($this->alamat),
            'Media sosial'        => filled($this->linkedin_url) || filled($this->instagram_url),
            'Pendidikan'          => $ada('dataAkademik'),
            'Pengalaman / proyek' => $ada('pengalamanOrganisasi') || $ada('pengalamanProfesional') || $ada('proyek'),
            'Hard skill'          => $skill('hard'),
            'Soft skill'          => $skill('soft'),
            'Tools'               => $skill('tools'),
        ];

        $terisi = collect($cek)->filter()->count();
        $total  = count($cek);

        return [
            'terisi' => $terisi,
            'total'  => $total,
            'persen' => (int) round($terisi / $total * 100),
            'kurang' => collect($cek)->reject()->keys()->all(),
            'item'   => $cek,
        ];
    }
}