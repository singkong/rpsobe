<?php

use function Livewire\Volt\{state, mount, computed};
use App\Models\RPS;
use App\Models\MateriPertemuan;
use App\Models\SubCPMK;
use App\Models\Referensi;

state('rpsId', null);
state('rps', null);
state('pertemuanList', []);
state('maxPertemuan', 16);
state('allSubCpmk', []);
state('allReferensi', []);
state('saved', false);

$totalPertemuan = fn () => count($this->pertemuanList);

$availableSubCpmk = function () {
    return SubCPMK::whereHas('cpmk', function ($q) {
        $q->where('rps_id', $this->rpsId);
    })->with('cpmk')->get();
};

mount(function ($rpsId) {
    $this->rpsId = $rpsId;

    if ($this->rpsId) {
        $this->rps = RPS::with('materiPertemuan.subCpmk.cpmk')->findOrFail($this->rpsId);

        $this->allSubCpmk = $this->availableSubCpmk()->map(function ($sub) {
            return [
                'id' => $sub->id,
                'code' => $sub->code,
                'cpml_code' => $sub->cpmk?->code ?? '',
                'deskripsi' => $sub->deskripsi,
                'pertemuan_terkait' => $sub->pertemuan_terkait ?? [],
            ];
        })->toArray();

        $this->allReferensi = Referensi::get()->map(function ($r) {
            return [
                'id' => $r->id,
                'judul' => $r->judul,
                'penulis' => $r->penulis,
                'tahun' => $r->tahun,
            ];
        })->toArray();

        $existing = $this->rps->materiPertemuan;
        $maxExisting = $existing->max('pertemuan_ke') ?: 0;
        $initialMax = max($maxExisting, 16);

        $this->pertemuanList = collect(range(1, $initialMax))->map(function ($ke) use ($existing) {
            $match = $existing->firstWhere('pertemuan_ke', $ke);
            return [
                'id' => $match->id ?? null,
                'pertemuan_ke' => $ke,
                'sub_cpmk_id' => $match->sub_cpmk_id ?? null,
                'materi' => $match->materi ?? '',
                'indikator' => $match->indikator ?? '',
                'referensi_ids' => $match->referensi_ids ?? [],
            ];
        })->toArray();

        $this->maxPertemuan = $initialMax;
    }
});

$filteredSubCpmk = function ($pertemuanKe) {
    return array_filter($this->allSubCpmk, function ($sub) use ($pertemuanKe) {
        $related = $sub['pertemuan_terkait'] ?? [];
        if (empty($related)) {
            return true;
        }
        return in_array($pertemuanKe, $related);
    });
};

$tambahPertemuan = function () {
    $newKe = $this->maxPertemuan + 1;
    $this->pertemuanList[] = [
        'id' => null,
        'pertemuan_ke' => $newKe,
        'sub_cpmk_id' => null,
        'materi' => '',
        'indikator' => '',
        'referensi_ids' => [],
    ];
    $this->maxPertemuan = $newKe;
};

$hapusPertemuan = function () {
    if ($this->maxPertemuan > 1) {
        $last = array_pop($this->pertemuanList);
        if (!empty($last['id'])) {
            MateriPertemuan::find($last['id'])?->delete();
        }
        $this->maxPertemuan = count($this->pertemuanList);
    }
};

$toggleReferensi = function ($pertemuanIndex, $refId) {
    $current = $this->pertemuanList[$pertemuanIndex]['referensi_ids'] ?? [];
    if (in_array($refId, $current)) {
        $this->pertemuanList[$pertemuanIndex]['referensi_ids'] = array_values(array_filter($current, fn ($id) => $id != $refId));
    } else {
        $this->pertemuanList[$pertemuanIndex]['referensi_ids'][] = $refId;
    }
};

$save = function () {
    $this->validate([
        'pertemuanList.*.materi' => ['required', 'string'],
    ], [
        'pertemuanList.*.materi.required' => 'Materi untuk setiap pertemuan wajib diisi.',
    ]);

    foreach ($this->pertemuanList as $data) {
        $payload = [
            'rps_id' => $this->rpsId,
            'pertemuan_ke' => $data['pertemuan_ke'],
            'sub_cpmk_id' => $data['sub_cpmk_id'] ?: null,
            'materi' => $data['materi'],
            'indikator' => $data['indikator'] ?: null,
            'referensi_ids' => $data['referensi_ids'] ?: null,
        ];

        if ($data['id']) {
            MateriPertemuan::find($data['id'])->update($payload);
        } else {
            $record = MateriPertemuan::create($payload);
            $data['id'] = $record->id;
        }
    }

    $this->saved = true;
    $this->dispatch('rps-step-saved', step: 'materi');
};

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Materi Pembelajaran per Pertemuan</h3>
            <div class="ms-auto d-flex gap-2">
                <span class="badge bg-primary-lt">{{ $this->totalPertemuan() }} Pertemuan</span>
                <button wire:click="tambahPertemuan" class="btn btn-sm btn-outline-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                    Tambah Pertemuan
                </button>
                @if($this->totalPertemuan() > 1)
                    <button wire:click="hapusPertemuan" class="btn btn-sm btn-outline-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                        Hapus Terakhir
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if(count($allSubCpmk) === 0)
                <div class="alert alert-warning">Belum ada Sub-CPMK. Silakan isi Sub-CPMK terlebih dahulu pada Step 4.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th style="width: 80px">Pertemuan Ke-</th>
                                <th style="width: 200px">Sub-CPMK</th>
                                <th>Materi</th>
                                <th style="width: 200px">Indikator</th>
                                <th style="width: 200px">Referensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pertemuanList as $index => $pert)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $pert['pertemuan_ke'] }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $filtered = $this->filteredSubCpmk($pert['pertemuan_ke']);
                                        @endphp
                                        <select wire:model="pertemuanList.{{ $index }}.sub_cpmk_id" class="form-select form-select-sm">
                                            <option value="">-- Pilih Sub-CPMK --</option>
                                            @foreach($filtered as $sub)
                                                <option value="{{ $sub['id'] }}">{{ $sub['code'] }} ({{ $sub['cpml_code'] }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <textarea wire:model="pertemuanList.{{ $index }}.materi" class="form-control form-control-sm @error('pertemuanList.'.$index.'.materi') is-invalid @enderror" rows="2" placeholder="Materi pertemuan..."></textarea>
                                        @error('pertemuanList.'.$index.'.materi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <textarea wire:model="pertemuanList.{{ $index }}.indikator" class="form-control form-control-sm" rows="2" placeholder="Indikator..."></textarea>
                                    </td>
                                    <td>
                                        <div class="dropdown" wire:ignore.self>
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Pilih Referensi
                                                @php $refCount = count($pert['referensi_ids'] ?? []); @endphp
                                                @if($refCount > 0)
                                                    <span class="badge bg-primary-lt ms-1">{{ $refCount }}</span>
                                                @endif
                                            </button>
                                            <div class="dropdown-menu p-2" style="max-height: 250px; overflow-y: auto; width: 400px;">
                                                @foreach($allReferensi as $ref)
                                                    <label class="dropdown-item d-flex align-items-center gap-2">
                                                        <input type="checkbox" class="form-check-input m-0"
                                                               wire:change="toggleReferensi({{ $index }}, {{ $ref['id'] }})"
                                                               @checked(in_array($ref['id'], $pert['referensi_ids'] ?? []))>
                                                        <span class="small text-wrap">{{ $ref['judul'] }} ({{ $ref['penulis'] ?? '-' }}, {{ $ref['tahun'] ?? '-' }})</span>
                                                    </label>
                                                @endforeach
                                                @if(empty($allReferensi))
                                                    <span class="dropdown-item text-muted">Tidak ada referensi</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if(!empty($pert['referensi_ids']))
                                            <div class="mt-1">
                                                @foreach($pert['referensi_ids'] as $rid)
                                                    @php $r = collect($allReferensi)->firstWhere('id', $rid); @endphp
                                                    @if($r)
                                                        <span class="badge bg-secondary-lt me-1 small" title="{{ $r['judul'] }}">
                                                            Ref {{ $rid }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="d-flex justify-content-end mt-3">
                <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Simpan Materi</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>

            @if($saved)
                <div class="alert alert-success mt-2 mb-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10"/></svg>
                    Materi pertemuan berhasil disimpan.
                </div>
            @endif
        </div>
    </div>
</div>

