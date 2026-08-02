<?php

namespace App\Jobs;

use App\Models\RPS;
use App\Models\User;
use App\Services\WordExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WordExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public RPS $rps,
        public User $user,
    ) {}

    public function handle(WordExportService $exportService): void
    {
        try {
            $filePath = $exportService->export($this->rps);

            if (!file_exists($filePath)) {
                Log::error('Word export failed: file not created', [
                    'rps_id' => $this->rps->id,
                    'user_id' => $this->user->id,
                ]);
                return;
            }

            $relativePath = str_replace(storage_path('app') . DIRECTORY_SEPARATOR, '', $filePath);
            $relativePath = str_replace('\\', '/', $relativePath);

            $this->user->notify(new \App\Notifications\ExportReadyNotification(
                'RPS "' . $this->rps->mataKuliah->name . '" berhasil diexport ke Word.',
                route('rps.export.download', ['rpsId' => $this->rps->id, 'format' => 'word']),
                $relativePath,
                'word'
            ));

            Log::info('Word export completed', [
                'rps_id' => $this->rps->id,
                'user_id' => $this->user->id,
                'file' => $relativePath,
            ]);
        } catch (\Throwable $e) {
            Log::error('Word export job failed', [
                'rps_id' => $this->rps->id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
