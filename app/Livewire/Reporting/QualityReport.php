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
    return app(ReportingService::class)->getQualityData([
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
                <h2 class="page-title">Quality Report</h2>
                <div class="text-secondary">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= route('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= route('reports.index') ?>">Laporan</a></li>
                        <li class="breadcrumb-item active">Quality</li>
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
                        <?php foreach($this->semesters as $semester)
                            <option value="<?= $semester->id ?>"><?= $semester->name ?> - <?= $semester->tahun_akademik ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <label class="form-label">Fakultas</label>
                    <select wire:model.live="fakultasId" class="form-select">
                        <option value="">Semua Fakultas</option>
                        <?php foreach($this->fakultasses as $fak)
                            <option value="<?= $fak->id ?>"><?= $fak->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <label class="form-label">Program Studi</label>
                    <select wire:model.live="prodiId" class="form-select">
                        <option value="">Semua Prodi</option>
                        <?php foreach($this->prodis as $prodi)
                            <option value="<?= $prodi->id ?>"><?= $prodi->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <?php if($data)
        <div class="row mb-3">
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0"><?= $data['reviewCount'] ?></div>
                        <div class="text-secondary">Total Review</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0"><?= $data['overallAvgScore'] ?></div>
                        <div class="text-secondary">Average Score</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0"><?= $data['validationSummary']['maxScore'] ?></div>
                        <div class="text-secondary">Max Score</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0"><?= $data['validationSummary']['minScore'] ?></div>
                        <div class="text-secondary">Min Score</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Rata-rata Skor Review per Program Studi</h3>
            </div>
            <div class="card-body">
                <?php if(empty($data['prodiAverages']))
                    <div class="text-center text-secondary py-4">Belum ada data review untuk filter yang dipilih</div>
                <?php else: ?>
                    <?php $maxAvgScore = max(array_column($data['prodiAverages'], 'avgScore')) ?: 1; ?>
                    <?php foreach($data['prodiAverages'] as $pa)
                        <?php $barPct = ($pa['avgScore'] / $maxAvgScore) * 100; ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-bold"><?= $pa['name'] ?></span>
                                <span><?= $pa['avgScore'] ?> (<?= $pa['reviewCount'] ?> reviews)</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar <?= $pa['avgScore'] >= 80 ? 'bg-green' : ($pa['avgScore'] >= 60 ? 'bg-yellow' : 'bg-red') ?>" style="width: <?= $barPct ?>%;" role="progressbar">
                                    <span class="fw-bold"><?= $pa['avgScore'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Ringkasan Validasi</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Total Review</div>
                                <div class="datagrid-content fw-bold"><?= $data['validationSummary']['totalReviews'] ?></div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Review dengan Skor</div>
                                <div class="datagrid-content fw-bold"><?= $data['validationSummary']['totalWithScore'] ?></div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Skor Rata-rata Keseluruhan</div>
                                <div class="datagrid-content">
                                    <span class="badge <?= $data['overallAvgScore'] >= 80 ? 'bg-green-lt' : ($data['overallAvgScore'] >= 60 ? 'bg-yellow-lt' : 'bg-red-lt') ?>">
                                        <?= $data['overallAvgScore'] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Skor Tertinggi</div>
                                <div class="datagrid-content fw-bold text-green"><?= $data['validationSummary']['maxScore'] ?></div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Skor Terendah</div>
                                <div class="datagrid-content fw-bold text-red"><?= $data['validationSummary']['minScore'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Constructive Alignment</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="h2"><?= number_format($data['overallAvgScore'], 1) ?>%</div>
                            <div class="text-secondary">Overall Quality Score</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar <?= $data['overallAvgScore'] >= 80 ? 'bg-green' : ($data['overallAvgScore'] >= 60 ? 'bg-yellow' : 'bg-red') ?>" style="width: <?= min($data['overallAvgScore'], 100) ?>%" role="progressbar">
                                <?= $data['overallAvgScore'] ?>%
                            </div>
                        </div>
                        <div class="mt-2 small text-secondary">
                            Berdasarkan <?= $data['reviewCount'] ?> review untuk filter yang dipilih
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Review Terbaru</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Mata Kuliah</th>
                                <th>Program Studi</th>
                                <th>Reviewer</th>
                                <th>Skor</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['reviews'] as $review)
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= $review->rps?->mataKuliah?->name ?? '-' ?></div>
                                        <div class="text-secondary small"><?= $review->rps?->mataKuliah?->code ?? '' ?></div>
                                    </td>
                                    <td class="text-secondary"><?= $review->rps?->mataKuliah?->kurikulum?->programStudi?->name ?? '-' ?></td>
                                    <td><?= $review->reviewer?->name ?? '-' ?></td>
                                    <td>
                                        <span class="badge <?= ($review->skor_total ?? 0) >= 80 ? 'bg-green-lt' : (($review->skor_total ?? 0) >= 60 ? 'bg-yellow-lt' : 'bg-red-lt') ?>">
                                            <?= $review->skor_total ?? '-' ?>
                                        </span>
                                    </td>
                                    <td><?= $review->status ?? '-' ?></td>
                                    <td class="text-secondary small"><?= $review->created_at?->format('d-m-Y H:i') ?></td>
                                </tr>
                            <?php endforeach; ?><?php if(empty($items)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">Belum ada data review</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

