<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use App\Jobs\WordExportJob;
use App\Jobs\PDFExportJob;
use App\Jobs\BatchExportJob;
use App\Models\RPS;
use App\Services\WordExportService;
use App\Services\PDFExportService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    public ?int $rpsId = null;
    public string $format = 'word';
    public bool $isLoading = false;
    public bool $useQueue = true;
    public ?array $batchIds = null;

    #[Computed]
    public function rps(): ?RPS
    {
        if (!$this->rpsId) {
            return null;
        }

        return RPS::with([
            'mataKuliah.kurikulum.programStudi.fakultas.tenant',
            'semester',
            'cpl',
            'cpml.cpl',
            'cpml.subCpmk',
            'materiPertemuan',
            'assessment.subCpmk',
        ])->find($this->rpsId);
    }

    public function export(): void
    {
        $this->validate([
            'rpsId' => 'required|integer|exists:rps,id',
            'format' => 'required|in:word,pdf',
        ]);

        $this->isLoading = true;

        try {
            if ($this->useQueue) {
                $rps = RPS::with('mataKuliah')->find($this->rpsId);

                if ($this->format === 'word') {
                    WordExportJob::dispatch($rps, auth()->user());
                } else {
                    PDFExportJob::dispatch($rps, auth()->user());
                }

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Export sedang diproses. Anda akan menerima notifikasi setelah selesai.',
                ]);
            } else {
                $filePath = $this->generateInline();

                if ($filePath) {
                    $this->dispatch('download-file', [
                        'url' => route('rps.export.download', [
                            'rpsId' => $this->rpsId,
                            'format' => $this->format,
                        ]),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Gagal mengexport: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function batchExport(): void
    {
        $this->validate([
            'batchIds' => 'required|array|min:1',
            'batchIds.*' => 'integer|exists:rps,id',
            'format' => 'required|in:word,pdf',
        ]);

        $this->isLoading = true;

        try {
            BatchExportJob::dispatch($this->batchIds, $this->format, auth()->user());

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Batch export sedang diproses. Anda akan menerima notifikasi setelah selesai.',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Gagal batch export: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    private function generateInline(): ?string
    {
        $rps = $this->rps();

        if (!$rps) {
            return null;
        }

        if ($this->format === 'word') {
            $service = app(WordExportService::class);
            return $service->export($rps);
        } else {
            $service = app(PDFExportService::class);
            return $service->export($rps);
        }
    }
};

return view('livewire.export.export-button');