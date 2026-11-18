<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LowonganKerja;
use App\Models\PersyaratanBerkas;
use App\Models\ProgramMagang;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminLowonganController extends Controller
{
    // Sesuaikan dengan nilai status yang kamu pakai di database
    private const STATUS = ['draft', 'dibuka', 'ditutup'];

    
public function index()
{
    // Lowongan kerja: ambil jumlah seluruh lamaran
    // dan data lamaran yang sudah diterima.
    $kerja = LowonganKerja::withCount('lamaran')
        ->with([
            'lamaran' => function ($query) {
                $query->where('status_lamaran', 'diterima');
            },
        ])
        ->get()
        ->map(fn ($l) => $this->mapKerja($l));

    // Program magang: ambil jumlah seluruh lamaran
    // dan jumlah anggota tim dari setiap lamaran yang diterima.
    $magang = ProgramMagang::withCount('lamaran')
        ->with([
            'lamaran' => function ($query) {
                $query->where('status_lamaran', 'diterima')
                    ->withCount('anggotaTim');
            },
        ])
        ->get()
        ->map(fn ($p) => $this->mapMagang($p));

    $items = $kerja
        ->concat($magang)
        ->sortByDesc('created_at')
        ->values();

    $statusList = self::STATUS;

    return view('admin.lowongan.index', compact('items', 'statusList'));
}


    // /admin/lowongan/create?jenis=kerja|magang  -> jenis langsung terpilih
    public function create(Request $request)
    {
        $awal = ucfirst(strtolower((string) $request->query('jenis')));

        return view('admin.lowongan.form', [
            'item'       => null,
            'statusList' => self::STATUS,
            'jenisAwal'  => in_array($awal, ['Kerja', 'Magang'], true) ? $awal : 'Magang',
        ]);
    }

    public function edit(string $jenis, int $id)
    {
        $jenis = ucfirst($jenis); // kerja -> Kerja, magang -> Magang
        abort_unless(in_array($jenis, ['Kerja', 'Magang'], true), 404);

        $item = $jenis === 'Kerja'
            ? $this->mapKerja(LowonganKerja::with('persyaratan')->findOrFail($id), true)
            : $this->mapMagang(ProgramMagang::with('persyaratan')->findOrFail($id), true);

        return view('admin.lowongan.form', [
            'item'       => $item,
            'statusList' => self::STATUS,
            'jenisAwal'  => $jenis,
        ]);
    }

    public function store(Request $request)
    {
        $jenis = $request->input('jenis');
        abort_unless(in_array($jenis, ['Kerja', 'Magang'], true), 422);

        return $this->save($request, $jenis, null);
    }

    public function update(Request $request, string $jenis, int $id)
    {
        $jenis = ucfirst($jenis);
        abort_unless(in_array($jenis, ['Kerja', 'Magang'], true), 404);

        return $this->save($request, $jenis, $id);
    }

    private function save(Request $request, string $jenis, ?int $id)
    {
        $common = [
            'judul'     => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string'],
            'kuota'     => ['required', 'integer', 'min:1', 'max:99999'],
            'tgl_buka'  => ['required', 'date'],
            'tgl_tutup' => ['required', 'date', 'after_or_equal:tgl_buka'],
            'status'    => ['required', Rule::in(self::STATUS)],

            'persyaratan'                       => ['required', 'array', 'min:1'],
            'persyaratan.*.id_persyaratan'      => ['nullable', 'integer'],
            'persyaratan.*.nama_berkas'         => ['required', 'string', 'max:255'],
            'persyaratan.*.format_file'         => ['required', 'string', Rule::in(['pdf', 'doc', 'docx'])],
            'persyaratan.*.wajib'               => ['required', Rule::in(['0', '1', 0, 1])],
        ];

        if ($jenis === 'Kerja') {
            $data = $request->validate(
                $common + [
                    'kualifikasi' => ['required', 'string'],
                    'gaji_min'    => ['required', 'integer', 'min:1'],
                    'gaji_max'    => ['required', 'integer', 'min:1', 'gte:gaji_min'],
                    'urgent'      => ['required', 'boolean'],
                ],
                $this->messages(),
                $this->attributes($jenis)
            );

            $model = DB::transaction(function () use ($data, $id) {
                $model = $id ? LowonganKerja::findOrFail($id) : new LowonganKerja();
                $model->fill(Arr::except($data, ['persyaratan']));

                if (! $id) {
                    $model->id_admin = auth()->id();
                }

                $model->save();

                return $model;
            });

            $gagal = $this->simpanPersyaratan($data['persyaratan'], 'Kerja', $model->id_lowongan);
        } else {
            $existing = $id ? ProgramMagang::findOrFail($id) : null;

            // Saat edit: kalau file silabus lama masih ada, upload ulang tidak wajib
            $sudahAdaFile = $existing && $this->silabusAda($existing->silabus);
            $silabusRule  = array_merge(
                $sudahAdaFile ? ['nullable'] : ['required'],
                ['file', 'mimes:pdf,doc,docx', 'max:5120'] // maks 5 MB
            );

            $data = $request->validate(
                $common + [
                    'silabus'     => $silabusRule,
                    'tgl_mulai'   => ['required', 'date'],
                    'tgl_selesai' => ['required', 'date', 'after_or_equal:tgl_mulai'],
                ],
                $this->messages(),
                $this->attributes($jenis)
            );

            // Simpan file silabus (kalau ada upload baru), hapus file lama
            $silabusPath = $existing?->silabus;

            if ($request->hasFile('silabus')) {
                $file = $request->file('silabus');

                if ($existing && $existing->silabus) {
                    Storage::disk('public')->delete($existing->silabus);
                }

                $nama = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                      . '.' . $file->extension();

                $silabusPath = $file->storeAs('silabus', $nama, 'public');
            }

            $model = DB::transaction(function () use ($existing, $data, $silabusPath, $id) {
                $model = $existing ?? new ProgramMagang();
                $model->fill([
                    'nama_program' => $data['judul'],
                    'deskripsi'    => $data['deskripsi'],
                    'silabus'      => $silabusPath,
                    'kuota'        => $data['kuota'],
                    'tgl_buka'     => $data['tgl_buka'],
                    'tgl_tutup'    => $data['tgl_tutup'],
                    'tgl_mulai'    => $data['tgl_mulai'],
                    'tgl_selesai'  => $data['tgl_selesai'],
                    'status'       => $data['status'],
                    // durasi dihitung otomatis dari tanggal mulai sampai selesai
                    'durasi_hari'  => (int) Carbon::parse($data['tgl_mulai'])->startOfDay()
                                        ->diffInDays(Carbon::parse($data['tgl_selesai'])->startOfDay()) + 1,
                ]);

                if (! $id) {
                    $model->id_admin = auth()->id();
                }

                $model->save();

                return $model;
            });

            $gagal = $this->simpanPersyaratan($data['persyaratan'], 'Magang', $model->id_program);
        }

        $redirect = redirect()->route('admin.lowongan.index')
            ->with('success', $id ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');

        if (! empty($gagal)) {
            $redirect->with('warning', 'Persyaratan "' . implode('", "', $gagal)
                . '" tidak dihapus karena sudah dipakai oleh berkas pelamar. Data lainnya tersimpan.');
        }

        return $redirect;
    }

    /**
     * Sinkronkan persyaratan berkas: baris lama diperbarui (ID dipertahankan supaya berkas pelamar
     * tetap terhubung), baris baru dibuat, baris yang dihapus admin dibuang.
     * Mengembalikan daftar nama persyaratan yang gagal dihapus (karena sudah dipakai pelamar).
     */
    private function simpanPersyaratan(array $items, string $jenis, int $parentId): array
    {
        $kolom = $jenis === 'Kerja' ? 'id_lowongan' : 'id_program';

        $lama = PersyaratanBerkas::where($kolom, $parentId)->get()->keyBy('id_persyaratan');
        $dipertahankan = [];

        DB::transaction(function () use ($items, $jenis, $parentId, $lama, &$dipertahankan) {
            foreach ($items as $item) {
                $attrs = [
                    'nama_berkas' => trim($item['nama_berkas']),
                    'format_file' => strtolower($item['format_file']),
                    'wajib'       => (int) $item['wajib'],
                ];

                $idLama = isset($item['id_persyaratan']) ? (int) $item['id_persyaratan'] : 0;

                if ($idLama && $lama->has($idLama)) {
                    $lama[$idLama]->update($attrs);
                    $dipertahankan[] = $idLama;
                } else {
                    PersyaratanBerkas::create($attrs + [
                        'id_lowongan' => $jenis === 'Kerja' ? $parentId : null,
                        'id_program'  => $jenis === 'Magang' ? $parentId : null,
                    ]);
                }
            }
        });

        $gagal = [];
        foreach ($lama as $idLama => $row) {
            if (in_array((int) $idLama, $dipertahankan, true)) {
                continue;
            }
            try {
                $row->delete();
            } catch (QueryException $e) {
                $gagal[] = $row->nama_berkas;
            }
        }

        return $gagal;
    }

    
private function mapKerja($l, bool $detail = false): array
{
    // Hitung jumlah pelamar yang sudah diterima.
    // Jika relasi sudah dimuat dari index(), gunakan data tersebut.
    // Jika belum dimuat (misalnya saat edit), hitung langsung dari database.
    $kuotaTerpakai = $l->relationLoaded('lamaran')
        ? $l->lamaran->count()
        : $l->lamaran()
            ->where('status_lamaran', 'diterima')
            ->count();

    $kuotaAwal = (int) ($l->kuota ?? 0);

    return [
        'id'             => $l->id_lowongan,
        'jenis'          => 'Kerja',
        'judul'          => $l->judul,
        'deskripsi'      => $l->deskripsi,
        'kualifikasi'    => $l->kualifikasi,
        'gaji_min'       => $l->gaji_min,
        'gaji_max'       => $l->gaji_max,
        'urgent'         => (int) ($l->urgent ?? 0),
        'silabus'        => '',
        'silabus_url'    => null,
        'silabus_nama'   => '',

        // Kuota awal tetap disimpan.
        'kuota'          => $kuotaAwal,

        // Data tambahan untuk perhitungan kuota.
        'kuota_terpakai' => $kuotaTerpakai,
        'sisa_kuota'     => max(0, $kuotaAwal - $kuotaTerpakai),

        'tgl_buka'       => $this->tanggal($l->tgl_buka),
        'tgl_tutup'      => $this->tanggal($l->tgl_tutup),
        'tgl_mulai'      => '',
        'tgl_selesai'    => '',
        'durasi_hari'    => null,
        'status'         => $l->status,

        // Tetap menghitung semua lamaran, bukan hanya yang diterima.
        'jumlah_pelamar' => (int) ($l->lamaran_count ?? 0),

        'created_at'     => $l->created_at?->timestamp ?? 0,
        'persyaratan'    => $detail
            ? $this->mapPersyaratan($l->persyaratan)
            : [],
    ];
}

    
private function mapMagang($p, bool $detail = false): array
{
    $ada = $this->silabusAda($p->silabus);

    // Ambil lamaran yang sudah diterima.
    // Hitung jumlah orang dari anggota tim, bukan jumlah formulir lamaran.
    if ($p->relationLoaded('lamaran')) {
        $kuotaTerpakai = $p->lamaran->sum(function ($lamaran) {
            return $lamaran->anggota_tim_count > 0
                ? $lamaran->anggota_tim_count
                : 1;
        });
    } else {
        $kuotaTerpakai = $p->lamaran()
            ->where('status_lamaran', 'diterima')
            ->withCount('anggotaTim')
            ->get()
            ->sum(function ($lamaran) {
                return $lamaran->anggota_tim_count > 0
                    ? $lamaran->anggota_tim_count
                    : 1;
            });
    }

    $kuotaAwal = (int) ($p->kuota ?? 0);

    return [
        'id'             => $p->id_program,
        'jenis'          => 'Magang',
        'judul'          => $p->nama_program,
        'deskripsi'      => $p->deskripsi,
        'kualifikasi'    => '',
        'silabus'        => $p->silabus ?? '',
        'silabus_url'    => $ada
            ? Storage::disk('public')->url($p->silabus)
            : null,
        'silabus_nama'   => $ada ? basename($p->silabus) : '',

        // Kuota awal tetap disimpan.
        'kuota'          => $kuotaAwal,

        // Data tambahan untuk perhitungan kuota.
        'kuota_terpakai' => $kuotaTerpakai,
        'sisa_kuota'     => max(0, $kuotaAwal - $kuotaTerpakai),

        'tgl_buka'       => $this->tanggal($p->tgl_buka),
        'tgl_tutup'      => $this->tanggal($p->tgl_tutup),
        'tgl_mulai'      => $this->tanggal($p->tgl_mulai),
        'tgl_selesai'    => $this->tanggal($p->tgl_selesai),
        'durasi_hari'    => $p->durasi_hari,
        'status'         => $p->status,

        // Tetap menghitung semua formulir lamaran.
        'jumlah_pelamar' => (int) ($p->lamaran_count ?? 0),

        'created_at'     => $p->created_at?->timestamp ?? 0,
        'persyaratan'    => $detail
            ? $this->mapPersyaratan($p->persyaratan)
            : [],
    ];
}

    private function mapPersyaratan($daftar): array
    {
        return collect($daftar)
            ->sortBy('id_persyaratan')
            ->map(fn ($s) => [
                'id_persyaratan' => $s->id_persyaratan,
                'nama_berkas'    => $s->nama_berkas,
                'format_file'    => $s->format_file,
                'wajib'          => (int) $s->wajib,
            ])
            ->values()
            ->all();
    }

    // Cek apakah kolom silabus berisi path file yang benar-benar ada di storage
    private function silabusAda(?string $path): bool
    {
        return ! empty($path) && Storage::disk('public')->exists($path);
    }

    // Menyeragamkan format tanggal (Y-m-d) untuk input type="date"
    private function tanggal($value): string
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : '';
    }

    private function messages(): array
    {
        return [
            'required'                   => ':attribute wajib diisi.',
            'max.string'                 => ':attribute maksimal :max karakter.',
            'date'                       => ':attribute tidak valid.',
            'in'                         => ':attribute tidak valid.',
            'kuota.integer'              => 'Kuota harus berupa angka bulat.',
            'kuota.min'                  => 'Kuota harus berupa angka positif (minimal 1).',
            'kuota.max'                  => 'Kuota maksimal :max.',
            'gaji_min.required'          => 'Gaji minimum wajib diisi.',
            'gaji_min.integer'           => 'Gaji minimum harus berupa angka bulat.',
            'gaji_min.min'               => 'Gaji minimum harus lebih dari 0.',
            'gaji_max.required'          => 'Gaji maksimum wajib diisi.',
            'gaji_max.integer'           => 'Gaji maksimum harus berupa angka bulat.',
            'gaji_max.min'               => 'Gaji maksimum harus lebih dari 0.',
            'gaji_max.gte'               => 'Gaji maksimum tidak boleh lebih kecil dari gaji minimum.',
            'urgent.required'            => 'Pengaturan urgent wajib dikirim.',
            'urgent.boolean'             => 'Pengaturan urgent tidak valid.',
            'tgl_tutup.after_or_equal'   => 'Tanggal tutup tidak boleh sebelum tanggal buka.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai magang tidak boleh sebelum tanggal mulai.',
            'silabus.file'               => 'Silabus harus berupa file.',
            'silabus.mimes'              => 'Silabus harus berupa file PDF, DOC, atau DOCX.',
            'silabus.max'                => 'Ukuran file silabus maksimal 5 MB.',

            'persyaratan.required'               => 'Persyaratan berkas wajib diisi.',
            'persyaratan.array'                  => 'Format persyaratan berkas tidak valid.',
            'persyaratan.min'                    => 'Minimal harus ada satu persyaratan berkas.',
            'persyaratan.*.nama_berkas.required' => 'Nama berkas persyaratan wajib diisi.',
            'persyaratan.*.nama_berkas.max'      => 'Nama berkas maksimal 255 karakter.',
            'persyaratan.*.format_file.required' => 'Format file persyaratan wajib dipilih.',
            'persyaratan.*.format_file.in'       => 'Format file hanya boleh PDF, DOC, atau DOCX.',
            'persyaratan.*.wajib.required'       => 'Status persyaratan wajib dipilih.',
            'persyaratan.*.wajib.in'             => 'Status persyaratan tidak valid.',
        ];
    }

    private function attributes(string $jenis): array
    {
        return [
            'judul'       => $jenis === 'Magang' ? 'Nama program magang' : 'Judul lowongan',
            'deskripsi'   => 'Deskripsi',
            'kualifikasi' => 'Kualifikasi',
            'gaji_min'    => 'Gaji minimum',
            'gaji_max'    => 'Gaji maksimum',
            'silabus'     => 'Silabus',
            'kuota'       => 'Kuota',
            'status'      => 'Status',
            'tgl_buka'    => 'Tanggal buka',
            'tgl_tutup'   => 'Tanggal tutup',
            'tgl_mulai'   => 'Tanggal mulai magang',
            'tgl_selesai' => 'Tanggal selesai magang',

            'persyaratan'               => 'Persyaratan berkas',
            'persyaratan.*.nama_berkas' => 'Nama berkas',
            'persyaratan.*.format_file' => 'Format file',
            'persyaratan.*.wajib'       => 'Status persyaratan',
        ];
    }
}