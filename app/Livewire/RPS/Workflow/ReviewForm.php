<?php

namespace App\Livewire\RPS\Workflow;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Enums\RPSStatus;
use App\Models\RPS;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

class ReviewForm extends Component
{
    public $rpsId;
    public $rps;
    public array $skorPerKomponen = [
        'cpl_cpmk' => null, 'sub_cpmk' => null, 'materi' => null,
        'metode' => null, 'assessment' => null, 'referensi' => null, 'alignment' => null,
    ];
    public array $komentar = [
        'cpl_cpmk' => '', 'sub_cpmk' => '', 'materi' => '',
        'metode' => '', 'assessment' => '', 'referensi' => '', 'alignment' => '',
    ];
    public string $catatan = '';
    public bool $showConfirmApprove = false;
    public bool $showConfirmRevision = false;
    public string $confirmAction = '';

    protected function rules(): array
    {
        return [
            'skorPerKomponen.cpl_cpmk' => ['required', 'integer', 'min:1', 'max:10'],
            'skorPerKomponen.sub_cpmk' => ['required', 'integer', 'min:1', 'max:10'],
            'skorPerKomponen.materi' => ['required', 'integer', 'min:1', 'max:10'],
            'skorPerKomponen.metode' => ['required', 'integer', 'min:1', 'max:10'],
            'skorPerKomponen.assessment' => ['required', 'integer', 'min:1', 'max:10'],
            'skorPerKomponen.referensi' => ['required', 'integer', 'min:1', 'max:10'],
            'skorPerKomponen.alignment' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function mount($rpsId): void
    {
        $this->rpsId = $rpsId;
        $this->rps = RPS::with([
            'mataKuliah.kurikulum.programStudi', 'semester', 'user', 'cpl',
            'cpml.cpl', 'cpml.subCpmk', 'materiPertemuan.subCpmk', 'assessment.subCpmk',
        ])->findOrFail($rpsId);
    }

    #[Computed]
    public function skorTotal(): int
    {
        $total = 0;
        foreach ($this->skorPerKomponen as $skor) {
            $total += (int) ($skor ?? 0);
        }
        return $total;
    }

    #[Computed]
    public function skorMax(): int
    {
        return count($this->skorPerKomponen) * 10;
    }

    #[Computed]
    public function komponenLabels(): array
    {
        return [
            'cpl_cpmk' => 'CPL & CPMK', 'sub_cpmk' => 'Sub-CPMK', 'materi' => 'Materi',
            'metode' => 'Metode Pembelajaran', 'assessment' => 'Assessment', 'referensi' => 'Referensi', 'alignment' => 'Alignment',
        ];
    }

    public function confirmApprove(): void
    {
        $this->validate();
        $this->confirmAction = 'approve';
        $this->showConfirmApprove = true;
    }

    public function confirmRevision(): void
    {
        if (empty(trim($this->catatan))) {
            session()->flash('error', 'Catatan alasan revisi wajib diisi.');
            return;
        }
        $this->validate();
        $this->confirmAction = 'revision';
        $this->showConfirmRevision = true;
    }

    public function executeApprove()
    {
        $user = Auth::user();
        $service = app(WorkflowService::class);
        $reviewData = [
            'rps_id' => $this->rps->id, 'skor_total' => $this->skorTotal,
            'skor_per_komponen' => $this->skorPerKomponen, 'komentar' => array_filter($this->komentar),
            'status' => 'approved', 'catatan' => $this->catatan ?: 'Disetujui',
        ];
        $service->review($this->rps, $user, $reviewData);
        $this->showConfirmApprove = false;
        session()->flash('message', 'RPS berhasil disetujui.');
        $this->redirect(route('review.list'), navigate: true);
    }

    public function executeRevision()
    {
        $user = Auth::user();
        $service = app(WorkflowService::class);
        $reviewData = [
            'rps_id' => $this->rps->id, 'skor_total' => $this->skorTotal,
            'skor_per_komponen' => $this->skorPerKomponen, 'komentar' => array_filter($this->komentar),
            'catatan' => $this->catatan,
        ];
        $service->requestRevision($this->rps, $user, $reviewData);
        $this->showConfirmRevision = false;
        session()->flash('message', 'Revisi diminta. Dosen akan diberi notifikasi.');
        $this->redirect(route('review.list'), navigate: true);
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmApprove = false;
        $this->showConfirmRevision = false;
    }

    public function render()
    {
        return view('livewire.rps.workflow.review-form');
    }
}
