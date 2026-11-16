<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Satu model untuk seluruh konten halaman Tentang (menggantikan KontenWebsite, ProdukLayanan,
 * Legalitas, KantorCabang, TimPerusahaan, dan TonggakSejarah). Jenisnya dibedakan oleh kolom "tipe".
 *
 * Kolom umum dipakai berbeda per tipe. Supaya tampilan lama tetap bisa memakai nama yang akrab
 * (mis. $cabang->kota, $tim->jabatan, $legalitas->nomor_izin), tersedia accessor alias di bawah.
 */
class ProfilPerusahaan extends Model
{
    protected $table = 'profil_perusahaan';
    protected $primaryKey = 'id_profil';

    protected $fillable = [
        'tipe', 'judul', 'label', 'kategori', 'deskripsi', 'alamat', 'telepon',
        'nomor', 'tautan', 'foto', 'is_utama', 'urutan', 'aktif',
    ];

    protected $casts = ['is_utama' => 'boolean', 'aktif' => 'boolean', 'urutan' => 'integer'];

    /*
    |--------------------------------------------------------------------------
    | HALAMAN: 3 halaman situs yang diedit lewat admin, beserta bagian-bagiannya
    | (urutan = urutan tampil di halaman). Dipakai menu admin supaya jelas
    | "bagian ini ada di halaman mana".
    |--------------------------------------------------------------------------
    */
    public const HALAMAN = [
        'perusahaan' => [
            'nama'    => 'Halaman Perusahaan',
            'route'   => 'tentang',
            'ringkas' => 'Profil, visi & misi, nilai, sejarah, legalitas, dan kantor cabang.',
            'bagian'  => ['hero', 'profil', 'visi', 'misi', 'nilai', 'tonggak', 'legalitas', 'cabang'],
        ],
        'tim' => [
            'nama'    => 'Halaman Tim',
            'route'   => 'tentang.tim',
            'ringkas' => 'Foto dan profil orang-orang di perusahaan.',
            'bagian'  => ['hero', 'tim'],
        ],
        'produk' => [
            'nama'    => 'Halaman Produk',
            'route'   => 'tentang.produk',
            'ringkas' => 'Daftar produk dan layanan perdagangan berjangka.',
            'bagian'  => ['hero', 'produk'],
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi tipe konten. Dipakai form admin untuk membuat input & validasi otomatis.
    |--------------------------------------------------------------------------
    | halaman : halaman situs tempat bagian ini tampil (hero = null, tergantung barisnya)
    | anchor  : id bagian di halaman situs (untuk tombol "Lihat di situs")
    | mode    : tunggal = hanya satu baris, langsung dibuka form edit
    |           tetap   = jumlah baris tetap (hero), hanya bisa diedit
    |           daftar  = banyak baris, bisa tambah / ubah / hapus (ada urutan & status tayang)
    | utama / sub / badge : kolom yang ditampilkan di tabel daftar admin
    | field   : kolom yang bisa diisi admin. tipe: teks | area | angka | url | foto | centang
    |           foto + 'gaya' => 'logo' = tampil persegi (untuk logo)
    */
    public const TIPE = [
        'hero' => [
            'nama' => 'Banner atas', 'halaman' => null, 'anchor' => null, 'mode' => 'tetap',
            'bantuan' => 'Judul besar dan foto latar di bagian paling atas halaman.',
            'utama' => 'judul', 'sub' => 'label',
            'field' => [
                'judul' => ['label' => 'Judul baris 1', 'tipe' => 'teks', 'aturan' => 'required|string|max:80'],
                'label' => ['label' => 'Judul baris 2', 'tipe' => 'teks', 'aturan' => 'required|string|max:80',
                            'bantuan' => 'Bungkus kata dengan tanda *bintang* supaya tampil oranye & tebal. Contoh: Investasi *Berjangka*'],
                'foto'  => ['label' => 'Foto latar banner', 'tipe' => 'foto',
                            'bantuan' => 'Foto lebar, disarankan 1920 × 1200 px. Kosongkan untuk memakai foto bawaan.'],
            ],
        ],

        'profil' => [
            'nama' => 'Profil perusahaan', 'halaman' => 'perusahaan', 'anchor' => 'profil', 'mode' => 'tunggal',
            'bantuan' => 'Teks pengenalan dan foto di bagian "Mengenal lebih dekat".',
            'utama' => 'deskripsi',
            'field' => [
                'deskripsi' => ['label' => 'Teks profil perusahaan', 'tipe' => 'area', 'aturan' => 'required|string|max:3000',
                                'bantuan' => 'Boleh beberapa paragraf (tekan Enter).'],
                'foto'      => ['label' => 'Foto profil', 'tipe' => 'foto',
                                'bantuan' => 'Foto lebar di bawah teks profil. Kosongkan untuk memakai foto bawaan.'],
            ],
        ],

        'visi' => [
            'nama' => 'Visi', 'halaman' => 'perusahaan', 'anchor' => 'fondasi', 'mode' => 'tunggal',
            'bantuan' => 'Kalimat visi di kartu oranye.',
            'utama' => 'deskripsi',
            'field' => [
                'deskripsi' => ['label' => 'Visi perusahaan', 'tipe' => 'area', 'aturan' => 'required|string|max:600'],
            ],
        ],

        'misi' => [
            'nama' => 'Misi', 'halaman' => 'perusahaan', 'anchor' => 'fondasi', 'mode' => 'tunggal',
            'bantuan' => 'Daftar misi bernomor.',
            'utama' => 'deskripsi',
            'field' => [
                'deskripsi' => ['label' => 'Misi perusahaan', 'tipe' => 'area', 'aturan' => 'required|string|max:3000',
                                'bantuan' => 'Tulis satu misi per baris.'],
            ],
        ],

        'nilai' => [
            'nama' => 'Nilai inti', 'halaman' => 'perusahaan', 'anchor' => 'nilai', 'mode' => 'daftar',
            'bantuan' => 'Kartu "Cara kami bekerja".',
            'utama' => 'judul', 'sub' => 'deskripsi',
            'field' => [
                'judul'     => ['label' => 'Judul nilai', 'tipe' => 'teks', 'aturan' => 'required|string|max:80'],
                'deskripsi' => ['label' => 'Penjelasan', 'tipe' => 'area', 'aturan' => 'required|string|max:400'],
            ],
        ],

        // Sejarah: tanpa foto
        'tonggak' => [
            'nama' => 'Sejarah', 'halaman' => 'perusahaan', 'anchor' => 'sejarah', 'mode' => 'daftar',
            'bantuan' => 'Tonggak sejarah di garis waktu. Diurutkan otomatis dari tahun terlama.',
            'utama' => 'judul', 'sub' => 'label',
            'field' => [
                'label'     => ['label' => 'Tahun', 'tipe' => 'angka', 'aturan' => 'required|digits:4'],
                'judul'     => ['label' => 'Judul tonggak', 'tipe' => 'teks', 'aturan' => 'required|string|max:120'],
                'deskripsi' => ['label' => 'Penjelasan', 'tipe' => 'area', 'aturan' => 'nullable|string|max:600'],
            ],
        ],

        // Legalitas: ada logo instansi
        'legalitas' => [
            'nama' => 'Legalitas', 'halaman' => 'perusahaan', 'anchor' => 'legalitas', 'mode' => 'daftar',
            'bantuan' => 'Izin dan keanggotaan resmi, lengkap dengan logo instansinya.',
            'utama' => 'judul', 'sub' => 'nomor', 'badge' => 'label',
            'field' => [
                'judul'     => ['label' => 'Nama lembaga', 'tipe' => 'teks', 'aturan' => 'required|string|max:160'],
                'label'     => ['label' => 'Singkatan', 'tipe' => 'teks', 'aturan' => 'required|string|max:20'],
                'nomor'     => ['label' => 'Nomor izin', 'tipe' => 'teks', 'aturan' => 'required|string|max:160'],
                'deskripsi' => ['label' => 'Keterangan', 'tipe' => 'area', 'aturan' => 'nullable|string|max:500'],
                'foto'      => ['label' => 'Logo instansi', 'tipe' => 'foto', 'gaya' => 'logo',
                                'bantuan' => 'Disarankan PNG/WebP berlatar transparan, persegi (mis. 400 × 400 px). Kosong = tampil singkatan.'],
            ],
        ],

        'cabang' => [
            'nama' => 'Kantor cabang', 'halaman' => 'perusahaan', 'anchor' => 'cabang', 'mode' => 'daftar',
            'bantuan' => 'Kantor pusat dan cabang. Hanya satu kantor yang bisa menjadi kantor pusat.',
            'utama' => 'judul', 'sub' => 'alamat', 'badge' => 'kategori',
            'field' => [
                'judul'    => ['label' => 'Nama kantor', 'tipe' => 'teks', 'aturan' => 'required|string|max:160'],
                'kategori' => ['label' => 'Kota', 'tipe' => 'teks', 'aturan' => 'required|string|max:80'],
                'alamat'   => ['label' => 'Alamat lengkap', 'tipe' => 'area', 'aturan' => 'required|string|max:500'],
                'telepon'  => ['label' => 'Telepon', 'tipe' => 'teks', 'aturan' => 'nullable|string|max:60'],
                'tautan'   => ['label' => 'Tautan Google Maps', 'tipe' => 'url', 'aturan' => 'nullable|url|max:500',
                               'bantuan' => 'Opsional. Kalau kosong, tautan dibuat otomatis dari nama kantor dan alamat.'],
                'is_utama' => ['label' => 'Jadikan kantor pusat', 'tipe' => 'centang'],
                'foto'     => ['label' => 'Foto kantor', 'tipe' => 'foto', 'bantuan' => 'Kosongkan untuk memakai foto bawaan.'],
            ],
        ],

        'tim' => [
            'nama' => 'Anggota tim', 'halaman' => 'tim', 'anchor' => 'tim', 'mode' => 'daftar',
            'bantuan' => 'Anggota tim yang tampil di halaman Tim. Filter divisi dibuat otomatis dari isian "Divisi".',
            'utama' => 'judul', 'sub' => 'label', 'badge' => 'kategori',
            'field' => [
                'judul'     => ['label' => 'Nama lengkap', 'tipe' => 'teks', 'aturan' => 'required|string|max:120'],
                'label'     => ['label' => 'Jabatan', 'tipe' => 'teks', 'aturan' => 'required|string|max:120'],
                'kategori'  => ['label' => 'Divisi', 'tipe' => 'teks', 'aturan' => 'required|string|max:80'],
                'deskripsi' => ['label' => 'Bio singkat', 'tipe' => 'area', 'aturan' => 'nullable|string|max:600'],
                'tautan'    => ['label' => 'Tautan LinkedIn', 'tipe' => 'url', 'aturan' => 'nullable|url|max:500'],
                'foto'      => ['label' => 'Foto', 'tipe' => 'foto',
                                'bantuan' => 'Disarankan rasio 4:5. Kosongkan untuk menampilkan inisial nama.'],
            ],
        ],

        'produk' => [
            'nama' => 'Produk & layanan', 'halaman' => 'produk', 'anchor' => 'produk', 'mode' => 'daftar',
            'bantuan' => 'Produk yang tampil di halaman Produk. Filter dibuat otomatis dari isian "Kategori".',
            'utama' => 'judul', 'sub' => 'label', 'badge' => 'kategori',
            'field' => [
                'label'     => ['label' => 'Kode', 'tipe' => 'teks', 'aturan' => 'required|string|max:10',
                                'bantuan' => 'Singkatan pendek yang tampil di ikon kartu, mis. XAU.'],
                'judul'     => ['label' => 'Nama produk', 'tipe' => 'teks', 'aturan' => 'required|string|max:160'],
                'kategori'  => ['label' => 'Kategori', 'tipe' => 'teks', 'aturan' => 'nullable|string|max:80'],
                'deskripsi' => ['label' => 'Deskripsi', 'tipe' => 'area', 'aturan' => 'nullable|string|max:600'],
                'foto'      => ['label' => 'Foto (opsional)', 'tipe' => 'foto', 'bantuan' => 'Tampil di bagian atas kartu produk.'],
            ],
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Scope
    |--------------------------------------------------------------------------
    */
    public function scopeTipe(Builder $q, string $tipe): Builder
    {
        return $q->where('tipe', $tipe);
    }

    // Yang tampil di situs, urut sesuai "urutan"
    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true)->orderBy('urutan')->orderBy('id_profil');
    }

    /*
    |--------------------------------------------------------------------------
    | Foto & teks
    |--------------------------------------------------------------------------
    */

    // URL foto: hasil unggahan, atau tautan luar, atau foto contoh kalau diminta ($seed) dan belum ada foto.
    public function fotoUrl(?string $seed = null, int $lebar = 800, int $tinggi = 600): ?string
    {
        if (filled($this->foto)) {
            return Str::startsWith($this->foto, ['http://', 'https://'])
                ? $this->foto
                : asset('storage/' . ltrim($this->foto, '/'));
        }

        return $seed ? "https://picsum.photos/seed/{$seed}/{$lebar}/{$tinggi}" : null;
    }

    // "Investasi *Berjangka*" -> "Investasi <em>Berjangka</em>" (aman: teks di-escape dulu)
    public static function tebal(?string $teks): string
    {
        return preg_replace('/\*(.+?)\*/u', '<em>$1</em>', e((string) $teks));
    }

    // Misi: satu butir per baris
    public static function daftarMisi(?string $teks): array
    {
        return $teks ? array_values(array_filter(array_map('trim', preg_split('/\R+/', $teks)))) : [];
    }

    // Pastikan 3 baris hero (perusahaan, tim, produk) selalu ada
    public static function pastikanHero(): void
    {
        if (static::where('tipe', 'hero')->count() >= 3) {
            return;
        }

        $awal = [
            'perusahaan' => ['Mitra Tepercaya', 'Investasi *Berjangka*'],
            'tim'        => ['Orang-orang di Balik', '*Equityworld.*'],
            'produk'     => ['Produk & Layanan', '*Unggulan Kami.*'],
        ];

        foreach ($awal as $halaman => [$baris1, $baris2]) {
            static::firstOrCreate(
                ['tipe' => 'hero', 'kategori' => $halaman],
                ['judul' => $baris1, 'label' => $baris2, 'aktif' => true]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Navigasi admin: halaman -> bagian (dengan status & tautan), dipakai menu & halaman depan admin
    |--------------------------------------------------------------------------
    */
    public static function navigasi(): array
    {
        static::pastikanHero();

        $per = static::query()
            ->selectRaw("id_profil, tipe, kategori, aktif, CASE WHEN deskripsi IS NOT NULL AND deskripsi <> '' THEN 1 ELSE 0 END AS terisi")
            ->get()
            ->groupBy('tipe');

        $hasil = [];

        foreach (self::HALAMAN as $kode => $h) {
            $urlHalaman = route($h['route']);
            $bagian = [];

            foreach ($h['bagian'] as $i => $tipe) {
                $cfg = self::TIPE[$tipe];
                $set = $per->get($tipe, collect());

                if ($tipe === 'hero') {
                    $row    = $set->firstWhere('kategori', $kode);
                    $url    = route('admin.konten.edit', ['hero', $row?->id_profil ?? 0]);
                    $ok     = true;
                    $status = 'Judul & foto';
                } elseif ($cfg['mode'] === 'tunggal') {
                    $ok     = (bool) $set->first()?->terisi;
                    $url    = route('admin.konten.daftar', $tipe);
                    $status = $ok ? 'Terisi' : 'Belum diisi';
                } else {
                    $n      = $set->count();
                    $sembunyi = $n - $set->where('aktif', true)->count();
                    $ok     = $n > 0;
                    $url    = route('admin.konten.daftar', $tipe);
                    $status = $n === 0 ? 'Belum ada data' : $n . ' item' . ($sembunyi > 0 ? " · {$sembunyi} disembunyikan" : '');
                }

                $bagian[] = [
                    'tipe'    => $tipe,
                    'nama'    => $cfg['nama'],
                    'bantuan' => $cfg['bantuan'],
                    'nomor'   => $i + 1,
                    'url'     => $url,
                    'status'  => $status,
                    'ok'      => $ok,
                    'situs'   => $urlHalaman . (! empty($cfg['anchor']) ? '#' . $cfg['anchor'] : ''),
                ];
            }

            $hasil[$kode] = [
                'nama'    => $h['nama'],
                'ringkas' => $h['ringkas'],
                'url'     => $urlHalaman,
                'path'    => parse_url($urlHalaman, PHP_URL_PATH) ?: '/',
                'bagian'  => $bagian,
            ];
        }

        return $hasil;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor alias: nama lama -> kolom baru (hanya untuk dibaca)
    |--------------------------------------------------------------------------
    */
    protected function nama(): Attribute        { return Attribute::make(get: fn () => $this->judul); }
    protected function namaKantor(): Attribute  { return Attribute::make(get: fn () => $this->judul); }
    protected function lembaga(): Attribute     { return Attribute::make(get: fn () => $this->judul); }
    protected function kota(): Attribute        { return Attribute::make(get: fn () => $this->kategori); }
    protected function divisi(): Attribute      { return Attribute::make(get: fn () => $this->kategori); }
    protected function jabatan(): Attribute     { return Attribute::make(get: fn () => $this->label); }
    protected function kode(): Attribute        { return Attribute::make(get: fn () => $this->label); }
    protected function singkatan(): Attribute   { return Attribute::make(get: fn () => $this->label); }
    protected function tahun(): Attribute       { return Attribute::make(get: fn () => $this->label); }
    protected function bio(): Attribute         { return Attribute::make(get: fn () => $this->deskripsi); }
    protected function isiKonten(): Attribute   { return Attribute::make(get: fn () => $this->deskripsi); }
    protected function nomorIzin(): Attribute   { return Attribute::make(get: fn () => $this->nomor); }
    protected function linkedinUrl(): Attribute { return Attribute::make(get: fn () => $this->tautan); }
    protected function isPusat(): Attribute     { return Attribute::make(get: fn () => (bool) $this->is_utama); }

    // Pakai tautan kalau diisi, kalau tidak buat otomatis dari nama kantor + alamat
    protected function linkPeta(): Attribute
    {
        return Attribute::make(get: fn () => filled($this->tautan)
            ? $this->tautan
            : 'https://www.google.com/maps/search/?api=1&query=' . urlencode(trim($this->judul . ' ' . $this->alamat)));
    }
}