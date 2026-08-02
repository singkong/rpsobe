<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - RPS OBE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-book">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/>
                    <path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/>
                    <path d="M3 6l0 13"/>
                    <path d="M12 6l0 13"/>
                    <path d="M21 6l0 13"/>
                </svg>
                <h1 class="mt-2">RPS OBE</h1>
                <p class="text-secondary">Rencana Pembelajaran Semester Outcome-Based Education</p>
            </div>

            <livewire:auth.login />
        </div>
    </div>
</body>
</html>
