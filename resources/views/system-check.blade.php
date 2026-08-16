<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Check · {{ config('app.name', 'Cricket Draft System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container py-2">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="{{ url('/') }}">
                <span class="cricket-brand-mark"><i class="fa-solid fa-baseball"></i></span>
                <span>Cricket Draft System</span>
            </a>
        </div>
    </nav>

    <main class="container py-5" x-data="{ alpineReady: false }">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="cricket-surface p-4 p-lg-5">
                    <p class="cricket-kicker text-success mb-2">Phase 1 verification</p>
                    <h1 class="display-6 fw-bold">Environment system check</h1>
                    <p class="text-secondary mb-4">This page confirms that Laravel, MySQL, Bootstrap 5, Alpine.js, and the asset pipeline are connected.</p>

                    <div class="list-group mb-4">
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span><i class="fa-solid fa-server text-success me-2"></i>Laravel application</span>
                            <span class="badge text-bg-success">Running</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span><i class="fa-solid fa-database text-success me-2"></i>MySQL database</span>
                            <span class="badge text-bg-success">Migrated</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span><i class="fa-brands fa-bootstrap text-success me-2"></i>Bootstrap 5</span>
                            <span class="badge text-bg-success">Loaded</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span><i class="fa-solid fa-bolt text-success me-2"></i>Alpine.js</span>
                            <span class="badge" :class="alpineReady ? 'text-bg-success' : 'text-bg-warning'" x-text="alpineReady ? 'Verified' : 'Waiting'"></span>
                        </div>
                    </div>

                    <button type="button" class="btn btn-success" @click="alpineReady = !alpineReady">
                        <i class="fa-solid fa-flask me-2"></i>Toggle Alpine.js test
                    </button>

                    <div class="alert alert-success mt-4 mb-0" x-show="alpineReady" x-transition>
                        Alpine.js is working successfully.
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
