@php
$title = 'Template RPS';
$pageTitle = 'Template RPS';
$breadcrumb = ['Dashboard' => route('dashboard'), 'Template RPS' => '#'];
@endphp
<x-layouts.app :title="$title" :pageTitle="$pageTitle" :breadcrumb="$breadcrumb">
    <div>
        <div class="card">
            <div class="card-body text-center py-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="text-secondary mb-3">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h14a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-14a1 1 0 0 1 -1 -1z"/><path d="M4 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 12l6 0"/><path d="M14 16l6 0"/><path d="M14 20l6 0"/>
                </svg>
                <h3>Template RPS</h3>
                <p class="text-secondary">Fitur template RPS akan tersedia di versi selanjutnya. Template memungkinkan universitas menyesuaikan format RPS sesuai kebutuhan.</p>
            </div>
        </div>
    </div>
</x-layouts.app>
