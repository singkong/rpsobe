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
state('newNama', ''); state('newJenis', ''); state('newBobot', 0); state('newDeskripsi', ''); state('newRubrik', ''); state('newSubCpmkIds', []);
state('editingId', null);
state('editNama', ''); state('editJenis', ''); state('editBobot', 0); state('editDeskripsi', ''); state('editRubrik', ''); state('editSubCpmkIds', []);
state('expandedRubrik', []);

$jenisOptions = fn () => collect(AssessmentJenis::cases())->map(fn ($j) => ['value' => $j->value, 'label' => $j->label()])->toArray();
$totalBobot = fn () => round(array_sum(array_column($this->assessmentList, 'bobot')), 2);
$bobotClass = fn () => abs($this->totalBobot() - 100) < 0.01 ? 'text-green' : 'text-red';

mount(function ($rpsId) {
    $this->rpsId = $rpsId;
    if ($this->rpsId) {
        $this->rps = RPS::with(['assessment.subCpmk.cpmk'])->findOrFail($this->rpsId);
        $this->allSubCpmk = SubCPMK::whereHas('cpmk', fn ($q) => $q->where('rps_id', $this->rpsId))->with('cpmk')->get()->map(fn ($s) => ['id' => $s->id, 'code' => $s->code, 'cpml_code' => $s->cpmk?->code ?? '', 'deskripsi' => $s->deskripsi])->toArray();
        $this->assessmentList = $this->rps->assessment->map(fn ($a) => ['id' => $a->id, 'nama' => $a->nama, 'jenis' => $a->jenis->value, 'jenis_label' => $a->jenis->label(), 'bobot' => $a->bobot_persen, 'deskripsi' => $a->deskripsi, 'rubrik' => $a->rubrik, 'sub_cpmk_ids' => $a->subCpmk->pluck('id')->toArray(), 'sub_cpmk_codes' => $a->subCpmk->pluck('code')->implode(', ')])->toArray();
    }
});

$startAdd = function () { $this->addingNew = true; $this->newNama = ''; $this->newJenis = ''; $this->newBobot = 0; $this->newDeskripsi = ''; $this->newRubrik = ''; $this->newSubCpmkIds = []; };
$cancelAdd = function () { $this->addingNew = false; };

$saveNew = function () {
    $this->validate(['newNama' => ['required','string'], 'newJenis' => ['required','in:formatif,sumatif'], 'newBobot' => ['required','numeric','min:1','max:100']], ['newNama.required' => 'Nama assessment wajib diisi.', 'newJenis.required' => 'Jenis assessment wajib dipilih.', 'newBobot.required' => 'Bobot assessment wajib diisi.']);
    $assessment = Assessment::create(['rps_id' => $this->rpsId, 'nama' => $this->newNama, 'bobot_persen' => $this->newBobot, 'jenis' => $this->newJenis, 'deskripsi' => $this->newDeskripsi, 'rubrik' => $this->newRubrik ?: null]);
    if (!empty($this->newSubCpmkIds)) $assessment->subCpmk()->sync($this->newSubCpmkIds);
    $codes = SubCPMK::whereIn('id', $this->newSubCpmkIds)->pluck('code')->implode(', ');
    $this->assessmentList[] = ['id' => $assessment->id, 'nama' => $assessment->nama, 'jenis' => $assessment->jenis->value, 'jenis_label' => $assessment->jenis->label(), 'bobot' => $assessment->bobot_persen, 'deskripsi' => $assessment->deskripsi, 'rubrik' => $assessment->rubrik, 'sub_cpmk_ids' => $this->newSubCpmkIds, 'sub_cpmk_codes' => $codes];
    $this->addingNew = false; $this->dispatch('rps-step-saved', step: 'assessment');
};

$startEdit = function ($id) {
    $item = collect($this->assessmentList)->firstWhere('id', $id);
    if ($item) { $this->editingId = $id; $this->editNama = $item['nama']; $this->editJenis = $item['jenis']; $this->editBobot = $item['bobot']; $this->editDeskripsi = $item['deskripsi'] ?? ''; $this->editRubrik = $item['rubrik'] ?? ''; $this->editSubCpmkIds = $item['sub_cpmk_ids']; }
};
$cancelEdit = function () { $this->editingId = null; };

$saveEdit = function ($id) {
    $this->validate(['editNama' => ['required','string'], 'editJenis' => ['required','in:formatif,sumatif'], 'editBobot' => ['required','numeric','min:1','max:100']]);
    $assessment = Assessment::findOrFail($id);
    $assessment->update(['nama' => $this->editNama, 'bobot_persen' => $this->editBobot, 'jenis' => $this->editJenis, 'deskripsi' => $this->editDeskripsi, 'rubrik' => $this->editRubrik ?: null]);
    $assessment->subCpmk()->sync($this->editSubCpmkIds);
    $codes = SubCPMK::whereIn('id', $this->editSubCpmkIds)->pluck('code')->implode(', ');
    $index = collect($this->assessmentList)->search(fn ($item) => $item['id'] === $id);
    if ($index !== false) { $this->assessmentList[$index] = ['id' => $assessment->id, 'nama' => $assessment->nama, 'jenis' => $assessment->jenis->value, 'jenis_label' => $assessment->jenis->label(), 'bobot' => $assessment->bobot_persen, 'deskripsi' => $assessment->deskripsi, 'rubrik' => $assessment->rubrik, 'sub_cpmk_ids' => $this->editSubCpmkIds, 'sub_cpmk_codes' => $codes]; }
    $this->editingId = null; $this->dispatch('rps-step-saved', step: 'assessment');
};

$deleteAssessment = function ($id) { Assessment::findOrFail($id)->delete(); $this->assessmentList = array_values(array_filter($this->assessmentList, fn ($item) => $item['id'] !== $id)); };

$toggleRubrik = function ($id) {
    if (in_array($id, $this->expandedRubrik ?? [])) { $this->expandedRubrik = array_values(array_filter($this->expandedRubrik, fn ($v) => $v != $id)); }
    else { $exp = $this->expandedRubrik ?? []; $exp[] = $id; $this->expandedRubrik = $exp; }
};

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Assessment</h3>
            <div class="ms-auto d-flex gap-3 align-items-center">
                <div><span class="small text-secondary">Total Bobot:</span> <strong class="<?= $this->bobotClass() ?> ms-1"><?= $this->totalBobot() ?>%</strong>
                    <?php if(abs($this->totalBobot() - 100) > 0.01): ?><span class="badge bg-danger-lt ms-1">Harus 100%</span><?php else: ?><span class="badge bg-green-lt ms-1">OK</span>
                </div>
                <button wire:click="startAdd" class="btn btn-sm btn-primary" <?php if($addingNew): ?>disabled><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg> Tambah Assessment</button>
            </div>
        </div>
        <div class="card-body">
            <?php if(abs($this->totalBobot() - 100) > 0.01 && $this->totalBobot() > 0): ?>
                <div class="alert alert-warning alert-dismissible" role="alert"><strong>Perhatian!</strong> Total bobot assessment harus 100%. Saat ini: <?= $this->totalBobot() ?>%. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            
            <?php if($addingNew): ?>
                <div class="card card-sm bg-primary-lt mb-4"><div class="card-body"><h5 class="mb-3">Tambah Assessment Baru</h5>
                    <div class="row mb-2">
                        <div class="col-md-4"><label class="form-label required">Nama Assessment</label><input type="text" wire:model="newNama" class="form-control" placeholder="UTS, UAS, Tugas 1, Kuis 1..."><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('newNama') ?></div></div>
                        <div class="col-md-3"><label class="form-label required">Jenis</label><select wire:model="newJenis" class="form-select is-invalid"><option value="">-- Pilih --</option><?php foreach($this->jenisOptions() as $opt): ?><option value="<?= $opt['value'] ?>"><?= $opt['label'] ?></option><?php endforeach; ?></select><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('newJenis') ?></div></div>
                        <div class="col-md-2"><label class="form-label required">Bobot (%)</label><input type="number" wire:model="newBobot" class="form-control" step="1" min="1" max="100"><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('newBobot') ?></div></div>
                    </div>
                    <div class="mb-2"><label class="form-label">Sub-CPMK Terkait</label><div class="d-flex flex-wrap gap-1"><?php foreach($allSubCpmk as $sub): ?><label class="form-check form-check-inline"><input type="checkbox" class="form-check-input" wire:model="newSubCpmkIds" value="<?= $sub['id'] ?>"><span class="form-check-label small"><?= $sub['code'] ?> (<?= $sub['cpml_code'] ?>)</span></label><?php endforeach; ?></div></div>
                    <div class="mb-2"><label class="form-label">Deskripsi</label><input type="text" wire:model="newDeskripsi" class="form-control" placeholder="Deskripsi singkat..."></div>
                    <div class="mb-2"><label class="form-label">Rubrik</label><textarea wire:model="newRubrik" class="form-control" rows="3" placeholder="Rubrik penilaian (opsional)..."></textarea></div>
                    <div class="d-flex gap-2"><button wire:click="saveNew" class="btn btn-sm btn-primary">Simpan</button><button wire:click="cancelAdd" class="btn btn-sm btn-ghost-secondary">Batal</button></div>
                </div></div>
            
            <?php if(count($assessmentList) === 0): ?>
                <div class="alert alert-info">Belum ada assessment. Tambahkan assessment untuk memulai.</div>
            <?php else: ?>
                <div class="table-responsive"><table class="table table-vcenter card-table">
                    <thead><tr><th>Nama</th><th>Jenis</th><th style="width: 80px">Bobot</th><th>Sub-CPMK</th><th style="width: 140px">Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach($assessmentList as $item): ?>
                            <?php if($editingId === $item['id']): ?>
                                <tr>
                                    <td><input type="text" wire:model="editNama" class="form-control form-control-sm is-invalid"></td>
                                    <td><select wire:model="editJenis" class="form-select form-select-sm"><?php foreach($this->jenisOptions() as $opt): ?><option value="<?= $opt['value'] ?>"><?= $opt['label'] ?></option><?php endforeach; ?></select></td>
                                    <td><input type="number" wire:model="editBobot" class="form-control form-control-sm" step="1" min="1" max="100"></td>
                                    <td><div class="d-flex flex-wrap gap-1"><?php foreach($allSubCpmk as $sub): ?><label class="form-check form-check-inline small"><input type="checkbox" class="form-check-input" wire:model="editSubCpmkIds" value="<?= $sub['id'] ?>"><?= $sub['code'] ?></label><?php endforeach; ?></div></td>
                                    <td><div class="btn-list"><button wire:click="saveEdit(<?= $item['id'] ?>)" class="btn btn-sm btn-icon btn-outline-success" title="Simpan"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10"/></svg></button><button wire:click="cancelEdit" class="btn btn-sm btn-icon btn-outline-secondary" title="Batal"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg></button></div></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td><strong><?= $item['nama'] ?></strong><?php if(!empty($item['deskripsi'])): ?><div class="text-secondary small"><?= $item['deskripsi'] ?></div></td>
                                    <td><span class="badge <?= $item['jenis'] === 'formatif' ? 'bg-blue-lt' : 'bg-orange-lt' ?>"><?= $item['jenis_label'] ?></span></td>
                                    <td><strong><?= $item['bobot'] ?>%</strong></td>
                                    <td><?php if(!empty($item['sub_cpmk_codes'])): ?><?php foreach(explode(', ', $item['sub_cpmk_codes']) as $code): ?><span class="badge bg-primary-lt me-1"><?= $code ?></span><?php endforeach; ?><?php else: ?><span class="text-secondary">-</span></td>
                                    <td><div class="btn-list">
                                        <?php if(!empty($item['rubrik'])): ?><button wire:click="toggleRubrik(<?= $item['id'] ?>)" class="btn btn-sm btn-icon btn-outline-info" title="Lihat Rubrik"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg></button>
                                        <button wire:click="startEdit(<?= $item['id'] ?>)" class="btn btn-sm btn-icon btn-outline-primary" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg></button>
                                        <button wire:click="deleteAssessment(<?= $item['id'] ?>)" onclick="return confirm('Hapus assessment ini?')" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg></button>
                                    </div></td>
                                </tr>
                                <?php if(in_array($item['id'], $expandedRubrik ?? []) && !empty($item['rubrik'])): ?>
                                    <tr><td colspan="5"><div class="card card-sm bg-light"><div class="card-body"><h6>Rubrik: <?= $item['nama'] ?></h6><pre class="mb-0" style="white-space: pre-wrap;"><?= $item['rubrik'] ?></pre></div></div></td></tr>
                                
                            
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot><tr><td colspan="2" class="text-end"><strong>Total Bobot</strong></td><td><strong class="<?= $this->bobotClass() ?>"><?= $this->totalBobot() ?>%</strong></td><td colspan="2"></td></tr></tfoot>
                </table></div>
            
        </div>
    </div>
</div>
