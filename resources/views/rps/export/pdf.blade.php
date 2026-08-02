<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RPS - {{ $mk->name ?? 'Mata Kuliah' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
        }

        .cover-page {
            text-align: center;
            padding-top: 80px;
            page-break-after: always;
        }
        .cover-page .university {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .cover-page h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .cover-page h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 40px;
        }
        .cover-page .info-table {
            margin: 20px auto;
            text-align: left;
            display: inline-block;
        }
        .cover-page .info-table td {
            padding: 4px 10px;
            font-size: 11pt;
        }
        .cover-page .info-table td:first-child {
            font-weight: bold;
            white-space: nowrap;
        }
        .cover-page .footer-info {
            margin-top: 60px;
            font-weight: bold;
            font-size: 12pt;
            line-height: 1.8;
        }

        .section {
            page-break-after: always;
            padding: 10px 0;
        }
        .section:last-child {
            page-break-after: auto;
        }
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #000;
        }
        .section-subtitle {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 12px;
            margin-bottom: 5px;
        }
        .section-text {
            font-size: 11pt;
            margin-left: 20px;
            margin-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
        }
        table th {
            background-color: #D9E2F3;
            font-weight: bold;
            text-align: center;
        }
        table td {
            text-align: left;
        }

        .ref-list {
            list-style-type: decimal;
            padding-left: 30px;
        }
        .ref-list li {
            margin-bottom: 6px;
            font-size: 11pt;
        }

        .approval-table td {
            border: none;
            text-align: center;
            padding: 10px 20px;
            vertical-align: top;
        }
        .approval-table .signature-space {
            height: 80px;
        }
        .approval-table .name-line {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 60px;
        }
        .approval-table .date-line {
            font-size: 10pt;
            margin-top: 5px;
        }

        @page {
            margin: 30mm 25mm 20mm 25mm;
        }
        @media print {
            .section { page-break-after: always; }
            .section:last-child { page-break-after: auto; }
        }
    </style>
</head>
<body>

    {{-- COVER PAGE --}}
    <div class="cover-page">
        <div class="university">{{ strtoupper($tenant->name ?? '') }}</div>

        <h1>RENCANA PEMBELAJARAN SEMESTER</h1>
        <h2>(RPS)</h2>

        <table class="info-table" style="width:auto; border:none;">
            <tr><td style="border:none;">Mata Kuliah</td><td style="border:none;">: {{ $mk->name ?? '..................................' }}</td></tr>
            <tr><td style="border:none;">Kode MK</td><td style="border:none;">: {{ $mk->code ?? '..................................' }}</td></tr>
            <tr><td style="border:none;">Program Studi</td><td style="border:none;">: {{ $prodi->name ?? '..................................' }}</td></tr>
            <tr><td style="border:none;">Fakultas</td><td style="border:none;">: {{ $fakultas->name ?? '..................................' }}</td></tr>
            <tr><td style="border:none;">SKS</td><td style="border:none;">: {{ $mk->sks ?? '' }}</td></tr>
            <tr><td style="border:none;">Semester</td><td style="border:none;">: {{ $mk->semester ?? '' }}</td></tr>
            <tr><td style="border:none;">Dosen Pengampu</td><td style="border:none;">: {{ $dosenPengampu }}</td></tr>
        </table>

        <div class="footer-info">
            <div>{{ strtoupper($prodi->name ?? '') }}</div>
            <div>{{ strtoupper($fakultas->name ?? '') }}</div>
            <div>{{ strtoupper($tenant->name ?? '') }}</div>
            <div>{{ $rps->semester->tahun_akademik ?? '' }}</div>
        </div>
    </div>

    {{-- A. IDENTITAS MATA KULIAH --}}
    <div class="section">
        <div class="section-title">A. IDENTITAS MATA KULIAH</div>

        <table>
            <tr>
                <td style="width:35%; background:#D9E2F3; font-weight:bold;">Nama Universitas</td>
                <td style="width:65%;">{{ $tenant->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="background:#D9E2F3; font-weight:bold;">Fakultas</td>
                <td>{{ $fakultas->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="background:#D9E2F3; font-weight:bold;">Program Studi</td>
                <td>{{ $prodi->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="background:#D9E2F3; font-weight:bold;">Jenjang</td>
                <td>{{ $prodi->jenjang?->label() ?? '-' }}</td>
            </tr>
            <tr>
                <td style="background:#D9E2F3; font-weight:bold;">Kode Mata Kuliah</td>
                <td>{{ $mk->code ?? '-' }}</td>
            </tr>
            <tr>
                <td style="background:#D9E2F3; font-weight:bold;">Nama Mata Kuliah</td>
                <td>{{ $mk->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="background:#D9E2F3; font-weight:bold;">Jumlah SKS</td>
                <td>{{ $mk->sks ?? '-' }}</td>
            </tr>
            <tr>
                <td style="background:#D9E2F3; font-weight:bold;">Semester</td>
                <td>{{ $mk->semester ?? '-' }}</td>
            </tr>
            <tr>
                <td style="background:#D9E2F3; font-weight:bold;">Tahun Akademik</td>
                <td>{{ $rps->semester->tahun_akademik ?? '-' }}</td>
            </tr>
            <tr>
                <td style="background:#D9E2F3; font-weight:bold;">Dosen Pengampu</td>
                <td>{{ $dosenPengampu }}</td>
            </tr>
            <tr>
                <td style="background:#D9E2F3; font-weight:bold;">Deskripsi Mata Kuliah</td>
                <td>{{ $rps->deskripsi ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- B. CAPAIAN PEMBELAJARAN LULUSAN (CPL) --}}
    <div class="section">
        <div class="section-title">B. CAPAIAN PEMBELAJARAN LULUSAN (CPL)</div>

        @foreach($cplByKategori as $kategori => $list)
        <div class="section-subtitle">{{ $kategori }} :</div>
            @foreach($list as $cpl)
            <div class="section-text">{{ $cpl->code }} &nbsp; {{ $cpl->deskripsi }}</div>
            @endforeach
        <br>
        @endforeach
    </div>

    {{-- C. CAPAIAN PEMBELAJARAN MATA KULIAH (CPMK) --}}
    <div class="section">
        <div class="section-title">C. CAPAIAN PEMBELAJARAN MATA KULIAH (CPMK)</div>

        <table>
            <thead>
                <tr>
                    <th style="width:12%;">Kode CPMK</th>
                    <th style="width:53%;">Deskripsi CPMK</th>
                    <th style="width:35%;">CPL Terkait</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rps->cpml as $cpml)
                <tr>
                    <td>{{ $cpml->code }}</td>
                    <td>{{ $cpml->deskripsi }}</td>
                    <td>{{ $cpml->cpl->pluck('code')->implode(', ') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center;">Tidak ada data CPMK.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- D. SUB-CPMK --}}
    <div class="section">
        <div class="section-title">D. SUB-CAPAIAN PEMBELAJARAN MATA KULIAH (Sub-CPMK)</div>

        <table>
            <thead>
                <tr>
                    <th style="width:12%;">Kode Sub-CPMK</th>
                    <th style="width:65%;">Deskripsi</th>
                    <th style="width:23%;">Level Taksonomi</th>
                </tr>
            </thead>
            <tbody>
                @php $hasSub = false; @endphp
                @foreach($rps->cpml as $cpml)
                    @foreach($cpml->subCpmk as $sub)
                    @php $hasSub = true; @endphp
                    <tr>
                        <td>{{ $sub->code }}</td>
                        <td>{{ $sub->deskripsi }}</td>
                        <td>
                            @if($sub->level_taksonomi)
                                {{ $sub->level_taksonomi->value }} - {{ $sub->level_taksonomi->label() }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @endforeach
                @if(!$hasSub)
                <tr>
                    <td colspan="3" style="text-align:center;">Tidak ada data Sub-CPMK.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- E. MATERI PEMBELAJARAN PER PERTEMUAN --}}
    <div class="section">
        <div class="section-title">E. MATERI PEMBELAJARAN PER PERTEMUAN</div>

        <table>
            <thead>
                <tr>
                    <th style="width:7%;">Minggu Ke-</th>
                    <th style="width:20%;">Sub-CPMK</th>
                    <th style="width:35%;">Materi</th>
                    <th style="width:18%;">Indikator</th>
                    <th style="width:20%;">Metode</th>
                </tr>
            </thead>
            <tbody>
                @php $sorted = $rps->materiPertemuan->sortBy('pertemuan_ke'); @endphp
                @forelse($sorted as $pertemuan)
                <tr>
                    <td style="text-align:center;">{{ $pertemuan->pertemuan_ke }}</td>
                    <td>{{ $pertemuan->subCpmk->code ?? '-' }}</td>
                    <td>{{ $pertemuan->materi ?? '-' }}</td>
                    <td>{{ $pertemuan->indikator ?? '-' }}</td>
                    <td>
                        @if(is_array($pertemuan->metode_pembelajaran))
                            {{ implode(', ', $pertemuan->metode_pembelajaran) }}
                        @else
                            {{ $pertemuan->metode_pembelajaran ?? '-' }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;">Tidak ada data materi pertemuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- F. ASSESSMENT --}}
    <div class="section">
        <div class="section-title">F. ASSESSMENT / PENILAIAN</div>

        <table>
            <thead>
                <tr>
                    <th style="width:35%;">Nama Assessment</th>
                    <th style="width:15%;">Jenis</th>
                    <th style="width:12%;">Bobot (%)</th>
                    <th style="width:38%;">Sub-CPMK Terkait</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rps->assessment as $assessment)
                <tr>
                    <td>{{ $assessment->nama }}</td>
                    <td>{{ $assessment->jenis->label() }}</td>
                    <td style="text-align:center;">{{ $assessment->bobot_persen }}</td>
                    <td>{{ $assessment->subCpmk->pluck('code')->implode(', ') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;">Tidak ada data assessment.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- G. REFERENSI --}}
    <div class="section">
        <div class="section-title">G. REFERENSI</div>

        @if($referensis->isNotEmpty())
        <ol class="ref-list">
            @foreach($referensis as $ref)
            <li>
                @if($ref->penulis){{ $ref->penulis }}. @endif
                @if($ref->tahun)({{ $ref->tahun }}). @endif
                <em>{{ $ref->judul }}</em>.
                @if($ref->penerbit){{ $ref->penerbit }}.@endif
            </li>
            @endforeach
        </ol>
        @else
        <p>Tidak ada referensi.</p>
        @endif
    </div>

    {{-- H. PENGESAHAN --}}
    <div class="section" style="page-break-after:auto;">
        <div class="section-title">H. PENGESAHAN</div>

        <table class="approval-table" style="width:100%;">
            <tr>
                <td style="width:10%; border:none;"></td>
                <td style="width:45%; border:none; text-align:center;">
                    <div style="font-weight:bold;">Mengetahui,</div>
                    <div style="font-weight:bold;">Ketua Program Studi</div>
                    <div class="signature-space"></div>
                    <div class="name-line">{{ $prodi->kaprodi_name ?? '......................................' }}</div>
                    <div class="date-line">Tanggal: {{ $tanggalCetak }}</div>
                </td>
                <td style="width:45%; border:none; text-align:center;">
                    <div style="font-weight:bold;">Menyetujui,</div>
                    <div style="font-weight:bold;">Dekan</div>
                    <div class="signature-space"></div>
                    <div class="name-line">{{ $fakultas->dekan ?? '......................................' }}</div>
                    <div class="date-line">Tanggal: {{ $tanggalCetak }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
