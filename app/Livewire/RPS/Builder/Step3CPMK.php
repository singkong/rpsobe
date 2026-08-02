<?php

namespace App\Livewire\RPS\Builder;

use Livewire\Component;
use App\Enums\TaksonomiLevel;
use App\Models\RPS;
use App\Models\CPMK;
use App\Models\CPL;

class Step3CPMK extends Component
{
    public $rpsId = null;
    public $rps = null;
    public array $cpmlList = [];
    public $cplList = [];
    public $editingId = null;
    public string $editCode = '';
    public string $editDeskripsi = '';
    public array $editCplIds = [];
    public string $editLevelTaksonomi = '';
    public bool $addingNew = false;
    public string $newCode = '';
    public string $newDeskripsi = '';
    public array $newCplIds = [];
    public string $newLevelTaksonomi = '';

    public function taksonomiOptions(): array
    {
        return collect(TaksonomiLevel::cases())->map(fn($l) => [
            'value' => $l->value,
            'label' => $l->value . ' - ' . $l->label(),
        ])->toArray();
    }

    public function mount($rpsId): void
    {
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
    }

    public function startAdd(): void
    {
        $this->addingNew = true;
        $count = CPMK::where('rps_id', $this->rpsId)->withTrashed()->count();
        $this->newCode = 'CPMK-' . str_pad($count + 1, 2, '0', STR_PAD_LEFT);
        $this->newDeskripsi = '';
        $this->newCplIds = [];
        $this->newLevelTaksonomi = '';
    }

    public function cancelAdd(): void
    {
        $this->addingNew = false;
        $this->resetNewForm();
    }

    public function saveNew(): void
    {
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
    }

    public function startEdit(int $id): void
    {
        $this->editingId = $id;
        $item = collect($this->cpmlList)->firstWhere('id', $id);

        if ($item) {
            $this->editCode = $item['code'];
            $this->editDeskripsi = $item['deskripsi'];
            $this->editCplIds = $item['cpl_ids'];
            $this->editLevelTaksonomi = $item['level_taksonomi'] ?? '';
        }
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function saveEdit(int $id): void
    {
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

        $index = collect($this->cpmlList)->search(fn($item) => $item['id'] === $id);
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
    }

    public function deleteCpml(int $id): void
    {
        CPMK::findOrFail($id)->delete();
        $this->cpmlList = array_values(array_filter($this->cpmlList, fn($item) => $item['id'] !== $id));
    }

    public function moveUp(int $index): void
    {
        if ($index > 0) {
            $temp = $this->cpmlList[$index - 1];
            $this->cpmlList[$index - 1] = $this->cpmlList[$index];
            $this->cpmlList[$index] = $temp;
        }
    }

    public function moveDown(int $index): void
    {
        if ($index < count($this->cpmlList) - 1) {
            $temp = $this->cpmlList[$index + 1];
            $this->cpmlList[$index + 1] = $this->cpmlList[$index];
            $this->cpmlList[$index] = $temp;
        }
    }

    public function resetNewForm(): void
    {
        $this->newCode = '';
        $this->newDeskripsi = '';
        $this->newCplIds = [];
        $this->newLevelTaksonomi = '';
    }

    public function render()
    {
        return view('livewire.rps.builder.step3-cpmk');
    }
}
