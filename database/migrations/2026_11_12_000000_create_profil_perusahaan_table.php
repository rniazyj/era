<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menggabungkan 6 tabel konten halaman Tentang menjadi SATU tabel: profil_perusahaan.
 *
 *   konten_website   -> tipe profil, visi, misi, nilai
 *   tonggak_sejarah  -> tipe tonggak
 *   legalitas        -> tipe legalitas
 *   kantor_cabang    -> tipe cabang
 *   tim_perusahaan   -> tipe tim
 *   produk_layanan   -> tipe produk
 *   (baru)           -> tipe hero (judul & foto bagian atas tiap halaman)
 *
 * Tabel-tabel lama TIDAK dihapus dan TIDAK diubah, hanya disalin. Setelah yakin semuanya beres,
 * tabel lama boleh dihapus sendiri kapan pun.
 *
 * Arti kolom umum (berbeda sedikit per tipe):
 *   judul    = nama / judul utama (nama kantor, nama orang, nama lembaga, nama produk, judul nilai)
 *   label    = penanda singkat (tahun, singkatan lembaga, kode produk, jabatan)
 *   kategori = pengelompok (kota, divisi, kategori produk, halaman untuk hero)
 *   nomor    = nomor izin (legalitas)
 *   tautan   = link Google Maps (cabang) / LinkedIn (tim)
 *   is_utama = kantor pusat (cabang)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('profil_perusahaan')) {
            Schema::create('profil_perusahaan', function (Blueprint $t) {
                $t->id('id_profil');
                $t->string('tipe', 20)->index();
                $t->string('judul', 255)->nullable();
                $t->string('label', 120)->nullable();
                $t->string('kategori', 120)->nullable();
                $t->text('deskripsi')->nullable();
                $t->string('alamat', 500)->nullable();
                $t->string('telepon', 60)->nullable();
                $t->string('nomor', 160)->nullable();
                $t->string('tautan', 500)->nullable();
                $t->string('foto', 500)->nullable();
                $t->boolean('is_utama')->default(false);
                $t->unsignedInteger('urutan')->default(0);
                $t->boolean('aktif')->default(true);
                $t->timestamps();

                $t->index(['tipe', 'aktif', 'urutan']);
            });
        }

        // Jangan menyalin dua kali kalau migration dijalankan ulang
        if (DB::table('profil_perusahaan')->exists()) {
            return;
        }

        $sekarang = now();
        $baris = [];
        $tambah = function (array $r) use (&$baris, $sekarang) {
            $baris[] = array_merge([
                'tipe' => null, 'judul' => null, 'label' => null, 'kategori' => null, 'deskripsi' => null,
                'alamat' => null, 'telepon' => null, 'nomor' => null, 'tautan' => null, 'foto' => null,
                'is_utama' => false, 'urutan' => 0, 'aktif' => true,
            ], $r, ['created_at' => $sekarang, 'updated_at' => $sekarang]);
        };

        // 1. konten_website -> profil, visi, misi (masing-masing satu), nilai (banyak)
        if (Schema::hasTable('konten_website')) {
            $konten = DB::table('konten_website')->where('status_tayang', 'tayang')->orderBy('id_konten')->get();

            foreach (['profil', 'visi', 'misi'] as $jenis) {
                if ($r = $konten->firstWhere('jenis_konten', $jenis)) {
                    $tambah(['tipe' => $jenis, 'judul' => $r->judul, 'deskripsi' => $r->isi_konten]);
                }
            }
            foreach ($konten->where('jenis_konten', 'nilai')->values() as $i => $r) {
                $tambah(['tipe' => 'nilai', 'judul' => $r->judul, 'deskripsi' => $r->isi_konten, 'urutan' => $i + 1]);
            }
        }

        // 2. tonggak_sejarah
        if (Schema::hasTable('tonggak_sejarah')) {
            foreach (DB::table('tonggak_sejarah')->orderBy('tahun')->orderBy('urutan')->get() as $r) {
                $tambah(['tipe' => 'tonggak', 'label' => (string) $r->tahun, 'judul' => $r->judul,
                         'deskripsi' => $r->deskripsi, 'urutan' => (int) $r->urutan]);
            }
        }

        // 3. legalitas
        if (Schema::hasTable('legalitas')) {
            foreach (DB::table('legalitas')->orderBy('urutan')->orderBy('id_legalitas')->get() as $r) {
                $tambah(['tipe' => 'legalitas', 'judul' => $r->lembaga, 'label' => $r->singkatan, 'nomor' => $r->nomor_izin,
                         'deskripsi' => $r->deskripsi, 'urutan' => (int) $r->urutan, 'aktif' => (bool) $r->aktif]);
            }
        }

        // 4. kantor_cabang
        if (Schema::hasTable('kantor_cabang')) {
            foreach (DB::table('kantor_cabang')->orderBy('urutan')->orderBy('id_cabang')->get() as $r) {
                $tambah(['tipe' => 'cabang', 'judul' => $r->nama_kantor, 'kategori' => $r->kota, 'alamat' => $r->alamat,
                         'telepon' => $r->telepon, 'tautan' => $r->tautan_peta, 'is_utama' => (bool) $r->is_pusat,
                         'urutan' => (int) $r->urutan, 'aktif' => (bool) $r->aktif]);
            }
        }

        // 5. tim_perusahaan
        if (Schema::hasTable('tim_perusahaan')) {
            foreach (DB::table('tim_perusahaan')->orderBy('urutan')->orderBy('id_tim')->get() as $r) {
                $tambah(['tipe' => 'tim', 'judul' => $r->nama, 'label' => $r->jabatan, 'kategori' => $r->divisi,
                         'foto' => $r->foto, 'deskripsi' => $r->bio, 'tautan' => $r->linkedin_url,
                         'urutan' => (int) $r->urutan, 'aktif' => (bool) $r->aktif]);
            }
        }

        // 6. produk_layanan
        if (Schema::hasTable('produk_layanan')) {
            foreach (DB::table('produk_layanan')->orderBy('urutan')->orderBy('id_produk')->get() as $r) {
                $tambah(['tipe' => 'produk', 'label' => $r->kode, 'judul' => $r->nama, 'kategori' => $r->kategori,
                         'deskripsi' => $r->deskripsi, 'urutan' => (int) $r->urutan, 'aktif' => (bool) $r->aktif]);
            }
        }

        // 7. hero tiap halaman (judul 2 baris + foto). Kata di antara *bintang* tampil tebal.
        $tambah(['tipe' => 'hero', 'kategori' => 'perusahaan', 'judul' => 'Mitra Tepercaya',      'label' => 'Investasi *Berjangka*']);
        $tambah(['tipe' => 'hero', 'kategori' => 'tim',        'judul' => 'Orang-orang di Balik', 'label' => '*Equityworld.*']);
        $tambah(['tipe' => 'hero', 'kategori' => 'produk',     'judul' => 'Produk & Layanan',     'label' => '*Unggulan Kami.*']);

        foreach (array_chunk($baris, 100) as $paket) {
            DB::table('profil_perusahaan')->insert($paket);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_perusahaan');
    }
};