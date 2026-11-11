<?php

namespace App\Http\Controllers;

use App\Models\BerkasLamaran;
use App\Models\Lamaran;
use App\Models\LowonganKerja;
use App\Models\ProfilPelamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class LowonganController extends Controller
{
    /** Nama route halaman edit profil pelamar. Sesuaikan dengan route-mu. */
    private const ROUTE_PROFIL = 'pelamar.profil';

    /** Item kelengkapan profil (label dari ProfilPelamar::kelengkapan()) yang WAJIB sebelum melamar. */
    private const SYARAT_PROFIL = [
        'Nama lengkap', 'No. telepon', 'Foto', 'Ringkasan diri',
        'Jenis kelamin', 'Tempat lahir', 'Tanggal lahir', 'Kota', 'Alamat',
        'Pendidikan',
    ];

    /* ===================== PUBLIK ===================== */

    public function index()
    {
        $daftarLowongan = LowonganKerja::buka()
            ->orderByDesc('urgent')
            ->orderByDesc('tgl_buka')
            ->orderByDesc('id_lowongan')
            ->get()
            ->map(fn (LowonganKerja $l) => [
                'id'       => $l->id_lowongan,
                'judul'    => $l->judul,
                'huruf'    => $l->huruf,
                'kategori' => $l->kategori_label,
                'posisi'   => (int) $l->kuota,
                'urgent'   => $l->is_mendesak,
                'status'   => $l->status_label,
                'gaji'     => $l->gaji_label,
                'gaji_max' => $l->gaji_max_jt,
                'deskripsi' => \Illuminate\Support\Str::limit(strip_tags((string) $l->deskripsi), 160),
            ])
            ->all();

        return view('pages.lowongan.index', compact('daftarLowongan'));
    }

    /** Semua orang (termasuk guest) boleh melihat detail. */
    public function show($id)
    {
        $lowongan = LowonganKerja::with('persyaratan')->findOrFail($id);

        $user = Auth::user();
        $kelayakan = null;

        if ($user && $user->isPelamar()) {
            $kelayakan = $this->kelayakan($user, $lowongan);
        }

        return view('pages.lowongan.show', [
            'lowongan'   => $lowongan,
            'kelayakan'  => $kelayakan,
            'linkProfil' => $this->linkProfil(),
        ]);
    }

    /* ===================== WAJIB LOGIN ===================== */

    public function apply($id)
    {
        $lowongan = LowonganKerja::with('persyaratan')->findOrFail($id);
        $user = Auth::user();

        if ($tolak = $this->tolakJikaTidakBisa($user, $lowongan)) {
            return $tolak;
        }

        $kelayakan = $this->kelayakan($user, $lowongan);

        return view('pages.lowongan.apply', [
            'lowongan'   => $lowongan,
            'kelayakan'  => $kelayakan,
            'user'       => $user,
            'linkProfil' => $this->linkProfil(),
        ]);
    }

    public function store(Request $request, $id)
    {
        $lowongan = LowonganKerja::with('persyaratan')->findOrFail($id);
        $user = Auth::user();

        if ($tolak = $this->tolakJikaTidakBisa($user, $lowongan)) {
            return $tolak;
        }

        $kelayakan = $this->kelayakan($user, $lowongan);
        if (! $kelayakan['profil_lengkap']) {
            return redirect()->route('lowongan.apply', $id)
                ->with('error', 'Lengkapi profilmu terlebih dahulu sebelum melamar.');
        }

        // Aturan validasi dinamis per persyaratan berkas
        $rules = ['pernyataan' => ['accepted']];
        $names = ['pernyataan' => 'pernyataan'];

        foreach ($lowongan->persyaratan as $p) {
            $key = 'berkas.' . $p->id_persyaratan;
            $rules[$key] = [
                $p->wajib ? 'required' : 'nullable',
                'file',
                'mimes:' . $p->format_list,
                'max:' . $p->maks_kb_attr,
            ];
            $names[$key] = $p->nama_berkas;
        }

        $request->validate($rules, [
            'pernyataan.accepted' => 'Kamu harus menyetujui pernyataan kebenaran data.',
            'berkas.*.required'   => ':attribute wajib diunggah.',
            'berkas.*.mimes'      => 'Format :attribute tidak sesuai.',
            'berkas.*.max'        => 'Ukuran :attribute melebihi batas.',
            'berkas.*.file'       => ':attribute harus berupa file.',
        ], $names);

        $tersimpan = [];

        try {
            DB::transaction(function () use ($request, $lowongan, $user, &$tersimpan) {
                // Cek ganda di dalam transaksi (menghindari double-submit)
                $sudah = Lamaran::where('id_pelamar', $user->id)
                    ->where('id_lowongan', $lowongan->id_lowongan)
                    ->lockForUpdate()
                    ->exists();

                if ($sudah) {
                    abort(409, 'Kamu sudah pernah melamar lowongan ini.');
                }

                $lamaran = Lamaran::create([
                    'id_pelamar'     => $user->id,
                    'id_lowongan'    => $lowongan->id_lowongan,
                    'tgl_pengajuan'  => now()->toDateString(),
                    'status_lamaran' => 'diajukan',
                ]);

                foreach ($lowongan->persyaratan as $p) {
                    $file = $request->file('berkas.' . $p->id_persyaratan);
                    if (! $file) {
                        continue;
                    }

                    $path = $file->store("berkas_lamaran/{$lamaran->id_lamaran}", 'public');
                    $tersimpan[] = $path;

                    BerkasLamaran::create([
                        'id_lamaran'     => $lamaran->id_lamaran,
                        'id_persyaratan' => $p->id_persyaratan,
                        'nama_file'      => $file->getClientOriginalName(),
                        'path_file'      => $path,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            // Transaksi gagal -> hapus file yang sempat tersimpan
            foreach ($tersimpan as $path) {
                Storage::disk('public')->delete($path);
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                return redirect()->route('lowongan.show', $id)->with('error', $e->getMessage());
            }

            report($e);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengirim lamaran. Silakan coba lagi.');
        }

        return redirect()->route('lowongan.show', $id)
            ->with('success', 'Lamaran berhasil dikirim. Pantau statusnya lewat akunmu.');
    }

    /* ===================== HELPER ===================== */

    private function linkProfil(): string
    {
        return Route::has(self::ROUTE_PROFIL) ? route(self::ROUTE_PROFIL) : url('/profil');
    }

    /** Alasan kenapa user tidak boleh melamar (null jika boleh). */
    private function tolakJikaTidakBisa($user, LowonganKerja $lowongan)
    {
        if (! $user->isPelamar()) {
            return redirect()->route('lowongan.show', $lowongan->id_lowongan)
                ->with('error', 'Hanya akun pelamar yang dapat melamar lowongan.');
        }

        if (! $lowongan->sedang_buka) {
            return redirect()->route('lowongan.show', $lowongan->id_lowongan)
                ->with('error', 'Pendaftaran lowongan ini sudah ditutup.');
        }

        $sudah = Lamaran::where('id_pelamar', $user->id)
            ->where('id_lowongan', $lowongan->id_lowongan)->exists();

        if ($sudah) {
            return redirect()->route('lowongan.show', $lowongan->id_lowongan)
                ->with('error', 'Kamu sudah pernah melamar lowongan ini.');
        }

        return null;
    }

    /**
     * Ringkasan kelayakan pelamar untuk satu lowongan.
     * - profil_lengkap : semua item di SYARAT_PROFIL terpenuhi
     * - profil_kurang  : daftar item yang belum
     * - sudah_melamar  : sudah pernah melamar lowongan ini
     * - boleh_melamar  : sedang buka + belum melamar + profil lengkap
     */
    private function kelayakan($user, LowonganKerja $lowongan): array
    {
        $profil = $user->profilPelamar ?? new ProfilPelamar(['id_pengguna' => $user->id]);
        $profil->setRelation('user', $user);

        $k = $profil->kelengkapan();

        $item   = collect($k['item'])->only(self::SYARAT_PROFIL);
        $kurang = $item->reject()->keys()->all();

        $sudah = Lamaran::where('id_pelamar', $user->id)
            ->where('id_lowongan', $lowongan->id_lowongan)->exists();

        $lengkap = count($kurang) === 0;

        return [
            'profil_item'    => $item->all(),
            'profil_kurang'  => $kurang,
            'profil_lengkap' => $lengkap,
            'persen_profil'  => $k['persen'],
            'sudah_melamar'  => $sudah,
            'boleh_melamar'  => $lowongan->sedang_buka && ! $sudah && $lengkap,
        ];
    }
}