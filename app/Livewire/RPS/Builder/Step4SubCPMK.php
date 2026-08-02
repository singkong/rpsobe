<?php

use function Livewire\Volt\{state, mount};
use App\Enums\TaksonomiLevel;
use App\Models\RPS;
use App\Models\CPMK;
use App\Models\SubCPMK;

state('rpsId', null);
state('rps', null);
state('subCpmkList', []);
state('cpmlList', []);

state('editingId', null);
state('editCpmlId', '');
state('editCode', '');
state('editDeskripsi', '');
state('editLevelTaksonomi', '');
state('editPertemuanTerkait', []);

state('addingNew', false);
state('newCpmlId', '');
state('newCode', '');
state('newDeskripsi', '');
state('newLevelTaksonomi', '');
state('newPertemuanTerkait', []);

$taksonomiOptions = fn () => collect(TaksonomiLevel::cases())->map(fn ($l) => [
    'value' => $l->value,
    'label' => $l->value . ' - ' . $l->label(),
])->toArray();

$pertemuanRange = fn () => range(1, 16);

mount(function ($rpsId) {
    $this->rpsId = $rpsId;

    if ($this->rpsId) {
        $this->rps = RPS::with('cpml.subCpmk')->findOrFail($this->rpsId);
        $this->cpmlList = $this->rps->cpml;

        $this->subCpmkList = $this->rps->cpml->map(function ($cpml) {
            return [
                'cpml_id' => $cpml->id,
                'cpml_code' => $cpml->code,
                'cpml_deskripsi' => $cpml->deskripsi,
                'items' => $cpml->subCpmk->map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'code' => $sub->code,
                        'deskripsi' => $sub->deskripsi,
                        'level_taksonomi' => $sub->level_taksonomi?->value ?? '',
                        'pertemuan_terkait' => $sub->pertemuan_terkait ?? [],
                    ];
                })->toArray(),
            ];
        })->toArray();
    }
});

$subCountForCpml = function ($cpmlId) {
    return SubCPMK::where('cpml_id', $cpmlId)->withTrashed()->count();
};

$startAdd = function () {
    $this->addingNew = true;
    $this->newCpmlId = '';
    $this->newCode = '';
    $this->newDeskripsi = '';
    $this->newLevelTaksonomi = '';
    $this->newPertemuanTerkait = [];
};

$cancelAdd = function () {
    $this->addingNew = false;
    $this->resetNewForm();
};

$updatedNewCpmlId = function ($value) {
    if ($value) {
        $count = $this->subCountForCpml($value);
        $this->newCode = 'SCPMK-' . str_pad($count + 1, 2, '0', STR_PAD_LEFT);
    }
};

$togglePertemuan = function ($num) {
    if (in_array($num, $this->newPertemuanTerkait ?? [])) {
        $this->newPertemuanTerkait = array_values(array_filter($this->newPertemuanTerkait, fn ($n) => $n != $num));
    } else {
        $pt = $this->newPertemuanTerkait ?? [];
        $pt[] = $num;
        $this->newPertemuanTerkait = $pt;
    }
};

$toggleEditPertemuan = function ($num) {
    if (in_array($num, $this->editPertemuanTerkait ?? [])) {
        $this->editPertemuanTerkait = array_values(array_filter($this->editPertemuanTerkait, fn ($n) => $n != $num));
    } else {
        $pt = $this->editPertemuanTerkait ?? [];
        $pt[] = $num;
        $this->editPertemuanTerkait = $pt;
    }
};

$saveNew = function () {
    $this->validate([
        'newCpmlId' => ['required'],
        'newDeskripsi' => ['required', 'string'],
    ]);

    $sub = SubCPMK::create([
        'cpml_id' => $this->newCpmlId,
        'code' => $this->newCode,
        'deskripsi' => $this->newDeskripsi,
        'level_taksonomi' => $this->newLevelTaksonomi ?: null,
        'pertemuan_terkait' => $this->newPertemuanTerkait ?: null,
    ]);

    $cpmlIndex = collect($this->subCpmkList)->search(fn ($g) => $g['cpml_id'] == $this->newCpmlId);
    if ($cpmlIndex !== false) {
        $this->subCpmkList[$cpmlIndex]['items'][] = [
            'id' => $sub->id,
            'code' => $sub->code,
            'deskripsi' => $sub->deskripsi,
            'level_taksonomi' => $sub->level_taksonomi?->value ?? '',
            'pertemuan_terkait' => $sub->pertemuan_terkait ?? [],
        ];
    }

    $this->addingNew = false;
    $this->resetNewForm();
    $this->dispatch('rps-step-saved', step: 'sub-cpmk');
};

$startEdit = function ($id) {
    $this->editingId = $id;

    foreach ($this->subCpmkList as $group) {
        foreach ($group['items'] as $item) {
            if ($item['id'] === $id) {
                $this->editCpmlId = $group['cpml_id'];
                $this->editCode = $item['code'];
                $this->editDeskripsi = $item['deskripsi'];
                $this->editLevelTaksonomi = $item['level_taksonomi'] ?? '';
                $this->editPertemuanTerkait = $item['pertemuan_terkait'] ?? [];
                break 2;
            }
        }
    }
};

$cancelEdit = function () {
    $this->editingId = null;
};

$saveEdit = function ($id) {
    $this->validate([
        'editDeskripsi' => ['required', 'string'],
    ]);

    $sub = SubCPMK::findOrFail($id);
    $sub->update([
        'cpml_id' => $this->editCpmlId,
        'code' => $this->editCode,
        'deskripsi' => $this->editDeskripsi,
        'level_taksonomi' => $this->editLevelTaksonomi ?: null,
        'pertemuan_terkait' => $this->editPertemuanTerkait ?: null,
    ]);

    foreach ($this->subCpmkList as &$group) {
        foreach ($group['items'] as &$item) {
            if ($item['id'] === $id) {
                $item['code'] = $sub->code;
                $item['deskripsi'] = $sub->deskripsi;
                $item['level_taksonomi'] = $sub->level_taksonomi?->value ?? '';
                $item['pertemuan_terkait'] = $sub->pertemuan_terkait ?? [];
                if ($group['cpml_id'] != $this->editCpmlId) {
                    $group['cpml_id'] = (int) $this->editCpmlId;
                    $cpml = CPMK::find($this->editCpmlId);
                    $group['cpml_code'] = $cpml?->code ?? '';
                    $group['cpml_deskripsi'] = $cpml?->deskripsi ?? '';
                }
                break 2;
            }
        }
    }

    $this->editingId = null;
};

$deleteSubCpmk = function ($id) {
    SubCPMK::findOrFail($id)->delete();

    foreach ($this->subCpmkList as &$group) {
        $group['items'] = array_values(array_filter($group['items'], fn ($item) => $item['id'] !== $id));
    }

    $this->subCpmkList = array_values(array_filter($this->subCpmkList, fn ($g) => count($g['items']) > 0));
};

$resetNewForm = function () {
    $this->newCpmlId = '';
    $this->newCode = '';
    $this->newDeskripsi = '';
    $this->newLevelTaksonomi = '';
    $this->newPertemuanTerkait = [];
};

return view('livewire.rps.builder.step4-sub-cpmk');
