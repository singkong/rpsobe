<?php

namespace App\Livewire\RPS\Builder;

use Livewire\Component;
use App\Enums\CPKategori;
use App\Models\RPS;
use App\Models\CPL;
use App\Services\RPSService;

class Step2PilihCPL extends Component
{
    public $rpsId = null;
    public $rps = null;
    public array $selectedCpl = [];
    public array $cplGrouped = [];

    public function mount($rpsId): void
    {
        $this->rpsId = $rpsId;

        if ($this->rpsId) {
            $this->rps = RPS::with('mataKuliah.kurikulum.programStudi', 'cpl')->findOrFail($this->rpsId);

            $this->selectedCpl = $this->rps->cpl->pluck('id')->toArray();

            $service = app(RPSService::class);
            $allCpl = $service->getAvailableCPL($this->rps->mataKuliah);

            $this->cplGrouped = [
                CPKategori::Sikap->value => ['label' => CPKategori::Sikap->label(), 'items' => []],
                CPKategori::Pengetahuan->value => ['label' => CPKategori::Pengetahuan->label(), 'items' => []],
                CPKategori::KeterampilanUmum->value => ['label' => CPKategori::KeterampilanUmum->label(), 'items' => []],
                CPKategori::KeterampilanKhusus->value => ['label' => CPKategori::KeterampilanKhusus->label(), 'items' => []],
            ];

            foreach ($allCpl as $cpl) {
                $cat = $cpl->kategori?->value ?? CPKategori::Sikap->value;
                if (isset($this->cplGrouped[$cat])) {
                    $this->cplGrouped[$cat]['items'][] = $cpl;
                }
            }

            $this->cplGrouped = array_filter($this->cplGrouped, fn($g) => count($g['items']) > 0);
        }
    }

    public function toggleCpl(int $cplId): void
    {
        if (in_array($cplId, $this->selectedCpl)) {
            $this->selectedCpl = array_values(array_filter($this->selectedCpl, fn($id) => $id != $cplId));
        } else {
            $this->selectedCpl[] = $cplId;
        }
    }

    public function save(): void
    {
        if ($this->rps) {
            $this->rps->cpl()->sync($this->selectedCpl);
        }

        $this->dispatch('rps-step-saved', step: 'cpl');
    }

    public function render()
    {
        return view('livewire.rps.builder.step2-pilih-cpl');
    }
}
