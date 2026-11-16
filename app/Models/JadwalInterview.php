<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JadwalInterview extends Model
{
    // Nilai kolom status_jadwal. Samakan dengan data yang sudah ada di databasemu kalau berbeda.
    public const TERJADWAL  = 'terjadwal';
    public const SELESAI    = 'selesai';
    public const DIBATALKAN = 'dibatalkan';

    protected $table = 'jadwal_interview';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'id_lamaran',
        'tgl_interview',
        'jam_mulai',
        'lokasi_atau_link',
        'status_jadwal',
        'catatan',
    ];

    protected $casts = [
        'tgl_interview' => 'date',
    ];

    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class, 'id_lamaran', 'id_lamaran');
    }

    public function scopeTerjadwal(Builder $q): Builder
    {
        return $q->where('status_jadwal', self::TERJADWAL);
    }

    // "09:30" (kolom TIME tersimpan sebagai 09:30:00)
    public function getJamAttribute(): string
    {
        return substr((string) $this->jam_mulai, 0, 5);
    }

    // Masih berstatus terjadwal tapi tanggalnya sudah lewat -> perlu ditandai selesai / dijadwal ulang
    public function getTerlewatAttribute(): bool
    {
        return $this->status_jadwal === self::TERJADWAL
            && $this->tgl_interview !== null
            && $this->tgl_interview->lt(today());
    }

    public function getAdalahTautanAttribute(): bool
    {
        return (bool) filter_var($this->lokasi_atau_link, FILTER_VALIDATE_URL);
    }
}