<?php

use function Livewire\Volt\{state, rules};
use Illuminate\Support\Facades\Password;

state('email', '');

rules(['email' => ['required', 'string', 'email']]);

$sendResetLink = function () {
    $this->validate();

    $status = Password::sendResetLink(['email' => $this->email]);

    if ($status === Password::RESET_LINK_SENT) {
        session()->flash('status', 'Tautan reset password telah dikirim ke email Anda.');
        $this->email = '';
    } else {
        session()->flash('error', 'Gagal mengirim tautan reset. Periksa email Anda.');
    }
};

?>

<div>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Lupa Password</h2>
            <p class="text-secondary mb-4">Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset password.</p>

            <?php if (session('status')): ?>
                <div class="alert alert-success"><?= session('status') ?></div>
            <?php endif; ?>

            <?php if (session('error')): ?>
                <div class="alert alert-danger"><?= session('error') ?></div>
            <?php endif; ?>

            <form wire:submit="sendResetLink">
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" wire:model="email" class="form-control" placeholder="email@example.com" required autofocus>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Kirim Tautan Reset</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="<?= route('login') ?>" wire:navigate>Kembali ke Login</a>
        </div>
    </div>
</div>
