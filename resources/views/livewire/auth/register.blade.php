<div>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Daftar Akun Baru</h2>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form wire:submit="register">
                <div class="mb-3">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <input type="text" id="name" wire:model="name" class="form-control" placeholder="Nama lengkap" required autofocus autocomplete="name">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" wire:model="email" class="form-control" placeholder="email@example.com" required autocomplete="email">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="invitation_code">Kode Undangan</label>
                    <input type="text" id="invitation_code" wire:model="invitation_code" class="form-control" placeholder="Masukkan kode undangan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" wire:model="password" class="form-control" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" wire:model="password_confirmation" class="form-control" placeholder="Ulangi password" required autocomplete="new-password">
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        Daftar
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            Sudah punya akun? <a href="{{ route('login') }}" wire:navigate>Masuk</a>
        </div>
    </div>
</div>
