<div>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Daftar Akun Baru</h2>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
                <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form wire:submit="register">
                <div class="mb-3">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <input type="text" id="name" wire:model="name" class="form-control" placeholder="Nama lengkap" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" wire:model="email" class="form-control" placeholder="email@example.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="invitation_code">Kode Undangan</label>
                    <input type="text" id="invitation_code" wire:model="invitation_code" class="form-control" placeholder="Masukkan kode undangan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" wire:model="password" class="form-control" placeholder="Minimal 8 karakter" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" wire:model="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Daftar</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            Sudah punya akun? <a href="<?php echo e(route('login')); ?>" wire:navigate>Masuk</a>
        </div>
    </div>
</div>
<?php /**PATH E:\laragon\www\rps-obe\resources\views/livewire/auth/register.blade.php ENDPATH**/ ?>