<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataAkademik;
use App\Models\JadwalInterview;
use App\Models\Lamaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SeleksiController extends Controller
{
    private const STATUS = ['diajukan', 'diproses', 'diterima', 'ditolak'];

    // ---------- DAFTAR PELAMAR ----------
    public function index()
    {
        $items = Lamaran::with(['pelamar', 'lowongan', 'program'])
            ->orderByDesc('tgl_pengajuan')
            ->orderByDesc('id_lamaran')
            ->get()
            ->map(fn ($l) => [
                'id'     => $l->id_lamaran,
                'nama'   => $l->pelamar?->name ?? '-',
                'email'  => $l->pelamar?->email ?? '',
                'posisi' => $l->id_program
                    ? ($l->program?->nama_program ?? '-')
                    : ($l->lowongan?->judul ?? '-'),
                'jenis'  => $l->id_program ? 'Magang' : 'Kerja',
                'tgl'    => $l->tgl_pengajuan ? Carbon::parse($l->tgl_pengajuan)->format('Y-m-d') : '',
                'status' => $l->status_lamaran,
            ]);

        return view('admin.seleksi.index', [
            'items'      => $items,
            'statusList' => self::STATUS,
        ]);
    }

    // ---------- DETAIL PELAMAR ----------
    public function show(int $id)
    {
        $lamaran = Lamaran::with([
            'pelamar.profilPelamar.cv',
            'lowongan.persyaratan',
            'program.persyaratan',
            'berkas.persyaratan',
            'jadwal',
            'surat',
        ])->findOrFail($id);

        $profil = $lamaran->pelamar?->profilPelamar;
        $cv = $profil?->cv;

        // Riwayat pendidikan dari CV
        $idCv     = $profil?->cv?->id_cv;
        $akademik = $idCv
            ? DataAkademik::where('id_cv', $idCv)->orderByDesc('tahun_masuk')->get()
            : collect();

        // Cocokkan persyaratan berkas dengan berkas yang sudah diunggah
        $sumber  = $lamaran->id_program ? $lamaran->program : $lamaran->lowongan;
        $syarat  = $sumber?->persyaratan ?? collect();
        $berkas  = $lamaran->berkas;

        $daftarBerkas = $syarat->map(fn ($s) => [
            'syarat' => $s,
            'berkas' => $berkas->firstWhere('id_persyaratan', $s->id_persyaratan),
        ]);

        // Berkas yang diunggah tapi tidak terkait persyaratan tertentu
        $berkasLain = $berkas->filter(
            fn ($b) => empty($b->id_persyaratan) || ! $syarat->contains('id_persyaratan', $b->id_persyaratan)
        );

        return view('admin.seleksi.show', [
            'lamaran'      => $lamaran,
            'profil'       => $profil,
            'cv'           => $cv,
            'akademik'     => $akademik,
            'daftarBerkas' => $daftarBerkas,
            'berkasLain'   => $berkasLain,
            'jadwal'       => $lamaran->jadwal,
        ]);
    }

    // ---------- LIHAT CV PELAMAR ----------
public function lihatCv(int $id)
{
    // Ambil lamaran beserta profil dan CV pelamar
    $lamaran = Lamaran::with([
        'pelamar.profilPelamar.cv',
    ])->findOrFail($id);

    $user = $lamaran->pelamar;
    $profil = $user?->profilPelamar;
    $cv = $profil?->cv;

    // Pastikan pelamar memiliki CV
    abort_unless($user && $profil && $cv, 404, 'CV pelamar belum tersedia.');

    // Ambil data CV, sama seperti CvController milik pelamar
    $data = [
        'user'        => $user,
        'profil'      => $profil,
        'cv'          => $cv,
        'kelengkapan' => $profil->kelengkapan(),

        'akademik' => $cv->dataAkademik()
            ->orderByDesc('tahun_masuk')
            ->get(),

        'profesional' => $cv->pengalamanProfesional()
            ->orderByDesc('tgl_mulai')
            ->get(),

        'organisasi' => $cv->pengalamanOrganisasi()
            ->orderByDesc('tgl_mulai')
            ->get(),

        'proyek' => $cv->proyek()
            ->orderByDesc('id_proyek')
            ->get(),

        'hard' => $cv->skill()
            ->where('kategori_skill', 'hard')
            ->orderBy('nama_skill')
            ->get(),

        'soft' => $cv->skill()
            ->where('kategori_skill', 'soft')
            ->orderBy('nama_skill')
            ->get(),

        'tools' => $cv->skill()
            ->where('kategori_skill', 'tools')
            ->orderBy('nama_skill')
            ->get(),

        'fotoData' => $this->fotoCvBase64($profil),
    ];

    // Buat PDF menggunakan template CV yang sama
    $pdf = Pdf::loadView('pelamar.profil.cv-pdf', $data)
        ->setPaper('a4', 'portrait');

    $namaFile = 'CV_' . str_replace(
        ' ',
        '_',
        trim(preg_replace('/[^A-Za-z0-9 ]/', '', $user->name))
    ) . '.pdf';

    return $pdf->stream($namaFile);
}

// Foto menjadi base64 agar DomPDF dapat membacanya
private function fotoCvBase64($profil): ?string
{
    if (
        blank($profil->foto) ||
        !Storage::disk('public')->exists($profil->foto)
    ) {
        return null;
    }

    $path = Storage::disk('public')->path($profil->foto);
    $mime = mime_content_type($path);

    if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
        return null;
    }

    return 'data:' . $mime . ';base64,' . base64_encode(
        file_get_contents($path)
    );
}

    // ---------- UBAH STATUS LAMARAN ----------
    public function updateStatus(Request $request, int $id)
    {
        $lamaran = Lamaran::findOrFail($id);

        $data = $request->validate([
            'status'  => ['required', Rule::in(self::STATUS)],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status tidak valid.',
            'catatan.max'     => 'Catatan maksimal :max karakter.',
        ]);

        $status = $data['status'];
        $lamaran->status_lamaran = $status;

        if (in_array($status, ['diterima', 'ditolak'], true)) {
            // Keputusan akhir
            $lamaran->hasil_seleksi = $status;
            $lamaran->tgl_keputusan = now();

            if (filled($data['catatan'] ?? null)) {
                $lamaran->catatan_seleksi = $data['catatan'];
            }
        } else {
            // Kembali ke diajukan / diproses: keputusan dibatalkan
            $lamaran->hasil_seleksi = null;
            $lamaran->tgl_keputusan = null;
        }

        $lamaran->save();

        return back()->with('success', 'Status lamaran diubah menjadi "' . ucfirst($status) . '".');
    }

    // ---------- JADWAL INTERVIEW ----------
    public function simpanJadwal(Request $request, int $id)
    {
        $lamaran = Lamaran::findOrFail($id);

        $data = $request->validate([
            'tgl_interview'    => ['required', 'date', 'after_or_equal:today'],
            'jam_mulai'        => ['required', 'date_format:H:i'],
            'lokasi_atau_link' => ['required', 'string', 'max:255'],
            'catatan'          => ['nullable', 'string', 'max:1000'],
        ], [
            'required'                    => ':attribute wajib diisi.',
            'tgl_interview.date'          => 'Tanggal interview tidak valid.',
            'tgl_interview.after_or_equal' => 'Tanggal interview tidak boleh sebelum hari ini.',
            'jam_mulai.date_format'       => 'Jam mulai tidak valid.',
            'max'                         => ':attribute maksimal :max karakter.',
        ], [
            'tgl_interview'    => 'Tanggal interview',
            'jam_mulai'        => 'Jam mulai',
            'lokasi_atau_link' => 'Lokasi atau link',
            'catatan'          => 'Catatan',
        ]);

        JadwalInterview::updateOrCreate(
            ['id_lamaran' => $lamaran->id_lamaran],
            $data + ['status_jadwal' => 'terjadwal']
        );

        // Lamaran yang baru diajukan otomatis dianggap mulai diproses
        if ($lamaran->status_lamaran === 'diajukan') {
            $lamaran->status_lamaran = 'diproses';
            $lamaran->save();
        }

        return back()->with('success', 'Jadwal interview berhasil disimpan.');
    }
}