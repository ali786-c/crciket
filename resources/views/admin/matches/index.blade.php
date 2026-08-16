@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="cricket-kicker mb-1">{{ $tournament->name }}</p>
            <h1 class="h2 fw-bold mb-1">Match center</h1>
            <p class="text-secondary mb-0">Operational matches created from the tournament draft squads.</p>
        </div>
        <a href="{{ route('admin.tournaments.matches.create', $tournament) }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Create match</a>
    </div>

    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

    <div class="cricket-surface overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Match</th><th>Scheduled</th><th>Rules</th><th>Overs</th><th>Status</th><th>Toss</th><th></th></tr></thead>
                <tbody>
                @forelse($matches as $match)
                    @php($teams = $match->players->pluck('team')->unique('id'))
                    <tr>
                        <td><strong>{{ $match->fixture?->title ?: ($teams->pluck('short_name')->filter()->join(' vs ') ?: 'Match #'.$match->id) }}</strong><div class="small text-secondary">{{ $match->fixture?->round_name ?: 'Draft-squad match' }}</div></td>
                        <td>@if($match->fixture)<div class="fw-semibold">{{ $match->fixture->scheduled_at->setTimezone($match->fixture->timezone)->format('d M Y, H:i') }}</div><div class="small text-secondary">{{ $match->fixture->timezone }}</div>@else<span class="text-secondary">Not scheduled</span>@endif</td>
                        <td>{{ $match->ruleProfile?->name ?? '—' }}</td>
                        <td><strong>{{ $match->overs_per_innings ?: ($match->ruleProfile?->overs_per_innings ?: '—') }}</strong> <span class="small text-secondary">per innings</span></td>
                        <td><span class="badge text-bg-{{ $match->status === 'live' ? 'success' : 'secondary' }}">{{ str_replace('_', ' ', ucfirst($match->status)) }}</span></td>
                        <td>{{ $match->tossWinner?->short_name ? $match->tossWinner->short_name.' · '.ucfirst($match->toss_decision) : 'Pending' }}</td>
                        <td class="text-end"><div class="d-flex justify-content-end gap-2">@if(in_array($match->status, ['live', 'completed', 'result_pending', 'approved'], true))<a class="btn btn-sm btn-outline-success" href="{{ route('public.matches.show', $match) }}" target="_blank">Public</a>@endif<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.tournaments.matches.show', [$tournament, $match]) }}">Open</a></div></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5 text-secondary">No matches have been created yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $matches->links() }}</div>
</div>
@endsection
