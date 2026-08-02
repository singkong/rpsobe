<?php

use function Livewire\Volt\{state, mount, computed};
use App\Enums\AssessmentJenis;
use App\Models\RPS;
use App\Models\Assessment;
use App\Models\SubCPMK;

state('rpsId', null);
state('rps', null);
state('assessmentList', []);
state('allSubCpmk', []);
state('saved', false);

state('addingNew', false);
state('newNama', '');
state('newJenis', '');
state('newBobot', 0);
state('newDeskripsi', '');
state('newRubrik', '');
state('newSubCpmkIds', []);

state('editingId', null);
state('editNama', '');
state('editJenis', '');
state('editBobot', 0);
state('editDeskripsi', '');
state('editRubrik', '');
state('editSubCpmkIds', []);

state('expandedRubrik', []);

$jenisOptions = fn () => collect(AssessmentJenis::cases())->map(fn ($j) => [
    'value' => $j->value,
    'label' => $j->label(),
])->toArray();

$totalBobot = fn () => round(array_sum(array_column($this->assessmentList, 'bobot')), 2);

$bobotClass = fn () => abs($this->totalBobot() - 100) < 0.01 ? 'text-green' : 'text-red';

mount(function ($rpsId) {
    $this->rpsId = $rpsId;

    if ($this->rpsId) {
        $this->rps = RPS::with(['assessment.subCpmk.cpmk'])->findOrFail($this->rpsId);

        $this->allSubCpmk = SubCPMK::whereHas('cpmk', function ($q) {
            $q->where('rps_id', $this->rpsId);
        })->with('cpmk')->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'code' => $s->code,
                'cpml_code' => $s->cpmk?->code ?? '',
                'deskripsi' => $s->deskripsi,
            ];
        })->toArray();

        $this->assessmentList = $this->rps->assessment->map(function ($a) {
            return [
                'id' => $a->id,
                'nama' => $a->nama,
                'jenis' => $a->jenis->value,
                'jenis_label' => $a->jenis->label(),
                'bobot' => $a->bobot_persen,
                'deskripsi' => $a->deskripsi,
                'rubrik' => $a->rubrik,
                'sub_cpmk_ids' => $a->subCpmk->pluck('id')->toArray(),
                'sub_cpmk_codes' => $a->subCpmk->pluck('code')->implode(', '),
            ];
        })->toArray();
    }
});

$startAdd = function () {
    $this->addingNew = true;
    $this->newNama = '';
    $this->newJenis = '';
    $this->newBobot = 0;
    $this->newDeskripsi = '';
    $this->newRubrik = '';
    $this->newSubCpmkIds = [];
};

$cancelAdd = function () {
    $this->addingNew = false;
};

$saveNew = function () {
    $this->validate([
        'newNama' => ['required', 'string'],
        'newJenis' => ['required', 'in:formatif,sumatif'],
        'newBobot' => ['required', 'numeric', 'min:1', 'max:100'],
    ], [
        'newNama.required' => 'Nama assessment wajib diisi.',
        'newJenis.required' => 'Jenis assessment wajib dipilih.',
        'newBobot.required' => 'Bobot assessment wajib diisi.',
    ]);

    $assessment = Assessment::create([
        'rps_id' => $this->rpsId,
        'nama' => $this->newNama,
        'bobot_persen' => $this->newBobot,
        'jenis' => $this->newJenis,
        'deskripsi' => $this->newDeskripsi,
        'rubrik' => $this->newRubrik ?: null,
    ]);

    if (!empty($this->newSubCpmkIds)) {
        $assessment->subCpmk()->sync($this->newSubCpmkIds);
    }

    $codes = SubCPMK::whereIn('id', $this->newSubCpmkIds)->pluck('code')->implode(', ');

    $this->assessmentList[] = [
        'id' => $assessment->id,
        'nama' => $assessment->nama,
        'jenis' => $assessment->jenis->value,
        'jenis_label' => $assessment->jenis->label(),
        'bobot' => $assessment->bobot_persen,
        'deskripsi' => $assessment->deskripsi,
        'rubrik' => $assessment->rubrik,
        'sub_cpmk_ids' => $this->newSubCpmkIds,
        'sub_cpmk_codes' => $codes,
    ];

    $this->addingNew = false;
    $this->dispatch('rps-step-saved', step: 'assessment');
};

$startEdit = function ($id) {
    $item = collect($this->assessmentList)->firstWhere('id', $id);
    if ($item) {
        $this->editingId = $id;
        $this->editNama = $item['nama'];
        $this->editJenis = $item['jenis'];
        $this->editBobot = $item['bobot'];
        $this->editDeskripsi = $item['deskripsi'] ?? '';
        $this->editRubrik = $item['rubrik'] ?? '';
        $this->editSubCpmkIds = $item['sub_cpmk_ids'];
    }
};

$cancelEdit = function () {
    $this->editingId = null;
};

$saveEdit = function ($id) {
    $this->validate([
        'editNama' => ['required', 'string'],
        'editJenis' => ['required', 'in:formatif,sumatif'],
        'editBobot' => ['required', 'numeric', 'min:1', 'max:100'],
    ]);

    $assessment = Assessment::findOrFail($id);
    $assessment->update([
        'nama' => $this->editNama,
        'bobot_persen' => $this->editBobot,
        'jenis' => $this->editJenis,
        'deskripsi' => $this->editDeskripsi,
        'rubrik' => $this->editRubrik ?: null,
    ]);

    $assessment->subCpmk()->sync($this->editSubCpmkIds);

    $codes = SubCPMK::whereIn('id', $this->editSubCpmkIds)->pluck('code')->implode(', ');

    $index = collect($this->assessmentList)->search(fn ($item) => $item['id'] === $id);
    if ($index !== false) {
        $this->assessmentList[$index] = [
            'id' => $assessment->id,
            'nama' => $assessment->nama,
            'jenis' => $assessment->jenis->value,
            'jenis_label' => $assessment->jenis->label(),
            'bobot' => $assessment->bobot_persen,
            'deskripsi' => $assessment->deskripsi,
            'rubrik' => $assessment->rubrik,
            'sub_cpmk_ids' => $this->editSubCpmkIds,
            'sub_cpmk_codes' => $codes,
        ];
    }

    $this->editingId = null;
    $this->dispatch('rps-step-saved', step: 'assessment');
};

$deleteAssessment = function ($id) {
    Assessment::findOrFail($id)->delete();
    $this->assessmentList = array_values(array_filter($this->assessmentList, fn ($item) => $item['id'] !== $id));
};

$toggleRubrik = function ($id) {
    if (in_array($id, $this->expandedRubrik ?? [])) {
        $this->expandedRubrik = array_values(array_filter($this->expandedRubrik, fn ($v) => $v != $id));
    } else {
        $exp = $this->expandedRubrik ?? [];
        $exp[] = $id;
        $this->expandedRubrik = $exp;
    }
};

return view('livewire.rps.builder.step7-assessment');