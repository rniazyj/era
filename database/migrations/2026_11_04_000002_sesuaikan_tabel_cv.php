<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyesuaikan database yang SUDAH ADA dengan fitur CV.
 * Hanya menambah 4 kolom; semua tabel lain dipakai apa adanya.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Instagram untuk bagian "Data Media Sosial" di CV
        $this->tambah('profil_pelamar', 'instagram_url', fn (Blueprint $t) => $t->string('instagram_url')->nullable());

        // Kategori organisasi (contoh: "Kesenian dan Kebudayaan") + status masih menjabat
        $this->tambah('pengalaman_organisasi', 'kategori', fn (Blueprint $t) => $t->string('kategori', 100)->nullable());
        $this->tambah('pengalaman_organisasi', 'masih_berjalan', fn (Blueprint $t) => $t->boolean('masih_berjalan')->default(false));

        // Status masih bekerja
        $this->tambah('pengalaman_profesional', 'masih_berjalan', fn (Blueprint $t) => $t->boolean('masih_berjalan')->default(false));

        // Bersihkan kolom salah yang mungkin sempat dibuat migration versi sebelumnya
        // (aman: hanya dihapus kalau ada, dan kolom-kolom ini bukan bagian ERD kamu)
        $this->hapus('pengalaman_organisasi', ['organisasi']);
        $this->hapus('pengalaman_profesional', ['perusahaan', 'tipe', 'deskripsi']);
        $this->hapus('proyek', ['tipe', 'tautan']);
        $this->hapus('skill', ['jenis', 'nama', 'level']);
    }

    public function down(): void
    {
        $this->hapus('profil_pelamar', ['instagram_url']);
        $this->hapus('pengalaman_organisasi', ['kategori', 'masih_berjalan']);
        $this->hapus('pengalaman_profesional', ['masih_berjalan']);
    }

    private function tambah(string $tabel, string $kolom, callable $buat): void
    {
        if (Schema::hasTable($tabel) && ! Schema::hasColumn($tabel, $kolom)) {
            Schema::table($tabel, fn (Blueprint $t) => $buat($t));
        }
    }

    private function hapus(string $tabel, array $kolom): void
    {
        if (! Schema::hasTable($tabel)) {
            return;
        }

        $ada = array_values(array_filter($kolom, fn ($k) => Schema::hasColumn($tabel, $k)));

        if ($ada) {
            Schema::table($tabel, fn (Blueprint $t) => $t->dropColumn($ada));
        }
    }
};