@php
$title = 'Profil Lulusan';
$pageTitle = 'Profil Lulusan';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Master Data' => '#', 'Profil Lulusan' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    @livewire('master-data.profil-lulusan-index')
</x-layouts.app>
