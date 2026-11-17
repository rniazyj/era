
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Surat Penerimaan Magang</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 2.5cm 2.7cm 2.5cm 2.7cm;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #222;
            font-size: 11pt;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #222;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .company {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: .5px;
        }

        .subtitle {
            font-size: 9pt;
            margin-top: 3px;
        }

        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 25px;
            margin-bottom: 4px;
        }

        .number {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 28px;
        }

        .content {
            text-align: justify;
        }

        table.identity {
            width: 100%;
            margin: 16px 0;
            border-collapse: collapse;
        }

        table.identity td {
            padding: 4px 0;
            vertical-align: top;
        }

        table.identity td:first-child {
            width: 145px;
        }

        .signature {
            width: 260px;
            margin-left: auto;
            margin-top: 35px;
            text-align: left;
        }

        .signature img {
            width: 210px;
            height: auto;
            margin: 5px 0;
        }

        .sign-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .footer {
            margin-top: 35px;
            font-size: 9pt;
            color: #555;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="company">PT EQUITYWORLD FUTURES</div>
        <div class="subtitle">
            SURAT RESMI PENERIMAAN PESERTA MAGANG
        </div>
    </div>

    <div class="title">SURAT PENERIMAAN MAGANG</div>

    <div class="number">
        Nomor: {{ $nomorSurat }}
    </div>

    <div class="content">

        <p>
            Yang bertanda tangan di bawah ini, mewakili PT Equityworld Futures,
            dengan ini menyatakan bahwa:
        </p>

        <table class="identity">
            <tr>
                <td>Nama</td>
                <td>: <strong>{{ $lamaran->pelamar?->name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Program Magang</td>
                <td>: {{ $lamaran->program?->nama_program ?? '-' }}</td>
            </tr>
            <tr>
                <td>Periode Magang</td>
                <td>:
                    {{ \Carbon\Carbon::parse($lamaran->tgl_mulai_magang)->translatedFormat('d F Y') }}
                    s.d.
                    {{ \Carbon\Carbon::parse($lamaran->tgl_selesai_magang)->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Durasi</td>
                <td>: {{ $lamaran->durasi_hari }} hari</td>
            </tr>
        </table>

        <p>
            Dengan ini dinyatakan <strong>DITERIMA</strong> sebagai peserta
            program magang di PT Equityworld Futures sesuai dengan program
            dan periode yang telah disebutkan di atas.
        </p>

        <p>
            Peserta diharapkan dapat mengikuti seluruh ketentuan, arahan,
            serta kegiatan yang berlaku selama pelaksanaan program magang.
        </p>

        <p>
            Demikian surat penerimaan ini dibuat untuk dapat dipergunakan
            sebagaimana mestinya.
        </p>

    </div>

    <div class="signature">

        <p>
            Jakarta,
            {{ \Carbon\Carbon::parse($tanggalTerbit)->translatedFormat('d F Y') }}
            <br>
            PT Equityworld Futures
        </p>

        <img src="{{ $gambarTtdStempel }}" alt="Tanda tangan dan stempel">

        <div class="sign-name">
            {{ $namaPenandatangan }}
        </div>

        <div>
            {{ $jabatanPenandatangan }}
        </div>

    </div>

    <div class="footer">
        Surat ini diterbitkan secara resmi oleh PT Equityworld Futures.
    </div>

</body>
</html>
