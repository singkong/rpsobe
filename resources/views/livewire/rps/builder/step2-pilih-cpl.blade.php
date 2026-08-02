<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pilih Capaian Pembelajaran Lulusan (CPL)</h3>
            <span class="badge bg-primary-lt ms-auto">{{ count($selectedCpl) }} CPL dipilih</span>
        </div>
        <div class="card-body">
            @if(empty($cplGrouped))
                <div class="alert alert-warning">
                    Tidak ada CPL yang tersedia untuk mata kuliah ini. Pastikan prodi terkait telah memiliki CPL.
                </div>
            @else
                @foreach($cplGrouped as $kategori => $group)
                    <div class="mb-4">
                        @php
                            $colors = [
                                'S' => 'blue',
                                'P' => 'green',
                                'KU' => 'orange',
                                'KK' => 'purple',
                            ];
                            $color = $colors[$kategori] ?? 'gray';
                        @endphp
                        <h4>
                            <span class="badge bg-{{ $color }}-lt text-{{ $color }}">{{ $group['label'] }}</span>
                        </h4>
                        @foreach($group['items'] as $cpl)
                            <div class="card card-sm mb-2 {{ in_array($cpl->id, $selectedCpl) ? 'border-primary bg-primary-lt' : '' }}">
                                <div class="card-body">
                                    <label class="form-check mb-0">
                                        <input type="checkbox" class="form-check-input"
                                               wire:change="toggleCpl({{ $cpl->id }})"
                                               @checked(in_array($cpl->id, $selectedCpl))>
                                        <span class="form-check-label">
                                            <strong>{{ $cpl->code }}</strong> - {{ $cpl->deskripsi }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif

            <div class="d-flex justify-content-end mt-3">
                <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Simpan CPL</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>
