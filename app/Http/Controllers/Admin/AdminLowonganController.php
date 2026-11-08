<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LowonganKerja;
use App\Models\ProgramMagang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminLowonganController extends Controller
{
    // Sesuaikan dengan nilai status yang kamu pakai di database
    private const STATUS = ['draft', 'dibuka', 'ditutup'];

    public function index()
    {
        $kerja  = LowonganKerja::get()->map(fn ($l) => $this->mapKerja($l));
        $magang = ProgramMagang::get()->map(fn ($p) => $this->mapMagang($p));

        $items = $kerja->concat($magang)->sortByDesc('created_at')->values();
        $statusList = self::STATUS;

        return view('admin.lowongan.index', compact('items', 'statusList'));
    }

    public function create()
    {
        return view('admin.lowongan.form', [
            'item'       => null,
            'statusList' => self::STATUS,
        ]);
    }

    public function edit(string $jenis, int $id)
    {
        $jenis = ucfirst($jenis); // kerja -> Kerja, magang -> Magang
        abort_unless(in_array($jenis, ['Kerja', 'Magang'], true), 404);

        $item = $jenis === 'Kerja'
            ? $this->mapKerja(LowonganKerja::findOrFail($id))
            : $this->mapMagang(ProgramMagang::findOrFail($id));

        return view('admin.lowongan.form', [
            'item'       => $item,
            'statusList' => self::STATUS,
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
        ];

        if ($jenis === 'Kerja') {
            $data = $request->validate(
                $common + ['kualifikasi' => ['required', 'string']],
                $this->messages(),
                $this->attributes($jenis)
            );

            $model = $id ? LowonganKerja::findOrFail($id) : new LowonganKerja();
            $model->fill($data);

            if (! $id) {
                $model->id_admin = auth()->id();
            }

            $model->save();
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
        }

        return redirect()->route('admin.lowongan.index')
            ->with('success', $id ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
    }

    private function mapKerja($l): array
    {
        return [
            'id'           => $l->id_lowongan,
            'jenis'        => 'Kerja',
            'judul'        => $l->judul,
            'deskripsi'    => $l->deskripsi,
            'kualifikasi'  => $l->kualifikasi,
            'silabus'      => '',
            'silabus_url'  => null,
            'silabus_nama' => '',
            'kuota'        => $l->kuota,
            'tgl_buka'     => $this->tanggal($l->tgl_buka),
            'tgl_tutup'    => $this->tanggal($l->tgl_tutup),
            'tgl_mulai'    => '',
            'tgl_selesai'  => '',
            'durasi_hari'  => null,
            'status'       => $l->status,
            'created_at'   => $l->created_at?->timestamp ?? 0,
        ];
    }

    private function mapMagang($p): array
    {
        $ada = $this->silabusAda($p->silabus);

        return [
            'id'           => $p->id_program,
            'jenis'        => 'Magang',
            'judul'        => $p->nama_program,
            'deskripsi'    => $p->deskripsi,
            'kualifikasi'  => '',
            'silabus'      => $p->silabus ?? '',
            'silabus_url'  => $ada ? Storage::disk('public')->url($p->silabus) : null,
            'silabus_nama' => $ada ? basename($p->silabus) : '',
            'kuota'        => $p->kuota,
            'tgl_buka'     => $this->tanggal($p->tgl_buka),
            'tgl_tutup'    => $this->tanggal($p->tgl_tutup),
            'tgl_mulai'    => $this->tanggal($p->tgl_mulai),
            'tgl_selesai'  => $this->tanggal($p->tgl_selesai),
            'durasi_hari'  => $p->durasi_hari,
            'status'       => $p->status,
            'created_at'   => $p->created_at?->timestamp ?? 0,
        ];
    }

    // Cek apakah kolom silabus berisi path file yang benar-benar ada di storage
    private function silabusAda(?string $path): bool
    {
        return ! empty($path) && Storage::disk('public')->exists($path);
    }

    // Model belum meng-cast kolom tanggal, jadi nilainya string; ini menyeragamkan formatnya
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
            'tgl_tutup.after_or_equal'   => 'Tanggal tutup tidak boleh sebelum tanggal buka.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai magang tidak boleh sebelum tanggal mulai.',
            'silabus.file'               => 'Silabus harus berupa file.',
            'silabus.mimes'              => 'Silabus harus berupa file PDF, DOC, atau DOCX.',
            'silabus.max'                => 'Ukuran file silabus maksimal 5 MB.',
        ];
    }

    private function attributes(string $jenis): array
    {
        return [
            'judul'       => $jenis === 'Magang' ? 'Nama program magang' : 'Judul lowongan',
            'deskripsi'   => 'Deskripsi',
            'kualifikasi' => 'Kualifikasi',
            'silabus'     => 'Silabus',
            'kuota'       => 'Kuota',
            'status'      => 'Status',
            'tgl_buka'    => 'Tanggal buka',
            'tgl_tutup'   => 'Tanggal tutup',
            'tgl_mulai'   => 'Tanggal mulai magang',
            'tgl_selesai' => 'Tanggal selesai magang',
        ];
    }
}