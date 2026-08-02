@php
$title = 'Kurikulum';
$pageTitle = 'Kurikulum';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Master Data' => '#', 'Kurikulum' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('master-data.kurikulum-index')
</x-layouts.app>
