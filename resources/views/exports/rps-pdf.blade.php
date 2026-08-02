<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 5px; }
        .meta { text-align: center; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #206bc4; color: white; padding: 8px 6px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <div class="meta">Generated: {{ $generatedAt }}</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Mata Kuliah</th>
                <th>Kode MK</th>
                <th>Program Studi</th>
                <th>Fakultas</th>
                <th>Semester</th>
                <th>Dosen</th>
                <th>Status</th>
                <th>Versi</th>
                <th>Diperbarui</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rpsList as $index => $rps)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $rps->mataKuliah?->name ?? '-' }}</td>
                    <td>{{ $rps->mataKuliah?->code ?? '-' }}</td>
                    <td>{{ $rps->mataKuliah?->kurikulum?->programStudi?->name ?? '-' }}</td>
                    <td>{{ $rps->mataKuliah?->kurikulum?->programStudi?->fakultas?->name ?? '-' }}</td>
                    <td>{{ $rps->semester?->name ?? '-' }}</td>
                    <td>{{ $rps->user?->name ?? '-' }}</td>
                    <td>{{ $rps->status->label() }}</td>
                    <td>{{ $rps->version_label }}</td>
                    <td>{{ $rps->updated_at?->format('d-m-Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">RPS OBE System - Generated automatically</div>
</body>
</html>
