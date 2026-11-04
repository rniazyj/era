<?php

namespace App\Http\Controllers;

use App\Models\Bantuan;
use Illuminate\Http\Request;

class BantuanController extends Controller
{
    // Halaman Pusat Bantuan (FAQ), bisa dicari lewat ?q=
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $grup = Bantuan::aktif()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('pertanyaan', 'like', $like)
                      ->orWhere('jawaban', 'like', $like);
                });
            })
            ->orderBy('kategori')
            ->orderBy('urutan')
            ->get()
            ->groupBy('kategori');

        return view('bantuan.index', [
            'grup'  => $grup,
            'q'     => $q,
            'email' => config('mail.from.address'),
        ]);
    }
}