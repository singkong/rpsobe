@php
$title = 'Semester';
$pageTitle = 'Semester';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Master Data' => '#', 'Semester' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('master-data.semester-index')
</x-layouts.app>
