<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - RPS OBE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <div class="page">
        <!-- Sidebar -->
        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/></svg>
                    </a>
                </h1>
                <span class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="{{ route('dashboard') }}" class="text-reset text-decoration-none"><strong>RPS</strong> OBE</a>
                </span>
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-home"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/></svg></span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        @if(auth()->user()->hasRole(['super-admin']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle collapsed" href="#sidebar-admin" data-bs-toggle="collapse" data-bs-auto-close="false" role="button">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-shield"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/></svg></span>
                                <span class="nav-link-title">Administrasi</span>
                            </a>
                            <div class="collapse" id="sidebar-admin" data-bs-parent="#sidebar-menu">
                                <ul class="navbar-nav ps-3">
                                    <li class="nav-item"><a class="nav-link" href="#">Manajemen Tenant</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#">Paket Langganan</a></li>
                                </ul>
                            </div>
                        </li>
                        @endif

                        @if(auth()->user()->hasRole(['super-admin','admin-univ','admin-fakultas','admin-prodi','kaprodi']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="#">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-users"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg></span>
                                <span class="nav-link-title">Manajemen User</span>
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->hasRole(['super-admin','admin-univ','kaprodi']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('template.*') ? 'active' : '' }}" href="#">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-template"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h14a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-14a1 1 0 0 1 -1 -1z"/><path d="M4 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 12l6 0"/><path d="M14 16l6 0"/><path d="M14 20l6 0"/></svg></span>
                                <span class="nav-link-title">Template RPS</span>
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->hasRole(['super-admin','admin-univ','admin-fakultas','admin-prodi','kaprodi']))
                        <li class="nav-item dropdown {{ request()->routeIs('master-data.*') ? 'show' : '' }}">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('master-data.*') ? '' : 'collapsed' }}" href="#sidebar-masterdata" data-bs-toggle="collapse" data-bs-auto-close="false" role="button" aria-expanded="{{ request()->routeIs('master-data.*') ? 'true' : 'false' }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-database"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0"/><path d="M4 6v6a8 3 0 0 0 16 0v-6"/><path d="M4 12v6a8 3 0 0 0 16 0v-6"/></svg></span>
                                <span class="nav-link-title">Master Data</span>
                            </a>
                            <div class="collapse {{ request()->routeIs('master-data.*') ? 'show' : '' }}" id="sidebar-masterdata" data-bs-parent="#sidebar-menu">
                                <ul class="navbar-nav ps-3">
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('master-data.fakultas') ? 'active' : '' }}" href="{{ route('master-data.fakultas') }}">Fakultas</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('master-data.program-studi') ? 'active' : '' }}" href="{{ route('master-data.program-studi') }}">Program Studi</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('master-data.kurikulum') ? 'active' : '' }}" href="{{ route('master-data.kurikulum') }}">Kurikulum</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('master-data.mata-kuliah') ? 'active' : '' }}" href="{{ route('master-data.mata-kuliah') }}">Mata Kuliah</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('master-data.dosen') ? 'active' : '' }}" href="{{ route('master-data.dosen') }}">Dosen</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('master-data.cpl') ? 'active' : '' }}" href="{{ route('master-data.cpl') }}">CPL</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('master-data.profil-lulusan') ? 'active' : '' }}" href="{{ route('master-data.profil-lulusan') }}">Profil Lulusan</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('master-data.semester') ? 'active' : '' }}" href="{{ route('master-data.semester') }}">Semester</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('master-data.referensi') ? 'active' : '' }}" href="{{ route('master-data.referensi') }}">Referensi</a></li>
                                </ul>
                            </div>
                        </li>
                        @endif

                        @if(auth()->user()->hasRole(['super-admin','admin-univ','admin-fakultas','admin-prodi','kaprodi','dosen']))
                        <li class="nav-item dropdown {{ request()->routeIs('rps.*') ? 'show' : '' }}">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('rps.*') ? '' : 'collapsed' }}" href="#sidebar-rps" data-bs-toggle="collapse" data-bs-auto-close="false" role="button" aria-expanded="{{ request()->routeIs('rps.*') ? 'true' : 'false' }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-book"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/></svg></span>
                                <span class="nav-link-title">RPS</span>
                            </a>
                            <div class="collapse {{ request()->routeIs('rps.*') ? 'show' : '' }}" id="sidebar-rps" data-bs-parent="#sidebar-menu">
                                <ul class="navbar-nav ps-3">
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('rps.index') ? 'active' : '' }}" href="{{ route('rps.index') }}">Daftar RPS</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('rps.create') ? 'active' : '' }}" href="{{ route('rps.create') }}">Buat RPS Baru</a></li>
                                </ul>
                            </div>
                        </li>
                        @endif

                        @if(auth()->user()->hasRole(['super-admin','reviewer','kaprodi']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('review.*') ? 'active' : '' }}" href="{{ route('review.list') }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-clipboard-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 14l2 2l4 -4"/></svg></span>
                                <span class="nav-link-title">Review</span>
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->hasRole(['super-admin','kaprodi']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('approval.*') ? 'active' : '' }}" href="{{ route('approval.list') }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg></span>
                                <span class="nav-link-title">Approval</span>
                            </a>
                        </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-chart-line"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h16"/><path d="M4 20v-5l4 -2l4 4l4 -6l4 3"/></svg></span>
                                <span class="nav-link-title">Laporan</span>
                            </a>
                        </li>

                        @if(auth()->user()->hasRole(['super-admin','admin-univ','lpm']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-history"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2"/><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5"/></svg></span>
                                <span class="nav-link-title">Audit Log</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Topbar -->
        <header class="navbar navbar-expand-md navbar-light d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <span class="navbar-brand d-md-none">RPS OBE</span>
                <div class="navbar-nav flex-row order-md-last ms-auto">
                    <!-- Notifications -->
                    <livewire:notification.notification-center />

                    <!-- User Dropdown -->
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Menu Pengguna">
                            <span class="avatar avatar-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            <div class="d-none d-xl-block ps-2">
                                <div class="text-truncate" style="max-width:120px">{{ auth()->user()->name }}</div>
                                <div class="mt-1 small text-secondary">@php
    $roleLabels = ['super-admin' => 'Super Admin', 'admin-univ' => 'Admin Universitas', 'admin-fakultas' => 'Admin Fakultas', 'admin-prodi' => 'Admin Prodi', 'kaprodi' => 'Kaprodi', 'reviewer' => 'Reviewer', 'dosen' => 'Dosen', 'lpm' => 'LPM', 'mahasiswa' => 'Mahasiswa'];
@endphp
{{ $roleLabels[auth()->user()->roles->first()->name ?? 'dosen'] ?? 'User' }}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <span class="dropdown-header">{{ auth()->user()->email }}</span>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('dashboard') }}" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/></svg>
                                Dashboard
                            </a>
                            <a href="{{ route('notifications.index') }}" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/><path d="M9 17v1a3 3 0 0 0 6 0v-1"/></svg>
                                Notifikasi
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12l-3 -3"/><path d="M18 15l3 -3"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title">{{ $pageTitle ?? $title ?? 'Dashboard' }}</h2>
                            @isset($breadcrumb)
                                <div class="text-secondary">
                                    <ol class="breadcrumb breadcrumb-arrows">
                                        @foreach($breadcrumb as $label => $url)
                                            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                                                @if(!$loop->last)<a href="{{ $url }}">{{ $label }}</a>@else{{ $label }}@endif
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endisset
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-body">
                <div class="container-xl">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">v1.0</li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            &copy; {{ date('Y') }} RPS OBE. Smart Outcome Based Education Platform.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    @livewireScripts
</body>
</html>
