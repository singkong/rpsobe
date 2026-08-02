<?php

use function Livewire\Volt\{state, mount, rules, on};
use App\Models\RPS;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Semester;
use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Services\RPSService;
use Illuminate\Support\Facades\Auth;

state('rpsId', null);
state('rps', null);
state('kurikulum_id', null);
state('mata_kuliah_id', null);
state('semester_id', null);
state('dosen_pengampu', []);
state('deskripsi', '');
state('selectedKurikulum', null);
state('selectedMK', null);

state('kurikulumList', []);
state('mataKuliahList', []);
state('semesterList', []);
state('dosenList', []);

rules([
    'kurikulum_id' => ['required'],
    'mata_kuliah_id' => ['required'],
    'semester_id' => ['required'],
    'deskripsi' => ['required', 'string'],
]);

mount(function ($rpsId) {
    $this->rpsId = $rpsId;

    $user = Auth::user();

    $this->kurikulumList = Kurikulum::where('is_active', true)
        ->when($user->tenant_id && !$user->hasRole('super-admin'), function ($q) use ($user) {
            $q->whereHas('programStudi', function ($q2) use ($user) {
                $q2->where('tenant_id', $user->tenant_id);
            });
        })
        ->with('programStudi')
        ->get();

    $this->semesterList = Semester::where('is_active', true)->get();

    $this->dosenList = Dosen::when($user->tenant_id && !$user->hasRole('super-admin'), function ($q) use ($user) {
        $q->where('tenant_id', $user->tenant_id);
    })->where('is_active', true)->get();

    if ($this->rpsId) {
        $rps = RPS::findOrFail($this->rpsId);
        $this->rps = $rps;
        $mk = $rps->mataKuliah;

        if ($mk) {
            $this->mata_kuliah_id = $rps->mata_kuliah_id;
            $this->kurikulum_id = $mk->kurikulum_id;
            $this->semester_id = $rps->semester_id;
            $this->deskripsi = $rps->deskripsi ?? '';
            $this->dosen_pengampu = $rps->dosen_pengampu_json ?? [];

            $this->selectedKurikulum = Kurikulum::with('programStudi')->find($mk->kurikulum_id);
            $this->selectedMK = $mk;
            $this->loadMataKuliahOptions();
        }
    }
});

$loadMataKuliahOptions = function () {
    if ($this->kurikulum_id) {
        $this->mataKuliahList = MataKuliah::where('kurikulum_id', $this->kurikulum_id)
            ->orderBy('semester')
            ->orderBy('name')
            ->get();
    } else {
        $this->mataKuliahList = collect();
    }
};

$updatedKurikulumId = function ($value) {
    $this->kurikulum_id = $value;
    $this->mata_kuliah_id = null;
    $this->selectedMK = null;
    $this->selectedKurikulum = Kurikulum::with('programStudi')->find($value);
    $this->loadMataKuliahOptions();
};

$updatedMataKuliahId = function ($value) {
    $this->mata_kuliah_id = $value;
    if ($value) {
        $this->selectedMK = MataKuliah::find($value);
    }
};

$toggleDosen = function ($dosenId) {
    if (in_array($dosenId, $this->dosen_pengampu)) {
        $this->dosen_pengampu = array_values(array_filter($this->dosen_pengampu, fn ($id) => $id != $dosenId));
    } else {
        $this->dosen_pengampu[] = $dosenId;
    }
};

$save = function () {
    $this->validate();

    if ($this->rps && $this->rps->exists) {
        $this->rps->update([
            'mata_kuliah_id' => $this->mata_kuliah_id,
            'semester_id' => $this->semester_id,
            'dosen_pengampu_json' => $this->dosen_pengampu,
            'deskripsi' => $this->deskripsi,
        ]);
    } else {
        $service = app(RPSService::class);
        $this->rps = $service->create([
            'mata_kuliah_id' => $this->mata_kuliah_id,
            'semester_id' => $this->semester_id,
            'dosen_pengampu_json' => $this->dosen_pengampu,
            'deskripsi' => $this->deskripsi,
        ]);
    }

    $this->dispatch('rps-saved', rpsId: $this->rps->id);
};

return view('livewire.rps.builder.step1-informasi-mk');