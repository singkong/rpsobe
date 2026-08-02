<?php

namespace App\Services;

use App\Models\RPS;
use App\Models\Tenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;

class WordExportService
{
    private PhpWord $phpWord;
    private RPS $rps;

    public function export(RPS $rps): string
    {
        $this->rps = $rps->load([
            'mataKuliah.kurikulum.programStudi.fakultas.tenant',
            'semester',
            'cpl',
            'cpml.cpl',
            'cpml.subCpmk',
            'materiPertemuan.subCpmk.cpmk',
            'assessment.subCpmk',
        ]);

        $tenant = $rps->mataKuliah?->kurikulum?->programStudi?->fakultas?->tenant;
        $templatePath = $this->findTemplate($tenant);

        if ($templatePath && file_exists($templatePath)) {
            return $this->exportFromTemplate($templatePath);
        }

        return $this->exportFromScratch();
    }

    public function exportMultiple(Collection $rpsList): string
    {
        $zipPath = storage_path('app/exports/batch_' . now()->format('Ymd_His') . '.zip');
        $dir = dirname($zipPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Cannot create ZIP file');
        }

        foreach ($rpsList as $rps) {
            $filePath = $this->export($rps);
            $fileName = Str::slug($rps->mataKuliah->code . ' ' . $rps->mataKuliah->name) . '.docx';
            $zip->addFile($filePath, $fileName);
        }

        $zip->close();

        return $zipPath;
    }

    private function findTemplate(?Tenant $tenant): ?string
    {
        if ($tenant) {
            $tenantTemplate = 'templates/tenant_' . $tenant->id . '.docx';
            $fullPath = storage_path('app/' . $tenantTemplate);
            if (file_exists($fullPath)) {
                return $fullPath;
            }
        }

        $defaultTemplate = storage_path('app/templates/default.docx');
        if (file_exists($defaultTemplate)) {
            return $defaultTemplate;
        }

        return null;
    }

    private function exportFromTemplate(string $templatePath): string
    {
        $processor = new TemplateProcessor($templatePath);
        $this->setTemplateValues($processor);

        $outputPath = $this->getOutputPath();
        $processor->saveAs($outputPath);

        return $outputPath;
    }

    private function setTemplateValues(TemplateProcessor $processor): void
    {
        $rps = $this->rps;
        $mk = $rps->mataKuliah;
        $prodi = $mk?->kurikulum?->programStudi;
        $fakultas = $prodi?->fakultas;
        $tenant = $fakultas?->tenant;

        $values = [
            'nama_universitas' => $tenant?->name ?? '......................................',
            'nama_fakultas' => $fakultas?->name ?? '',
            'nama_prodi' => $prodi?->name ?? '',
            'jenjang' => $prodi?->jenjang?->label() ?? '',
            'kode_mk' => $mk?->code ?? '',
            'nama_mk' => $mk?->name ?? '',
            'sks' => (string) ($mk?->sks ?? ''),
            'semester' => (string) ($mk?->semester ?? ''),
            'tahun_akademik' => $rps->semester?->tahun_akademik ?? '',
            'nama_semester' => $rps->semester?->name ?? '',
            'deskripsi_mk' => $rps->deskripsi ?? '',
            'dosen_pengampu' => $this->formatDosenPengampu(),
            'kaprodi' => $prodi?->kaprodi_name ?? '......................................',
            'dekan' => $fakultas?->dekan ?? '......................................',
            'tanggal' => now()->translatedFormat('d F Y'),
            'akreditasi_prodi' => $prodi?->akreditasi ?? '-',
        ];

        foreach ($values as $key => $value) {
            $processor->setValue($key, $value);
        }
    }

    private function exportFromScratch(): string
    {
        $this->phpWord = new PhpWord;
        $this->phpWord->setDefaultFontName('Times New Roman');
        $this->phpWord->setDefaultFontSize(11);

        $this->addCoverPage();
        $this->addIdentitasMK();
        $this->addCPL();
        $this->addCPMK();
        $this->addSubCPMK();
        $this->addMateriPertemuan();
        $this->addAssessment();
        $this->addReferensi();
        $this->addPengesahan();

        $outputPath = $this->getOutputPath();
        $objWriter = IOFactory::createWriter($this->phpWord, 'Word2007');
        $objWriter->save($outputPath);

        return $outputPath;
    }

    private function getOutputPath(): string
    {
        $dir = storage_path('app/exports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileName = Str::slug($this->rps->mataKuliah->code . ' ' . $this->rps->mataKuliah->name);
        $fileName .= '_RPS_' . now()->format('Ymd_His') . '.docx';

        return $dir . DIRECTORY_SEPARATOR . $fileName;
    }

    // ─── COVER PAGE ───────────────────────────────────────────────

    private function addCoverPage(): void
    {
        $section = $this->phpWord->addSection();
        $rps = $this->rps;
        $mk = $rps->mataKuliah;
        $prodi = $mk?->kurikulum?->programStudi;
        $fakultas = $prodi?->fakultas;
        $tenant = $fakultas?->tenant;

        $section->addTextBreak(3);

        $section->addText(
            strtoupper($tenant?->name ?? ''),
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::CENTER]
        );

        $section->addTextBreak(2);

        $section->addText(
            'RENCANA PEMBELAJARAN SEMESTER',
            ['bold' => true, 'size' => 16, 'name' => 'Times New Roman'],
            ['alignment' => Jc::CENTER]
        );
        $section->addText(
            '(RPS)',
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::CENTER]
        );

        $section->addTextBreak(3);

        $this->addInfoLine($section, 'Mata Kuliah', $mk?->name ?? '', 20);
        $this->addInfoLine($section, 'Kode MK', $mk?->code ?? '', 20);
        $this->addInfoLine($section, 'Program Studi', $prodi?->name ?? '', 20);
        $this->addInfoLine($section, 'Fakultas', $fakultas?->name ?? '', 20);
        $this->addInfoLine($section, 'SKS', (string) ($mk?->sks ?? ''), 20);
        $this->addInfoLine($section, 'Semester', (string) ($mk?->semester ?? ''), 20);
        $this->addInfoLine($section, 'Dosen Pengampu', $this->formatDosenPengampu(), 20);

        $section->addTextBreak(4);

        $section->addText(
            strtoupper($prodi?->name ?? ''),
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::CENTER]
        );
        $section->addText(
            strtoupper($fakultas?->name ?? ''),
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::CENTER]
        );
        $section->addText(
            strtoupper($tenant?->name ?? ''),
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::CENTER]
        );
        $section->addText(
            $rps->semester?->tahun_akademik ?? '',
            ['bold' => true, 'size' => 12, 'name' => 'Times New Roman'],
            ['alignment' => Jc::CENTER]
        );
    }

    private function addInfoLine($section, string $label, string $value, int $indent): void
    {
        $fontLabel = ['bold' => true, 'size' => 11, 'name' => 'Times New Roman'];
        $fontValue = ['size' => 11, 'name' => 'Times New Roman'];

        $section->addText(
            $label . ' : ' . ($value ?: '..................................'),
            $fontValue,
            ['alignment' => Jc::CENTER, 'indentLeft' => $indent]
        );
    }

    // ─── IDENTITAS MATA KULIAH ────────────────────────────────────

    private function addIdentitasMK(): void
    {
        $section = $this->phpWord->addSection();
        $rps = $this->rps;
        $mk = $rps->mataKuliah;
        $prodi = $mk?->kurikulum?->programStudi;
        $fakultas = $prodi?->fakultas;
        $tenant = $fakultas?->tenant;

        $section->addText(
            'A. IDENTITAS MATA KULIAH',
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::LEFT]
        );

        $section->addTextBreak(1);

        $table = $section->addTable($this->tableStyle());

        $rows = [
            ['Nama Universitas', $tenant?->name ?? '-'],
            ['Fakultas', $fakultas?->name ?? '-'],
            ['Program Studi', $prodi?->name ?? '-'],
            ['Jenjang', $prodi?->jenjang?->label() ?? '-'],
            ['Kode Mata Kuliah', $mk?->code ?? '-'],
            ['Nama Mata Kuliah', $mk?->name ?? '-'],
            ['Jumlah SKS', (string) ($mk?->sks ?? '-')],
            ['Semester', (string) ($mk?->semester ?? '-')],
            ['Tahun Akademik', $rps->semester?->tahun_akademik ?? '-'],
            ['Dosen Pengampu', $this->formatDosenPengampu()],
            ['Deskripsi Mata Kuliah', $rps->deskripsi ?? '-'],
        ];

        foreach ($rows as $row) {
            $tableRow = $table->addRow();
            $tableRow->addCell(3600, ['bgColor' => 'D9E2F3'])->addText(
                $row[0],
                ['bold' => true, 'size' => 10, 'name' => 'Times New Roman']
            );
            $tableRow->addCell(7200)->addText(
                $row[1],
                ['size' => 10, 'name' => 'Times New Roman']
            );
        }
    }

    // ─── CAPAIAN PEMBELAJARAN LULUSAN (CPL) ───────────────────────

    private function addCPL(): void
    {
        $section = $this->phpWord->addSection();
        $rps = $this->rps;

        $section->addText(
            'B. CAPAIAN PEMBELAJARAN LULUSAN (CPL)',
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::LEFT]
        );

        $section->addTextBreak(1);

        $cplByKategori = $rps->cpl->groupBy(fn($cpl) => $cpl->kategori->label());

        foreach ($cplByKategori as $kategori => $list) {
            $section->addText(
                $kategori . ' :',
                ['bold' => true, 'size' => 11, 'name' => 'Times New Roman']
            );

            foreach ($list as $cpl) {
                $section->addText(
                    $cpl->code . '  ' . $cpl->deskripsi,
                    ['size' => 11, 'name' => 'Times New Roman'],
                    ['indentLeft' => 720]
                );
            }

            $section->addTextBreak(0.5);
        }
    }

    // ─── CAPAIAN PEMBELAJARAN MATA KULIAH (CPMK) ──────────────────

    private function addCPMK(): void
    {
        $section = $this->phpWord->addSection();
        $rps = $this->rps;

        $section->addText(
            'C. CAPAIAN PEMBELAJARAN MATA KULIAH (CPMK)',
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::LEFT]
        );

        $section->addTextBreak(1);

        $table = $section->addTable($this->tableStyle());
        $headerRow = $table->addRow();
        $this->addCell($headerRow, 1000, 'Kode CPMK', true);
        $this->addCell($headerRow, 4800, 'Deskripsi CPMK', true);
        $this->addCell($headerRow, 3000, 'CPL Terkait', true);

        foreach ($rps->cpml as $cpml) {
            $row = $table->addRow();
            $this->addCell($row, 1000, $cpml->code);
            $this->addCell($row, 4800, $cpml->deskripsi);
            $this->addCell($row, 3000, $cpml->cpl->pluck('code')->implode(', '));
        }
    }

    // ─── SUB-CPMK ─────────────────────────────────────────────────

    private function addSubCPMK(): void
    {
        $section = $this->phpWord->addSection();
        $rps = $this->rps;

        $section->addText(
            'D. SUB-CAPAIAN PEMBELAJARAN MATA KULIAH (Sub-CPMK)',
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::LEFT]
        );

        $section->addTextBreak(1);

        $table = $section->addTable($this->tableStyle());
        $headerRow = $table->addRow();
        $this->addCell($headerRow, 1000, 'Kode Sub-CPMK', true);
        $this->addCell($headerRow, 5800, 'Deskripsi', true);
        $this->addCell($headerRow, 2000, 'Level Taksonomi', true);

        foreach ($rps->cpml as $cpml) {
            foreach ($cpml->subCpmk as $sub) {
                $row = $table->addRow();
                $this->addCell($row, 1000, $sub->code);
                $this->addCell($row, 5800, $sub->deskripsi);
                $this->addCell(
                    $row,
                    2000,
                    $sub->level_taksonomi
                        ? $sub->level_taksonomi->value . ' - ' . $sub->level_taksonomi->label()
                        : '-'
                );
            }
        }
    }

    // ─── MATERI PEMBELAJARAN PER PERTEMUAN ────────────────────────

    private function addMateriPertemuan(): void
    {
        $section = $this->phpWord->addSection();
        $rps = $this->rps;

        $section->addText(
            'E. MATERI PEMBELAJARAN PER PERTEMUAN',
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::LEFT]
        );

        $section->addTextBreak(1);

        $table = $section->addTable($this->tableStyle());
        $headerRow = $table->addRow();
        $this->addCell($headerRow, 700, 'Minggu', true);
        $this->addCell($headerRow, 2000, 'Sub-CPMK', true);
        $this->addCell($headerRow, 3000, 'Materi', true);
        $this->addCell($headerRow, 1800, 'Indikator', true);
        $this->addCell($headerRow, 1500, 'Metode', true);

        $sorted = $rps->materiPertemuan->sortBy('pertemuan_ke');

        foreach ($sorted as $pertemuan) {
            $row = $table->addRow();
            $this->addCell($row, 700, (string) $pertemuan->pertemuan_ke);
            $this->addCell($row, 2000, $pertemuan->subCpmk?->code ?? '-');
            $this->addCell($row, 3000, $pertemuan->materi ?? '-');
            $this->addCell($row, 1800, $pertemuan->indikator ?? '-');
            $this->addCell(
                $row,
                1500,
                is_array($pertemuan->metode_pembelajaran)
                    ? implode(', ', $pertemuan->metode_pembelajaran)
                    : ($pertemuan->metode_pembelajaran ?? '-')
            );
        }
    }

    // ─── ASSESSMENT ───────────────────────────────────────────────

    private function addAssessment(): void
    {
        $section = $this->phpWord->addSection();
        $rps = $this->rps;

        $section->addText(
            'F. ASSESSMENT / PENILAIAN',
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::LEFT]
        );

        $section->addTextBreak(1);

        $table = $section->addTable($this->tableStyle());
        $headerRow = $table->addRow();
        $this->addCell($headerRow, 3000, 'Nama Assessment', true);
        $this->addCell($headerRow, 1500, 'Jenis', true);
        $this->addCell($headerRow, 1000, 'Bobot (%)', true);
        $this->addCell($headerRow, 3300, 'Sub-CPMK Terkait', true);

        foreach ($rps->assessment as $assessment) {
            $row = $table->addRow();
            $this->addCell($row, 3000, $assessment->nama);
            $this->addCell($row, 1500, $assessment->jenis->label());
            $this->addCell($row, 1000, (string) $assessment->bobot_persen);
            $this->addCell(
                $row,
                3300,
                $assessment->subCpmk->pluck('code')->implode(', ')
            );
        }
    }

    // ─── REFERENSI ────────────────────────────────────────────────

    private function addReferensi(): void
    {
        $section = $this->phpWord->addSection();
        $rps = $this->rps;

        $section->addText(
            'G. REFERENSI',
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => Jc::LEFT]
        );

        $section->addTextBreak(1);

        $referensiIds = $rps->materiPertemuan
            ->pluck('referensi_ids')
            ->filter()
            ->flatten()
            ->unique()
            ->toArray();

        if (!empty($referensiIds)) {
            $referensis = \App\Models\Referensi::whereIn('id', $referensiIds)
                ->orderBy('penulis')
                ->get();

            $num = 1;
            foreach ($referensis as $ref) {
                $text = $num . '. ';
                if ($ref->penulis) {
                    $text .= $ref->penulis . '. ';
                }
                if ($ref->tahun) {
                    $text .= '(' . $ref->tahun . '). ';
                }
                $text .= $ref->judul . '. ';
                if ($ref->penerbit) {
                    $text .= $ref->penerbit . '.';
                }

                $section->addText($text, ['size' => 11, 'name' => 'Times New Roman']);
                $num++;
            }
        } else {
            $section->addText(
                'Tidak ada referensi.',
                ['size' => 11, 'name' => 'Times New Roman']
            );
        }
    }

    // ─── PENGESAHAN ───────────────────────────────────────────────

    private function addPengesahan(): void
    {
        $section = $this->phpWord->addSection();
        $rps = $this->rps;
        $prodi = $rps->mataKuliah?->kurikulum?->programStudi;
        $fakultas = $prodi?->fakultas;

        $section->addTextBreak(2);

        $table = $section->addTable($this->tableStyle());
        $headerRow = $table->addRow();
        $this->addCell($headerRow, 1200, '', false, ['bgColor' => null]);
        $this->addCell($headerRow, 3800, 'Mengetahui,\nKetua Program Studi', true, ['alignment' => Jc::CENTER]);
        $this->addCell($headerRow, 3800, 'Menyetujui,\nDekan', true, ['alignment' => Jc::CENTER]);

        $blankRow = $table->addRow();
        $this->addCell($blankRow, 1200, '', false, ['bgColor' => null]);
        $this->addCell($blankRow, 3800, "\n\n\n\n" . ($prodi?->kaprodi_name ?? '......................................'), false, ['alignment' => Jc::CENTER]);
        $this->addCell($blankRow, 3800, "\n\n\n\n" . ($fakultas?->dekan ?? '......................................'), false, ['alignment' => Jc::CENTER]);

        $dateRow = $table->addRow();
        $this->addCell($dateRow, 1200, '', false, ['bgColor' => null]);
        $this->addCell($dateRow, 3800, 'Tanggal: ' . now()->translatedFormat('d F Y'), false, ['alignment' => Jc::CENTER, 'size' => 10]);
        $this->addCell($dateRow, 3800, 'Tanggal: ' . now()->translatedFormat('d F Y'), false, ['alignment' => Jc::CENTER, 'size' => 10]);
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    private function tableStyle(): array
    {
        return [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 60,
            'width' => TblWidth::PERCENT,
            'unit' => 'pct',
        ];
    }

    private function addCell($row, int $width, string $text, bool $isHeader = false, array $extraStyle = []): void
    {
        $style = array_merge(
            [
                'bgColor' => $isHeader ? 'D9E2F3' : null,
                'size' => 9,
                'name' => 'Times New Roman',
            ],
            $extraStyle
        );

        $cell = $row->addCell($width, [
            'bgColor' => $style['bgColor'],
            'valign' => 'center',
        ]);

        $fontStyle = [
            'bold' => $isHeader,
            'size' => $style['size'] ?? 9,
            'name' => $style['name'] ?? 'Times New Roman',
        ];

        $paragraphStyle = [
            'alignment' => $style['alignment'] ?? Jc::LEFT,
        ];

        $cell->addText($text, $fontStyle, $paragraphStyle);
    }

    private function formatDosenPengampu(): string
    {
        $rps = $this->rps;

        if (is_array($rps->dosen_pengampu_json) && !empty($rps->dosen_pengampu_json)) {
            return implode(', ', $rps->dosen_pengampu_json);
        }

        $mk = $rps->mataKuliah;

        if ($mk && $mk->relationLoaded('dosens')) {
            return $mk->dosens->pluck('name')->implode(', ') ?: '-';
        }

        return $rps->user?->name ?? '-';
    }
}
