<?php

namespace App\Http\Controllers;

use App\Models\KantorCabang;
use App\Models\KontenWebsite;
use App\Models\Legalitas;
use App\Models\ProdukLayanan;
use App\Models\TimPerusahaan;
use App\Models\TonggakSejarah;

class TentangController extends Controller
{
    // /tentang -> profil, visi & misi, nilai, sejarah, legalitas, cabang
    public function perusahaan()
    {
        $konten  = KontenWebsite::tayang()->orderBy('id_konten')->get()->groupBy('jenis_konten');
        $tonggak = TonggakSejarah::orderBy('tahun')->orderBy('urutan')->get();

        $legalitas = Legalitas::aktif()->get();

        // kantor pusat ditaruh paling depan
        $cabang = KantorCabang::aktif()->get()->sortByDesc('is_pusat')->values();

        $misi = optional($konten->get('misi')?->first())->isi_konten;

        $tahunMulai = $tonggak->min('tahun');
        $stat = collect([
            ['angka' => $tahunMulai ? max(1, now()->year - (int) $tahunMulai) : 0, 'akhiran' => '+', 'label' => 'Tahun beroperasi'],
            ['angka' => $cabang->count(),                'akhiran' => '', 'label' => 'Kantor cabang'],
            ['angka' => ProdukLayanan::aktif()->count(), 'akhiran' => '', 'label' => 'Produk unggulan'],
            ['angka' => $legalitas->count(),             'akhiran' => '', 'label' => 'Izin & keanggotaan resmi'],
        ])->filter(fn ($s) => $s['angka'] > 0)->values();

        return view('pages.tentang.perusahaan', [
            'profil'    => optional($konten->get('profil')?->first())->isi_konten,
            'visi'      => optional($konten->get('visi')?->first())->isi_konten,
            'misi'      => $misi ? array_values(array_filter(array_map('trim', preg_split('/\R+/', $misi)))) : [],
            'nilai'     => $konten->get('nilai', collect()),
            'tonggak'   => $tonggak,
            'stat'      => $stat,
            'legalitas' => $legalitas,
            'cabang'    => $cabang,
            'kota'      => $cabang->pluck('kota')->unique()->values(),
        ]);
    }

    public function tim()
    {
        $tim = TimPerusahaan::aktif()->get();

        return view('pages.tentang.tim', [
            'tim'    => $tim,
            'divisi' => $tim->pluck('divisi')->unique()->values(),
        ]);
    }

    public function produk()
    {
        $produk = ProdukLayanan::aktif()->get();

        return view('pages.tentang.produk', [
            'produk'   => $produk,
            'kategori' => $produk->pluck('kategori')->unique()->values(),
        ]);
    }
}