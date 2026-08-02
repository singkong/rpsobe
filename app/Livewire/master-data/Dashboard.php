<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\CPL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

mount(function () {
    if (!auth()->user() || !auth()->user()->tenant_id) {
        abort(403);
    }
});

// Stats cached as properties
$totalFakultas = fn() => Fakultas::count();
$totalProdi = fn() => ProgramStudi::count();
$totalKurikulum = fn() => Kurikulum::count();
$totalMataKuliah = fn() => MataKuliah::count();
$totalDosen = fn() => Dosen::count();
$totalCpl = fn() => CPL::count();

$cards = function () {
    return [
        [
            'title' => 'Fakultas',
            'route' => 'master-data.fakultas',
            'icon' => 'building',
            'color' => 'primary',
            'permission' => 'fakultas.view-any',
        ],
        [
            'title' => 'Program Studi',
            'route' => 'master-data.program-studi',
            'icon' => 'school',
            'color' => 'azure',
            'permission' => 'program-studi.view-any',
        ],
        [
            'title' => 'Kurikulum',
            'route' => 'master-data.kurikulum',
            'icon' => 'books',
            'color' => 'green',
            'permission' => 'kurikulum.view-any',
        ],
        [
            'title' => 'Mata Kuliah',
            'route' => 'master-data.mata-kuliah',
            'icon' => 'book',
            'color' => 'orange',
            'permission' => 'mata-kuliah.view-any',
        ],
        [
            'title' => 'Dosen',
            'route' => 'master-data.dosen',
            'icon' => 'users',
            'color' => 'purple',
            'permission' => 'dosen.view-any',
        ],
        [
            'title' => 'CPL',
            'route' => 'master-data.cpl',
            'icon' => 'list-check',
            'color' => 'teal',
            'permission' => 'cpl.view-any',
        ],
        [
            'title' => 'Profil Lulusan',
            'route' => 'master-data.profil-lulusan',
            'icon' => 'user-check',
            'color' => 'pink',
            'permission' => 'profil-lulusan.view-any',
        ],
        [
            'title' => 'Semester',
            'route' => 'master-data.semester',
            'icon' => 'calendar',
            'color' => 'cyan',
            'permission' => 'semester.view-any',
        ],
        [
            'title' => 'Referensi',
            'route' => 'master-data.referensi',
            'icon' => 'bookmark',
            'color' => 'yellow',
            'permission' => 'referensi.view-any',
        ],
    ];
};

return view('livewire.master-data.dashboard');
