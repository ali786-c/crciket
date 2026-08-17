<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
            <div>
                <p class="cricket-kicker mb-2">Team Squad Management</p>
                <h1 class="display-6 fw-bold mb-2">{{ $team->name }} Roster</h1>
                <p class="text-secondary mb-0">{{ $tournament->name }} · Manually add, create, or remove players from this team.</p>
            </div>
            <div>
                <a href="{{ route('admin.tournaments.teams.index', $tournament) }}" class="btn btn-light"><i class="fa-solid fa-arrow-left me-2"></i>Back to Teams</a>
            </div>
        </div>
    </x-slot>

    <div class="container pb-5">
        @if (session('status'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <strong>Error:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <!-- Left side: Squad List -->
            <div class="col-lg-7">
                <div class="cricket-surface p-4 p-lg-5">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h2 class="h3 fw-bold mb-1">Current Squad</h2>
                            <p class="text-secondary small mb-0">Total players: {{ $squadPicks->count() }}</p>
                        </div>
                        <span class="badge text-bg-success">Roster List</span>
                    </div>

                    @if ($squadPicks->isEmpty())
                        <div class="p-5 text-center bg-light rounded-4 border">
                            <span class="cricket-brand-mark mb-3"><i class="fa-solid fa-user-xmark text-secondary"></i></span>
                            <h3 class="h5 fw-bold">No players assigned</h3>
                            <p class="text-secondary small mb-0">Use the right panel to assign existing players or create new ones.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-secondary">
                                        <th>Player Name</th>
                                        <th>Role</th>
                                        <th>Location</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($squadPicks as $pick)
                                        @php
                                            $profile = $pick->tournamentPlayer?->playerProfile;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="cricket-brand-mark flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.85rem;"><i class="fa-solid fa-user"></i></span>
                                                    <div>
                                                        <div class="fw-bold">{{ $profile?->full_name }}</div>
                                                        <div class="small text-muted" style="font-size: 0.75rem;">{{ $profile?->user?->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge text-bg-light">{{ $profile?->playing_role ?: 'Not set' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary small">{{ $profile?->city ?: 'Not set' }}</span>
                                            </td>
                                            <td class="text-end">
                                                <form method="POST" action="{{ route('admin.tournaments.teams.players.destroy', [$tournament, $team, $pick]) }}" onsubmit="return confirm('Remove this player from the team squad?')" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Player"><i class="fa-solid fa-user-minus"></i> Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right side: Add Options -->
            <div class="col-lg-5">
                <div class="cricket-surface p-4 p-lg-5 sticky-lg-top" style="top: 6rem;">
                    <h2 class="h4 fw-bold mb-4"><i class="fa-solid fa-user-plus me-2 text-success"></i>Add Players</h2>

                    <!-- Bootstrap Nav Tabs -->
                    <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3" id="addPlayerTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="btn btn-sm btn-outline-success active" id="registered-tab" data-bs-toggle="pill" data-bs-target="#registered" type="button" role="tab" aria-controls="registered" aria-selected="true"><i class="fa-solid fa-list me-1"></i>Approved Pool</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="btn btn-sm btn-outline-success" id="newplayer-tab" data-bs-toggle="pill" data-bs-target="#newplayer" type="button" role="tab" aria-controls="newplayer" aria-selected="false"><i class="fa-solid fa-plus me-1"></i>Create New</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="addPlayerTabContent">
                        <!-- Registered Players Tab -->
                        <div class="tab-pane fade show active" id="registered" role="tabpanel" aria-labelledby="registered-tab">
                            <p class="text-secondary small mb-3">Assign an approved tournament player who is not yet in any team squad.</p>
                            
                            @if ($availablePlayers->isEmpty())
                                <div class="p-4 text-center bg-light rounded-3 border">
                                    <span class="text-secondary small">No available approved players remaining. All players are already assigned or registered list is empty.</span>
                                </div>
                            @else
                                <form method="POST" action="{{ route('admin.tournaments.teams.players.store', [$tournament, $team]) }}" class="vstack gap-3">
                                    @csrf
                                    <input type="hidden" name="action" value="select_registered">
                                    
                                    <div>
                                        <label for="tournament_player_id" class="form-label small fw-bold text-secondary">Select Player</label>
                                        <select name="tournament_player_id" id="tournament_player_id" class="form-select" required>
                                            <option value="">-- Choose Player --</option>
                                            @foreach($availablePlayers as $ap)
                                                <option value="{{ $ap->id }}">{{ $ap->playerProfile?->full_name }} ({{ $ap->playerProfile?->playing_role }}) - {{ $ap->playerProfile?->city }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button class="btn btn-success mt-2" type="submit"><i class="fa-solid fa-user-plus me-2"></i>Add to Squad</button>
                                </form>
                            @endif
                        </div>

                        <!-- Create New Player Tab -->
                        <div class="tab-pane fade" id="newplayer" role="tabpanel" aria-labelledby="newplayer-tab">
                            <p class="text-secondary small mb-3">Create a new player profile and assign them directly to this squad in one click.</p>
                            
                            <form method="POST" action="{{ route('admin.tournaments.teams.players.store', [$tournament, $team]) }}" class="vstack gap-3">
                                @csrf
                                <input type="hidden" name="action" value="create_new">

                                <div>
                                    <label for="full_name" class="form-label small fw-bold text-secondary">Full Name</label>
                                    <input type="text" name="full_name" id="full_name" class="form-control" placeholder="e.g. Roman Tarar" required>
                                </div>

                                <div>
                                    <label for="phone" class="form-label small fw-bold text-secondary">Phone Number</label>
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="e.g. +923001234567" required>
                                </div>

                                <div>
                                    <label for="email" class="form-label small fw-bold text-secondary">Email Address <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="e.g. player@domain.com">
                                </div>

                                <div>
                                    <label for="location" class="form-label small fw-bold text-secondary">City/Location</label>
                                    <input type="text" name="location" id="location" class="form-control" placeholder="e.g. ajnala" required>
                                </div>

                                <div>
                                    <label for="playing_role" class="form-label small fw-bold text-secondary">Playing Role</label>
                                    <select name="playing_role" id="playing_role" class="form-select" required>
                                        <option value="">Select role</option>
                                        <option value="Batter">Batter</option>
                                        <option value="Bowler">Bowler</option>
                                        <option value="All-rounder">All-rounder</option>
                                        <option value="Wicketkeeper">Wicketkeeper</option>
                                    </select>
                                </div>

                                <button class="btn btn-success mt-2" type="submit"><i class="fa-solid fa-user-plus me-2"></i>Create & Add to Squad</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
