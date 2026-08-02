<?php

namespace App\Exports;

use App\Models\RPS;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RPSExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(
        private ?int $semesterId = null,
        private ?int $prodiId = null,
        private ?int $fakultasId = null,
        private ?string $title = 'RPS Data',
    ) {}

    public function title(): string
    {
        return $this->title;
    }

    public function query()
    {
        $query = RPS::query()
            ->with(['mataKuliah.kurikulum.programStudi.fakultas', 'semester', 'user']);

        if ($this->semesterId) {
            $query->where('semester_id', $this->semesterId);
        }
        if ($this->prodiId) {
            $query->byProdi($this->prodiId);
        }
        if ($this->fakultasId) {
            $query->whereHas('mataKuliah.kurikulum.programStudi', function ($q) {
                $q->where('fakultas_id', $this->fakultasId);
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Mata Kuliah',
            'Kode MK',
            'Program Studi',
            'Fakultas',
            'Semester',
            'Dosen Pengampu',
            'Status',
            'Versi',
            'Deskripsi',
            'Dibuat',
            'Terakhir Diperbarui',
        ];
    }

    public function map($rps): array
    {
        return [
            $rps->id,
            $rps->mataKuliah?->name ?? '-',
            $rps->mataKuliah?->code ?? '-',
            $rps->mataKuliah?->kurikulum?->programStudi?->name ?? '-',
            $rps->mataKuliah?->kurikulum?->programStudi?->fakultas?->name ?? '-',
            $rps->semester?->name ?? '-',
            $rps->user?->name ?? '-',
            $rps->status->label(),
            $rps->version_label,
            $rps->deskripsi ?? '-',
            $rps->created_at?->format('d-m-Y H:i'),
            $rps->updated_at?->format('d-m-Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
