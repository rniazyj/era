<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LamaranController extends Controller
{
    // Menampilkan halaman form lamaran
    public function create()
    {
        return view('pages.program.form'); // Memanggil file view yang kita buat di folder views/pelamar/
    }

    // Menyimpan data lamaran yang dikirim dari form
    public function store(Request $request)
    {
        // Validasi dan proses simpan data berkas ke database di sini
        // ...
        
        return redirect()->route('pelamar.dashboard')->with('success', 'Lamaran berhasil dikirim!');
    }
}