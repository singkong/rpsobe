<?php

namespace App\Exports;

use App\Models\AuditLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditReportExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(
        private ?string $action = null,
        private ?int $userId = null,
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
        private ?string $title = 'Audit Log',
    ) {}

    public function title(): string
    {
        return $this->title;
    }

    public function query()
    {
        $query = AuditLog::query()->with(['user', 'tenant']);

        if ($this->action) {
            $query->where('action', $this->action);
        }
        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }
        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'User',
            'Email',
            'Tenant',
            'Aksi',
            'Model Type',
            'Model ID',
            'IP Address',
            'User Agent',
            'Waktu',
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->user?->name ?? 'System',
            $log->user?->email ?? '-',
            $log->tenant?->name ?? '-',
            $log->action,
            $log->model_type,
            $log->model_id,
            $log->ip_address ?? '-',
            $log->user_agent ?? '-',
            $log->created_at?->format('d-m-Y H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
