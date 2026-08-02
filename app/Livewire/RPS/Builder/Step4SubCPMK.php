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
        $this->subCpmkList[$cpmlIndex]['items'][] = ['id' => $sub->id, 'code' => $sub->code, 'deskripsi' => $sub->deskripsi, 'level_taksonomi' => $sub->level_taksonomi?->value ?? '', 'pertemuan_terkait' => $sub->pertemuan_terkait ?? []];
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

$cancelEdit = function () { $this->editingId = null; };

$saveEdit = function ($id) {
    $this->validate(['editDeskripsi' => ['required', 'string']]);
    $sub = SubCPMK::findOrFail($id);
    $sub->update(['cpml_id' => $this->editCpmlId, 'code' => $this->editCode, 'deskripsi' => $this->editDeskripsi, 'level_taksonomi' => $this->editLevelTaksonomi ?: null, 'pertemuan_terkait' => $this->editPertemuanTerkait ?: null]);
    foreach ($this->subCpmkList as &$group) {
        foreach ($group['items'] as &$item) {
            if ($item['id'] === $id) {
                $item['code'] = $sub->code; $item['deskripsi'] = $sub->deskripsi;
                $item['level_taksonomi'] = $sub->level_taksonomi?->value ?? '';
                $item['pertemuan_terkait'] = $sub->pertemuan_terkait ?? [];
                if ($group['cpml_id'] != $this->editCpmlId) {
                    $group['cpml_id'] = (int) $this->editCpmlId;
                    $cpml = CPMK::find($this->editCpmlId);
                    $group['cpml_code'] = $cpml?->code ?? ''; $group['cpml_deskripsi'] = $cpml?->deskripsi ?? '';
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

$resetNewForm = function () { $this->newCpmlId = ''; $this->newCode = ''; $this->newDeskripsi = ''; $this->newLevelTaksonomi = ''; $this->newPertemuanTerkait = []; };

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sub-CPMK</h3>
            <button wire:click="startAdd" class="btn btn-sm btn-primary" <?php if($addingNew): ?>disabled>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                Tambah Sub-CPMK
            </button>
        </div>
        <div class="card-body">
            <?php if($addingNew): ?>
                <div class="card card-sm bg-primary-lt mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Tambah Sub-CPMK Baru</h5>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label class="form-label required">CPMK Induk</label>
                                <select wire:model.live="newCpmlId" class="form-select is-invalid">
                                    <option value="">-- Pilih CPMK --</option>
                                    <?php foreach($cpmlList as $cpml): ?><option value="<?= $cpml->id ?>"><?= $cpml->code ?> - <?= $cpml->deskripsi ?></option><?php endforeach; ?>
                                </select><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('newCpmlId') ?></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kode</label><input type="text" wire:model="newCode" class="form-control" placeholder="SCPMK-01">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Level Taksonomi</label>
                                <select wire:model="newLevelTaksonomi" class="form-select"><option value="">-- Pilih --</option>
                                    <?php foreach($this->taksonomiOptions() as $opt): ?><option value="<?= $opt['value'] ?>"><?= $opt['label'] ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label required">Deskripsi</label>
                            <textarea wire:model="newDeskripsi" class="form-control" rows="2" placeholder="Deskripsi Sub-CPMK..."></textarea>
                            <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('newDeskripsi') ?></div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Pertemuan Terkait</label>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach($this->pertemuanRange() as $p): ?>
                                    <button type="button" wire:click="togglePertemuan(<?= $p ?>)" class="btn btn-sm <?= in_array($p, $newPertemuanTerkait ?? []) ? 'btn-primary' : 'btn-outline-secondary' ?>" style="min-width: 38px"><?= $p ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="d-flex gap-2"><button wire:click="saveNew" class="btn btn-sm btn-primary">Simpan</button><button wire:click="cancelAdd" class="btn btn-sm btn-ghost-secondary">Batal</button></div>
                    </div>
                </div>
            

            <?php if(count($subCpmkList) === 0): ?>
                <div class="alert alert-info">Belum ada Sub-CPMK. Silakan tambahkan CPMK terlebih dahulu pada Step 3.</div>
            <?php else: ?>
                <?php foreach($subCpmkList as $group): ?>
                    <div class="card card-sm mb-3">
                        <div class="card-header bg-light"><strong><?= $group['cpml_code'] ?></strong> - <?= $group['cpml_deskripsi'] ?></div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table mb-0">
                                <thead><tr><th>Kode</th><th>Deskripsi</th><th>Taksonomi</th><th>Pertemuan</th><th style="width: 100px">Aksi</th></tr></thead>
                                <tbody>
                                    <?php if(count($group['items']) === 0): ?>
                                        <tr><td colspan="5" class="text-secondary text-center py-2">Belum ada Sub-CPMK</td></tr>
                                    <?php else: ?>
                                        <?php foreach($group['items'] as $item): ?>
                                            <?php if($editingId === $item['id']): ?>
                                                <tr>
                                                    <td><input type="text" wire:model="editCode" class="form-control form-control-sm"></td>
                                                    <td><textarea wire:model="editDeskripsi" class="form-control form-control-sm is-invalid" rows="2"></textarea></td>
                                                    <td><select wire:model="editLevelTaksonomi" class="form-select form-select-sm"><option value="">--</option><?php foreach($this->taksonomiOptions() as $opt): ?><option value="<?= $opt['value'] ?>"><?= $opt['label'] ?></option><?php endforeach; ?></select></td>
                                                    <td><div class="d-flex flex-wrap gap-1"><?php foreach($this->pertemuanRange() as $p): ?><button type="button" wire:click="toggleEditPertemuan(<?= $p ?>)" class="btn btn-xs <?= in_array($p, $editPertemuanTerkait ?? []) ? 'btn-primary' : 'btn-outline-secondary' ?>" style="padding: 1px 6px; font-size: 11px;"><?= $p ?></button><?php endforeach; ?></div></td>
                                                    <td><div class="btn-list"><button wire:click="saveEdit(<?= $item['id'] ?>)" class="btn btn-sm btn-icon btn-outline-success" title="Simpan"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10"/></svg></button><button wire:click="cancelEdit" class="btn btn-sm btn-icon btn-outline-secondary" title="Batal"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg></button></div></td>
                                                </tr>
                                            <?php else: ?>
                                                <tr>
                                                    <td><span class="badge bg-primary-lt"><?= $item['code'] ?></span></td>
                                                    <td><?= $item['deskripsi'] ?></td>
                                                    <td><?= $item['level_taksonomi'] ?: '-' ?></td>
                                                    <td><?php if(!empty($item['pertemuan_terkait'])): ?><?php foreach($item['pertemuan_terkait'] as $p): ?><span class="badge bg-secondary-lt me-1"><?= $p ?></span><?php endforeach; ?><?php else: ?>-</td>
                                                    <td><div class="btn-list"><button wire:click="startEdit(<?= $item['id'] ?>)" class="btn btn-sm btn-icon btn-outline-primary" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg></button><button wire:click="deleteSubCpmk(<?= $item['id'] ?>)" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg></button></div></td>
                                                </tr>
                                            
                                        <?php endforeach; ?>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            
        </div>
    </div>
</div>
