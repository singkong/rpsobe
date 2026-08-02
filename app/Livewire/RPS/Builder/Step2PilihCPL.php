<?php

use function Livewire\Volt\{state, mount};
use App\Enums\CPKategori;
use App\Models\RPS;
use App\Models\CPL;
use App\Services\RPSService;

state('rpsId', null);
state('rps', null);
state('selectedCpl', []);
state('cplGrouped', []);

mount(function ($rpsId) {
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

        $this->cplGrouped = array_filter($this->cplGrouped, fn ($g) => count($g['items']) > 0);
    }
});

$toggleCpl = function ($cplId) {
    if (in_array($cplId, $this->selectedCpl)) {
        $this->selectedCpl = array_values(array_filter($this->selectedCpl, fn ($id) => $id != $cplId));
    } else {
        $this->selectedCpl[] = $cplId;
    }
};

$save = function () {
    if ($this->rps) {
        $this->rps->cpl()->sync($this->selectedCpl);
    }

    $this->dispatch('rps-step-saved', step: 'cpl');
};

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pilih Capaian Pembelajaran Lulusan (CPL)</h3>
            <span class="badge bg-primary-lt ms-auto">{{ count($selectedCpl) }} CPL dipilih</span>
        </div>
        <div class="card-body">
            @if(empty($cplGrouped))
                <div class="alert alert-warning">
                    Tidak ada CPL yang tersedia untuk mata kuliah ini. Pastikan prodi terkait telah memiliki CPL.
                </div>
            @else
                @foreach($cplGrouped as $kategori => $group)
                    <div class="mb-4">
                        @php
                            $colors = [
                                'S' => 'blue',
                                'P' => 'green',
                                'KU' => 'orange',
                                'KK' => 'purple',
                            ];
                            $color = $colors[$kategori] ?? 'gray';
                        @endphp
                        <h4>
                            <span class="badge bg-{{ $color }}-lt text-{{ $color }}">{{ $group['label'] }}</span>
                        </h4>
                        @foreach($group['items'] as $cpl)
                            <div class="card card-sm mb-2 {{ in_array($cpl->id, $selectedCpl) ? 'border-primary bg-primary-lt' : '' }}">
                                <div class="card-body">
                                    <label class="form-check mb-0">
                                        <input type="checkbox" class="form-check-input"
                                               wire:change="toggleCpl({{ $cpl->id }})"
                                               @checked(in_array($cpl->id, $selectedCpl))>
                                        <span class="form-check-label">
                                            <strong>{{ $cpl->code }}</strong> - {{ $cpl->deskripsi }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif

            <div class="d-flex justify-content-end mt-3">
                <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Simpan CPL</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>

