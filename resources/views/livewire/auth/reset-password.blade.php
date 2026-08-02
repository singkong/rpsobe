<div>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Reset Password</h2>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form wire:submit="resetPassword">
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" wire:model="email" class="form-control" placeholder="email@example.com" required readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password Baru</label>
                    <input type="password" id="password" wire:model="password" class="form-control" placeholder="Minimal 8 karakter" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" wire:model="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('login') }}" wire:navigate>Kembali ke Login</a>
        </div>
    </div>
</div>
