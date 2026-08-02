<x-layouts.auth title="Verifikasi Email">
    <div class="card card-md">
        <div class="card-body text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon text-primary mb-3">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"/><path d="M3 7l9 6l9 -6"/>
            </svg>
            <h2 class="card-title mb-3">Verifikasi Email</h2>

            @if (session('status'))
                <div class="alert alert-success mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <p class="text-secondary mb-4">
                Sebelum melanjutkan, silakan periksa email Anda untuk tautan verifikasi.
                Jika Anda tidak menerima email, klik tombol di bawah untuk mengirim ulang.
            </p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-ghost-secondary w-100">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</x-layouts.auth>
