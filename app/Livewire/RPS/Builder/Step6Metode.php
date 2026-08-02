<?php

use function Livewire\Volt\{state, mount};
use App\Models\RPS;
use App\Models\MateriPertemuan;

state('rpsId', null);
state('rps', null);
state('metodeMap', []);
state('allMetode', []);
state('metodeColors', []);
state('saved', false);

$DAFTAR_METODE = ['Ceramah','Diskusi','Tanya Jawab','Problem-Based Learning (PBL)','Case-Based Learning (CBL)','Project-Based Learning (PJBL)','Simulasi','Praktikum','Studi Kasus','Presentasi','Tutorial','Cooperative Learning','Discovery Learning','Self-Directed Learning','Blended Learning','Lainnya'];

$METODE_COLORS = ['Ceramah' => 'primary','Diskusi' => 'green','Tanya Jawab' => 'orange','Problem-Based Learning (PBL)' => 'purple','Case-Based Learning (CBL)' => 'teal','Project-Based Learning (PJBL)' => 'red','Simulasi' => 'cyan','Praktikum' => 'yellow','Studi Kasus' => 'pink','Presentasi' => 'indigo','Tutorial' => 'blue','Cooperative Learning' => 'lime','Discovery Learning' => 'azure','Self-Directed Learning' => 'green','Blended Learning' => 'primary','Lainnya' => 'gray'];

mount(function ($rpsId) {
    $this->rpsId = $rpsId;
    $this->allMetode = $this->DAFTAR_METODE;
    $this->metodeColors = $this->METODE_COLORS;
    if ($this->rpsId) {
        $this->rps = RPS::with('materiPertemuan.subCpmk')->findOrFail($this->rpsId);
        $this->metodeMap = $this->rps->materiPertemuan->sortBy('pertemuan_ke')->mapWithKeys(fn ($m) => [$m->pertemuan_ke => ['id' => $m->id, 'materi' => $m->materi, 'selected_metode' => $m->metode_pembelajaran ?? []]])->toArray();
    }
});

$toggleMetode = function ($pertemuanKe, $metode) {
    $current = $this->metodeMap[$pertemuanKe]['selected_metode'] ?? [];
    if (in_array($metode, $current)) { $this->metodeMap[$pertemuanKe]['selected_metode'] = array_values(array_filter($current, fn ($m) => $m !== $metode)); }
    else { $this->metodeMap[$pertemuanKe]['selected_metode'][] = $metode; }
};

$save = function () {
    foreach ($this->metodeMap as $pertemuanKe => $data) {
        if (!empty($data['id'])) { MateriPertemuan::where('id', $data['id'])->update(['metode_pembelajaran' => $data['selected_metode'] ?? []]); }
    }
    $this->saved = true;
    $this->dispatch('rps-step-saved', step: 'metode');
};

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Metode Pembelajaran</h3>
            <span class="badge bg-primary-lt ms-auto"><?= count($metodeMap) ?> Pertemuan</span>
        </div>
        <div class="card-body">
            <?php if(count($metodeMap) === 0): ?>
                <div class="alert alert-info">Belum ada materi pertemuan. Silakan isi materi pada Step 5 terlebih dahulu.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead><tr><th style="width: 80px">Pertemuan</th><th style="width: 300px">Materi</th><th>Metode Pembelajaran</th><th style="width: 60px">Jml</th></tr></thead>
                        <tbody>
                            <?php foreach($metodeMap as $pertemuanKe => $data): ?>
                                <?php $selectedMetode = $data['selected_metode'] ?? []; $metodeCount = count($selectedMetode); ?>
                                <tr>
                                    <td><span class="badge bg-primary"><?= $pertemuanKe ?></span></td>
                                    <td class="text-wrap small"><?= \Illuminate\Support\Str::limit($data['materi'] ?? '-', 100) ?></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach($allMetode as $metode): ?>
                                                <?php $isSelected = in_array($metode, $selectedMetode); $color = $metodeColors[$metode] ?? 'gray'; ?>
                                                <button type="button" wire:click="toggleMetode(<?= $pertemuanKe ?>, '<?= addslashes($metode) ?>')" class="btn btn-sm <?= $isSelected ? 'bg-'.$color.' text-white' : 'btn-outline-secondary' ?> mb-1" style="font-size: 12px; padding: 2px 8px;" title="<?= $metode ?>"><?= \Illuminate\Support\Str::limit($metode, 20) ?></button>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td><span class="badge <?= $metodeCount > 0 ? 'bg-primary-lt' : 'bg-danger-lt' ?>"><?= $metodeCount ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-end mt-3"><button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled"><span wire:loading.remove wire:target="save">Simpan Metode</span><span wire:loading wire:target="save">Menyimpan...</span></button></div>
            <?php if($saved): ?><div class="alert alert-success mt-2 mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10"/></svg> Metode pembelajaran berhasil disimpan.</div><?php endif; ?>
        </div>
    </div>
</div>
