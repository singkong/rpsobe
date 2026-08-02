@php
$title = 'Fakultas';
$pageTitle = 'Fakultas';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Master Data' => '#', 'Fakultas' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('master-data.fakultas-index')
</x-layouts.app>
