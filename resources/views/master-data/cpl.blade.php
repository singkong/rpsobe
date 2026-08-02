@php
$title = 'CPL';
$pageTitle = 'CPL';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Master Data' => '#', 'CPL' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('master-data.cpl-index')
</x-layouts.app>
