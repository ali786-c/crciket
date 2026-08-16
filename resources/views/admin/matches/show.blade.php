@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><p class="cricket-kicker mb-1">{{ $tournament->name }}</p><h1 class="h2 fw-bold mb-1">Match #{{ $match->id }} · {{ ucfirst(str_replace('_', ' ', $match->status)) }}</h1><p class="text-secondary mb-0">{{ $match->fixture?->title ?: 'Draft-squad match' }} · {{ $match->ruleProfile?->name }} · {{ $match->overs_per_innings ?: $match->ruleProfile?->overs_per_innings }} overs per innings · XI {{ $match->ruleProfile?->playing_xi_size }}</p></div>
        <div class="d-flex gap-2">@if(in_array($match->status, ['live', 'completed', 'result_pending', 'approved'], true))<a href="{{ route('public.matches.show', $match) }}" class="btn btn-outline-success" target="_blank"><i class="fa-solid fa-eye me-1"></i>Public scorecard</a>@endif @if($match->status === 'live')<a href="{{ route('admin.matches.scorer', $match) }}" class="btn btn-success"><i class="fa-solid fa-stopwatch me-1"></i>Open scorer</a>@endif @if($match->status === 'completed')<form method="POST" action="{{ route('admin.matches.result.submit', $match) }}">@csrf<button class="btn btn-primary" type="submit">Submit result</button></form>@elseif($match->status === 'result_pending')<form method="POST" action="{{ route('admin.matches.result.approve', $match) }}">@csrf<button class="btn btn-primary" type="submit">Approve result</button></form>@endif<a href="{{ route('admin.tournaments.matches.index', $tournament) }}" class="btn btn-outline-primary">All matches</a></div>
    </div>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="cricket-surface-soft p-3 mb-4"><div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3"><div><strong><i class="fa-solid fa-stopwatch text-success me-2"></i>Match format</strong><div class="small text-secondary mt-1">This match uses {{ $match->overs_per_innings ?: $match->ruleProfile?->overs_per_innings }} overs per innings. Scoring enforces this cap for every innings.</div></div>@if(in_array($match->status, ['scheduled', 'squad_selection', 'lineup_pending', 'toss_pending'], true))<form method="POST" action="{{ route('admin.tournaments.matches.overs', [$tournament, $match]) }}" class="d-flex gap-2 align-items-end">@csrf<div><label for="match_overs_per_innings" class="form-label small mb-1">Change overs</label><input id="match_overs_per_innings" name="overs_per_innings" type="number" min="1" max="100" value="{{ $match->overs_per_innings ?: $match->ruleProfile?->overs_per_innings }}" class="form-control" style="max-width:130px" required></div><button class="btn btn-outline-success" type="submit">Update</button></form>@else<span class="badge text-bg-secondary">Locked after match start</span>@endif</div></div>

    <div class="row g-4">
        @foreach($teams as $team)
            @php($squad = $match->players->where('team_id', $team->id))
            <div class="col-lg-6"><div class="cricket-surface p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3"><div><p class="cricket-kicker mb-1">Team</p><h2 class="h4 fw-bold mb-0">{{ $team->name }}</h2></div><span class="badge text-bg-light">{{ $squad->where('selection_type', 'playing_xi')->count() }}/{{ $match->ruleProfile?->playing_xi_size }} XI</span></div>
                <form method="POST" action="{{ route('admin.tournaments.matches.playing-xi', [$tournament, $match, $team->id]) }}">
                    @csrf
                    <div class="list-group mb-3">
                        @foreach($squad as $player)
                            <label class="list-group-item d-flex align-items-center gap-3"><input class="form-check-input" type="checkbox" name="player_ids[]" value="{{ $player->id }}" @checked($player->selection_type === 'playing_xi') @disabled(in_array($match->status, ['toss_pending', 'live', 'completed', 'result_pending', 'approved'], true))><span class="flex-grow-1"><strong>{{ $player->player_name_snapshot }}</strong><small class="d-block text-secondary">{{ $player->player_role_snapshot ?: 'Role not set' }} · Draft pick #{{ $player->draftPick?->pick_number ?? '—' }}</small></span>@if($player->is_captain)<span class="badge text-bg-warning">Captain</span>@endif</label>
                        @endforeach
                    </div>
                    @if(in_array($match->status, ['squad_selection', 'lineup_pending'], true))<button class="btn btn-sm btn-outline-primary" type="submit">Submit playing XI</button>@endif
                </form>
            </div></div>
        @endforeach
    </div>

    @if($match->status === 'lineup_pending')
        <div class="cricket-surface p-4 mt-4"><h2 class="h5 fw-bold">Approve lineups</h2><p class="text-secondary">Once approved, no XI changes are allowed. The next step is the toss.</p><form method="POST" action="{{ route('admin.tournaments.matches.approve-lineup', [$tournament, $match]) }}">@csrf<button class="btn btn-primary" type="submit">Approve both XIs</button></form></div>
    @endif

    @if($match->status === 'toss_pending')
        <div class="cricket-surface p-4 mt-4"><h2 class="h5 fw-bold">Record toss</h2><p class="text-secondary">Recording the toss moves the match to live state.</p><form method="POST" action="{{ route('admin.tournaments.matches.toss', [$tournament, $match]) }}"><div class="row g-3 align-items-end"><div class="col-md-5"><label class="form-label">Toss winner</label><select name="toss_winner_team_id" class="form-select" required>@foreach($teams as $team)<option value="{{ $team->id }}">{{ $team->name }}</option>@endforeach</select></div><div class="col-md-4"><label class="form-label">Decision</label><select name="toss_decision" class="form-select" required><option value="bat">Bat first</option><option value="field">Field first</option></select></div><div class="col-md-3"><button class="btn btn-primary w-100" type="submit">Record toss</button></div></div>@csrf</form></div>
    @endif

    @if($match->status === 'live')<div class="cricket-surface p-4 mt-4 border-success"><h2 class="h5 fw-bold text-success"><i class="fa-solid fa-circle me-2"></i>Match is live</h2><p class="text-secondary mb-3">Record every delivery in the scorer room. The public scorecard polls the server automatically and updates runs, wickets, overs, batting, bowling and recent balls without a page refresh.</p><div class="d-flex flex-wrap gap-2"><a href="{{ route('admin.matches.scorer', $match) }}" class="btn btn-success">Open scorer room</a><a href="{{ route('public.matches.show', $match) }}" class="btn btn-outline-success" target="_blank">Open public scorecard</a></div></div>@endif
</div>
@endsection
