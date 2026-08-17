<x-app-layout>
    <!-- Load Canvas Confetti Library from jsDelivr CDN -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <style>
        /* Premium Stamp celebration overlay */
        .celebration-overlay {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            width: 380px;
            max-width: 90vw;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            border-radius: 24px;
            border: none;
            background: linear-gradient(135deg, #ffffff 0%, #f9fbf9 100%);
            text-align: center;
            padding: 32px 24px;
        }
        
        .drafted-stamp {
            display: inline-block;
            border: 4px solid #d9383a !important;
            color: #d9383a;
            font-weight: 800;
            padding: 10px 20px;
            border-radius: 12px;
            text-transform: uppercase;
            font-family: 'Courier New', Courier, monospace;
            transform: rotate(-8deg);
            margin: 20px 0;
            font-size: 1.3rem;
            letter-spacing: 2px;
            line-height: 1.2;
            box-shadow: 0 0 10px rgba(217, 56, 58, 0.15);
            background-color: rgba(217, 56, 58, 0.05);
            text-shadow: 0 0 1px rgba(217, 56, 58, 0.2);
        }
    </style>

    <x-slot name="header">
        <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="cricket-live-dot"></span>
                    <span class="small text-uppercase fw-bold text-danger" style="letter-spacing:.12em;">Admin control room</span>
                </div>
                <h1 class="display-6 fw-bold mb-2 text-dark">{{ $tournament->name }}</h1>
                <p class="text-secondary mb-0">Server-authoritative draft operations.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.tournaments.reports.index', $tournament) }}" class="btn btn-outline-success"><i class="fa-solid fa-file-lines me-2"></i>Reports</a>
                <a href="{{ route('admin.tournaments.show', $tournament) }}" class="btn btn-light"><i class="fa-solid fa-arrow-left me-2"></i>Tournament</a>
                <a href="{{ route('admin.tournaments.draft.history.export', $tournament) }}" class="btn btn-success"><i class="fa-solid fa-file-csv me-2"></i>Export history</a>
            </div>
        </div>
    </x-slot>

    <div class="container pb-5" x-data="draftControl(@js($state), { state: @js(route('admin.tournaments.draft.state', $tournament)), start: @js(route('admin.tournaments.draft.start', $tournament)), selectPlayer: @js(route('admin.tournaments.draft.select-player', $tournament)), removePlayer: @js(route('admin.tournaments.draft.remove-player', $tournament)), reassignPlayer: @js(route('admin.tournaments.draft.reassign-player', $tournament)), extend: @js(route('admin.tournaments.draft.extend', $tournament)), skip: @js(route('admin.tournaments.draft.skip', $tournament)), pause: @js(route('admin.tournaments.draft.pause', $tournament)), resume: @js(route('admin.tournaments.draft.resume', $tournament)), undo: @js(route('admin.tournaments.draft.undo', $tournament)), reset: @js(route('admin.tournaments.draft.reset', $tournament)) })" x-init="init()">
        
        <!-- Stamp Celebration Overlay Pop-up -->
        <div x-show="showCelebration" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-75"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-75"
             class="celebration-overlay">
            
            <div class="mb-2">
                <span class="fs-1">🎉</span>
            </div>
            
            <p class="cricket-kicker text-success mb-1 fw-bold text-uppercase" style="font-size: 0.75rem;">Player Selected / Naya Khiladi!</p>
            <h3 class="fw-bold text-dark mb-1" x-text="lastPickedPlayerName"></h3>
            
            <!-- Red double-border stamp -->
            <div class="drafted-stamp">
                DRAFTED BY<br>
                <span style="font-size: 0.95rem;" x-text="lastPickedTeamName"></span>
            </div>
            
            <div class="small text-muted mt-2">Draft board updated.</div>
        </div>

        <div class="row g-2 mb-4">
            <div class="col-6 col-md">
                <div class="cricket-surface p-3">
                    <div class="small text-secondary">Total picks</div>
                    <div class="cricket-stat-value mt-2 text-dark" x-text="state.summary?.total ?? 0"></div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="cricket-surface p-3">
                    <div class="small text-secondary">Selected</div>
                    <div class="cricket-stat-value text-success mt-2" x-text="state.summary?.selected ?? 0"></div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="cricket-surface p-3">
                    <div class="small text-secondary">Pending</div>
                    <div class="cricket-stat-value mt-2 text-dark" x-text="state.summary?.pending ?? 0"></div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="cricket-surface p-3">
                    <div class="small text-secondary">Expired</div>
                    <div class="cricket-stat-value text-danger mt-2" x-text="state.summary?.expired ?? 0"></div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="cricket-surface p-3">
                    <div class="small text-secondary">Skipped</div>
                    <div class="cricket-stat-value mt-2 text-dark" x-text="state.summary?.skipped ?? 0"></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="cricket-pitch-panel p-4 p-lg-5">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-5">
                        <div>
                            <p class="cricket-kicker mb-2">Live command</p>
                            <div class="small text-white-50 mb-2" x-text="state.current_round ? `Round ${state.current_round} · Pick ${state.current_pick_number ?? '—'}` : 'No active assignment'"></div>
                            <h2 class="display-5 fw-bold mb-1" x-text="state.current_team?.name ?? 'Waiting for the first pick'"></h2>
                            <div class="text-white-50" x-text="state.status === 'expired' ? 'Timer expired — choose an admin action' : (state.status === 'paused' && state.next_pick ? 'Paused — administrator must start the next pick' : 'Current board status')"></div>
                        </div>
                        <span class="badge" style="background:var(--cricket-lime);color:var(--cricket-pitch-deep);" x-text="state.status"></span>
                    </div>
                    
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <div class="small text-white-50 mb-1">Time remaining</div>
                            <div class="display-1 fw-bold" style="color:var(--cricket-lime);" x-text="formattedTimer"></div>
                        </div>
                        <div class="col-md-6 mt-4 mt-md-0">
                            <div class="small text-white-50 mb-3">Admin actions</div>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-light" type="button" @click="runAction('start')" x-show="state.status === 'setup' || state.can_start_next_pick" :disabled="loading">
                                    <i class="fa-solid fa-play me-2"></i>
                                    <span x-text="state.status === 'setup' ? 'Start draft' : (state.next_pick?.round_number > ((state.rounds || []).find(round => round.status === 'active')?.round_number ?? 0) ? 'Start next round' : 'Start next pick')"></span>
                                </button>
                                <button class="btn btn-warning" type="button" @click="extend(30)" x-show="state.status === 'expired' || state.status === 'live'" :disabled="loading"><i class="fa-solid fa-clock me-2"></i>+30 sec</button>
                                <button class="btn btn-outline-warning" type="button" @click="extend(60)" x-show="state.status === 'expired' || state.status === 'live'" :disabled="loading"><i class="fa-solid fa-clock-rotate-left me-2"></i>+60 sec</button>
                                <button class="btn btn-outline-light" type="button" @click="runAction('skip')" x-show="state.status === 'expired'" :disabled="loading"><i class="fa-solid fa-forward me-2"></i>Skip</button>
                                <button class="btn btn-outline-light" type="button" @click="runAction('pause')" x-show="state.status === 'live'" :disabled="loading"><i class="fa-solid fa-pause me-2"></i>Pause</button>
                                <button class="btn btn-outline-light" type="button" @click="runAction('resume')" x-show="state.status === 'paused' && !state.can_start_next_pick" :disabled="loading"><i class="fa-solid fa-play me-2"></i>Resume</button>
                                <button class="btn btn-outline-light" type="button" @click="runAction('undo')" x-show="hasSelectedPick" :disabled="loading"><i class="fa-solid fa-rotate-left me-2"></i>Undo</button>
                                <button class="btn btn-danger" type="button" @click="if(confirm('Are you sure you want to reset this draft? This will clear all selected players and rounds, setting them back to pending from scratch!')) runAction('reset')" x-show="state.status !== 'setup' && completedPicks > 0" :disabled="loading"><i class="fa-solid fa-arrows-rotate me-2"></i>Reset draft</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert mt-4 mb-0 border-0" :class="state.status === 'expired' ? 'bg-warning text-dark' : 'bg-dark bg-opacity-25 text-white'" x-text="state.status === 'expired' ? 'The timer is paused at zero. Extend the pick or skip it manually.' : 'All state transitions are recorded in the audit trail.'"></div>
                </div>
            </div>
            
            <div class="col-xl-4">
                <div class="cricket-surface p-4 p-lg-5">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                        <div>
                            <p class="cricket-kicker mb-2">Pick sequence</p>
                            <h2 class="h3 fw-bold mb-1 text-dark">The board</h2>
                        </div>
                        <span class="badge text-bg-light" x-text="`${completedPicks}/${state.picks.length}`"></span>
                    </div>
                    
                    <div class="vstack gap-2" style="max-height:28rem;overflow-y:auto;">
                        <template x-for="pick in state.picks" :key="pick.pick_number">
                            <div class="cricket-surface-soft p-3 d-flex align-items-center gap-3">
                                <span class="badge rounded-circle p-2" :class="pick.status === 'selected' ? 'text-bg-success' : (pick.status === 'active' ? 'text-bg-warning' : 'text-bg-light')" x-text="pick.pick_number"></span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold text-dark text-truncate" x-text="pick.team?.name ?? 'Unassigned team'"></div>
                                    <div class="small text-secondary text-truncate" x-text="pick.player?.full_name ?? pick.status"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-xl-8">
                <div class="cricket-surface p-4 p-lg-5 h-100">
                    <div class="d-flex align-items-end justify-content-between gap-3 mb-4">
                        <div>
                            <p class="cricket-kicker mb-2">Team squads</p>
                            <h2 class="h3 fw-bold mb-1 text-dark">Selected players by team</h2>
                            <p class="text-secondary mb-0">Every confirmed selection grouped against its assigned team.</p>
                        </div>
                        <span class="badge text-bg-light" x-text="`${state.summary?.selected ?? 0} selected`"></span>
                    </div>
                    
                    <div class="row g-3">
                        <template x-for="team in state.team_squads" :key="team.id">
                            <div class="col-md-6">
                                <div class="cricket-surface-soft p-3 h-100">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                        <div>
                                            <h3 class="h5 fw-bold mb-1 text-dark" x-text="team.name"></h3>
                                            <div class="small text-secondary" x-text="`${team.selected_count} player${team.selected_count === 1 ? '' : 's'} selected`"></div>
                                        </div>
                                        <span class="badge text-bg-success" x-text="team.selected_count"></span>
                                    </div>
                                    
                                    <div class="vstack gap-2" x-show="team.selected_players.length">
                                        <template x-for="player in team.selected_players" :key="player.pick_number">
                                            <div class="bg-white rounded-3 p-2 d-flex align-items-center justify-content-between gap-2">
                                                <div class="min-w-0">
                                                    <div class="fw-bold text-dark text-truncate" x-text="player.full_name"></div>
                                                    <div class="small text-secondary text-truncate" x-text="`${player.playing_role || 'Unassigned'}${player.city ? ' · ' + player.city : ''}`"></div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge text-bg-light" x-text="`#${player.pick_number}`"></span>
                                                    <select class="form-select form-select-sm text-dark bg-white border-secondary-subtle" style="max-width:150px" x-model="reassignTargets[player.pick_number]" :disabled="loading">
                                                        <option value="">Move to pick</option>
                                                        <template x-for="target in (state.pending_picks || [])" :key="target.pick_number">
                                                            <option value="" class="text-dark" x-text="`#${target.pick_number} · ${target.team?.short_name ?? target.team?.name ?? 'Team'}`" :value="target.pick_number"></option>
                                                        </template>
                                                    </select>
                                                    <button type="button" class="btn btn-sm btn-outline-warning" @click="reassignPlayer(player.pick_number)" :disabled="loading || !reassignTargets[player.pick_number]" title="Reassign selected player"><i class="fa-solid fa-shuffle"></i></button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removePlayer(player.pick_number)" :disabled="loading" title="Remove selected player"><i class="fa-solid fa-user-minus"></i></button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="small text-secondary py-2" x-show="!team.selected_players.length">No player selected yet.</div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4">
                <div class="cricket-surface p-4 p-lg-5 h-100">
                    <div class="d-flex align-items-end justify-content-between gap-2 mb-4">
                        <div>
                            <p class="cricket-kicker mb-2">Player pool</p>
                            <h2 class="h3 fw-bold mb-1 text-dark">Remaining players</h2>
                        </div>
                        <span class="badge text-bg-light" x-text="filteredRemainingPlayers.length"></span>
                    </div>
                    
                    <!-- Search Input -->
                    <div class="mb-3">
                        <div class="input-group input-group-sm shadow-sm" style="border-radius: 6px; overflow: hidden;">
                            <span class="input-group-text bg-white border-secondary-subtle text-secondary"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" x-model="searchQuery" class="form-control border-secondary-subtle" placeholder="Search player by name...">
                        </div>
                    </div>
                    
                    <div class="vstack gap-2" style="max-height:28rem;overflow-y:auto;">
                        <template x-for="player in filteredRemainingPlayers" :key="player.id">
                            <div class="cricket-surface-soft p-3 d-flex align-items-center justify-content-between gap-2">
                                <div class="min-w-0">
                                    <div class="fw-bold text-dark text-truncate" x-text="player.full_name"></div>
                                    <div class="small text-secondary text-truncate" x-text="`${player.playing_role || 'Unassigned'}${player.city ? ' · ' + player.city : ''}`"></div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success" @click="selectPlayer(player)" :disabled="loading || !selectionPickNumber" x-show="selectionPickNumber"><i class="fa-solid fa-user-plus me-1"></i>Select</button>
                            </div>
                        </template>
                        <div class="small text-secondary py-3 text-center" x-show="!(state.remaining_players || []).length">No approved players remain.</div>
                        <div class="small text-secondary mt-2" x-show="selectionPickNumber">Select a player manually for <strong x-text="state.current_pick_number ? `Pick ${state.current_pick_number}` : `next pick ${state.next_pick?.pick_number ?? ''}`"></strong>.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cricket-surface p-4 mt-4">
            <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-3">
                <div>
                    <p class="cricket-kicker mb-2">Manual rounds</p>
                    <h2 class="h4 fw-bold mb-1 text-dark">Round control</h2>
                    <p class="text-secondary mb-0">Every pending pick waits here until the administrator starts it.</p>
                </div>
                <span class="badge text-bg-light" x-text="state.next_round ? `Next: Round ${state.next_round.round_number}` : 'No pending round'"></span>
            </div>
            
            <div class="row g-2">
                <template x-for="round in (state.rounds || [])" :key="round.round_number">
                    <div class="col-md-4">
                        <div class="cricket-surface-soft p-3 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <div class="fw-bold text-dark" x-text="round.name || `Round ${round.round_number}`"></div>
                                <div class="small text-secondary" x-text="`${round.selected}/${round.total} selected · ${round.status}`"></div>
                            </div>
                            <span class="badge" :class="round.status === 'completed' ? 'text-bg-success' : (round.status === 'active' ? 'text-bg-warning' : 'text-bg-light')" x-text="round.status"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        
        <div class="alert alert-danger border-0 shadow-sm mt-4" x-show="error" x-text="error"></div>
    </div>

    <script>
        function draftControl(initialState, urls) {
            return {
                state: initialState, 
                urls, 
                loading: false, 
                error: '', 
                reassignTargets: {}, 
                get selectionPickNumber() { return this.state.current_pick_number ?? this.state.next_pick?.pick_number ?? null; }, 
                remaining: initialState.timer?.remaining_seconds ?? null, 
                timerDeadline: initialState.timer?.expires_at ? Date.parse(initialState.timer.expires_at) : null, 
                serverOffsetMs: initialState.timer?.server_now ? Date.parse(initialState.timer.server_now) - Date.now() : 0, 
                timerHandle: null, 
                pollHandle: null,
                showCelebration: false,
                lastPickedPlayerName: '',
                lastPickedTeamName: '',
                celebrationTimeout: null,
                searchQuery: '',
                
                init() { 
                    this.syncTimer(); 
                    this.timerHandle = window.setInterval(() => this.syncTimer(), 250); 
                    this.pollHandle = window.setInterval(() => this.poll(), 2000); 
                }, 
                syncTimer() { 
                    if (this.timerDeadline === null || !['live', 'expired'].includes(this.state.status)) return; 
                    this.remaining = Math.max(0, Math.ceil((this.timerDeadline - (Date.now() + this.serverOffsetMs)) / 1000)); 
                }, 
                syncServerClock(payload, requestStartedAt = null, responseReceivedAt = null) { 
                    const serverNow = payload.timer?.server_now ? Date.parse(payload.timer.server_now) : null; 
                    if (Number.isFinite(serverNow)) { 
                        const midpoint = requestStartedAt !== null && responseReceivedAt !== null ? (requestStartedAt + responseReceivedAt) / 2 : Date.now(); 
                        this.serverOffsetMs = serverNow - midpoint; 
                    } 
                }, 
                get formattedTimer() { 
                    if (this.remaining === null) return '--:--'; 
                    return `${Math.floor(this.remaining / 60).toString().padStart(2, '0')}:${Math.floor(this.remaining % 60).toString().padStart(2, '0')}`; 
                }, 
                get completedPicks() { 
                    return this.state.picks.filter(pick => pick.status === 'selected' || pick.status === 'skipped').length; 
                }, 
                get hasSelectedPick() { 
                    return this.state.picks.some(pick => pick.status === 'selected'); 
                }, 
                get filteredRemainingPlayers() {
                    let players = this.state.remaining_players || [];
                    if (this.searchQuery && this.searchQuery.trim() !== '') {
                        const q = this.searchQuery.toLowerCase().trim();
                        players = players.filter(player => (player.full_name || '').toLowerCase().includes(q));
                    }
                    return players;
                }, 
                async poll() { 
                    if (document.hidden || this.loading) return; 
                    try { 
                        const requestStartedAt = Date.now(); 
                        const response = await fetch(this.urls.state, { headers: { 'Accept': 'application/json' } }); 
                        const responseReceivedAt = Date.now(); 
                        if (!response.ok) return; 
                        const nextState = await response.json(); 
                        this.syncServerClock(nextState, requestStartedAt, responseReceivedAt); 
                        
                        if (nextState.revision !== this.state.revision) { 
                            const oldSelected = this.state.summary?.selected ?? 0;
                            this.state = nextState; 
                            this.timerDeadline = nextState.timer?.expires_at ? Date.parse(nextState.timer.expires_at) : null; 
                            this.syncTimer(); 
                            
                            if (nextState.summary?.selected > oldSelected) {
                                this.celebrateSelection(nextState);
                            }
                        } 
                    } catch (error) { 
                        this.error = 'Polling connection interrupted. Retrying automatically.'; 
                    } 
                }, 
                async selectPlayer(player) { 
                    if (!this.selectionPickNumber) return; 
                    await this.post(this.urls.selectPlayer, { pick_number: this.selectionPickNumber, tournament_player_id: player.id }); 
                }, 
                async removePlayer(pickNumber) { 
                    if (!window.confirm('Remove this selected player and reopen the pick?')) return; 
                    await this.post(this.urls.removePlayer, { pick_number: pickNumber }); 
                }, 
                async reassignPlayer(fromPickNumber) { 
                    const toPickNumber = Number(this.reassignTargets[fromPickNumber]); 
                    if (!toPickNumber || !window.confirm(`Move this player to pick #${toPickNumber}?`)) return; 
                    await this.post(this.urls.reassignPlayer, { from_pick_number: fromPickNumber, to_pick_number: toPickNumber }); 
                    this.reassignTargets = {}; 
                }, 
                async extend(seconds) { 
                    await this.post(this.urls.extend, { seconds }); 
                }, 
                async runAction(action) { 
                    await this.post(this.urls[action]); 
                }, 
                async post(url, body = {}) { 
                    this.loading = true; 
                    this.error = ''; 
                    try { 
                        const requestStartedAt = Date.now(); 
                        const response = await fetch(url, { 
                            method: 'POST', 
                            headers: { 
                                'Accept': 'application/json', 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                            }, 
                            body: JSON.stringify(body) 
                        }); 
                        const responseReceivedAt = Date.now(); 
                        const payload = await response.json(); 
                        this.syncServerClock(payload, requestStartedAt, responseReceivedAt); 
                        if (!response.ok) { 
                            this.error = payload.message || Object.values(payload.errors || {}).flat()[0] || 'The action could not be completed.'; 
                            return; 
                        } 
                        const oldSelected = this.state.summary?.selected ?? 0;
                        this.state = payload; 
                        this.timerDeadline = payload.timer?.expires_at ? Date.parse(payload.timer.expires_at) : null; 
                        this.syncTimer(); 
                        
                        if (payload.summary?.selected > oldSelected) {
                            this.celebrateSelection(payload);
                        }
                    } catch (error) { 
                        this.error = 'The request failed. Please retry.'; 
                    } finally { 
                        this.loading = false; 
                    } 
                },
                celebrateSelection(payload) {
                    const picks = payload.picks || [];
                    const selectedPicks = picks.filter(p => p.status === 'selected');
                    if (selectedPicks.length === 0) return;
                    
                    selectedPicks.sort((a, b) => b.pick_number - a.pick_number);
                    const lastPick = selectedPicks[0];
                    
                    if (lastPick && lastPick.player) {
                        this.lastPickedPlayerName = lastPick.player.full_name;
                        this.lastPickedTeamName = lastPick.team?.name || 'New Team';
                        this.showCelebration = true;
                        
                        if (typeof confetti === 'function') {
                            confetti({
                                particleCount: 180,
                                spread: 100,
                                origin: { y: 0.6 }
                            });
                        }
                        
                        if (this.celebrationTimeout) clearTimeout(this.celebrationTimeout);
                        this.celebrationTimeout = setTimeout(() => {
                            this.showCelebration = false;
                        }, 4000);
                    }
                }
            }; 
        }
    </script>
</x-app-layout>
