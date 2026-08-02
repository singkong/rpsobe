<div @rps-saved.window="onRpsSaved($event)" x-data="{ rpsId: @entangle('rpsId') }">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Mata Kuliah</h3>
            @if($rps && $rps->exists)
                <span class="badge bg-green-lt ms-auto">{{ $rps->mataKuliah->name ?? 'Belum dipilih' }}</span>
            @endif
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label required">Kurikulum</label>
                    <select wire:model.live="kurikulum_id" class="form-select @error('kurikulum_id') is-invalid @enderror">
                        <option value="">-- Pilih Kurikulum --</option>
                        @foreach($kurikulumList as $k)
                            <option value="{{ $k->id }}">{{ $k->name }} ({{ $k->programStudi->name ?? '' }})</option>
                        @endforeach
                    </select>
                    @error('kurikulum_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Mata Kuliah</label>
                    <select wire:model.live="mata_kuliah_id" class="form-select @error('mata_kuliah_id') is-invalid @enderror" @disabled(!$kurikulum_id)>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($mataKuliahList as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->code }} - {{ $mk->name }} ({{ $mk->sks }} SKS)</option>
                        @endforeach
                    </select>
                    @error('mata_kuliah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            @if($selectedMK)
                <div class="card card-sm bg-primary-lt mb-3">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <div><strong>Kode:</strong> {{ $selectedMK->code }}</div>
                            <div><strong>{{ $selectedMK->name }}</strong></div>
                            <div><strong>SKS:</strong> {{ $selectedMK->sks }}</div>
                            <div><strong>Semester:</strong> {{ $selectedMK->semester }}</div>
                            <div><strong>Jenis:</strong> {{ ucfirst($selectedMK->jenis) }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label required">Semester</label>
                    <select wire:model="semester_id" class="form-select @error('semester_id') is-invalid @enderror">
                        <option value="">-- Pilih Semester --</option>
                        @foreach($semesterList as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->tahun_akademik }})</option>
                        @endforeach
                    </select>
                    @error('semester_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Dosen Pengampu</label>
                <div class="row">
                    @foreach($dosenList as $dosen)
                        <div class="col-md-4 mb-2">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input"
                                       wire:change="toggleDosen({{ $dosen->id }})"
                                       @checked(in_array($dosen->id, $dosen_pengampu))>
                                <span class="form-check-label">{{ $dosen->name }}{{ $dosen->nidn ? ' (NIDN: ' . $dosen->nidn . ')' : '' }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label required">Deskripsi Mata Kuliah</label>
                <textarea wire:model="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" placeholder="Deskripsi singkat mata kuliah..."></textarea>
                @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Simpan & Lanjutkan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>
