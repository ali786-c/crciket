@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4"><a href="{{ route('admin.tournaments.matches.index', $tournament) }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left me-2"></i>Back to matches</a></div>
    <div class="row justify-content-center"><div class="col-xl-8">
        <div class="cricket-surface p-4 p-lg-5">
            <p class="cricket-kicker mb-1">{{ $tournament->name }}</p>
            <h1 class="h2 fw-bold mb-2">Create match from draft squads</h1>
            <p class="text-secondary mb-4">The selected teams’ drafted, approved players will be copied into an immutable match squad snapshot. The tournament draft must be complete.</p>
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <form method="POST" action="{{ route('admin.tournaments.matches.store', $tournament) }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="home_team_id" class="form-label">Team 1</label>
                        <select name="home_team_id" id="home_team_id" class="form-select" required>
                            <option value="">Choose team</option>
                            @foreach($teams as $team)<option value="{{ $team->id }}" @selected(old('home_team_id') == $team->id)>{{ $team->name }} ({{ $team->short_name }})</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="away_team_id" class="form-label">Team 2</label>
                        <select name="away_team_id" id="away_team_id" class="form-select" required>
                            <option value="">Choose team</option>
                            @foreach($teams as $team)<option value="{{ $team->id }}" @selected(old('away_team_id') == $team->id)>{{ $team->name }} ({{ $team->short_name }})</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="overs_per_innings" class="form-label">Total overs per innings</label>
                    <div class="input-group">
                        <input id="overs_per_innings" name="overs_per_innings" type="number" min="1" max="100" class="form-control @error('overs_per_innings') is-invalid @enderror" value="{{ old('overs_per_innings', $tournament->default_overs_per_innings ?: $tournament->cricketRuleProfile?->overs_per_innings) }}" required>
                        <span class="input-group-text">overs</span>
                    </div>
                    @error('overs_per_innings')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="small text-secondary mt-2">Tournament default: {{ $tournament->default_overs_per_innings ?: ($tournament->cricketRuleProfile?->overs_per_innings ?: 'not configured') }} overs. Change this value to create a custom match format.</div>
                </div>
                <div class="cricket-surface-soft p-3 my-4"><strong><i class="fa-solid fa-shield-halved text-success me-2"></i>Historical snapshot</strong><div class="small text-secondary mt-1">Player names, roles, team ownership, and draft pick references are frozen at match creation.</div></div>
                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-flag-checkered me-2"></i>Create match</button>
            </form>
        </div>
    </div></div>
</div>
@endsection
