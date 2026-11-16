<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    protected $table = 'lamaran';
    protected $primaryKey = 'id_lamaran';

    protected $fillable = [
        'id_pelamar', 'id_lowongan', 'id_program', 'tgl_pengajuan', 'tgl_mulai_magang', 'tgl_selesai_magang', 'durasi_hari', 'status_lamaran', 'hasil_seleksi', 'catatan_seleksi', 'tgl_keputusan'
    ];

    public function pelamar()
    {
        return $this->belongsTo(User::class, 'id_pelamar', 'id');
    }

    public function lowongan()
    {
        return $this->belongsTo(LowonganKerja::class, 'id_lowongan', 'id_lowongan');
    }

    public function program()
    {
        return $this->belongsTo(ProgramMagang::class, 'id_program', 'id_program');
    }

    public function berkas()
    {
        return $this->hasMany(BerkasLamaran::class, 'id_lamaran', 'id_lamaran');
    }

    public function jadwal()
    {
        return $this->hasOne(JadwalInterview::class, 'id_lamaran', 'id_lamaran');
    }

    public function surat()
    {
        return $this->hasOne(SuratPenerimaan::class, 'id_lamaran', 'id_lamaran');
    }

    // Nama posisi yang dilamar (judul lowongan kerja atau nama program magang)
    public function getNamaPosisiAttribute(): string
    {
        return $this->lowongan->judul ?? $this->program->nama_program ?? '-';
    }

    // Magang kalau melamar program, selain itu Kerja
    public function getJenisLamaranAttribute(): string
    {
        return $this->id_program ? 'Magang' : 'Kerja';
    }
}