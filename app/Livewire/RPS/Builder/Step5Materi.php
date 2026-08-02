<?php

namespace App\Livewire\RPS\Builder;

use Livewire\Component;
use App\Models\RPS;
use App\Models\MateriPertemuan;
use App\Models\SubCPMK;
use App\Models\Referensi;

class Step5Materi extends Component
{
    public $rpsId = null;
    public $rps = null;
    public array $pertemuanList = [];
    public int $maxPertemuan = 16;
    public array $allSubCpmk = [];
    public array $allReferensi = [];
    public bool $saved = false;

    public function totalPertemuan(): int
    {
        return count($this->pertemuanList);
    }

    public function availableSubCpmk()
    {
        return SubCPMK::whereHas('cpmk', function ($q) { $q->where('rps_id', $this->rpsId); })->with('cpmk')->get();
    }

    public function mount($rpsId): void
    {
        $this->rpsId = $rpsId;
        if ($this->rpsId) {
            $this->rps = RPS::with('materiPertemuan.subCpmk.cpmk')->findOrFail($this->rpsId);
            $this->allSubCpmk = $this->availableSubCpmk()->map(function ($sub) {
                return ['id' => $sub->id, 'code' => $sub->code, 'cpml_code' => $sub->cpmk?->code ?? '', 'deskripsi' => $sub->deskripsi, 'pertemuan_terkait' => $sub->pertemuan_terkait ?? []];
            })->toArray();
            $this->allReferensi = Referensi::get()->map(fn($r) => ['id' => $r->id, 'judul' => $r->judul, 'penulis' => $r->penulis, 'tahun' => $r->tahun])->toArray();
            $existing = $this->rps->materiPertemuan;
            $maxExisting = $existing->max('pertemuan_ke') ?: 0;
            $initialMax = max($maxExisting, 16);
            $this->pertemuanList = collect(range(1, $initialMax))->map(function ($ke) use ($existing) {
                $match = $existing->firstWhere('pertemuan_ke', $ke);
                return ['id' => $match->id ?? null, 'pertemuan_ke' => $ke, 'sub_cpmk_id' => $match->sub_cpmk_id ?? null, 'materi' => $match->materi ?? '', 'indikator' => $match->indikator ?? '', 'referensi_ids' => $match->referensi_ids ?? []];
            })->toArray();
            $this->maxPertemuan = $initialMax;
        }
    }

    public function filteredSubCpmk(int $pertemuanKe): array
    {
        return array_filter($this->allSubCpmk, function ($sub) use ($pertemuanKe) {
            $related = $sub['pertemuan_terkait'] ?? [];
            return empty($related) ? true : in_array($pertemuanKe, $related);
        });
    }

    public function tambahPertemuan(): void
    {
        $newKe = $this->maxPertemuan + 1;
        $this->pertemuanList[] = ['id' => null, 'pertemuan_ke' => $newKe, 'sub_cpmk_id' => null, 'materi' => '', 'indikator' => '', 'referensi_ids' => []];
        $this->maxPertemuan = $newKe;
    }

    public function hapusPertemuan(): void
    {
        if ($this->maxPertemuan > 1) {
            $last = array_pop($this->pertemuanList);
            if (!empty($last['id'])) MateriPertemuan::find($last['id'])?->delete();
            $this->maxPertemuan = count($this->pertemuanList);
        }
    }

    public function toggleReferensi(int $pertemuanIndex, int $refId): void
    {
        $current = $this->pertemuanList[$pertemuanIndex]['referensi_ids'] ?? [];
        if (in_array($refId, $current)) {
            $this->pertemuanList[$pertemuanIndex]['referensi_ids'] = array_values(array_filter($current, fn($id) => $id != $refId));
        } else {
            $this->pertemuanList[$pertemuanIndex]['referensi_ids'][] = $refId;
        }
    }

    public function save(): void
    {
        $this->validate(['pertemuanList.*.materi' => ['required', 'string']], ['pertemuanList.*.materi.required' => 'Materi untuk setiap pertemuan wajib diisi.']);
        foreach ($this->pertemuanList as &$data) {
            $payload = ['rps_id' => $this->rpsId, 'pertemuan_ke' => $data['pertemuan_ke'], 'sub_cpmk_id' => $data['sub_cpmk_id'] ?: null, 'materi' => $data['materi'], 'indikator' => $data['indikator'] ?: null, 'referensi_ids' => $data['referensi_ids'] ?: null];
            if ($data['id']) { MateriPertemuan::find($data['id'])->update($payload); } else { $record = MateriPertemuan::create($payload); $data['id'] = $record->id; }
        }
        $this->saved = true;
        $this->dispatch('rps-step-saved', step: 'materi');
    }

    public function render()
    {
        return view('livewire.rps.builder.step5-materi');
    }
}
