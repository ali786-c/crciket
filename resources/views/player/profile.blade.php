<x-app-layout>
    <x-slot name="header"><div><p class="cricket-kicker text-success mb-1">Player account</p><h2 class="h3 fw-bold mb-0">Player profile</h2></div></x-slot>
    <div class="container py-4"><div class="row justify-content-center"><div class="col-xl-8"><div class="cricket-surface p-4 p-lg-5">
        @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
        <form method="POST" action="{{ route('player.profile.update') }}">@csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label" for="full_name">Full name</label><input class="form-control" id="full_name" name="full_name" value="{{ old('full_name', $profile?->full_name) }}" required></div>
                <div class="col-md-4"><label class="form-label" for="phone">Phone</label><input class="form-control" id="phone" name="phone" value="{{ old('phone', $profile?->phone) }}"></div>
                <div class="col-md-6"><label class="form-label" for="city">City</label><input class="form-control" id="city" name="city" value="{{ old('city', $profile?->city) }}"></div>
                <div class="col-md-6"><label class="form-label" for="playing_role">Playing role</label><select class="form-select" id="playing_role" name="playing_role" required><option value="">Select playing role</option>@foreach (['Batter', 'Bowler', 'All-rounder', 'Wicketkeeper'] as $role)<option value="{{ $role }}" @selected(old('playing_role', $profile?->playing_role) === $role)>{{ $role }}</option>@endforeach</select><div class="form-text">This role appears beside your name for captains during the live draft.</div></div>
                <div class="col-md-6"><label class="form-label" for="batting_style">Batting style</label><input class="form-control" id="batting_style" name="batting_style" value="{{ old('batting_style', $profile?->batting_style) }}"></div>
                <div class="col-md-6"><label class="form-label" for="bowling_style">Bowling style</label><input class="form-control" id="bowling_style" name="bowling_style" value="{{ old('bowling_style', $profile?->bowling_style) }}"></div>
                <div class="col-12"><label class="form-label" for="bio">Bio</label><textarea class="form-control" id="bio" name="bio" rows="4">{{ old('bio', $profile?->bio) }}</textarea></div>
            </div>
            <div class="d-flex justify-content-end mt-4"><button class="btn btn-success" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Save profile</button></div>
        </form>
    </div></div></div></div>
</x-app-layout>
