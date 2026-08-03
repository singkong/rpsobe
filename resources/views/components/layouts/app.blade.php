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
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/>
                        </svg>
                        <span class="ms-2">RPS OBE</span>
                    </a>
                </h1>
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('dashboard.*') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/></svg></span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        @if(auth()->user()->can('manage-master-data'))
                        <!-- Master Data -->
                        <li class="nav-item dropdown {{ request()->routeIs('master-data.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#sidebar-masterdata" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 9l5 -5l5 5"/><path d="M12 4l0 12"/></svg></span>
                                <span class="nav-link-title">Master Data</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item {{ request()->routeIs('master-data.fakultas') ? 'active' : '' }}" href="{{ route('master-data.fakultas') }}">Fakultas</a>
                                <a class="dropdown-item {{ request()->routeIs('master-data.program-studi') ? 'active' : '' }}" href="{{ route('master-data.program-studi') }}">Program Studi</a>
                                <a class="dropdown-item {{ request()->routeIs('master-data.kurikulum') ? 'active' : '' }}" href="{{ route('master-data.kurikulum') }}">Kurikulum</a>
                                <a class="dropdown-item {{ request()->routeIs('master-data.mata-kuliah') ? 'active' : '' }}" href="{{ route('master-data.mata-kuliah') }}">Mata Kuliah</a>
                                <a class="dropdown-item {{ request()->routeIs('master-data.dosen') ? 'active' : '' }}" href="{{ route('master-data.dosen') }}">Dosen</a>
                                <a class="dropdown-item {{ request()->routeIs('master-data.cpl') ? 'active' : '' }}" href="{{ route('master-data.cpl') }}">CPL</a>
                                <a class="dropdown-item {{ request()->routeIs('master-data.profil-lulusan') ? 'active' : '' }}" href="{{ route('master-data.profil-lulusan') }}">Profil Lulusan</a>
                                <a class="dropdown-item {{ request()->routeIs('master-data.semester') ? 'active' : '' }}" href="{{ route('master-data.semester') }}">Semester</a>
                                <a class="dropdown-item {{ request()->routeIs('master-data.referensi') ? 'active' : '' }}" href="{{ route('master-data.referensi') }}">Referensi</a>
                            </div>
                        </li>
                        @endif

                        @if(auth()->user()->can('manage-rps'))
                        <!-- RPS -->
                        <li class="nav-item dropdown {{ request()->routeIs('rps.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#sidebar-rps" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/></svg></span>
                                <span class="nav-link-title">RPS</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item {{ request()->routeIs('rps.index') ? 'active' : '' }}" href="{{ route('rps.index') }}">Daftar RPS</a>
                                <a class="dropdown-item {{ request()->routeIs('rps.create') ? 'active' : '' }}" href="{{ route('rps.create') }}">Buat RPS Baru</a>
                            </div>
                        </li>
                        @endif

                        @if(auth()->user()->can('review-rps'))
                        <!-- Review -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('review.*') ? 'active' : '' }}" href="{{ route('review.list') }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg></span>
                                <span class="nav-link-title">Review</span>
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('approve-rps'))
                        <!-- Approval -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('approval.*') ? 'active' : '' }}" href="{{ route('approval.list') }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12l2 2l4 -4"/></svg></span>
                                <span class="nav-link-title">Approval</span>
                            </a>
                        </li>
                        @endif

                        <!-- Reports -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12h4l3 8l4 -16l3 8h4"/></svg></span>
                                <span class="nav-link-title">Laporan</span>
                            </a>
                        </li>

                        @if(auth()->user()->can('manage-master-data'))
                        <!-- Audit -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}">
                                <span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06 .06a2 2 0 0 1 0 2.83a2 2 0 0 1 -2.83 0l-.06 -.06a1.65 1.65 0 0 0 -1.82 -.33a1.65 1.65 0 0 0 -1 1.51v.17a2 2 0 0 1 -2 2a2 2 0 0 1 -2 -2v-.09a1.65 1.65 0 0 0 -1.08 -1.51a1.65 1.65 0 0 0 -1.82 .33l-.06 .06a2 2 0 0 1 -2.83 0a2 2 0 0 1 0 -2.83l.06 -.06a1.65 1.65 0 0 0 .33 -1.82a1.65 1.65 0 0 0 -1.51 -1h-.17a2 2 0 0 1 -2 -2a2 2 0 0 1 2 -2h.09a1.65 1.65 0 0 0 1.51 -1.08"/></svg></span>
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
                                <div class="mt-1 small text-secondary">{{ auth()->user()->roles->first()->name ?? 'User' }}</div>
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
