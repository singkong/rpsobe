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

return view('livewire.rps.builder.step5-materi');