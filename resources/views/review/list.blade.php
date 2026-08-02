@php
$title = 'Daftar Review';
$pageTitle = 'Daftar Review';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Review' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('rps.workflow.review-list')
</x-layouts.app>
