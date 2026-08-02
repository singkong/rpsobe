@php
$title = 'Dosen';
$pageTitle = 'Dosen';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Master Data' => '#', 'Dosen' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('master-data.dosen-index')
</x-layouts.app>
