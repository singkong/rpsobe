@php
$title = 'Manajemen User';
$pageTitle = 'Manajemen User';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Manajemen User' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Pengguna</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\User::with('roles')->get() as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-blue-lt me-1">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="badge {{ $user->is_active ? 'bg-green' : 'bg-red' }}-lt">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td class="text-secondary">{{ $user->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
