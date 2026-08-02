<div>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Masuk</h2>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>{{ session('status') }}</div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            @if (!empty($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <form wire:submit="login">
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@example.com" required autofocus autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label d-flex" for="password">
                        Password
                        <span class="ms-auto">
                            <a href="{{ route('password.request') }}" wire:navigate class="text-decoration-none">Lupa password?</a>
                        </span>
                    </label>
                    <input type="password" id="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required autocomplete="current-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" wire:model="remember" class="form-check-input">
                        <span class="form-check-label">Ingat saya</span>
                    </label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="login">Masuk</span>
                        <span wire:loading wire:target="login">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            Belum punya akun? <a href="{{ route('register') }}" wire:navigate>Daftar</a>
        </div>
    </div>
</div>
