<?php

namespace Database\Seeders;

use App\Models\KontenWebsite;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Isi awal halaman Tentang (diambil dari teks halaman lama).
 * Aman dijalankan berulang (updateOrInsert, tidak menggandakan data).
 *
 *   php artisan db:seed --class=TentangSeeder
 */
class TentangSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->whereHas('role', fn ($q) => $q->where('nama_role', 'admin_hr'))->value('id')
              ?? User::query()->value('id');

        if (! $admin) {
            $this->command?->warn('Belum ada user. Buat akun admin dulu, lalu jalankan seeder ini lagi.');
            return;
        }

        $now = now();

        // ===== konten_website: profil, visi, misi, nilai =====
        $konten = [
            ['profil', 'Profil Perusahaan',
                'PT Equityworld Futures ("EWF") hadir sebagai mitra investasi yang mengutamakan kepercayaan dan profesionalisme di industri perdagangan berjangka. Dengan fokus pada kualitas layanan dan edukasi berkelanjutan, EWF membantu nasabah menavigasi peluang pasar secara lebih terarah dan aman.'],
            ['visi', 'Visi',
                'Menjadi perusahaan pialang berjangka yang unggul dan terpercaya, dengan standar operasional yang modern, transparan, serta memberikan nilai strategis bagi perkembangan industri dan perekonomian nasional.'],
            ['misi', 'Misi',
                "Menyediakan layanan perdagangan berjangka yang profesional, aman, dan berintegritas tinggi.\n"
              . "Mengembangkan kualitas sumber daya manusia untuk menghadirkan layanan yang kompeten dan berdaya saing.\n"
              . "Mengimplementasikan tata kelola perusahaan yang transparan dan akuntabel.\n"
              . "Memperluas akses edukasi serta literasi perdagangan berjangka kepada masyarakat.\n"
              . "Mengoptimalkan teknologi untuk mendukung operasional dan pengalaman nasabah secara efisien."],
            ['nilai', 'Integritas Profesional',
                'Menegakkan standar etika yang tinggi serta menjaga konsistensi tindakan dan keputusan dalam setiap aktivitas perusahaan.'],
            ['nilai', 'Fokus pada Nasabah',
                'Mengutamakan kebutuhan dan tujuan investasi nasabah melalui layanan yang responsif, akurat, dan transparan.'],
            ['nilai', 'Disiplin Kepatuhan',
                'Menjalankan seluruh proses bisnis sesuai kerangka regulasi dengan kontrol internal yang kuat dan berkelanjutan.'],
            ['nilai', 'Inovasi Berkelanjutan',
                'Mendorong pengembangan teknologi, produk, dan proses kerja untuk menghadirkan pengalaman transaksi yang semakin efisien dan kompetitif.'],
        ];

        foreach ($konten as [$jenis, $judul, $isi]) {
            DB::table('konten_website')->updateOrInsert(
                ['jenis_konten' => $jenis, 'judul' => $judul],
                [
                    'id_admin'      => $admin,
                    'isi_konten'    => $isi,
                    'status_tayang' => KontenWebsite::STATUS_TAYANG,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]
            );
        }

        // ===== legalitas =====
        $legalitas = [
            ['BAPPEBTI', 'Badan Pengawas Perdagangan Berjangka Komoditi (BAPPEBTI)', 'Keputusan Kepala BAPPEBTI Nomor 850/BAPPEBTI/SI/12/2005',
                'Izin usaha sebagai Pialang Berjangka dari badan pengawas perdagangan berjangka.', 1],
            ['JFX', 'Jakarta Futures Exchange (JFX) / Bursa Berjangka Jakarta', 'SPAB-143/BBJ/09/05',
                'Terdaftar sebagai Anggota Bursa Berjangka Jakarta.', 2],
            ['KBI', 'PT Kliring Berjangka Indonesia (Persero)', '36/AK-KBI/IV/2006',
                'Resmi terdaftar sebagai anggota Kliring Berjangka Indonesia.', 3],
        ];

        foreach ($legalitas as [$singkatan, $lembaga, $nomor, $deskripsi, $urutan]) {
            DB::table('legalitas')->updateOrInsert(
                ['singkatan' => $singkatan],
                ['lembaga' => $lembaga, 'nomor_izin' => $nomor, 'deskripsi' => $deskripsi, 'urutan' => $urutan, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // ===== kantor_cabang (sesuai halaman lama; tambahkan cabang lain lewat database/admin) =====
        $cabang = [
            ['Sahid Sudirman Center Lt. 9', 'Jakarta',
                'Jl. Jenderal Sudirman No.86, RT.10/RW.11, Karet Tengsin, Kecamatan Tanah Abang, Kota Jakarta Pusat, DKI Jakarta 10220', '+62 21 2788 9200', true, 1],
            ['Cyber 2 Tower Lt. 19', 'Jakarta', 'Jl. H. R. Rasuna Said, Jakarta Selatan', null, false, 2],
            ['Trillium Office Residence Lt. 1', 'Surabaya', 'Jl. Pemuda No. 108 - 116, Surabaya', null, false, 3],
            ['Forum Nine Building Lt. 8', 'Medan', 'Jl. Imam Bonjol No. 9, Medan', null, false, 4],
        ];

        foreach ($cabang as [$nama, $kota, $alamat, $telepon, $pusat, $urutan]) {
            DB::table('kantor_cabang')->updateOrInsert(
                ['nama_kantor' => $nama],
                ['kota' => $kota, 'alamat' => $alamat, 'telepon' => $telepon, 'is_pusat' => $pusat, 'urutan' => $urutan, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // ===== produk_layanan =====
        $produk = [
            ['XAU', 'Kontrak Emas Loco London (XAU/USD)', 'Logam Mulia',
                'Produk investasi berbasis pergerakan harga emas dunia yang diperdagangkan secara online dengan satuan troy ounce.', 1],
            ['EUR', 'Euro vs US Dollar (EUR/USD)', 'Forex',
                'Pasangan mata uang paling likuid di dunia yang menawarkan spread kompetitif dan peluang trading dua arah.', 2],
            ['GBP', 'Great Britain Pound (GBP/USD)', 'Forex',
                'Perdagangan mata uang Poundsterling terhadap Dollar AS dengan volatilitas tinggi.', 3],
            ['JPY', 'Japanese Yen (USD/JPY)', 'Forex',
                'Pasangan mata uang utama untuk pasar sesi Asia dengan stabilitas pergerakan harian.', 4],
            ['AUD', 'Australian Dollar (AUD/USD)', 'Forex',
                'Mata uang komoditas yang sangat dipengaruhi oleh pergerakan harga komoditas global.', 5],
            ['HKG', 'Hang Seng Index', 'Indeks',
                'Perdagangan indeks saham gabungan bursa Hong Kong, cocok untuk diversifikasi portofolio jangka pendek.', 6],
        ];

        foreach ($produk as [$kode, $nama, $kategori, $deskripsi, $urutan]) {
            DB::table('produk_layanan')->updateOrInsert(
                ['nama' => $nama],
                ['kode' => $kode, 'kategori' => $kategori, 'deskripsi' => $deskripsi, 'urutan' => $urutan, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // ===== tonggak_sejarah =====
        // Tahun diambil dari nomor izin di atas (SK BAPPEBTI Des 2005, keanggotaan BBJ Sep 2005, KBI Apr 2006).
        // Cek ulang dengan data resmi perusahaan, lalu tambahkan tonggak lain (pembukaan cabang, dll).
        $tonggak = [
            [2005, 'Keanggotaan Bursa Berjangka Jakarta', 'Terdaftar sebagai anggota Bursa Berjangka Jakarta.', 1],
            [2005, 'Izin Pialang Berjangka BAPPEBTI', 'Ditetapkan sebagai Pialang Berjangka oleh BAPPEBTI.', 2],
            [2006, 'Keanggotaan Kliring Berjangka Indonesia', 'Resmi terdaftar sebagai anggota PT Kliring Berjangka Indonesia (Persero).', 3],
        ];

        foreach ($tonggak as [$tahun, $judul, $deskripsi, $urutan]) {
            DB::table('tonggak_sejarah')->updateOrInsert(
                ['judul' => $judul],
                ['tahun' => $tahun, 'deskripsi' => $deskripsi, 'urutan' => $urutan, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // ===== tim (HANYA contoh di lokal; ganti dengan data asli) =====
        if (app()->environment('local')) {
            $tim = [
                ['Nama Direktur Utama', 'Direktur Utama', 'Direksi', 1],
                ['Nama Direktur Kepatuhan', 'Direktur Kepatuhan', 'Direksi', 2],
                ['Nama Kepala Operasional', 'Kepala Operasional', 'Manajemen', 3],
                ['Nama Kepala SDM', 'Kepala SDM', 'Manajemen', 4],
            ];

            foreach ($tim as [$nama, $jabatan, $divisi, $urutan]) {
                DB::table('tim_perusahaan')->updateOrInsert(
                    ['nama' => $nama],
                    ['jabatan' => $jabatan, 'divisi' => $divisi, 'urutan' => $urutan, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }
}