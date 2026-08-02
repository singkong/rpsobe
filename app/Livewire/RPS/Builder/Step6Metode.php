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

$DAFTAR_METODE = [
    'Ceramah',
    'Diskusi',
    'Tanya Jawab',
    'Problem-Based Learning (PBL)',
    'Case-Based Learning (CBL)',
    'Project-Based Learning (PJBL)',
    'Simulasi',
    'Praktikum',
    'Studi Kasus',
    'Presentasi',
    'Tutorial',
    'Cooperative Learning',
    'Discovery Learning',
    'Self-Directed Learning',
    'Blended Learning',
    'Lainnya',
];

$METODE_COLORS = [
    'Ceramah' => 'primary',
    'Diskusi' => 'green',
    'Tanya Jawab' => 'orange',
    'Problem-Based Learning (PBL)' => 'purple',
    'Case-Based Learning (CBL)' => 'teal',
    'Project-Based Learning (PJBL)' => 'red',
    'Simulasi' => 'cyan',
    'Praktikum' => 'yellow',
    'Studi Kasus' => 'pink',
    'Presentasi' => 'indigo',
    'Tutorial' => 'blue',
    'Cooperative Learning' => 'lime',
    'Discovery Learning' => 'azure',
    'Self-Directed Learning' => 'green',
    'Blended Learning' => 'primary',
    'Lainnya' => 'gray',
];

mount(function ($rpsId) {
    $this->rpsId = $rpsId;
    $this->allMetode = $this->DAFTAR_METODE;
    $this->metodeColors = $this->METODE_COLORS;

    if ($this->rpsId) {
        $this->rps = RPS::with('materiPertemuan.subCpmk')->findOrFail($this->rpsId);

        $this->metodeMap = $this->rps->materiPertemuan
            ->sortBy('pertemuan_ke')
            ->mapWithKeys(function ($m) {
                return [$m->pertemuan_ke => [
                    'id' => $m->id,
                    'materi' => $m->materi,
                    'selected_metode' => $m->metode_pembelajaran ?? [],
                ]];
            })
            ->toArray();
    }
});

$toggleMetode = function ($pertemuanKe, $metode) {
    $current = $this->metodeMap[$pertemuanKe]['selected_metode'] ?? [];
    if (in_array($metode, $current)) {
        $this->metodeMap[$pertemuanKe]['selected_metode'] = array_values(array_filter($current, fn ($m) => $m !== $metode));
    } else {
        $this->metodeMap[$pertemuanKe]['selected_metode'][] = $metode;
    }
};

$save = function () {
    foreach ($this->metodeMap as $pertemuanKe => $data) {
        if (!empty($data['id'])) {
            MateriPertemuan::where('id', $data['id'])->update([
                'metode_pembelajaran' => $data['selected_metode'] ?? [],
            ]);
        }
    }

    $this->saved = true;
    $this->dispatch('rps-step-saved', step: 'metode');
};

return view('livewire.rps.builder.step6-metode');