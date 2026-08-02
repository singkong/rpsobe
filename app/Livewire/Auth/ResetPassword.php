<?php

use function Livewire\Volt\{state, rules};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

state('email', '');
state('password', '');
state('password_confirmation', '');
state('token', '');

rules([
    'email' => ['required', 'string', 'email'],
    'password' => ['required', 'string', 'min:8', 'confirmed'],
    'password_confirmation' => ['required'],
    'token' => ['required', 'string'],
]);

$resetPassword = function () {
    $this->validate();

    $status = Password::reset(
        [
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'token' => $this->token,
        ],
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->setRememberToken(Str::random(60));

            $user->save();
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        session()->flash('status', 'Password berhasil direset. Silakan login.');
        $this->redirect(route('login'), navigate: true);
    } else {
        $this->addError('email', 'Token reset password tidak valid atau telah kadaluarsa.');
    }
};

?>

<div>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Reset Password</h2>
            <p class="text-secondary mb-4">Masukkan password baru untuk akun Anda.</p>

            <?php if(session('status')): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/>
                            </svg>
                        </div>
                        <div><?= session('status') ?></div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            

            <?php if (!empty($errors) && $errors->any()): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M12 8v4"/><path d="M12 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors->all() as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            

            <form wire:submit="resetPassword">
                <input type="hidden" wire:model="token">
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" wire:model="email" class="form-control" placeholder="email@example.com" required readonly>
                    
                        <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('email') ?></div>
                    
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password Baru</label>
                    <input type="password" id="password" wire:model="password" class="form-control" placeholder="Minimal 8 karakter" required autofocus autocomplete="new-password">
                    
                        <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('password') ?></div>
                    
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" wire:model="password_confirmation" class="form-control" placeholder="Ulangi password baru" required autocomplete="new-password">
                    
                        <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('password_confirmation') ?></div>
                    
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                        <span wire:loading wire:target="resetPassword">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

