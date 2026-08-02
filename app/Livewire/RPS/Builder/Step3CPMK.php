<?php

use function Livewire\Volt\{state, mount};
use App\Enums\TaksonomiLevel;
use App\Models\RPS;
use App\Models\CPMK;
use App\Models\CPL;

state('rpsId', null);
state('rps', null);
state('cpmlList', []);
state('cplList', []);

state('editingId', null);
state('editCode', '');
state('editDeskripsi', '');
state('editCplIds', []);
state('editLevelTaksonomi', '');

state('addingNew', false);
state('newCode', '');
state('newDeskripsi', '');
state('newCplIds', []);
state('newLevelTaksonomi', '');

$taksonomiOptions = fn () => collect(TaksonomiLevel::cases())->map(fn ($l) => [
    'value' => $l->value,
    'label' => $l->value . ' - ' . $l->label(),
])->toArray();

mount(function ($rpsId) {
    $this->rpsId = $rpsId;

    if ($this->rpsId) {
        $this->rps = RPS::with(['cpl', 'cpml.cpl'])->findOrFail($this->rpsId);
        $this->cplList = $this->rps->cpl;
        $this->cpmlList = $this->rps->cpml->map(function ($cpml) {
            return [
                'id' => $cpml->id,
                'code' => $cpml->code,
                'deskripsi' => $cpml->deskripsi,
                'level_taksonomi' => $cpml->level_taksonomi,
                'cpl_ids' => $cpml->cpl->pluck('id')->toArray(),
                'cpl_labels' => $cpml->cpl->pluck('code')->implode(', '),
            ];
        })->toArray();
    }
});

$startAdd = function () {
    $this->addingNew = true;
    $count = CPMK::where('rps_id', $this->rpsId)->withTrashed()->count();
    $this->newCode = 'CPMK-' . str_pad($count + 1, 2, '0', STR_PAD_LEFT);
    $this->newDeskripsi = '';
    $this->newCplIds = [];
    $this->newLevelTaksonomi = '';
};

$cancelAdd = function () {
    $this->addingNew = false;
    $this->resetNewForm();
};

$saveNew = function () {
    $this->validate([
        'newDeskripsi' => ['required', 'string'],
    ]);

    $cpml = CPMK::create([
        'rps_id' => $this->rpsId,
        'code' => $this->newCode,
        'deskripsi' => $this->newDeskripsi,
        'level_taksonomi' => $this->newLevelTaksonomi ?: null,
    ]);

    $cpml->cpl()->sync($this->newCplIds);

    $this->cpmlList[] = [
        'id' => $cpml->id,
        'code' => $cpml->code,
        'deskripsi' => $cpml->deskripsi,
        'level_taksonomi' => $cpml->level_taksonomi,
        'cpl_ids' => $cpml->cpl->pluck('id')->toArray(),
        'cpl_labels' => $cpml->cpl->pluck('code')->implode(', '),
    ];

    $this->addingNew = false;
    $this->resetNewForm();
    $this->dispatch('rps-step-saved', step: 'cpml');
};

$startEdit = function ($id) {
    $this->editingId = $id;
    $item = collect($this->cpmlList)->firstWhere('id', $id);

    if ($item) {
        $this->editCode = $item['code'];
        $this->editDeskripsi = $item['deskripsi'];
        $this->editCplIds = $item['cpl_ids'];
        $this->editLevelTaksonomi = $item['level_taksonomi'] ?? '';
    }
};

$cancelEdit = function () {
    $this->editingId = null;
};

$saveEdit = function ($id) {
    $this->validate([
        'editDeskripsi' => ['required', 'string'],
    ]);

    $cpml = CPMK::findOrFail($id);
    $cpml->update([
        'code' => $this->editCode,
        'deskripsi' => $this->editDeskripsi,
        'level_taksonomi' => $this->editLevelTaksonomi ?: null,
    ]);

    $cpml->cpl()->sync($this->editCplIds);

    $index = collect($this->cpmlList)->search(fn ($item) => $item['id'] === $id);
    if ($index !== false) {
        $this->cpmlList[$index] = [
            'id' => $cpml->id,
            'code' => $cpml->code,
            'deskripsi' => $cpml->deskripsi,
            'level_taksonomi' => $cpml->level_taksonomi,
            'cpl_ids' => $cpml->cpl->pluck('id')->toArray(),
            'cpl_labels' => $cpml->cpl->pluck('code')->implode(', '),
        ];
    }

    $this->editingId = null;
};

$deleteCpml = function ($id) {
    CPMK::findOrFail($id)->delete();
    $this->cpmlList = array_values(array_filter($this->cpmlList, fn ($item) => $item['id'] !== $id));
};

$moveUp = function ($index) {
    if ($index > 0) {
        $temp = $this->cpmlList[$index - 1];
        $this->cpmlList[$index - 1] = $this->cpmlList[$index];
        $this->cpmlList[$index] = $temp;
    }
};

$moveDown = function ($index) {
    if ($index < count($this->cpmlList) - 1) {
        $temp = $this->cpmlList[$index + 1];
        $this->cpmlList[$index + 1] = $this->cpmlList[$index];
        $this->cpmlList[$index] = $temp;
    }
};

$resetNewForm = function () {
    $this->newCode = '';
    $this->newDeskripsi = '';
    $this->newCplIds = [];
    $this->newLevelTaksonomi = '';
};

return view('livewire.rps.builder.step3-cpmk');
