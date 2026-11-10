<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel pendukung halaman "Tentang" (tim, cabang, legalitas, produk, sejarah).
 * Teks profil / visi / misi / nilai memakai tabel konten_website yang sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tim_perusahaan')) {
            Schema::create('tim_perusahaan', function (Blueprint $t) {
                $t->id('id_tim');
                $t->string('nama', 150);
                $t->string('jabatan', 150);
                $t->string('divisi', 100)->default('Manajemen');
                $t->string('foto')->nullable();
                $t->text('bio')->nullable();
                $t->string('linkedin_url')->nullable();
                $t->unsignedSmallInteger('urutan')->default(0);
                $t->boolean('aktif')->default(true);
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('kantor_cabang')) {
            Schema::create('kantor_cabang', function (Blueprint $t) {
                $t->id('id_cabang');
                $t->string('nama_kantor', 200);
                $t->string('kota', 100);
                $t->text('alamat');
                $t->string('telepon', 30)->nullable();
                $t->string('tautan_peta')->nullable();
                $t->boolean('is_pusat')->default(false);
                $t->unsignedSmallInteger('urutan')->default(0);
                $t->boolean('aktif')->default(true);
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('legalitas')) {
            Schema::create('legalitas', function (Blueprint $t) {
                $t->id('id_legalitas');
                $t->string('lembaga');
                $t->string('singkatan', 30);
                $t->string('nomor_izin', 150);
                $t->text('deskripsi')->nullable();
                $t->unsignedSmallInteger('urutan')->default(0);
                $t->boolean('aktif')->default(true);
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('produk_layanan')) {
            Schema::create('produk_layanan', function (Blueprint $t) {
                $t->id('id_produk');
                $t->string('kode', 20);
                $t->string('nama', 200);
                $t->string('kategori', 60);
                $t->text('deskripsi');
                $t->unsignedSmallInteger('urutan')->default(0);
                $t->boolean('aktif')->default(true);
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('tonggak_sejarah')) {
            Schema::create('tonggak_sejarah', function (Blueprint $t) {
                $t->id('id_tonggak');
                $t->unsignedSmallInteger('tahun');
                $t->string('judul', 200);
                $t->text('deskripsi')->nullable();
                $t->unsignedSmallInteger('urutan')->default(0);
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tonggak_sejarah');
        Schema::dropIfExists('produk_layanan');
        Schema::dropIfExists('legalitas');
        Schema::dropIfExists('kantor_cabang');
        Schema::dropIfExists('tim_perusahaan');
    }
};