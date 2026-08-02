<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\Semester;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Services\ReportingService;
use Illuminate\Support\Facades\Auth;

state([
    'activeTab' => 'completion',
    'semesterId' => null,
    'prodiId' => null,
    'fakultasId' => null,
    'dateFrom' => null,
    'dateTo' => null,
    'semester2Id' => null,
    'actionFilter' => null,
]);

$user = fn() => Auth::user();

$semesters = fn() => Semester::orderBy('tahun_akademik', 'desc')->orderBy('name', 'asc')->get();

$prodis = function () {
    $query = ProgramStudi::query()->with('fakultas');
    if ($this->fakultasId) {
        $query->where('fakultas_id', $this->fakultasId);
    }
    return $query->orderBy('name')->get();
};

$fakultasses = fn() => Fakultas::orderBy('name')->get();

$completionData = function () {
    if ($this->activeTab !== 'completion') {
        return null;
    }
    return app(ReportingService::class)->getCompletionData([
        'semester_id' => $this->semesterId,
        'prodi_id' => $this->prodiId,
        'fakultas_id' => $this->fakultasId,
        'date_from' => $this->dateFrom,
        'date_to' => $this->dateTo,
    ]);
};

$qualityData = function () {
    if ($this->activeTab !== 'quality') {
        return null;
    }
    return app(ReportingService::class)->getQualityData([
        'semester_id' => $this->semesterId,
        'prodi_id' => $this->prodiId,
        'fakultas_id' => $this->fakultasId,
    ]);
};

$comparisonData = function () {
    if ($this->activeTab !== 'comparison' || !$this->semesterId || !$this->semester2Id) {
        return null;
    }
    return app(ReportingService::class)->getComparisonData(
        (int) $this->semesterId,
        (int) $this->semester2Id
    );
};

$auditData = function () {
    if ($this->activeTab !== 'audit') {
        return null;
    }
    return app(ReportingService::class)->getAuditData([
        'action' => $this->actionFilter,
        'date_from' => $this->dateFrom,
        'date_to' => $this->dateTo,
    ]);
};

$setTab = function (string $tab) {
    $this->activeTab = $tab;
};

?>

<div>
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Laporan & Report</h2>
                <div class="text-secondary">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan</li>
                    </ol>
                </div>
            </div>
            <div class="col-auto">
                <div class="btn-list">
                    <a href="{{ route('reports.export-excel') }}" class="btn btn-outline-green">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/></svg>
                        Export Excel
                    </a>
                    <a href="{{ route('reports.export-pdf') }}" class="btn btn-outline-red">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4"/></svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
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
                <div class="col-lg-2 col-sm-6">
                    <label class="form-label">Fakultas</label>
                    <select wire:model.live="fakultasId" class="form-select">
                        <option value="">Semua Fakultas</option>
                        @foreach($this->fakultasses as $fak)
                            <option value="{{ $fak->id }}">{{ $fak->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <label class="form-label">Program Studi</label>
                    <select wire:model.live="prodiId" class="form-select">
                        <option value="">Semua Prodi</option>
                        @foreach($this->prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>
                <div class="col-lg-2 col-sm-6">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>
                @if($activeTab === 'comparison')
                <div class="col-lg-3 col-sm-6">
                    <label class="form-label">Semester 2 (Perbandingan)</label>
                    <select wire:model.live="semester2Id" class="form-select">
                        <option value="">Pilih Semester</option>
                        @foreach($this->semesters as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->name }} - {{ $semester->tahun_akademik }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                @if($activeTab === 'audit')
                <div class="col-lg-3 col-sm-6">
                    <label class="form-label">Aksi</label>
                    <select wire:model.live="actionFilter" class="form-select">
                        <option value="">Semua Aksi</option>
                        <option value="rps_created">RPS Created</option>
                        <option value="rps_submitted">RPS Submitted</option>
                        <option value="rps_reviewed">RPS Reviewed</option>
                        <option value="rps_approved">RPS Approved</option>
                        <option value="rps_published">RPS Published</option>
                        <option value="rps_revision_requested">Revision Requested</option>
                        <option value="rps_archived">RPS Archived</option>
                    </select>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="card mb-3">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                <li class="nav-item">
                    <a href="#" wire:click.prevent="$parent.setTab('completion')" class="nav-link {{ $activeTab === 'completion' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/></svg>
                        Completion
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" wire:click.prevent="$parent.setTab('quality')" class="nav-link {{ $activeTab === 'quality' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M9 12l2 2l4 -4"/></svg>
                        Quality
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" wire:click.prevent="$parent.setTab('comparison')" class="nav-link {{ $activeTab === 'comparison' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3v8a3 3 0 0 0 3 3h3"/><path d="M21 21v-8a3 3 0 0 0 -3 -3h-3"/></svg>
                        Comparison
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" wire:click.prevent="$parent.setTab('audit')" class="nav-link {{ $activeTab === 'audit' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/><path d="M21 21l-6 -6"/></svg>
                        Audit
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Tab Content --}}
    @if($activeTab === 'completion' && $completionData)
        <div class="row mb-3">
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $completionData['totalRps'] }}</div>
                        <div class="text-secondary">Total RPS</div>
                    </div>
                </div>
            </div>
            @php $sd = $completionData['statusDistribution']; @endphp
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $sd['published'] ?? 0 }}</div>
                        <div class="text-secondary">Published</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $sd['draft'] ?? 0 }}</div>
                        <div class="text-secondary">Draft</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $sd['review'] ?? 0 }}</div>
                        <div class="text-secondary">In Review</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Distribution Chart --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Distribusi Status</h3>
            </div>
            <div class="card-body">
                @php
                    $totalRps = $completionData['totalRps'] ?: 1;
                    $colors = ['draft' => '#f59f00', 'review' => '#206bc4', 'revision' => '#d63939', 'approved' => '#2fb344', 'published' => '#0ca678', 'archived' => '#616876'];
                @endphp
                @foreach($sd as $status => $count)
                    @php
                        try {
                            $label = \App\Enums\RPSStatus::from($status)->label();
                        } catch (\Exception $e) {
                            $label = $status;
                        }
                        $color = $colors[$status] ?? '#616876';
                        $pct = round(($count / $totalRps) * 100, 1);
                    @endphp
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $label }}</span>
                            <span>{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar" style="width: {{ $pct }}%; background-color: {{ $color }};" role="progressbar"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Completion Per Prodi --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Completion per Program Studi</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Program Studi</th>
                                <th>Total RPS</th>
                                <th>Draft</th>
                                <th>Review</th>
                                <th>Published</th>
                                <th>Completion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($completionData['completionPerProdi'] as $prodi)
                                @php $prodiPct = $prodi['total'] > 0 ? round(($prodi['published'] / $prodi['total']) * 100, 1) : 0; @endphp
                                <tr>
                                    <td class="fw-bold">{{ $prodi['name'] }} ({{ $prodi['code'] }})</td>
                                    <td>{{ $prodi['total'] }}</td>
                                    <td>{{ $prodi['draft'] }}</td>
                                    <td>{{ $prodi['review'] }}</td>
                                    <td>{{ $prodi['published'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-xs flex-grow-1 me-2" style="max-width: 80px;">
                                                <div class="progress-bar {{ $prodiPct >= 80 ? 'bg-green' : ($prodiPct >= 50 ? 'bg-yellow' : 'bg-red') }}" style="width: {{ $prodiPct }}%" role="progressbar"></div>
                                            </div>
                                            <span class="small">{{ $prodiPct }}%</span>
                                        </div>
                                    </td>
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

        {{-- Detail Table --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail RPS per MK per Dosen</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Mata Kuliah</th>
                                <th>Dosen</th>
                                <th>Status</th>
                                <th>Semester</th>
                                <th>Diperbarui</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($completionData['detailList'] as $rps)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $rps->mataKuliah?->name ?? '-' }}</div>
                                        <div class="text-secondary small">{{ $rps->mataKuliah?->code ?? '' }}</div>
                                    </td>
                                    <td>{{ $rps->user?->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $rps->status === \App\Enums\RPSStatus::Published ? '#2fb344' : ($rps->status === \App\Enums\RPSStatus::Draft ? '#f59f00' : '#206bc4') }}">
                                            {{ $rps->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-secondary">{{ $rps->semester?->name ?? '-' }}</td>
                                    <td class="text-secondary small">{{ $rps->updated_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">Belum ada data RPS</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($activeTab === 'quality' && $qualityData)
        {{-- Quality Stats --}}
        <div class="row mb-3">
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $qualityData['reviewCount'] }}</div>
                        <div class="text-secondary">Total Review</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $qualityData['overallAvgScore'] }}</div>
                        <div class="text-secondary">Avg Score Overall</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $qualityData['validationSummary']['maxScore'] }}</div>
                        <div class="text-secondary">Max Score</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $qualityData['validationSummary']['minScore'] }}</div>
                        <div class="text-secondary">Min Score</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Avg Scores per Prodi --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Rata-rata Skor Review per Prodi</h3>
            </div>
            <div class="card-body">
                @if(empty($qualityData['prodiAverages']))
                    <div class="text-center text-secondary py-4">Belum ada data review</div>
                @else
                    @php $maxAvg = max(array_column($qualityData['prodiAverages'], 'avgScore')) ?: 1; @endphp
                    @foreach($qualityData['prodiAverages'] as $pa)
                        @php $barPct = ($pa['avgScore'] / $maxAvg) * 100; @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-bold">{{ $pa['name'] ?? 'Prodi #'.$pa['prodi_id'] }}</span>
                                <span>{{ $pa['avgScore'] }} ({{ $pa['reviewCount'] }} reviews)</span>
                            </div>
                            <div class="progress" style="height: 18px;">
                                <div class="progress-bar {{ $pa['avgScore'] >= 80 ? 'bg-green' : ($pa['avgScore'] >= 60 ? 'bg-yellow' : 'bg-red') }}" style="width: {{ $barPct }}%;" role="progressbar">
                                    {{ $pa['avgScore'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Validation Summary --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Ringkasan Validasi</h3>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Total Review</div>
                        <div class="datagrid-content">{{ $qualityData['validationSummary']['totalReviews'] }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Review dengan Skor</div>
                        <div class="datagrid-content">{{ $qualityData['validationSummary']['totalWithScore'] }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Skor Rata-rata</div>
                        <div class="datagrid-content">{{ $qualityData['validationSummary']['overallAvgScore'] }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Skor Tertinggi</div>
                        <div class="datagrid-content">{{ $qualityData['validationSummary']['maxScore'] }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Skor Terendah</div>
                        <div class="datagrid-content">{{ $qualityData['validationSummary']['minScore'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Review List --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Review</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>RPS / MK</th>
                                <th>Prodi</th>
                                <th>Reviewer</th>
                                <th>Skor</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($qualityData['reviews'] as $review)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $review->rps?->mataKuliah?->name ?? '-' }}</div>
                                        <div class="text-secondary small">{{ $review->rps?->mataKuliah?->code ?? '' }}</div>
                                    </td>
                                    <td class="text-secondary">{{ $review->rps?->mataKuliah?->kurikulum?->programStudi?->name ?? '-' }}</td>
                                    <td>{{ $review->reviewer?->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ ($review->skor_total ?? 0) >= 80 ? 'bg-green-lt' : (($review->skor_total ?? 0) >= 60 ? 'bg-yellow-lt' : 'bg-red-lt') }}">
                                            {{ $review->skor_total ?? '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $review->status ?? '-' }}</td>
                                    <td class="text-secondary small">{{ $review->created_at?->format('d-m-Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">Belum ada data review</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($activeTab === 'comparison' && $comparisonData)
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $comparisonData['semester1']['name'] ?? 'Semester 1' }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2 fw-bold">Total RPS: {{ $comparisonData['data1']['total'] }}</div>
                        @foreach($comparisonData['data1']['statusCounts'] as $status => $count)
                            @php
                                try { $label = \App\Enums\RPSStatus::from($status)->label(); }
                                catch (\Exception $e) { $label = $status; }
                            @endphp
                            <div class="d-flex justify-content-between small">{{ $label }} <span>{{ $count }}</span></div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $comparisonData['semester2']['name'] ?? 'Semester 2' }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2 fw-bold">Total RPS: {{ $comparisonData['data2']['total'] }}</div>
                        @foreach($comparisonData['data2']['statusCounts'] as $status => $count)
                            @php
                                try { $label = \App\Enums\RPSStatus::from($status)->label(); }
                                catch (\Exception $e) { $label = $status; }
                            @endphp
                            <div class="d-flex justify-content-between small">{{ $label }} <span>{{ $count }}</span></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Side by Side Comparison Chart --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Perbandingan Completion per Prodi</h3>
            </div>
            <div class="card-body">
                @php
                    $maxCompare = max(
                        max(array_column($comparisonData['data1']['byProdi'], 'total') ?: [1]),
                        max(array_column($comparisonData['data2']['byProdi'], 'total') ?: [1])
                    );
                @endphp
                @foreach($comparisonData['data1']['byProdi'] as $index => $d1)
                    @php
                        $d2 = $comparisonData['data2']['byProdi'][$index] ?? null;
                        $pct1 = ($d1['total'] / max($maxCompare, 1)) * 100;
                        $pct2 = $d2 ? ($d2['total'] / max($maxCompare, 1)) * 100 : 0;
                    @endphp
                    <div class="mb-3">
                        <div class="fw-bold small mb-1">{{ $d1['name'] }}</div>
                        <div class="d-flex align-items-center mb-1">
                            <small class="text-secondary me-2" style="min-width: 80px;">{{ $comparisonData['semester1']['name'] ?? 'S1' }}</small>
                            <div class="progress flex-grow-1" style="height: 12px;">
                                <div class="progress-bar bg-blue" style="width: {{ $pct1 }}%" role="progressbar">{{ $d1['total'] }}</div>
                            </div>
                        </div>
                        @if($d2)
                        <div class="d-flex align-items-center">
                            <small class="text-secondary me-2" style="min-width: 80px;">{{ $comparisonData['semester2']['name'] ?? 'S2' }}</small>
                            <div class="progress flex-grow-1" style="height: 12px;">
                                <div class="progress-bar bg-green" style="width: {{ $pct2 }}%" role="progressbar">{{ $d2['total'] }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    @elseif($activeTab === 'audit' && $auditData)
        <div class="row mb-3">
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $auditData['totalRecords'] }}</div>
                        <div class="text-secondary">Total Log</div>
                    </div>
                </div>
            </div>
            @foreach($auditData['actionCounts'] as $action => $count)
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $count }}</div>
                        <div class="text-secondary">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $action)) }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Audit Log Detail</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Tenant</th>
                                <th>Aksi</th>
                                <th>Model</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditData['logs'] as $log)
                                <tr>
                                    <td class="text-secondary small">{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                                    <td>{{ $log->user?->name ?? 'System' }}</td>
                                    <td class="text-secondary">{{ $log->tenant?->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-azure-lt">{{ $log->action }}</span>
                                    </td>
                                    <td class="text-secondary small">
                                        {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                    </td>
                                    <td class="text-secondary small">{{ $log->ip_address ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">Belum ada log audit</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="icon text-muted mb-3">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/>
                </svg>
                <h4>Pilih tab laporan dan filter untuk melihat data</h4>
            </div>
        </div>
    @endif
</div>

