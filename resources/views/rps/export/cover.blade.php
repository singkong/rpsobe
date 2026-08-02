<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cover RPS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
            text-align: center;
        }
        .university {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 60px;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 40px;
        }
        .info-table {
            margin: 20px auto;
            text-align: left;
            display: inline-block;
        }
        .info-table td {
            padding: 4px 10px;
            font-size: 11pt;
        }
        .info-table td:first-child {
            font-weight: bold;
            white-space: nowrap;
        }
        .footer-info {
            margin-top: 60px;
            font-weight: bold;
            font-size: 12pt;
            line-height: 1.8;
        }

        @page { margin: 30mm 25mm 20mm 25mm; }
    </style>
</head>
<body>

    <div class="university">{{ strtoupper($tenant->name ?? '') }}</div>

    <h1>RENCANA PEMBELAJARAN SEMESTER</h1>
    <h2>(RPS)</h2>

    <table class="info-table">
        <tr><td>Mata Kuliah</td><td>: {{ $mk->name ?? '..................................' }}</td></tr>
        <tr><td>Kode MK</td><td>: {{ $mk->code ?? '..................................' }}</td></tr>
        <tr><td>Program Studi</td><td>: {{ $prodi->name ?? '..................................' }}</td></tr>
        <tr><td>Fakultas</td><td>: {{ $fakultas->name ?? '..................................' }}</td></tr>
        <tr><td>SKS</td><td>: {{ $mk->sks ?? '' }}</td></tr>
        <tr><td>Semester</td><td>: {{ $mk->semester ?? '' }}</td></tr>
        <tr><td>Dosen Pengampu</td><td>: {{ $dosenPengampu ?? '' }}</td></tr>
    </table>

    <div class="footer-info">
        <div>{{ strtoupper($prodi->name ?? '') }}</div>
        <div>{{ strtoupper($fakultas->name ?? '') }}</div>
        <div>{{ strtoupper($tenant->name ?? '') }}</div>
        <div>{{ $rps->semester->tahun_akademik ?? '' }}</div>
    </div>

</body>
</html>
