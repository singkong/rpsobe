<?php

namespace App\Livewire\RPS\Builder;

use Livewire\Component;
use App\Models\RPS;
use App\Models\MateriPertemuan;

class Step6Metode extends Component
{
    public $rpsId = null;
    public $rps = null;
    public array $metodeMap = [];
    public array $allMetode = [];
    public array $metodeColors = [];
    public bool $saved = false;

    public array $DAFTAR_METODE = ['Ceramah','Diskusi','Tanya Jawab','Problem-Based Learning (PBL)','Case-Based Learning (CBL)','Project-Based Learning (PJBL)','Simulasi','Praktikum','Studi Kasus','Presentasi','Tutorial','Cooperative Learning','Discovery Learning','Self-Directed Learning','Blended Learning','Lainnya'];

    public array $METODE_COLORS = ['Ceramah' => 'primary','Diskusi' => 'green','Tanya Jawab' => 'orange','Problem-Based Learning (PBL)' => 'purple','Case-Based Learning (CBL)' => 'teal','Project-Based Learning (PJBL)' => 'red','Simulasi' => 'cyan','Praktikum' => 'yellow','Studi Kasus' => 'pink','Presentasi' => 'indigo','Tutorial' => 'blue','Cooperative Learning' => 'lime','Discovery Learning' => 'azure','Self-Directed Learning' => 'green','Blended Learning' => 'primary','Lainnya' => 'gray'];

    public function mount($rpsId): void
    {
        $this->rpsId = $rpsId;
        $this->allMetode = $this->DAFTAR_METODE;
        $this->metodeColors = $this->METODE_COLORS;
        if ($this->rpsId) {
            $this->rps = RPS::with('materiPertemuan.subCpmk')->findOrFail($this->rpsId);
            $this->metodeMap = $this->rps->materiPertemuan->sortBy('pertemuan_ke')->mapWithKeys(fn($m) => [$m->pertemuan_ke => ['id' => $m->id, 'materi' => $m->materi, 'selected_metode' => $m->metode_pembelajaran ?? []]])->toArray();
        }
    }

    public function toggleMetode(int $pertemuanKe, string $metode): void
    {
        $current = $this->metodeMap[$pertemuanKe]['selected_metode'] ?? [];
        if (in_array($metode, $current)) { $this->metodeMap[$pertemuanKe]['selected_metode'] = array_values(array_filter($current, fn($m) => $m !== $metode)); }
        else { $this->metodeMap[$pertemuanKe]['selected_metode'][] = $metode; }
    }

    public function save(): void
    {
        foreach ($this->metodeMap as $pertemuanKe => $data) {
            if (!empty($data['id'])) { MateriPertemuan::where('id', $data['id'])->update(['metode_pembelajaran' => $data['selected_metode'] ?? []]); }
        }
        $this->saved = true;
        $this->dispatch('rps-step-saved', step: 'metode');
    }

    public function render()
    {
        return view('livewire.rps.builder.step6-metode');
    }
}
