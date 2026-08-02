@php
$title = 'Mata Kuliah';
$pageTitle = 'Mata Kuliah';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Master Data' => '#', 'Mata Kuliah' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('master-data.mata-kuliah-index')
</x-layouts.app>
