<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dashboard</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <span class="avatar avatar-xl me-3">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        <div>
                            <h2 class="mb-0">Selamat Datang, {{ auth()->user()->name }}</h2>
                            <p class="text-secondary mb-0">
                                {{ auth()->user()->roles->first()->name ?? 'Pengguna' }}
                                @if(auth()->user()->tenant)
                                    - {{ auth()->user()->tenant->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <p>Sistem RPS OBE membantu Anda menyusun, mereview, dan mengelola RPS berbasis OBE.</p>
                    <a href="{{ route($redirectRoute) }}" class="btn btn-primary">Ke Dashboard Saya</a>
                </div>
            </div>
        </div>
    </div>
</div>
