<?php

namespace App\Services;

use App\Models\RPS;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PDFExportService
{
    public function export(RPS $rps): string
    {
        $rps->load([
            'mataKuliah.kurikulum.programStudi.fakultas.tenant',
            'semester',
            'cpl',
            'cpml.cpl',
            'cpml.subCpmk',
            'materiPertemuan.subCpmk.cpmk',
            'assessment.subCpmk',
        ]);

        $data = $this->prepareData($rps);

        $html = view('rps.export.pdf', $data)->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4');
        $pdf->setOptions([
            'defaultFont' => 'Times New Roman',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
            'margin_top' => '30mm',
            'margin_bottom' => '20mm',
            'margin_left' => '25mm',
            'margin_right' => '25mm',
        ]);

        $outputPath = $this->getOutputPath($rps);
        $pdf->save($outputPath);

        return $outputPath;
    }

    public function exportMultiple(Collection $rpsList): string
    {
        $zipPath = storage_path('app/exports/batch_pdf_' . now()->format('Ymd_His') . '.zip');
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
            $fileName = Str::slug($rps->mataKuliah->code . ' ' . $rps->mataKuliah->name) . '.pdf';
            $zip->addFile($filePath, $fileName);
        }

        $zip->close();

        return $zipPath;
    }

    private function prepareData(RPS $rps): array
    {
        $mk = $rps->mataKuliah;
        $prodi = $mk?->kurikulum?->programStudi;
        $fakultas = $prodi?->fakultas;
        $tenant = $fakultas?->tenant;

        $cplByKategori = $rps->cpl->groupBy(fn($cpl) => $cpl->kategori->label());

        $referensiIds = $rps->materiPertemuan
            ->pluck('referensi_ids')
            ->filter()
            ->flatten()
            ->unique()
            ->toArray();

        $referensis = !empty($referensiIds)
            ? \App\Models\Referensi::whereIn('id', $referensiIds)->orderBy('penulis')->get()
            : collect();

        return [
            'rps' => $rps,
            'mk' => $mk,
            'prodi' => $prodi,
            'fakultas' => $fakultas,
            'tenant' => $tenant,
            'cplByKategori' => $cplByKategori,
            'referensis' => $referensis,
            'dosenPengampu' => $this->formatDosenPengampu($rps),
            'tanggalCetak' => now()->translatedFormat('d F Y'),
        ];
    }

    private function getOutputPath(RPS $rps): string
    {
        $dir = storage_path('app/exports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileName = Str::slug($rps->mataKuliah->code . ' ' . $rps->mataKuliah->name);
        $fileName .= '_RPS_' . now()->format('Ymd_His') . '.pdf';

        return $dir . DIRECTORY_SEPARATOR . $fileName;
    }

    private function formatDosenPengampu(RPS $rps): string
    {
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
