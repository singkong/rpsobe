<?php

namespace App\Jobs;

use App\Models\RPS;
use App\Models\User;
use App\Services\PDFExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PDFExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public RPS $rps,
        public User $user,
    ) {}

    public function handle(PDFExportService $exportService): void
    {
        try {
            $filePath = $exportService->export($this->rps);

            if (!file_exists($filePath)) {
                Log::error('PDF export failed: file not created', [
                    'rps_id' => $this->rps->id,
                    'user_id' => $this->user->id,
                ]);
                return;
            }

            $relativePath = str_replace(storage_path('app') . DIRECTORY_SEPARATOR, '', $filePath);
            $relativePath = str_replace('\\', '/', $relativePath);

            $this->user->notify(new \App\Notifications\ExportReadyNotification(
                'RPS "' . $this->rps->mataKuliah->name . '" berhasil diexport ke PDF.',
                route('rps.export.download', ['rpsId' => $this->rps->id, 'format' => 'pdf']),
                $relativePath,
                'pdf'
            ));

            Log::info('PDF export completed', [
                'rps_id' => $this->rps->id,
                'user_id' => $this->user->id,
                'file' => $relativePath,
            ]);
        } catch (\Throwable $e) {
            Log::error('PDF export job failed', [
                'rps_id' => $this->rps->id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
