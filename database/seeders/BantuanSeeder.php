<?php

namespace Database\Seeders;

use App\Models\Bantuan;
use Illuminate\Database\Seeder;

class BantuanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Akun
            ['Akun', 'Bagaimana cara membuat akun di ERA?',
             'Klik tombol Get Started di pojok kanan atas, lalu pilih Daftar. Isi nama lengkap, e-mail aktif, dan password minimal 8 karakter. Setelah berhasil, silakan masuk menggunakan akun tersebut.'],
            ['Akun', 'Saya lupa password, apa yang harus dilakukan?',
             'Di halaman Masuk, klik "Lupa password?", lalu tulis e-mail yang kamu pakai saat mendaftar. Kami akan mengirim tautan untuk membuat password baru. Jangan lupa cek folder spam.'],
            ['Akun', 'Kenapa akun saya terkunci?',
             'Demi keamanan, akun dikunci selama 15 menit setelah 5 kali salah memasukkan password. Tunggu sampai waktunya habis, lalu coba masuk lagi. Kalau lupa password, gunakan fitur "Lupa password?".'],

            // Lamaran
            ['Lamaran', 'Bagaimana cara melamar lowongan kerja?',
             'Buka menu Job, pilih lowongan yang sesuai, lalu ikuti langkah pengiriman berkas dan CV. Pastikan data profil dan CV kamu sudah terisi sebelum melamar.'],
            ['Lamaran', 'Bagaimana alur seleksinya?',
             'Alurnya ada empat tahap: pilih posisi, kirim berkas dan CV, seleksi dan wawancara, lalu mulai berkarya bersama. Informasi tiap tahap akan kami kabarkan lewat akunmu.'],
            ['Lamaran', 'Apakah saya bisa melamar lebih dari satu posisi?',
             'Bisa. Namun, pilih posisi yang benar-benar sesuai dengan kemampuan dan minatmu supaya peluang lolosnya lebih besar.'],

            // Magang
            ['Magang', 'Apa bedanya magang konversi dan non-konversi?',
             'Magang konversi diakui sebagai SKS oleh kampus (sesuai ketentuan kampusmu), sedangkan non-konversi tidak dikonversi ke SKS. Keduanya sama-sama bersertifikat dan dilakukan di lingkungan kerja profesional.'],
            ['Magang', 'Apakah program magang mendapat uang saku?',
             'Ketentuan uang saku berbeda di tiap program dan perusahaan mitra. Detailnya tercantum di halaman masing-masing program magang.'],
        ];

        foreach ($data as $i => [$kategori, $tanya, $jawab]) {
            Bantuan::updateOrCreate(
                ['pertanyaan' => $tanya],
                ['kategori' => $kategori, 'jawaban' => $jawab, 'urutan' => $i + 1, 'aktif' => true]
            );
        }
    }
}