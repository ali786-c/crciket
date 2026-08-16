<x-guest-layout>
    <div class="d-flex align-items-start justify-content-between gap-3 mb-4"><div><p class="cricket-kicker mb-2">Secure access</p><h1 class="h2 fw-bold mb-2">Welcome back.</h1><p class="text-secondary mb-0">Sign in to continue to your draft workspace.</p></div><span class="cricket-brand-mark"><i class="fa-solid fa-arrow-right-to-bracket"></i></span></div>
    <x-auth-session-status class="mb-3" :status="session('status')" />
    <form method="POST" action="{{ route('login') }}" class="vstack gap-3">@csrf
        <div><label for="email" class="form-label">Email address</label><input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div><div class="d-flex justify-content-between align-items-center"><label for="password" class="form-label">Password</label>@if (Route::has('password.request'))<a class="small" href="{{ route('password.request') }}">Forgot password?</a>@endif</div><input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="form-check mt-1"><input id="remember_me" class="form-check-input" type="checkbox" name="remember"><label class="form-check-label small" for="remember_me">Keep me signed in on this device</label></div>
        <button class="btn btn-success btn-lg w-100 mt-2" type="submit">Continue to workspace <i class="fa-solid fa-arrow-right ms-2"></i></button>
    </form>
    @if (Route::has('register'))<div class="cricket-surface-soft p-3 mt-4 text-center small"><span class="text-secondary">New to the platform?</span> <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Create a player account</a></div>@endif
</x-guest-layout>
