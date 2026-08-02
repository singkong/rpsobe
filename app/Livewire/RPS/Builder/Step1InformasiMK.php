<?php

namespace App\Livewire\RPS\Builder;

use Livewire\Component;
use App\Models\RPS;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Semester;
use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Services\RPSService;
use Illuminate\Support\Facades\Auth;

class Step1InformasiMK extends Component
{
    public $rpsId = null;
    public $rps = null;
    public $kurikulum_id = null;
    public $mata_kuliah_id = null;
    public $semester_id = null;
    public array $dosen_pengampu = [];
    public string $deskripsi = '';
    public $selectedKurikulum = null;
    public $selectedMK = null;

    public $kurikulumList = [];
    public $mataKuliahList = [];
    public $semesterList = [];
    public $dosenList = [];

    protected function rules(): array
    {
        return [
            'kurikulum_id' => ['required'],
            'mata_kuliah_id' => ['required'],
            'semester_id' => ['required'],
            'deskripsi' => ['required', 'string'],
        ];
    }

    public function mount($rpsId): void
    {
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
                $this->dosen_pengampu = $rps->dosen_pengampu ?? [];

                $this->selectedKurikulum = Kurikulum::with('programStudi')->find($mk->kurikulum_id);
                $this->selectedMK = $mk;
                $this->loadMataKuliahOptions();
            }
        }
    }

    public function updatedKurikulumId($value): void
    {
        $this->kurikulum_id = $value;
        $this->mata_kuliah_id = null;
        $this->selectedMK = null;
        $this->selectedKurikulum = Kurikulum::with('programStudi')->find($value);
        $this->loadMataKuliahOptions();
    }

    public function updatedMataKuliahId($value): void
    {
        $this->mata_kuliah_id = $value;
        if ($value) {
            $this->selectedMK = MataKuliah::find($value);
        }
    }

    public function loadMataKuliahOptions(): void
    {
        if ($this->kurikulum_id) {
            $this->mataKuliahList = MataKuliah::where('kurikulum_id', $this->kurikulum_id)
                ->orderBy('semester')
                ->orderBy('name')
                ->get();
        } else {
            $this->mataKuliahList = collect();
        }
    }

    public function toggleDosen(int $dosenId): void
    {
        if (in_array($dosenId, $this->dosen_pengampu)) {
            $this->dosen_pengampu = array_values(array_filter($this->dosen_pengampu, fn($id) => $id != $dosenId));
        } else {
            $this->dosen_pengampu[] = $dosenId;
        }
    }

    public function save(): void
    {
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
    }

    public function render()
    {
        return view('livewire.rps.builder.step1-informasi-mk');
    }
}
