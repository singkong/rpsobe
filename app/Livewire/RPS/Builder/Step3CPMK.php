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

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Capaian Pembelajaran Mata Kuliah (CPMK)</h3>
            <button wire:click="startAdd" class="btn btn-sm btn-primary" <?php if($addingNew): ?>disabled>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                Tambah CPMK
            </button>
        </div>
        <div class="card-body">
            <?php if($addingNew): ?>
                <div class="card card-sm bg-primary-lt mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Tambah CPMK Baru</h5>
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <label class="form-label required">Kode</label>
                                <input type="text" wire:model="newCode" class="form-control" placeholder="CPMK-01">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Level Taksonomi</label>
                                <select wire:model="newLevelTaksonomi" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach($this->taksonomiOptions() as $opt): ?>
                                        <option value="<?= $opt['value'] ?>"><?= $opt['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CPL Terkait</label>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php foreach($cplList as $cpl): ?>
                                        <label class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input"
                                                   wire:model="newCplIds" value="<?= $cpl->id ?>">
                                            <span class="form-check-label small"><?= $cpl->code ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label required">Deskripsi</label>
                            <textarea wire:model="newDeskripsi" class="form-control" rows="2" placeholder="Deskripsi CPMK..."></textarea>
                            <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('newDeskripsi') ?></div>
                        </div>
                        <div class="d-flex gap-2">
                            <button wire:click="saveNew" class="btn btn-sm btn-primary">Simpan</button>
                            <button wire:click="cancelAdd" class="btn btn-sm btn-ghost-secondary">Batal</button>
                        </div>
                    </div>
                </div>
            

            <?php if(count($cpmlList) === 0): ?>
                <div class="alert alert-info">Belum ada CPMK yang ditambahkan. Silakan pilih CPL terlebih dahulu pada Step 2.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th style="width: 40px">#</th>
                                <th>Kode</th>
                                <th>Deskripsi</th>
                                <th>CPL Terkait</th>
                                <th>Taksonomi</th>
                                <th style="width: 120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cpmlList as $index => $cpml): ?>
                                <?php if($editingId === $cpml['id']): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><input type="text" wire:model="editCode" class="form-control form-control-sm"></td>
                                        <td><textarea wire:model="editDeskripsi" class="form-control form-control-sm is-invalid" rows="2"></textarea></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php foreach($cplList as $cpl): ?>
                                                    <label class="form-check form-check-inline small">
                                                        <input type="checkbox" class="form-check-input" wire:model="editCplIds" value="<?= $cpl->id ?>">
                                                        <?= $cpl->code ?>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <select wire:model="editLevelTaksonomi" class="form-select form-select-sm">
                                                <option value="">--</option>
                                                <?php foreach($this->taksonomiOptions() as $opt): ?>
                                                    <option value="<?= $opt['value'] ?>"><?= $opt['label'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button wire:click="saveEdit(<?= $cpml['id'] ?>)" class="btn btn-sm btn-icon btn-outline-success" title="Simpan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10"/></svg>
                                                </button>
                                                <button wire:click="cancelEdit" class="btn btn-sm btn-icon btn-outline-secondary" title="Batal">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button wire:click="moveUp(<?= $index ?>)" class="btn btn-sm btn-icon btn-ghost-secondary" title="Naik" <?php if($index === 0): ?>disabled>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M18 15l-6 -6l-6 6h12"/></svg>
                                                </button>
                                                <button wire:click="moveDown(<?= $index ?>)" class="btn btn-sm btn-icon btn-ghost-secondary" title="Turun" <?php if($index === count($cpmlList) - 1): ?>disabled>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M6 9l6 6l6 -6h-12"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary-lt"><?= $cpml['code'] ?></span></td>
                                        <td><?= $cpml['deskripsi'] ?></td>
                                        <td><span class="small"><?= $cpml['cpl_labels'] ?: '-' ?></span></td>
                                        <td><?= $cpml['level_taksonomi'] ?: '-' ?></td>
                                        <td>
                                            <div class="btn-list">
                                                <button wire:click="startEdit(<?= $cpml['id'] ?>)" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                                </button>
                                                <button wire:click="deleteCpml(<?= $cpml['id'] ?>)" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            
        </div>
    </div>
</div>
