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

    // ---- Tampilan status (dipakai badge di kartu & chip di kalender) ----

    public function getStatusLabelAttribute(): string
    {
        if ($this->terlewat) {
            return 'Terlewat';
        }

        return match ($this->status_jadwal) {
            self::SELESAI    => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
            default          => 'Terjadwal',
        };
    }

    // Kelas Tailwind untuk badge status
    public function getStatusKelasAttribute(): string
    {
        if ($this->terlewat) {
            return 'bg-amber-50 text-amber-700';
        }

        return match ($this->status_jadwal) {
            self::SELESAI    => 'bg-emerald-50 text-emerald-700',
            self::DIBATALKAN => 'bg-zinc-100 text-zinc-500',
            default          => 'bg-orange-50 text-orange-600',
        };
    }

    // Kelas Tailwind untuk chip kecil di sel kalender
    public function getChipKelasAttribute(): string
    {
        if ($this->terlewat) {
            return 'bg-amber-100 text-amber-800';
        }

        return match ($this->status_jadwal) {
            self::SELESAI    => 'bg-emerald-100 text-emerald-800',
            self::DIBATALKAN => 'bg-zinc-100 text-zinc-400 line-through',
            default          => 'bg-orange-100 text-orange-800',
        };
    }

    // Warna titik (tampilan mobile)
    public function getTitikKelasAttribute(): string
    {
        if ($this->terlewat) {
            return 'bg-amber-500';
        }

        return match ($this->status_jadwal) {
            self::SELESAI    => 'bg-emerald-500',
            self::DIBATALKAN => 'bg-zinc-300',
            default          => 'bg-orange-500',
        };
    }
}