
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Log Aktivitas</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #27272a;
        }

        .header {
            border-bottom: 2px solid #f97316;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 5px;
        }

        .subtitle {
            color: #71717a;
            font-size: 9px;
        }

        .info {
            margin: 12px 0;
            color: #52525b;
            line-height: 1.7;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f97316;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 8px;
        }

        td {
            border-bottom: 1px solid #e4e4e7;
            padding: 7px 5px;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background: #fafafa;
        }

        .berhasil {
            color: #059669;
            font-weight: bold;
        }

        .gagal {
            color: #e11d48;
            font-weight: bold;
        }

        .footer {
            margin-top: 15px;
            color: #71717a;
            font-size: 8px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Laporan Log Aktivitas</h1>
        <div class="subtitle">
            Sistem Rekrutmen PT Equityworld Futures
        </div>
    </div>

    <div class="info">
        <strong>Tanggal ekspor:</strong>
        {{ $tanggalEkspor->format('d-m-Y H:i:s') }}<br>

        <strong>Jumlah aktivitas:</strong>
        {{ number_format($logs->count(), 0, ',', '.') }} data<br>

        <strong>Filter tanggal:</strong>
        {{ $filter['dari'] ?? 'Semua' }}
        sampai
        {{ $filter['sampai'] ?? 'Semua' }}<br>

        <strong>Filter role:</strong>
        {{ $filter['role'] ?? 'Semua role' }}<br>

        <strong>Filter modul:</strong>
        {{ $filter['modul'] ?? 'Semua modul' }}<br>

        <strong>Filter hasil:</strong>
        {{ $filter['status'] ?? 'Semua hasil' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Waktu</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aktivitas</th>
                <th>Modul</th>
                <th>Hasil</th>
                <th>Alamat IP</th>
            </tr>
        </thead>

        <tbody>
            @forelse($logs as $i => $log)
                @php
                    $pengguna = $log->pengguna;
                    $status = strtolower($log->status_aksi);
                @endphp

                <tr>
                    <td>{{ $i + 1 }}</td>

                    <td>
                        {{ $log->waktu?->format('d-m-Y H:i:s') }}
                    </td>

                    <td>
                        {{ $pengguna?->name ?? 'Tidak dikenal' }}
                    </td>

                    <td>
                        {{ $pengguna?->email ?? '-' }}
                    </td>

                    <td>
                        {{ $pengguna?->role?->nama_role ?? 'Tamu' }}
                    </td>

                    <td>
                        {{ $log->aktivitas }}
                    </td>

                    <td>
                        {{ $log->modul }}
                    </td>

                    <td class="{{ $status === 'berhasil' ? 'berhasil' : 'gagal' }}">
                        {{ ucfirst($log->status_aksi) }}
                    </td>

                    <td>
                        {{ $log->alamat_ip ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" align="center">
                        Tidak ada aktivitas yang sesuai dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh sistem.
        Data log aktivitas bersifat hanya-baca.
    </div>

</body>
</html>
