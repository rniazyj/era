<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LowonganKerja extends Model
{
    public const STATUS_BUKA = 'dibuka'; // sesuaikan dgn nilai di DB-mu

    protected $table = 'lowongan_kerja';
    protected $primaryKey = 'id_lowongan';

    protected $fillable = [
        'id_admin', 'judul', 'deskripsi', 'kualifikasi', 'kategori',
        'kuota', 'gaji_min', 'gaji_max', 'urgent',
        'tgl_buka', 'tgl_tutup', 'status',
    ];

    protected $casts = [
        'tgl_buka'  => 'date',
        'tgl_tutup' => 'date',
        'urgent'    => 'boolean',
        'kuota'     => 'integer',
        'gaji_min'  => 'integer',
        'gaji_max'  => 'integer',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'id');
    }

    public function persyaratan(): HasMany
    {
        return $this->hasMany(PersyaratanBerkas::class, 'id_lowongan', 'id_lowongan');
    }

    public function lamaran()
    {
        return $this->hasMany(Lamaran::class, 'id_lowongan', 'id_lowongan');
    }

    /** Lowongan yang statusnya buka DAN masih dalam rentang tanggal tayang. */
    public function scopeBuka(Builder $q): Builder
    {
        $hariIni = today();

        return $q->where('status', self::STATUS_BUKA)
            ->where(fn ($w) => $w->whereNull('tgl_buka')->orWhere('tgl_buka', '<=', $hariIni))
            ->where(fn ($w) => $w->whereNull('tgl_tutup')->orWhere('tgl_tutup', '>=', $hariIni));
    }

    public function getSedangBukaAttribute(): bool
    {
        return $this->status === self::STATUS_BUKA
            && (! $this->tgl_buka || $this->tgl_buka->lte(today()))
            && (! $this->tgl_tutup || $this->tgl_tutup->gte(today()));
    }

    /** Sisa hari sampai ditutup (null jika tidak ada batas). */
    public function getSisaHariAttribute(): ?int
    {
        return $this->tgl_tutup ? (int) today()->diffInDays($this->tgl_tutup, false) : null;
    }

    public function getHurufAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->judul, 0, 1));
    }

    public function getKategoriLabelAttribute(): string
    {
        return filled($this->kategori) ? $this->kategori : 'Umum';
    }

    public function getGajiLabelAttribute(): string
    {
        $jt = fn (int $v) => rtrim(rtrim(number_format($v / 1_000_000, 1, ',', '.'), '0'), ',');

        if ($this->gaji_min && $this->gaji_max && $this->gaji_min !== $this->gaji_max) {
            return 'Rp ' . $jt($this->gaji_min) . ' - ' . $jt($this->gaji_max) . ' Jt';
        }
        if ($v = $this->gaji_max ?: $this->gaji_min) {
            return 'Rp ' . $jt($v) . ' Jt';
        }

        return 'Negosiasi';
    }

    /** Dalam jutaan, dipakai untuk pengurutan "gaji tertinggi" di halaman index. */
    public function getGajiMaxJtAttribute(): float
    {
        return round(($this->gaji_max ?: $this->gaji_min ?: 0) / 1_000_000, 1);
    }

    public function getStatusLabelAttribute(): string
    {
        if (! $this->sedang_buka) {
            return 'Pendaftaran ditutup';
        }

        $sisa = $this->sisa_hari;
        if ($sisa !== null && $sisa <= 14) {
            return $sisa <= 0 ? 'Ditutup hari ini' : "Ditutup {$sisa} hari lagi";
        }

        if ($this->tgl_buka) {
            $lalu = (int) $this->tgl_buka->diffInDays(today(), false);
            return $lalu <= 0 ? 'Tayang hari ini' : "Tayang {$lalu} hari lalu";
        }

        return 'Sedang dibuka';
    }

    /** Urgent manual dari admin, atau otomatis kalau tinggal <= 3 hari. */
    public function getIsMendesakAttribute(): bool
    {
        return $this->urgent || ($this->sedang_buka && $this->sisa_hari !== null && $this->sisa_hari <= 3);
    }

    /** Kualifikasi / deskripsi dipecah per baris agar bisa jadi bullet list. */
    public function getKualifikasiListAttribute(): array
    {
        return $this->pecahBaris($this->kualifikasi);
    }

    public function getDeskripsiListAttribute(): array
    {
        return $this->pecahBaris($this->deskripsi);
    }

    private function pecahBaris(?string $teks): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $teks))
            ->map(fn ($b) => trim(ltrim(trim($b), "-•*\t ")))
            ->filter()
            ->values()
            ->all();
    }
}