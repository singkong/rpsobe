@php
$title = 'Daftar Approval';
$pageTitle = 'Daftar Approval';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Approval' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('rps.workflow.approval-list')
</x-layouts.app>
