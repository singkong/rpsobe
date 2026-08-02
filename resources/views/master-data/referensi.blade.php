@php
$title = 'Referensi';
$pageTitle = 'Referensi';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Master Data' => '#', 'Referensi' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('master-data.referensi-index')
</x-layouts.app>
