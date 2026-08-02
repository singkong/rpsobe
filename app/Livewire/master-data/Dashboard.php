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

?>

<div>
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Master Data</h2>
                <div class="text-secondary">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Master Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4 col-sm-6">
            <div class="card">
                <div class="card-body text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon text-primary mb-2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M3 21l18 0"/><path d="M3 7v1a4 4 0 0 0 4 4h10a4 4 0 0 0 4 -4v-1"/><path d="M12 11l0 10"/><path d="M9 3h6l3 5h-12z"/></svg>
                    <h3 class="mb-0">{{ $totalFakultas() }} / {{ $totalProdi() }}</h3>
                    <p class="text-secondary">Fakultas / Program Studi</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card">
                <div class="card-body text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon text-green mb-2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 5h3m4 0h9"/><path d="M3 10h11m4 0h1"/><path d="M5 15h5m4 0h8"/><path d="M3 20h9m4 0h3"/></svg>
                    <h3 class="mb-0">{{ $totalKurikulum() }} / {{ $totalMataKuliah() }}</h3>
                    <p class="text-secondary">Kurikulum / Mata Kuliah</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card">
                <div class="card-body text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon text-purple mb-2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
                    <h3 class="mb-0">{{ $totalDosen() }} / {{ $totalCpl() }}</h3>
                    <p class="text-secondary">Dosen / CPL</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($this->cards() as $card)
            @if(Gate::allows($card['permission']))
                <div class="col-md-4 col-sm-6 mb-3">
                    <a href="{{ route($card['route']) }}" class="card card-link card-link-pop">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar bg-{{ $card['color'] }}-lt">
                                        @switch($card['icon'])
                                            @case('building')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21l18 0"/><path d="M3 7v1a4 4 0 0 0 4 4h10a4 4 0 0 0 4 -4v-1"/><path d="M12 11l0 10"/><path d="M9 3h6l3 5h-12z"/></svg>
                                                @break
                                            @case('school')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 9l-10 -4l-10 4l10 4l10 -4v6"/><path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4"/></svg>
                                                @break
                                            @case('books')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 5h3m4 0h9"/><path d="M3 10h11m4 0h1"/><path d="M5 15h5m4 0h8"/></svg>
                                                @break
                                            @case('book')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/></svg>
                                                @break
                                            @case('users')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
                                                @break
                                            @case('list-check')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 12l2 2l4 -4"/></svg>
                                                @break
                                            @case('user-check')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M15 11l2 2l4 -4"/></svg>
                                                @break
                                            @case('calendar')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 11h16"/><path d="M11 15h1"/><path d="M12 15v3"/></svg>
                                                @break
                                            @case('bookmark')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 4h6a2 2 0 0 1 2 2v14l-5 -3l-5 3v-14a2 2 0 0 1 2 -2"/></svg>
                                                @break
                                        @endswitch
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-1">{{ $card['title'] }}</h4>
                                    <p class="text-secondary mb-0">Kelola data {{ strtolower($card['title']) }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
        @endforeach
    </div>
</div>
