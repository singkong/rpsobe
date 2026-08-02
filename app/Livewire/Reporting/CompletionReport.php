<?php

use function Livewire\Volt\{state, computed, mount};
use App\Services\ReportingService;
use App\Models\Semester;
use App\Models\ProgramStudi;
use App\Models\Fakultas;

state([
    'semesterId' => null,
    'prodiId' => null,
    'fakultasId' => null,
]);

$semesters = fn() => Semester::orderBy('tahun_akademik', 'desc')->get();
$prodis = fn() => ProgramStudi::orderBy('name')->get();
$fakultasses = fn() => Fakultas::orderBy('name')->get();

$data = function () {
    return app(ReportingService::class)->getCompletionData([
        'semester_id' => $this->semesterId,
        'prodi_id' => $this->prodiId,
        'fakultas_id' => $this->fakultasId,
    ]);
};

?>

<div>
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Completion Report</h2>
                <div class="text-secondary">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Laporan</a></li>
                        <li class="breadcrumb-item active">Completion</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-lg-3 col-sm-6">
                    <label class="form-label">Semester</label>
                    <select wire:model.live="semesterId" class="form-select">
                        <option value="">Semua Semester</option>
                        @foreach($this->semesters as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->name }} - {{ $semester->tahun_akademik }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <label class="form-label">Fakultas</label>
                    <select wire:model.live="fakultasId" class="form-select">
                        <option value="">Semua Fakultas</option>
                        @foreach($this->fakultasses as $fak)
                            <option value="{{ $fak->id }}">{{ $fak->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <label class="form-label">Program Studi</label>
                    <select wire:model.live="prodiId" class="form-select">
                        <option value="">Semua Prodi</option>
                        @foreach($this->prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if($data)
        <div class="row mb-3">
            @php $sd = $data['statusDistribution']; $totalRps = $data['totalRps'] ?: 1; @endphp
            <div class="col-lg-2 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $data['totalRps'] }}</div>
                        <div class="text-secondary">Total</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-sm-4 col-6 mb-2">
                <div class="card card-status-start bg-yellow">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $sd['draft'] ?? 0 }}</div>
                        <div class="text-secondary">Draft</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-sm-4 col-6 mb-2">
                <div class="card card-status-start bg-blue">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $sd['review'] ?? 0 }}</div>
                        <div class="text-secondary">Review</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-sm-4 col-6 mb-2">
                <div class="card card-status-start bg-orange">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $sd['revision'] ?? 0 }}</div>
                        <div class="text-secondary">Revisi</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-sm-4 col-6 mb-2">
                <div class="card card-status-start bg-green">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $sd['approved'] ?? 0 }}</div>
                        <div class="text-secondary">Approved</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-sm-4 col-6 mb-2">
                <div class="card card-status-start bg-teal">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $sd['published'] ?? 0 }}</div>
                        <div class="text-secondary">Published</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Completion per Prodi</h3>
                    </div>
                    <div class="card-body">
                        @if(empty($data['completionPerProdi']))
                            <div class="text-center text-secondary py-4">Belum ada data</div>
                        @else
                            @php $maxTotal = max(array_column($data['completionPerProdi'], 'total')) ?: 1; @endphp
                            @foreach($data['completionPerProdi'] as $prodi)
                                @php $barPct = ($prodi['total'] / $maxTotal) * 100; @endphp
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between small">
                                        <span>{{ $prodi['name'] }}</span>
                                        <span>{{ $prodi['published'] }}/{{ $prodi['total'] }}</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $barPct }}%" role="progressbar">{{ $prodi['total'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Distribusi Status</h3>
                    </div>
                    <div class="card-body">
                        @php $colors = ['draft' => '#f59f00', 'review' => '#206bc4', 'revision' => '#d63939', 'approved' => '#2fb344', 'published' => '#0ca678', 'archived' => '#616876']; @endphp
                        @foreach($sd as $status => $count)
                            @php
                                try { $label = \App\Enums\RPSStatus::from($status)->label(); }
                                catch (\Exception $e) { $label = $status; }
                                $color = $colors[$status] ?? '#616876';
                                $pct = round(($count / $totalRps) * 100, 1);
                            @endphp
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>{{ $label }}</span>
                                    <span>{{ $count }} ({{ $pct }}%)</span>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar" style="width: {{ $pct }}%; background-color: {{ $color }};" role="progressbar"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail per MK per Dosen</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Mata Kuliah</th>
                                <th>Kode</th>
                                <th>Dosen</th>
                                <th>Prodi</th>
                                <th>Status</th>
                                <th>Diperbarui</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['detailList'] as $rps)
                                <tr>
                                    <td class="fw-bold">{{ $rps->mataKuliah?->name ?? '-' }}</td>
                                    <td class="text-secondary">{{ $rps->mataKuliah?->code ?? '' }}</td>
                                    <td>{{ $rps->user?->name ?? '-' }}</td>
                                    <td class="text-secondary">{{ $rps->mataKuliah?->kurikulum?->programStudi?->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $rps->status->color() }}-lt">{{ $rps->status->label() }}</span>
                                    </td>
                                    <td class="text-secondary small">{{ $rps->updated_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

