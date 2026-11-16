<?php

namespace App\Http\Controllers;

use App\Models\ProfilPerusahaan;

/**
 * Halaman publik Tentang. Semua data dibaca dari SATU tabel: profil_perusahaan
 * (diedit admin lewat /admin/konten).
 */
class TentangController extends Controller
{
    // /tentang -> profil, visi & misi, nilai, sejarah, legalitas, cabang
    public function perusahaan()
    {
        $semua = ProfilPerusahaan::aktif()->get()->groupBy('tipe');
        $ambil = fn (string $tipe) => $semua->get($tipe, collect());

        $profilRow = $ambil('profil')->first();
        $visiRow   = $ambil('visi')->first();
        $misiRow   = $ambil('misi')->first();

        // Sejarah urut tahun terlama -> terbaru
        $tonggak   = $ambil('tonggak')->sortBy([['label', 'asc'], ['urutan', 'asc']])->values();
        $legalitas = $ambil('legalitas')->values();
        $nilai     = $ambil('nilai')->values();

        // Kantor pusat paling depan, sisanya mengikuti urutan
        $cabang = $ambil('cabang')->sortByDesc(fn ($c) => (int) $c->is_utama)->values();

        $tahunMulai = (int) $tonggak->min('label');
        $stat = collect([
            ['angka' => $tahunMulai ? max(1, now()->year - $tahunMulai) : 0, 'akhiran' => '+', 'label' => 'Tahun beroperasi'],
            ['angka' => $cabang->count(),             'akhiran' => '', 'label' => 'Kantor cabang'],
            ['angka' => $ambil('produk')->count(),    'akhiran' => '', 'label' => 'Produk unggulan'],
            ['angka' => $legalitas->count(),          'akhiran' => '', 'label' => 'Izin & keanggotaan resmi'],
        ])->filter(fn ($s) => $s['angka'] > 0)->values();

        return view('pages.tentang.perusahaan', [
            'hero'       => $this->hero('perusahaan', ['Mitra Tepercaya', 'Investasi *Berjangka*']),
            'profil'     => $profilRow?->deskripsi,
            'profilFoto' => $profilRow?->fotoUrl('equityworld-profil', 1400, 700),
            'visi'       => $visiRow?->deskripsi,
            'misi'       => ProfilPerusahaan::daftarMisi($misiRow?->deskripsi),
            'nilai'      => $nilai,
            'tonggak'    => $tonggak,
            'stat'       => $stat,
            'legalitas'  => $legalitas,
            'cabang'     => $cabang,
            'kota'       => $cabang->pluck('kategori')->filter()->unique()->values(),
        ]);
    }

    public function tim()
    {
        $tim = ProfilPerusahaan::tipe('tim')->aktif()->get();

        return view('pages.tentang.tim', [
            'hero'   => $this->hero('tim', ['Orang-orang di Balik', '*Equityworld.*']),
            'tim'    => $tim,
            'divisi' => $tim->pluck('kategori')->filter()->unique()->values(),
        ]);
    }

    public function produk()
    {
        $produk = ProfilPerusahaan::tipe('produk')->aktif()->get();

        return view('pages.tentang.produk', [
            'hero'     => $this->hero('produk', ['Produk & Layanan', '*Unggulan Kami.*']),
            'produk'   => $produk,
            'kategori' => $produk->pluck('kategori')->filter()->unique()->values(),
        ]);
    }

    // Judul 2 baris + foto hero tiap halaman (diedit di admin: Konten Website > Hero halaman)
    private function hero(string $halaman, array $awal): array
    {
        $h = ProfilPerusahaan::tipe('hero')->where('kategori', $halaman)->first();

        return [
            'baris1' => $h?->judul ?: $awal[0],
            'baris2' => ProfilPerusahaan::tebal($h?->label ?: $awal[1]),   // *kata* -> <em>kata</em>
            'foto'   => $h?->fotoUrl(),                                     // null = pakai foto bawaan
        ];
    }
}