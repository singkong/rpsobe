@php
$title = 'Dashboard Admin';
$pageTitle = 'Dashboard Admin';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Admin' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('dashboard.admin-dashboard')
</x-layouts.app>
