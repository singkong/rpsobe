@php
$title = 'Manajemen Tenant';
$pageTitle = 'Manajemen Tenant';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Manajemen Tenant' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    <div>
        <div class="card">
            <div class="card-header d-flex">
                <h3 class="card-title">Daftar Universitas</h3>
                <div class="ms-auto">
                    <span class="badge bg-green-lt me-2">{{ \App\Models\Tenant::where('is_active', true)->count() }} Aktif</span>
                    <span class="badge bg-red-lt">{{ \App\Models\Tenant::where('is_active', false)->count() }} Nonaktif</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Alamat</th>
                            <th>Website</th>
                            <th>Paket</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\Tenant::withCount(['users','fakultas'])->get() as $tenant)
                            <tr>
                                <td>
                                    <strong>{{ $tenant->name }}</strong>
                                    <div class="text-secondary small">{{ $tenant->akronim }}</div>
                                </td>
                                <td>{{ $tenant->code }}</td>
                                <td>{{ $tenant->alamat ?? '-' }}</td>
                                <td>{{ $tenant->website ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-primary-lt">{{ $tenant->subscription_package ?? 'Enterprise' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $tenant->is_active ? 'bg-green' : 'bg-red' }}-lt">{{ $tenant->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="h1 m-0">{{ \App\Models\Tenant::count() }}</div>
                        <div class="text-secondary">Total Universitas</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="h1 m-0">{{ \App\Models\User::count() }}</div>
                        <div class="text-secondary">Total Pengguna</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="h1 m-0">{{ \App\Models\RPS::count() }}</div>
                        <div class="text-secondary">Total RPS</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
