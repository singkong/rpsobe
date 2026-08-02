<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\CPL;
use Illuminate\Support\Facades\Gate;

class Dashboard extends Component
{
    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->tenant_id) {
            abort(403);
        }
    }

    public function cards(): array
    {
        return [
            ['title' => 'Fakultas', 'route' => 'master-data.fakultas', 'icon' => 'building', 'color' => 'primary', 'permission' => 'fakultas.view-any'],
            ['title' => 'Program Studi', 'route' => 'master-data.program-studi', 'icon' => 'school', 'color' => 'azure', 'permission' => 'program-studi.view-any'],
            ['title' => 'Kurikulum', 'route' => 'master-data.kurikulum', 'icon' => 'books', 'color' => 'green', 'permission' => 'kurikulum.view-any'],
            ['title' => 'Mata Kuliah', 'route' => 'master-data.mata-kuliah', 'icon' => 'book', 'color' => 'orange', 'permission' => 'mata-kuliah.view-any'],
            ['title' => 'Dosen', 'route' => 'master-data.dosen', 'icon' => 'users', 'color' => 'purple', 'permission' => 'dosen.view-any'],
            ['title' => 'CPL', 'route' => 'master-data.cpl', 'icon' => 'list-check', 'color' => 'teal', 'permission' => 'cpl.view-any'],
            ['title' => 'Profil Lulusan', 'route' => 'master-data.profil-lulusan', 'icon' => 'user-check', 'color' => 'pink', 'permission' => 'profil-lulusan.view-any'],
            ['title' => 'Semester', 'route' => 'master-data.semester', 'icon' => 'calendar', 'color' => 'cyan', 'permission' => 'semester.view-any'],
            ['title' => 'Referensi', 'route' => 'master-data.referensi', 'icon' => 'bookmark', 'color' => 'yellow', 'permission' => 'referensi.view-any'],
        ];
    }

    public function render()
    {
        return view('livewire.master-data.dashboard', [
            'totalFakultas' => Fakultas::count(),
            'totalProdi' => ProgramStudi::count(),
            'totalKurikulum' => Kurikulum::count(),
            'totalMataKuliah' => MataKuliah::count(),
            'totalDosen' => Dosen::count(),
            'totalCpl' => CPL::count(),
        ]);
    }
}
