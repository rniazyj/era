<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Surat Keterangan Magang</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 1.7cm 2cm 1.7cm 2cm;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #222;
            font-size: 9pt;
            line-height: 1.45;
        }

        .header {
            width: 100%;
            margin-bottom: 18px;
        }

        .header td {
            vertical-align: top;
        }

        .logo {
            width: 82px;
            height: auto;
        }

        .tagline {
            font-size: 8pt;
            font-style: italic;
            text-align: right;
            padding-top: 3px;
        }

        .title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 12px 0 18px;
        }

        .content {
            text-align: justify;
        }

        .intro {
            margin-bottom: 10px;
        }

        table.peserta {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 15px;
            font-size: 8.5pt;
        }

        table.peserta th,
        table.peserta td {
            border: 1px solid #777;
            padding: 5px 7px;
            vertical-align: middle;
        }

        table.peserta th {
            text-align: center;
            font-weight: bold;
        }

        table.peserta td:first-child {
            width: 27%;
            text-align: center;
        }

        table.peserta td:nth-child(2) {
            width: 40%;
        }

        table.peserta td:nth-child(3) {
            width: 33%;
        }

        .jadwal {
            width: 100%;
            margin: 12px 0 15px;
            border-collapse: collapse;
        }

        .jadwal td {
            padding: 2px 0;
            vertical-align: top;
        }

        .jadwal td:first-child {
            width: 65px;
        }

        .ketentuan {
            margin-top: 12px;
        }

        .ketentuan ol {
            padding-left: 22px;
            margin-top: 5px;
        }

        .ketentuan li {
            padding-left: 2px;
            margin-bottom: 4px;
            text-align: justify;
        }

        .penutup {
            margin-top: 12px;
        }

        .signature {
            width: 245px;
            margin-left: auto;
            margin-top: 18px;
            text-align: center;
        }

        .signature p {
            margin: 0;
        }

        .signature img {
            width: 235px;
            height: auto;
            margin-top: 3px;
        }
    </style>
</head>

<body>

    {{-- HEADER LOGO --}}
    <table class="header">
        <tr>
            <td width="65%"></td>
            <td width="35%" align="right">
                <img
                    src="{{ $gambarLogo }}"
                    class="logo"
                    alt="Logo PT Equityworld Futures"
                >

                <div class="tagline">
                    Strive For Solid Future
                </div>
            </td>
        </tr>
    </table>

    {{-- JUDUL SURAT --}}
    <div class="title">
        SURAT KETERANGAN MAGANG
    </div>

    <div class="content">

        <p class="intro">
            Menyatakan bahwa:
        </p>

        {{-- TABEL SELURUH PESERTA --}}
        <table class="peserta">
            <thead>
                <tr>
                    <th>NPM</th>
                    <th>NAMA</th>
                    <th>PRODI</th>
                </tr>
            </thead>

            <tbody>
                @foreach($anggotaTim as $anggota)
                    <tr>
                        <td>{{ $anggota->npm ?? '-' }}</td>
                        <td>{{ $anggota->nama ?? '-' }}</td>
                        <td>{{ $anggota->program_studi ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>
            {{ $daftarFakultas }} {{ $daftarInstitusi }}
            yang tercatat sebagai peserta magang di PT Equityworld Futures
            Surabaya pada periode
            {{ \Carbon\Carbon::parse($lamaran->tgl_mulai_magang)->translatedFormat('d F Y') }}
            sampai dengan
            {{ \Carbon\Carbon::parse($lamaran->tgl_selesai_magang)->translatedFormat('d F Y') }},
            dengan jadwal mengikuti jam kantor yaitu:
        </p>

        {{-- JADWAL MAGANG --}}
        <table class="jadwal">
            <tr>
                <td>Hari</td>
                <td>: Senin - Jumat</td>
            </tr>
            <tr>
                <td>Pukul</td>
                <td>: 08.00 - 17.00 WIB</td>
            </tr>
        </table>

        <p>
            Diharapkan selama proses magang tersebut peserta dapat
            diizinkan untuk tetap hadir sesuai jadwal tertera sampai
            periode magang terselesaikan.
        </p>

        <div class="ketentuan">
            <p>
                Kehadiran peserta magang menjadi salah satu penilaian
                terhadap kinerja magang di perusahaan kami, dengan
                berdasarkan pada:
            </p>

            <ol>
                <li>
                    Maksud Kesepahaman Bersama ini adalah untuk
                    meningkatkan potensi masing-masing pihak guna
                    memperoleh hasil yang maksimal dalam kegiatan
                    pendidikan/pengajaran, penelitian dan sumber daya manusia.
                </li>

                <li>
                    Tujuan Kesepahaman Bersama ini meningkatkan
                    pelaksanaan program-program nasional khususnya
                    di bidang pendidikan, penelitian, dan dalam bentuk
                    kuliah tamu, seminar, kajian-kajian penelitian dan
                    pelaksanaan peningkatan Kualitas Sumber Daya Manusia.
                </li>
            </ol>
        </div>

        <p class="penutup">
            Demikian kami sampaikan untuk dapat menjadi acuan kerja sama
            yang baik kedua belah pihak. Atas perhatian Bapak/Ibu kami
            sampaikan terima kasih.
        </p>

    </div>

    {{-- TANDA TANGAN DAN STEMPEL --}}
    <div class="signature">

        <p>
            Surabaya,
            {{ \Carbon\Carbon::parse($tanggalTerbit)->translatedFormat('d F Y') }}
        </p>

        <img
            src="{{ $gambarTtdStempel }}"
            alt="Tanda tangan dan stempel PT Equityworld Futures"
        >

    </div>

</body>
</html>
