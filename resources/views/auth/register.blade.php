<x-guest-layout>
    <div class="text-center mb-4">
        <p class="cricket-kicker text-success mb-1">Player onboarding</p>
        <h1 class="h3 fw-bold mb-2">Create your account</h1>
        <p class="text-secondary mb-0">Join the tournament registration pool and complete your player profile.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="vstack gap-3">
        @csrf
        <div>
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required autocomplete="new-password">
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-2">
            <a href="{{ route('login') }}" class="small text-success">Already registered?</a>
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-user-plus me-2"></i>Register as player</button>
        </div>
    </form>
</x-guest-layout>
