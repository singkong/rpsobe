@php
$title = 'Dashboard Fakultas';
$pageTitle = 'Dashboard Fakultas';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Fakultas' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('dashboard.fakultas-dashboard')
</x-layouts.app>
