<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Riwayat RPS</h2>
                    <div class="text-secondary mt-1">
                        {{ $rps->mataKuliah->code }} - {{ $rps->mataKuliah->name }}
                        <span class="badge bg-{{ $rps->status->color() }} ms-2">{{ $rps->status->label() }}</span>
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('rps.index') }}" class="btn btn-ghost-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l14 0"/><path d="M5 12l4 4"/><path d="M5 12l4 -4"/></svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Timeline Workflow</h3>
                        </div>
                        <div class="card-body">
                            @if(empty($timeline))
                                <div class="empty">
                                    <div class="empty-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M10 3h4a1 1 0 0 1 1 1v3h-6v-3a1 1 0 0 1 1 -1z"/><path d="M5 6h14a2 2 0 0 1 2 2v11a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2z"/></svg>
                                    </div>
                                    <p class="empty-title">Belum ada aktivitas</p>
                                    <p class="empty-subtitle text-secondary">Riwayat workflow akan muncul di sini.</p>
                                </div>
                            @else
                                <ul class="timeline">
                                    @foreach($timeline as $item)
                                        @if($item['type'] === 'status_change')
                                            @php
                                                $from = $item['changes']['from'] ?? ($item['old_values']['status'] ?? '');
                                                $to = $item['changes']['to'] ?? ($item['new_values']['status'] ?? '');
                                                $icon = $this->statusIcon($to);
                                                $color = $this->statusColor($to);
                                                $statusLabel = \App\Enums\RPSStatus::from($to)->label();
                                            @endphp
                                            <li class="timeline-event">
                                                <div class="timeline-event-icon bg-{{ $color }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/></svg>
                                                </div>
                                                <div class="card timeline-event-card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <strong>Status: <span class="badge bg-{{ $color }}">{{ $statusLabel }}</span></strong>
                                                            <small class="text-secondary">{{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="text-secondary mt-1 mb-0">
                                                            Diubah oleh: <strong>{{ $item['actor'] }}</strong>
                                                        </p>
                                                        @if($from && $to)
                                                            <p class="text-secondary mb-0">
                                                                Dari <span class="badge bg-{{ $this->statusColor($from) }}">{{ \App\Enums\RPSStatus::tryFrom($from)?->label() ?? $from }}</span>
                                                                ke <span class="badge bg-{{ $this->statusColor($to) }}">{{ $statusLabel }}</span>
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @elseif($item['type'] === 'review')
                                            <li class="timeline-event">
                                                <div class="timeline-event-icon bg-cyan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 3h4a1 1 0 0 1 1 1v3h-6v-3a1 1 0 0 1 1 -1z"/><path d="M5 6h14a2 2 0 0 1 2 2v11a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2z"/></svg>
                                                </div>
                                                <div class="card timeline-event-card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <strong>Review oleh: {{ $item['actor'] }}</strong>
                                                            <small class="text-secondary">{{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</small>
                                                        </div>
                                                        @if($item['skor_total'])
                                                        <div class="mt-2">
                                                            <span class="badge bg-primary-lt me-2">Skor: {{ $item['skor_total'] }}</span>
                                                            <span class="badge bg-{{ ($item['status'] ?? '') === 'approved' ? 'success' : 'warning' }}-lt">
                                                                {{ ($item['status'] ?? '') === 'approved' ? 'Disetujui' : 'Revisi' }}
                                                            </span>
                                                        </div>
                                                        @endif
                                                        @if(!empty($item['skor_per_komponen']))
                                                        <div class="mt-2">
                                                            <small class="text-secondary">Detail Skor:</small>
                                                            <div class="row g-1 mt-1">
                                                                @foreach($item['skor_per_komponen'] as $k => $v)
                                                                    @if($v)
                                                                    <div class="col-auto">
                                                                        <span class="badge bg-light text-dark">{{ $k }}: {{ $v }}</span>
                                                                    </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @if(!empty($item['komentar']))
                                                        <div class="mt-2">
                                                            <small class="text-secondary">Komentar:</small>
                                                            @foreach($item['komentar'] as $k => $kom)
                                                                @if($kom)
                                                                <div class="small text-secondary mt-1"><strong>{{ $k }}:</strong> {{ $kom }}</div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        @endif
                                                        @if(!empty($item['catatan']))
                                                        <div class="mt-2 p-2 bg-light rounded">
                                                            <small class="text-secondary">{{ $item['catatan'] }}</small>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @elseif($item['type'] === 'version')
                                            <li class="timeline-event">
                                                <div class="timeline-event-icon bg-purple">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3c-1.2 0 -2.4 .6 -3 1.5a3.5 3.5 0 0 0 -3.5 3.5a3.5 3.5 0 0 0 3.5 3.5a3.5 3.5 0 0 0 3.5 3.5c1.1 0 2 -.9 2 -2"/></svg>
                                                </div>
                                                <div class="card timeline-event-card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <strong>Versi: <span class="badge bg-purple-lt">{{ $item['version_label'] }}</span></strong>
                                                            <small class="text-secondary">{{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="text-secondary mt-1 mb-0">Snapshot dibuat oleh: <strong>{{ $item['actor'] }}</strong></p>
                                                    </div>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi RPS</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Mata Kuliah</dt>
                                <dd class="col-sm-7">{{ $rps->mataKuliah->code }} - {{ $rps->mataKuliah->name }}</dd>
                                <dt class="col-sm-5">SKS</dt>
                                <dd class="col-sm-7">{{ $rps->mataKuliah->sks }}</dd>
                                <dt class="col-sm-5">Semester</dt>
                                <dd class="col-sm-7">{{ $rps->semester->name ?? '-' }}</dd>
                                <dt class="col-sm-5">Dosen</dt>
                                <dd class="col-sm-7">{{ $rps->user->name ?? '-' }}</dd>
                                <dt class="col-sm-5">Versi Saat Ini</dt>
                                <dd class="col-sm-7"><span class="badge bg-primary-lt">{{ $rps->version_label }}</span></dd>
                                <dt class="col-sm-5">Status</dt>
                                <dd class="col-sm-7"><span class="badge bg-{{ $rps->status->color() }}">{{ $rps->status->label() }}</span></dd>
                                <dt class="col-sm-5">Dibuat</dt>
                                <dd class="col-sm-7">{{ $rps->created_at->format('d M Y H:i') }}</dd>
                                <dt class="col-sm-5">Diperbarui</dt>
                                <dd class="col-sm-7">{{ $rps->updated_at->format('d M Y H:i') }}</dd>
                            </dl>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Versi Snapshot</h3>
                        </div>
                        <div class="card-body">
                            @php $versions = \App\Models\RPSVersion::with('createdBy')->where('rps_id', $rps->id)->orderBy('created_at', 'desc')->get(); @endphp
                            @if($versions->isEmpty())
                                <p class="text-secondary mb-0">Belum ada snapshot versi.</p>
                            @else
                                @foreach($versions as $ver)
                                <div class="mb-2 p-2 border rounded">
                                    <div class="d-flex justify-content-between">
                                        <span class="badge bg-purple-lt">{{ $ver->version_label }}</span>
                                        <small class="text-secondary">{{ $ver->created_at->format('d M Y H:i') }}</small>
                                    </div>
                                    <small class="text-secondary">Oleh: {{ $ver->createdBy->name ?? 'System' }}</small>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
