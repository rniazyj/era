<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_role',
        'name',
        'email',
        'no_telepon',
        'password',
        'status_akun',
        'percobaan_gagal',
        'terkunci_sampai',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'terkunci_sampai' => 'datetime',
        ];
    }

    // Relasi ke Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    // Relasi ke Profil Pelamar
    public function profilPelamar()
    {
        return $this->hasOne(ProfilPelamar::class, 'id_pengguna', 'id');
    }

    // Relasi ke Log Aktivitas
    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'id_pengguna', 'id');
    }

    // Helper Role Check
    public function isAdmin()
    {
        return optional($this->role)->nama_role === 'admin_hr';
    }

    public function isPimpinan()
    {
        return optional($this->role)->nama_role === 'pimpinan';
    }

    public function isPelamar()
    {
        return optional($this->role)->nama_role === 'pelamar';
    }
}