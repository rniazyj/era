<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';
    protected $primaryKey = 'id_log';

    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'aktivitas',
        'modul',
        'status_aksi',
        'waktu',
        'alamat_ip',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id');
    }

    /**
     * Catat satu aktivitas.
     *
     * $idPengguna:
     *   false (bawaan) = pakai user yang sedang login
     *   null           = tanpa pengguna (misalnya login gagal dari pengunjung tak dikenal)
     *   angka          = id pengguna tertentu
     *
     * Kegagalan mencatat tidak boleh merusak proses utama, jadi error hanya dilaporkan.
     */
    public static function catat(
        string $aktivitas,
        string $modul,
        string $status = 'berhasil',
        int|string|false|null $idPengguna = false
    ): void {
        try {
            static::create([
                'id_pengguna' => $idPengguna === false ? auth()->id() : $idPengguna,
                'aktivitas'   => mb_substr($aktivitas, 0, 255),
                'modul'       => mb_substr($modul, 0, 255),
                'status_aksi' => $status,
                'waktu'       => now(),
                'alamat_ip'   => request()?->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // Samarkan email supaya log tidak menyimpan alamat lengkap yang diketik saat login gagal
    public static function samarkanEmail(string $email): string
    {
        [$nama, $domain] = array_pad(explode('@', $email, 2), 2, '');

        if ($domain === '') {
            return mb_substr($nama, 0, 2) . '***';
        }

        return mb_substr($nama, 0, 2) . '***@' . $domain;
    }
}