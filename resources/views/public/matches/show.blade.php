<x-public-layout>
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><p class="cricket-kicker mb-1">{{ $match->tournament->name }}</p><h1 class="h2 fw-bold mb-1">Live match scorecard</h1><p class="text-secondary mb-0">{{ $match->ruleProfile?->name }} · {{ $match->overs_per_innings ?: $match->ruleProfile?->overs_per_innings }} overs per innings · {{ ucfirst(str_replace('_', ' ', $match->status)) }}</p></div><a href="{{ route('public.standings', $match->tournament) }}" class="btn btn-outline-primary">Points table</a></div>
    @foreach($match->innings as $innings)
        <div class="cricket-surface p-4 mb-4" data-innings-id="{{ $innings->id }}"><div class="d-flex flex-wrap justify-content-between align-items-center mb-3"><div><span class="badge text-bg-{{ $innings->status === 'live' ? 'success' : 'secondary' }}">INNINGS {{ $innings->innings_number }} · {{ ucfirst($innings->status) }}</span><h2 class="h3 fw-bold mt-2 mb-0">{{ $innings->battingTeam?->name }}</h2></div><div class="text-end"><div class="display-6 fw-bold" id="innings-score-{{ $innings->id }}">{{ $innings->total_runs }}/{{ $innings->wickets }}</div><div class="text-secondary"><span id="innings-overs-{{ $innings->id }}">{{ $innings->oversDisplay($match->ruleProfile->legal_balls_per_over) }}</span> / {{ $innings->maximum_overs }} overs @if($innings->target_runs) · target {{ $innings->target_runs }} @endif</div></div></div>
            <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Batter</th><th>Dismissal</th><th>R</th><th>B</th><th>4s</th><th>6s</th><th>SR</th></tr></thead><tbody id="innings-batting-{{ $innings->id }}">@foreach($innings->battingStats->sortBy('batting_position') as $stat)<tr><td><strong>{{ $stat->player?->player_name_snapshot }}</strong></td><td class="text-secondary small">{{ $stat->dismissal_type ? str_replace('_', ' ', ucfirst($stat->dismissal_type)) : ($stat->status === 'did_not_bat' ? 'Did not bat' : 'not out') }}</td><td>{{ $stat->runs }}</td><td>{{ $stat->balls }}</td><td>{{ $stat->fours }}</td><td>{{ $stat->sixes }}</td><td>{{ number_format((float) $stat->strike_rate, 2) }}</td></tr>@endforeach</tbody></table></div>
            <h3 class="h6 fw-bold mt-4">Bowling</h3><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Bowler</th><th>O</th><th>R</th><th>W</th><th>Wd</th><th>Nb</th><th>Econ</th></tr></thead><tbody id="innings-bowling-{{ $innings->id }}">@foreach($innings->bowlingStats as $stat)<tr><td>{{ $stat->player?->player_name_snapshot }}</td><td>{{ intdiv($stat->legal_balls, $match->ruleProfile->legal_balls_per_over) }}.{{ $stat->legal_balls % $match->ruleProfile->legal_balls_per_over }}</td><td>{{ $stat->runs_conceded }}</td><td>{{ $stat->wickets }}</td><td>{{ $stat->wides }}</td><td>{{ $stat->no_balls }}</td><td>{{ number_format((float) $stat->economy, 2) }}</td></tr>@endforeach</tbody></table></div>
            <h3 class="h6 fw-bold mt-4">Recent balls</h3><div class="d-flex flex-wrap gap-2" id="innings-recent-{{ $innings->id }}">@foreach($innings->deliveries->whereNull('voided_at')->sortByDesc('sequence_number')->take(12) as $delivery)<span class="badge rounded-pill text-bg-light border">{{ $delivery->over_number }}.{{ $delivery->ball_number }} · {{ $delivery->notation() }}</span>@endforeach</div>
        </div>
    @endforeach
    <div id="match-result-summary">@if($match->status === 'approved' || $match->status === 'result_pending')<div class="alert alert-success"><strong>{{ $match->result_summary }}</strong>@if($match->status === 'result_pending')<div class="small mt-1">Result pending official approval.</div>@endif</div>@endif</div>
</div>
<script>
(() => {
    let revision = {{ (int) $match->revision }};
    const stateUrl = @json(route('public.matches.state', $match));
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>\"']/g, (character) => ({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',"'":'&#039;'}[character]));
    const renderBatting = (inning) => {
        const target = document.getElementById(`innings-batting-${inning.id}`);
        if (!target) return;
        target.innerHTML = (inning.batting || []).map(stat => `<tr><td><strong>${escapeHtml(stat.player)}</strong></td><td class="text-secondary small">${escapeHtml(stat.dismissal)}</td><td>${stat.runs}</td><td>${stat.balls}</td><td>${stat.fours}</td><td>${stat.sixes}</td><td>${stat.strike_rate}</td></tr>`).join('');
    };
    const renderBowling = (inning) => {
        const target = document.getElementById(`innings-bowling-${inning.id}`);
        if (!target) return;
        target.innerHTML = (inning.bowling || []).map(stat => `<tr><td>${escapeHtml(stat.player)}</td><td>${stat.overs}</td><td>${stat.runs}</td><td>${stat.wickets}</td><td>${stat.wides}</td><td>${stat.no_balls}</td><td>${stat.economy}</td></tr>`).join('');
    };
    const poll = async () => {
        try {
            const response = await fetch(stateUrl, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) return;
            const state = await response.json();
            if (state.revision === revision) return;
            revision = state.revision;
            state.innings.forEach((inning) => {
                const score = document.getElementById(`innings-score-${inning.id}`);
                const overs = document.getElementById(`innings-overs-${inning.id}`);
                const recent = document.getElementById(`innings-recent-${inning.id}`);
                if (score) score.textContent = `${inning.runs}/${inning.wickets}`;
                if (overs) overs.textContent = inning.overs;
                if (recent) recent.innerHTML = inning.recent.map(ball => `<span class="badge rounded-pill text-bg-light border">${escapeHtml(ball.over)} · ${escapeHtml(ball.notation)}</span>`).join('') || '<span class="text-secondary">No deliveries yet.</span>';
                renderBatting(inning);
                renderBowling(inning);
            });
            const resultSummary = document.getElementById('match-result-summary');
            if (resultSummary && state.result_summary && ['approved', 'result_pending'].includes(state.status)) {
                resultSummary.innerHTML = `<div class="alert alert-success"><strong>${escapeHtml(state.result_summary)}</strong>${state.status === 'result_pending' ? '<div class="small mt-1">Result pending official approval.</div>' : ''}</div>`;
            }
        } catch (error) { /* The next poll retries transient network failures. */ }
    };
    setInterval(poll, 2000);
})();
</script>
</x-public-layout>
