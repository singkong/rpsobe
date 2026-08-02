@php
$title = 'Dashboard Dosen';
$pageTitle = 'Dashboard Dosen';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Dosen' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('dashboard.dosen-dashboard')
</x-layouts.app>
