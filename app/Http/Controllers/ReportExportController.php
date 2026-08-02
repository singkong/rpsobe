<?php

namespace App\Http\Controllers;

use App\Exports\RPSExport;
use App\Exports\AuditReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportController
{
    public function exportExcel(Request $request)
    {
        $semesterId = $request->input('semester_id');
        $prodiId = $request->input('prodi_id');
        $fakultasId = $request->input('fakultas_id');
        $type = $request->input('type', 'rps');

        if ($type === 'audit') {
            $action = $request->input('action');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            return Excel::download(
                new AuditReportExport(
                    action: $action,
                    dateFrom: $dateFrom,
                    dateTo: $dateTo,
                    title: 'Audit Report',
                ),
                'audit-report-' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        return Excel::download(
            new RPSExport(
                semesterId: $semesterId ? (int) $semesterId : null,
                prodiId: $prodiId ? (int) $prodiId : null,
                fakultasId: $fakultasId ? (int) $fakultasId : null,
                title: 'RPS Report',
            ),
            'rps-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $semesterId = $request->input('semester_id');
        $prodiId = $request->input('prodi_id');
        $fakultasId = $request->input('fakultas_id');

        $query = \App\Models\RPS::query()
            ->with(['mataKuliah.kurikulum.programStudi.fakultas', 'semester', 'user']);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }
        if ($prodiId) {
            $query->byProdi($prodiId);
        }
        if ($fakultasId) {
            $query->whereHas('mataKuliah.kurikulum.programStudi', function ($q) use ($fakultasId) {
                $q->where('fakultas_id', $fakultasId);
            });
        }

        $rpsList = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('exports.rps-pdf', [
            'rpsList' => $rpsList,
            'title' => 'RPS Report',
            'generatedAt' => now()->format('d-m-Y H:i:s'),
        ]);

        return $pdf->download('rps-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
