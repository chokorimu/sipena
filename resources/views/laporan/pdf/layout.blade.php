<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan - SIPENA</title>
    <style>
        @page {
            margin: 18mm 14mm 20mm;
        }

        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #1a2233;
        }

        .header {
            border-bottom: 2px solid #15803d;
            padding-bottom: 8px;
        }

        .header .sekolah {
            font-size: 14px;
            font-weight: bold;
            color: #15803d;
        }

        .header .sistem {
            font-size: 10px;
            color: #66758c;
        }

        .meta {
            font-size: 10px;
            color: #66758c;
            margin-bottom: 14px;
        }

        h2 {
            font-size: 13px;
            margin: 16px 0 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th {
            background-color: #f8f9fb;
            border: 1px solid #dfe6ef;
            padding: 5px 7px;
            font-size: 9px;
            text-transform: uppercase;
            text-align: left;
            color: #66758c;
        }

        td {
            border: 1px solid #dfe6ef;
            padding: 5px 7px;
            font-size: 10px;
        }

        .center {
            text-align: center;
        }

        .tuntas {
            color: #15803d;
            font-weight: bold;
        }

        .belum {
            color: #c13e45;
            font-weight: bold;
        }

        .muted {
            color: #66758c;
        }

        .footer {
            position: fixed;
            bottom: -14mm;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #96a2b4;
            border-top: 1px solid #dfe6ef;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="sekolah">MTs Negeri Ambon</div>
        <div class="sistem">SIPENA &mdash; Sistem Penilaian</div>
    </div>

    @yield('content')

    <div class="footer">
        Dicetak dari SIPENA pada {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
