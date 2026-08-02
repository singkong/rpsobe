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
?>

<div class="d-inline-flex gap-2 align-items-center">
    <select wire:model.change="format" class="form-select form-select-sm" style="width:auto;">
        <option value="word">Word (.docx)</option>
        <option value="pdf">PDF</option>
    </select>

    <button
        wire:click="export"
        wire:loading.attr="disabled"
        wire:target="export"
        class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1"
        @if(!$rpsId) disabled @endif
    >
        <span wire:loading.remove wire:target="export">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                <path d="M12 11v6"/>
                <path d="M9.5 13.5l2.5 -2.5l2.5 2.5"/>
            </svg>
        </span>
        <span wire:loading wire:target="export" class="spinner-border spinner-border-sm me-1" role="status"></span>
        Export
    </button>

    @if($batchIds)
        <button
            wire:click="batchExport"
            wire:loading.attr="disabled"
            wire:target="batchExport"
            class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1"
        >
            <span wire:loading.remove wire:target="batchExport">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M22 12h-6l-2 3h-4l-2 -3h-6"/>
                    <path d="M5.5 7l1.5 10h10l1.5 -10"/>
                    <path d="M5.5 7h13"/>
                </svg>
            </span>
            <span wire:loading wire:target="batchExport" class="spinner-border spinner-border-sm me-1" role="status"></span>
            Batch Export ({{ count($batchIds) }})
        </button>
    @endif
</div>
