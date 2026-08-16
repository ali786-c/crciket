<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
            <div><p class="cricket-kicker mb-2">Admin reporting</p><h1 class="display-6 fw-bold mb-2">{{ $report['tournament']->name }} reports</h1><p class="text-secondary mb-0">Complete operational reporting for draft history, squads, registrations, timers, and audit activity.</p></div>
            <a href="{{ route('admin.tournaments.show', $report['tournament']) }}" class="btn btn-light"><i class="fa-solid fa-arrow-left me-2"></i>Tournament workspace</a>
        </div>
    </x-slot>
    <div class="container pb-5">
        <div class="row g-3 mb-4">
            @foreach ([['teams','Teams'],['registered_players','Registered players'],['approved_players','Approved players'],['selected_players','Selected players'],['total_picks','Total picks'],['completed_picks','Completed picks']] as [$key,$label])
                <div class="col-6 col-lg-2"><div class="cricket-surface p-3 h-100"><div class="small text-secondary">{{ $label }}</div><div class="cricket-stat-value mt-2">{{ $report['summary'][$key] }}</div></div></div>
            @endforeach
        </div>
        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="cricket-surface p-4 p-lg-5 h-100">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                        <div>
                            <p class="cricket-kicker mb-2">Download center</p>
                            <h2 class="h3 fw-bold mb-1">Operational reports</h2>
                            <p class="text-secondary mb-0">Each PDF contains the complete admin-level data for its category.</p>
                        </div>
                        <i class="fa-solid fa-file-pdf text-danger fs-2"></i>
                    </div>
                    <div class="row g-3">
                        @foreach($reportTypes as $type => $label)
                            <div class="col-md-6">
                                <div class="cricket-surface-soft p-3 d-flex align-items-center justify-content-between gap-3 h-100">
                                    <div>
                                        <div class="fw-bold">{{ $label }}</div>
                                        <div class="small text-secondary">PDF download</div>
                                    </div>
                                    <a href="{{ route('admin.tournaments.reports.pdf', [$report['tournament'], $type]) }}" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-download me-1"></i>PDF</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="cricket-surface p-4 h-100">
                    <h2 class="h4 fw-bold mb-3">Timer health</h2>
                    <div class="vstack gap-3">
                        @foreach($report['timer'] as $key => $value)
                            <div class="d-flex justify-content-between border-bottom pb-2">
                                <span class="text-secondary">{{ ucwords(str_replace('_',' ',$key)) }}</span>
                                <strong>{{ $value }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="cricket-surface p-4 p-lg-5">
                    <h2 class="h3 fw-bold mb-4">Team squads</h2>
                    <div class="row g-3">
                        @foreach($report['team_squads'] as $squad)
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="cricket-surface-soft p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <strong class="text-truncate" style="max-width: 70%">{{ $squad['team'] }}</strong>
                                        <span class="badge text-bg-success">{{ $squad['selected_count'] }}</span>
                                    </div>
                                    <div class="vstack gap-2">
                                        @forelse($squad['players'] as $player)
                                            <div class="bg-white rounded-3 p-2">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <span class="badge text-bg-light" style="font-size: 0.75rem;">#{{ $player['pick_number'] }}</span>
                                                    @if($player['is_captain'] ?? false)
                                                        <span class="badge text-bg-warning" style="font-size: 0.65rem; padding: 0.2em 0.5em;"><i class="fa-solid fa-copyright me-1"></i>C</span>
                                                    @endif
                                                </div>
                                                <div class="fw-bold small text-truncate" title="{{ $player['player'] }}">{{ $player['player'] }}</div>
                                                <div class="small text-secondary d-flex align-items-center mt-1" style="font-size: 0.75rem;">
                                                    @if(($player['playing_role'] ?? '') === 'Batter')
                                                        <i class="fa-solid fa-baseball-bat-ball text-success me-1" title="Batter"></i>
                                                    @elseif(($player['playing_role'] ?? '') === 'Bowler')
                                                        <i class="fa-solid fa-baseball text-danger me-1" title="Bowler"></i>
                                                    @elseif(($player['playing_role'] ?? '') === 'All-rounder')
                                                        <i class="fa-solid fa-bolt text-warning me-1" title="All-rounder"></i>
                                                    @elseif(($player['playing_role'] ?? '') === 'Wicketkeeper')
                                                        <i class="fa-solid fa-hand-back-fist text-info me-1" title="Wicketkeeper"></i>
                                                    @else
                                                        <i class="fa-solid fa-user text-secondary me-1" title="Player"></i>
                                                    @endif
                                                    {{ $player['playing_role'] ?: 'Unassigned' }}
                                                </div>
                                            </div>
                                        @empty
                                            <div class="small text-secondary text-center py-3">No players selected yet.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
