<?php

namespace App\Http\Controllers;

use App\Jobs\BatchExportJob;
use App\Models\RPS;
use App\Services\PDFExportService;
use App\Services\WordExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function download(RPS $rps, string $format)
    {
        if (!in_array($format, ['word', 'pdf'])) {
            abort(404);
        }

        try {
            if ($format === 'word') {
                $service = app(WordExportService::class);
                $filePath = $service->export($rps);
                $contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                $extension = 'docx';
            } else {
                $service = app(PDFExportService::class);
                $filePath = $service->export($rps);
                $contentType = 'application/pdf';
                $extension = 'pdf';
            }

            if (!file_exists($filePath)) {
                abort(500, 'File gagal dibuat.');
            }

            $fileName = \Illuminate\Support\Str::slug(
                $rps->mataKuliah->code . ' ' . $rps->mataKuliah->name
            ) . '_RPS.' . $extension;

            return response()->download($filePath, $fileName, [
                'Content-Type' => $contentType,
            ])->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            abort(500, 'Gagal mengexport RPS: ' . $e->getMessage());
        }
    }

    public function batchExport(Request $request)
    {
        $validated = $request->validate([
            'rps_ids' => 'required|array|min:1',
            'rps_ids.*' => 'integer|exists:rps,id',
            'format' => 'required|in:word,pdf',
        ]);

        $job = BatchExportJob::dispatch(
            $validated['rps_ids'],
            $validated['format'],
            auth()->user()
        );

        return response()->json([
            'message' => 'Batch export sedang diproses.',
            'job_id' => $job,
        ]);
    }

    public function downloadBatch(string $path)
    {
        $decoded = base64_decode($path);

        if (!$decoded || !Storage::disk('local')->exists($decoded)) {
            abort(404, 'File tidak ditemukan atau sudah kadaluarsa.');
        }

        $fullPath = Storage::disk('local')->path($decoded);
        $fileName = basename($decoded);

        return response()->download($fullPath, $fileName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }
}
