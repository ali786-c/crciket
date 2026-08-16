<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Cricket Draft System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="min-vh-100 d-flex align-items-stretch cricket-hero">
        <div class="container-fluid p-0"><div class="row g-0 min-vh-100">
            <div class="col-lg-6 d-none d-lg-flex align-items-center p-5"><div class="text-white p-xl-5" style="max-width: 38rem;"><a href="{{ url('/') }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-2 fw-bold fs-4 mb-5"><span class="cricket-brand-mark" style="background:var(--cricket-lime); color:var(--cricket-pitch-deep);"><i class="fa-solid fa-baseball"></i></span><span>Cricket Draft <span style="color:var(--cricket-lime);">OS</span></span></a><p class="cricket-kicker mb-3">Your draft day workspace</p><h1 class="display-4 fw-bold mb-4">The room is ready when you are.</h1><p class="lead opacity-75 mb-5">A focused place for admins, captains, and players to make every pick count.</p><div class="d-flex flex-wrap gap-3 small opacity-75"><span><i class="fa-solid fa-shield-halved me-2" style="color:var(--cricket-lime);"></i>Secure access</span><span><i class="fa-solid fa-signal me-2" style="color:var(--cricket-lime);"></i>Live updates</span></div></div></div>
            <div class="col-lg-6 d-flex align-items-center justify-content-center bg-light p-4 p-md-5"><div class="w-100" style="max-width: 31rem;"><div class="d-lg-none text-center mb-4"><a href="{{ url('/') }}" class="text-dark text-decoration-none d-inline-flex align-items-center gap-2 fw-bold fs-4"><span class="cricket-brand-mark"><i class="fa-solid fa-baseball"></i></span><span>Cricket Draft <span class="text-success">OS</span></span></a></div><div class="cricket-surface p-4 p-md-5">{{ $slot }}</div><div class="text-center small text-secondary mt-4">{{ config('app.name', 'Cricket Draft System') }} · Draft day, under control.</div></div></div>
        </div></div>
    </main>
</body>
</html>
