
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Dashboard Pimpinan</title>

    <style>
        @page {
            margin: 28px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #27272a;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #f97316;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0 0 5px;
        }

        .header p {
            color: #71717a;
            margin: 3px 0;
        }

        h2 {
            font-size: 12px;
            color: #ea580c;
            margin: 18px 0 8px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th, td {
            border: 1px solid #e4e4e7;
            padding: 7px 8px;
            text-align: left;
        }

        th {
            background: #fff1e6;
            color: #9a3412;
            font-weight: bold;
        }

        .summary td:first-child {
            width: 65%;
            font-weight: bold;
        }

        .muted {
            color: #71717a;
        }

        .footer {
            margin-top: 25px;
            padding-top: 8px;
            border-top: 1px solid #e4e4e7;
            text-align: center;
            color: #a1a1aa;
            font-size: 8px;
        }

        .two-columns {
            width: 100%;
        }

        .two-columns td {
            width: 50%;
            vertical-align: top;
            border: none;
            padding: 0 8px 0 0;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN DASHBOARD PIMPINAN</h1>
        <p>PT Equityworld Futures</p>
        <p>Periode: <strong>{{ $periodeLabel }}</strong></p>
        <p class="muted">
            Diekspor pada:
            {{ $tanggalEkspor->locale('id')->translatedFormat('d F Y, H:i') }}
        </p>
    </div>

    <h2>1. Ringkasan Pendaftar</h2>
    <table class="summary">
        <tr><td>Total Lamaran</td><td>{{ number_format($d['totalLamaran'], 0, ',', '.') }}</td></tr>
        <tr><td>Pelamar Unik</td><td>{{ number_format($d['pelamarUnik'], 0, ',', '.') }}</td></tr>
        <tr><td>Pendaftar Magang</td><td>{{ number_format($d['totalMagang'], 0, ',', '.') }}</td></tr>
        <tr><td>Pendaftar Kerja</td><td>{{ number_format($d['totalKerja'], 0, ',', '.') }}</td></tr>
    </table>

    <h2>2. Status Lamaran</h2>
    <table>
        <thead>
            <tr><th>Status</th><th>Jumlah</th></tr>
        </thead>
        <tbody>
            @foreach($d['perStatus'] as $status => $jumlah)
                <tr>
                    <td>{{ ucfirst($status) }}</td>
                    <td>{{ number_format($jumlah, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>3. Persentase Diterima</h2>
    <table class="summary">
        <tr><td>Diterima</td><td>{{ $d['diterima'] }}</td></tr>
        <tr><td>Ditolak</td><td>{{ $d['ditolak'] }}</td></tr>
        <tr><td>Sudah Diputuskan</td><td>{{ $d['diputuskan'] }}</td></tr>
        <tr>
            <td>Persentase Diterima</td>
            <td>{{ $d['persenDiterima'] !== null ? $d['persenDiterima'] . '%' : '-' }}</td>
        </tr>
        <tr><td>Belum Diputuskan</td><td>{{ $d['belumDiputuskan'] }}</td></tr>
        <tr><td>Menunggu Melebihi SLA</td><td>{{ $d['lewatSla'] }}</td></tr>
    </table>

    <h2>4. Waktu Proses</h2>
    <table class="summary">
        <tr><td>Jumlah Lamaran Diputuskan</td><td>{{ $d['waktu']['jumlah'] }}</td></tr>
        <tr><td>Rata-rata (hari)</td><td>{{ $d['waktu']['rata'] ?? '-' }}</td></tr>
        <tr><td>Median (hari)</td><td>{{ $d['waktu']['median'] ?? '-' }}</td></tr>
        <tr><td>Tercepat (hari)</td><td>{{ $d['waktu']['min'] ?? '-' }}</td></tr>
        <tr><td>Terlama (hari)</td><td>{{ $d['waktu']['max'] ?? '-' }}</td></tr>
    </table>

    <h2>5. Tren Pendaftar - 6 Bulan Terakhir</h2>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Total</th>
                <th>Magang</th>
                <th>Kerja</th>
            </tr>
        </thead>
        <tbody>
            @foreach($d['tren'] as $tren)
                <tr>
                    <td>{{ $tren['bulan'] }}</td>
                    <td>{{ $tren['total'] }}</td>
                    <td>{{ $tren['magang'] }}</td>
                    <td>{{ $tren['kerja'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>6. Surat Penerimaan</h2>
    <table class="summary">
        <tr><td>Total Surat</td><td>{{ $d['surat']['total'] }}</td></tr>
        <tr><td>Surat Terbit</td><td>{{ $d['surat']['terbit'] }}</td></tr>
        <tr><td>Surat Draft</td><td>{{ $d['surat']['draft'] }}</td></tr>
        <tr><td>Diterima Belum Memiliki Surat Terbit</td><td>{{ $d['surat']['diterimaBelumSurat'] }}</td></tr>
    </table>

    @if(count($d['surat']['perStatus']) > 0)
        <table>
            <thead>
                <tr><th>Status Surat</th><th>Jumlah</th></tr>
            </thead>
            <tbody>
                @foreach($d['surat']['perStatus'] as $surat)
                    <tr>
                        <td>{{ $surat['status'] }}</td>
                        <td>{{ $surat['n'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>7. Pengiriman</h2>
    <table class="summary">
        <tr><td>Total Pengiriman</td><td>{{ $d['kirim']['total'] }}</td></tr>
        <tr><td>Berhasil</td><td>{{ $d['kirim']['berhasil'] }}</td></tr>
        <tr><td>Gagal</td><td>{{ $d['kirim']['gagal'] }}</td></tr>
        <tr><td>Menunggu</td><td>{{ $d['kirim']['menunggu'] }}</td></tr>
        <tr>
            <td>Persentase Berhasil</td>
            <td>{{ $d['kirim']['persenBerhasil'] !== null ? $d['kirim']['persenBerhasil'] . '%' : '-' }}</td>
        </tr>
    </table>

    @if(count($d['kirim']['perStatus']) > 0)
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($d['kirim']['perStatus'] as $kirim)
                    <tr>
                        <td>{{ $kirim['status'] }}</td>
                        <td>{{ ucfirst($kirim['kategori']) }}</td>
                        <td>{{ $kirim['n'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(count($d['kirim']['perKanal']) > 0)
        <table>
            <thead>
                <tr><th>Kanal Pengiriman</th><th>Jumlah</th></tr>
            </thead>
            <tbody>
                @foreach($d['kirim']['perKanal'] as $kanal)
                    <tr>
                        <td>{{ $kanal['kanal'] }}</td>
                        <td>{{ $kanal['n'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh sistem rekrutmen PT Equityworld Futures.
    </div>

</body>
</html>
