<?php

namespace App\Jobs;

use App\Models\RPS;
use App\Models\User;
use App\Services\PDFExportService;
use App\Services\WordExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BatchExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    public function __construct(
        public array $rpsIds,
        public string $format,
        public User $user,
    ) {}

    public function handle(WordExportService $wordService, PDFExportService $pdfService): void
    {
        try {
            $rpsList = RPS::with(['mataKuliah'])->whereIn('id', $this->rpsIds)->get();

            if ($rpsList->isEmpty()) {
                Log::warning('Batch export: no RPS found', ['ids' => $this->rpsIds]);
                return;
            }

            $zipPath = $this->format === 'pdf'
                ? $pdfService->exportMultiple($rpsList)
                : $wordService->exportMultiple($rpsList);

            if (!file_exists($zipPath)) {
                Log::error('Batch export failed: ZIP file not created');
                return;
            }

            $relativePath = str_replace(storage_path('app') . DIRECTORY_SEPARATOR, '', $zipPath);
            $relativePath = str_replace('\\', '/', $relativePath);

            $count = $rpsList->count();
            $formatLabel = $this->format === 'pdf' ? 'PDF' : 'Word';

            $this->user->notify(new \App\Notifications\ExportReadyNotification(
                "Batch export {$count} RPS ke format {$formatLabel} berhasil.",
                route('rps.export.download-batch', ['path' => base64_encode($relativePath)]),
                $relativePath,
                'zip'
            ));

            Log::info('Batch export completed', [
                'count' => $count,
                'format' => $this->format,
                'user_id' => $this->user->id,
                'file' => $relativePath,
            ]);
        } catch (\Throwable $e) {
            Log::error('Batch export job failed', [
                'rps_ids' => $this->rpsIds,
                'format' => $this->format,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
