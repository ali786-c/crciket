<x-app-layout>
    <!-- Load Canvas Confetti Library from jsDelivr CDN -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <style>
        @keyframes pulse-glow {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
            50% { transform: scale(1.02); box-shadow: 0 0 15px 5px rgba(25, 135, 84, 0.4); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
        }
        .active-glow {
            animation: pulse-glow 1.5s infinite ease-in-out;
        }
        
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
                    <span class="small text-uppercase fw-bold text-danger" style="letter-spacing:.12em;">Live draft room</span>
                </div>
                <h1 class="display-6 fw-bold mb-2">{{ $tournament->name }}</h1>
                <p class="text-secondary mb-0">Captain view · Your selection is validated server-side.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('captain.reports.index', $tournament) }}" class="btn btn-outline-success"><i class="fa-solid fa-file-lines me-2"></i>Reports</a>
                <a href="{{ route('captain.dashboard') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left me-2"></i>Workspace</a>
            </div>
        </div>
    </x-slot>

    <div class="container pb-5" x-data="captainDraft(@js($state), @js(route('captain.draft.state', $tournament)), @js(route('captain.draft.pick', $tournament)))" x-init="init()">
        
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

        <!-- Live Turn Notification Bar -->
        <div class="mb-4">
            <!-- Active Turn Banner -->
            <div x-show="state.captain_can_pick" class="alert alert-success border-0 shadow-lg text-center p-4 active-glow" style="border-radius: 16px;">
                <div class="display-5 fw-bold text-success mb-2"><i class="fa-solid fa-circle-play me-2"></i>👉 AAP KI BAARI HAI / YOUR TURN 🏏</div>
                <div class="h5 fw-bold text-dark mb-0">Niche list se player select karein aur pick confirm karein!</div>
            </div>
            
            <!-- Waiting Banner -->
            <div x-show="!state.captain_can_pick" class="alert alert-secondary border-0 shadow-sm text-center p-3" style="background: rgba(33, 37, 41, 0.05); border-radius: 16px;">
                <div class="h5 fw-bold mb-1 text-muted"><i class="fa-solid fa-clock me-2"></i>SABAR KAREIN / WAIT FOR TURN</div>
                <div class="small text-secondary mb-0">Dusri team ki baari chal rahi hai. Please wait.</div>
            </div>
        </div>

        <!-- Stacked Layout Sections: 1. Timer -> 2. Player Pool -> 3. Pick History -> 4. Your Team -->
        <div class="vstack gap-4">
            
            <!-- 1. Timer Box -->
            <div class="cricket-pitch-panel p-4 p-lg-5" style="border-radius: 20px;">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-5">
                    <div>
                        <p class="cricket-kicker mb-2">On the clock / Draft state</p>
                        <div class="small text-white-50 mb-2" x-text="state.current_round ? `Round ${state.current_round} · Pick ${state.current_pick_number ?? '—'}` : 'Waiting for next turn'"></div>
                        <h2 class="display-5 fw-bold mb-1" x-text="state.current_team?.name ?? 'Waiting for Admin'"></h2>
                        <div class="text-white-50" x-text="state.status === 'expired' ? 'Timer expired — waiting for admin action' : (state.captain_can_pick ? 'Your team can pick now.' : 'Waiting for your team turn.')"></div>
                    </div>
                    <span class="badge fs-6 px-3 py-2" style="background: var(--cricket-lime); color: var(--cricket-pitch-deep);" x-text="state.status"></span>
                </div>

                <div class="row align-items-end g-4">
                    <div class="col-md-7 text-center text-md-start">
                        <div class="small text-white-50 mb-1">Time Remaining / Baki Waqt</div>
                        <div class="display-1 fw-bold" style="color: var(--cricket-lime); text-shadow: 0 0 15px rgba(163, 230, 53, 0.5);" x-text="formattedTimer"></div>
                    </div>
                    <div class="col-md-5">
                        <div class="small text-white-50 mb-2">Draft Progress / Board bar</div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-white" x-text="`${state.summary?.selected ?? 0} Selected`"></span>
                            <span class="text-white" x-text="`${state.summary?.total ?? 0} Total`"></span>
                        </div>
                        <div class="progress shadow-sm" style="height: .75rem; background: rgba(255,255,255,.15); border-radius: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="background: var(--cricket-lime);" :style="`width: ${state.summary?.total ? ((state.summary.selected / state.summary.total) * 100) : 0}%`"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Player Pool Box -->
            <div class="cricket-surface p-4 p-lg-5" style="border-radius: 20px;">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                    <div>
                        <p class="cricket-kicker mb-2">Player Pool</p>
                        <h2 class="h3 fw-bold mb-1 text-dark">Select Players</h2>
                        <p class="text-secondary mb-0">Choose players below when it is your turn.</p>
                    </div>
                    <span class="badge bg-secondary text-white px-3 py-2" x-text="`${state.available_players.length} available`"></span>
                </div>

                <!-- Role Filters -->
                <div class="mb-4">
                    <label class="small fw-bold text-secondary d-block mb-2 text-uppercase" style="letter-spacing: .08em;"><i class="fa-solid fa-filter me-1"></i>Filter by Role / Category:</label>
                    <div class="d-flex flex-wrap gap-1">
                        <button type="button" class="btn btn-sm rounded-pill fw-bold border" 
                                :class="roleFilter === 'all' ? 'btn-success text-white border-success' : 'btn-outline-secondary bg-white'" 
                                @click="roleFilter = 'all'" style="font-size: 0.8rem; padding: 6px 12px;">
                            🌍 All / Sab (All)
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill fw-bold border" 
                                :class="roleFilter === 'Batter' ? 'btn-success text-white border-success' : 'btn-outline-secondary bg-white'" 
                                @click="roleFilter = 'Batter'" style="font-size: 0.8rem; padding: 6px 12px;">
                            🏏 Batter (Batters)
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill fw-bold border" 
                                :class="roleFilter === 'Bowler' ? 'btn-success text-white border-success' : 'btn-outline-secondary bg-white'" 
                                @click="roleFilter = 'Bowler'" style="font-size: 0.8rem; padding: 6px 12px;">
                            🥎 Bowler (Bowlers)
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill fw-bold border" 
                                :class="roleFilter === 'All-rounder' ? 'btn-success text-white border-success' : 'btn-outline-secondary bg-white'" 
                                @click="roleFilter = 'All-rounder'" style="font-size: 0.8rem; padding: 6px 12px;">
                            ⚡ All-Rounder
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill fw-bold border" 
                                :class="roleFilter === 'Wicketkeeper' ? 'btn-success text-white border-success' : 'btn-outline-secondary bg-white'" 
                                @click="roleFilter = 'Wicketkeeper'" style="font-size: 0.8rem; padding: 6px 12px;">
                            🧤 Keeper
                        </button>
                    </div>
                </div>

                <!-- Name Search -->
                <div class="mb-3">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                        <span class="input-group-text bg-white border-secondary-subtle text-secondary"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" x-model="searchQuery" class="form-control border-secondary-subtle" placeholder="Search player by name / Name se talash karein...">
                    </div>
                </div>

                <!-- Available Players List -->
                <div class="p-2 border rounded-4 bg-light" style="max-height: 38rem; overflow-y: auto;">
                    <div class="row g-2">
                        <template x-for="player in filteredPlayers" :key="player.id">
                            <div class="col-md-6 col-lg-4">
                                <button type="button" class="btn w-100 text-start p-3 h-100 border shadow-sm rounded-3" 
                                        :class="state.captain_can_pick ? 'btn-success text-white active-glow border-success' : 'btn-white text-dark bg-white border-secondary-subtle'"
                                        @click="confirmPick(player)" 
                                        :disabled="!state.captain_can_pick || loading"
                                        style="transition: all 0.2s; white-space: normal; overflow: hidden;">
                                    <span class="d-flex align-items-center gap-3 w-100">
                                        <!-- Visual Role Icon Box -->
                                        <span class="cricket-brand-mark flex-shrink-0 d-flex align-items-center justify-content-center bg-light text-dark shadow-sm" style="width: 2.75rem; height: 2.75rem; border-radius: 12px; font-size: 1.35rem;">
                                            <span x-text="player.playing_role === 'Batter' ? '🏏' : (player.playing_role === 'Bowler' ? '🥎' : (player.playing_role === 'All-rounder' ? '⚡' : (player.playing_role === 'Wicketkeeper' ? '🧤' : '👤')))"></span>
                                        </span>
                                        <!-- Player Details -->
                                        <span class="flex-grow-1 min-w-0" style="overflow: hidden;">
                                            <span class="d-block fw-bold text-truncate" :class="state.captain_can_pick ? 'text-white' : 'text-dark'" style="font-size: 1.05rem;" x-text="player.full_name"></span>
                                            <span class="d-block small text-truncate" :class="state.captain_can_pick ? 'text-white-50' : 'text-secondary'" x-text="`${player.playing_role || 'Unassigned'}${player.city ? ' · ' + player.city : ''}`"></span>
                                        </span>
                                        <i class="fa-solid fa-square-plus fs-4 flex-shrink-0" :class="state.captain_can_pick ? 'text-white' : 'text-success'"></i>
                                    </span>
                                </button>
                            </div>
                        </template>
                    </div>
                    
                    <div class="text-center text-secondary py-5" x-show="!filteredPlayers.length">
                        <i class="fa-solid fa-filter-circle-xmark fs-3 mb-2"></i>
                        <div>Is category me koi player nahi hai.</div>
                    </div>
                </div>
            </div>

            <!-- 3. Pick History Box -->
            <div class="cricket-surface p-4 p-lg-5" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <p class="cricket-kicker mb-2">Live sequence / History</p>
                        <h2 class="h3 fw-bold mb-1 text-dark">Pick History / Draft History</h2>
                    </div>
                    <span class="small text-secondary">Rev <span x-text="state.revision"></span></span>
                </div>

                <div class="vstack gap-2" style="max-height: 28rem; overflow-y: auto;">
                    <template x-for="pick in state.picks" :key="pick.pick_number">
                        <div class="p-3 border rounded-3 d-flex align-items-center gap-3" :style="pick.status === 'active' ? 'background: #fff3cd; border-color: #ffecb5 !important;' : 'background: #f8f9fa;'">
                            <span class="badge rounded-circle p-2" :class="pick.status === 'selected' ? 'text-bg-success' : (pick.status === 'active' ? 'text-bg-warning animate-pulse' : 'text-bg-light')" x-text="pick.pick_number"></span>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold text-dark text-truncate" x-text="pick.team?.name"></div>
                                <div class="small text-secondary text-truncate" x-text="pick.player?.full_name || (pick.status === 'active' ? '🔔 ACTIVE PICK (On clock)' : 'Pending')"></div>
                            </div>
                            <i class="fa-solid" :class="pick.status === 'selected' ? 'fa-check-double text-success fs-5' : (pick.status === 'active' ? 'fa-hourglass-half text-warning fs-5' : 'fa-clock text-secondary')"></i>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 4. Your Team Box -->
            <div class="cricket-surface p-4 p-lg-5" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <p class="cricket-kicker mb-2">Your Team / Aap Ki Team</p>
                        <h3 class="h3 fw-bold mb-0 text-dark" x-text="state.captain_team?.name ?? 'Assigned Team'"></h3>
                    </div>
                    <span class="badge bg-success px-3 py-2 fs-6" x-text="`${captainTeamPlayers.length} picked`"></span>
                </div>

                <div class="vstack gap-2" x-show="captainTeamPlayers.length" style="max-height: 24rem; overflow-y: auto;">
                    <template x-for="player in captainTeamPlayers" :key="player.pick_number">
                        <div class="p-3 border bg-light rounded-3 d-flex align-items-center gap-3">
                            <span class="badge bg-success rounded-circle p-2" x-text="player.pick_number"></span>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold text-dark text-truncate" x-text="player.full_name"></div>
                                <div class="small text-secondary" x-text="player.playing_role || 'Unassigned role'"></div>
                            </div>
                            <div class="small text-secondary text-end" x-text="player.selected_at ? new Date(player.selected_at).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'}) : ''"></div>
                        </div>
                    </template>
                </div>
                <div class="small text-secondary text-center py-4" x-show="!captainTeamPlayers.length">No players picked yet.</div>
            </div>

        </div>

        <div class="alert alert-danger border-0 shadow-sm mt-4" x-show="error" x-text="error"></div>

        <!-- Selection Confirmation Modal -->
        <div class="modal fade" id="pickConfirmationModal" tabindex="-1" aria-hidden="true" x-ref="pickModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 p-4 pb-0 text-center">
                        <h5 class="modal-title fw-bold w-100 text-dark" style="font-size: 1.45rem;">Player Select Karein? / Confirm Pick?</h5>
                    </div>
                    <div class="modal-body px-4 py-4 text-center">
                        <div class="bg-light p-4 rounded-4 mb-4 border d-flex flex-column align-items-center" style="border-radius: 16px;">
                            <span class="display-3 mb-2" x-text="selectedPlayer?.playing_role === 'Batter' ? '🏏' : (selectedPlayer?.playing_role === 'Bowler' ? '🥎' : (selectedPlayer?.playing_role === 'All-rounder' ? '⚡' : (selectedPlayer?.playing_role === 'Wicketkeeper' ? '🧤' : '👤')))"></span>
                            <div class="h2 fw-bold text-dark mb-1" x-text="selectedPlayer?.full_name"></div>
                            <span class="badge bg-success fs-6 mt-2 px-3 py-1.5" x-text="selectedPlayer?.playing_role || 'Player'"></span>
                            <div class="small text-muted mt-2" x-text="selectedPlayer?.city ? 'City: ' + selectedPlayer.city : ''"></div>
                        </div>
                        <p class="h6 text-danger mb-0 fw-bold">Kya aap is player ko apni team me shamil karna chahte hain?</p>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 justify-content-center gap-3">
                        <button type="button" class="btn btn-lg btn-light border px-4 rounded-pill fw-bold" data-bs-dismiss="modal" style="min-width: 130px; font-size: 1.05rem;">NAHI / NO</button>
                        <button type="button" class="btn btn-lg btn-success px-4 rounded-pill fw-bold" @click="submitPick()" :disabled="loading" style="min-width: 130px; font-size: 1.05rem;">HAAN / YES <i class="fa-solid fa-check ms-2"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function captainDraft(initialState, stateUrl, pickUrl) {
            return {
                state: initialState, stateUrl, pickUrl, selectedPlayer: null, loading: false, error: '', roleFilter: 'all', searchQuery: '', remaining: initialState.timer?.remaining_seconds ?? null, timerDeadline: initialState.timer?.expires_at ? Date.parse(initialState.timer.expires_at) : null, serverOffsetMs: initialState.timer?.server_now ? Date.parse(initialState.timer.server_now) - Date.now() : 0, timerHandle: null, pollHandle: null,
                showCelebration: false, lastPickedPlayerName: '', lastPickedTeamName: '', celebrationTimeout: null,
                init() { this.syncTimer(); this.timerHandle = window.setInterval(() => this.syncTimer(), 250); this.pollHandle = window.setInterval(() => this.poll(), 2000); },
                syncTimer() { if (this.timerDeadline === null || !['live', 'expired'].includes(this.state.status)) return; this.remaining = Math.max(0, Math.ceil((this.timerDeadline - (Date.now() + this.serverOffsetMs)) / 1000)); },
                syncServerClock(payload, requestStartedAt = null, responseReceivedAt = null) { const serverNow = payload.timer?.server_now ? Date.parse(payload.timer.server_now) : null; if (Number.isFinite(serverNow)) { const midpoint = requestStartedAt !== null && responseReceivedAt !== null ? (requestStartedAt + responseReceivedAt) / 2 : Date.now(); this.serverOffsetMs = serverNow - midpoint; } },
                get roleFilters() { return [{ value: 'all', label: 'All roles' }, { value: 'Batter', label: 'Batters' }, { value: 'Bowler', label: 'Bowlers' }, { value: 'All-rounder', label: 'All-rounders' }, { value: 'Wicketkeeper', label: 'Wicketkeepers' }, { value: 'Unassigned', label: 'Unassigned' }]; },
                get filteredPlayers() { let players = this.state.available_players || []; if (this.roleFilter !== 'all') { players = players.filter(player => (player.playing_role || 'Unassigned') === this.roleFilter); } if (this.searchQuery && this.searchQuery.trim() !== '') { const q = this.searchQuery.toLowerCase().trim(); players = players.filter(player => (player.full_name || '').toLowerCase().includes(q)); } return players; },
                get captainTeamPlayers() { const teamId = this.state.captain_team?.id; return this.state.team_squads?.find(team => team.id === teamId)?.selected_players ?? []; },
                get formattedTimer() { if (this.remaining === null) return '--:--'; return `${Math.floor(this.remaining / 60).toString().padStart(2, '0')}:${Math.floor(this.remaining % 60).toString().padStart(2, '0')}`; },
                confirmPick(player) { this.selectedPlayer = player; bootstrap.Modal.getOrCreateInstance(this.$refs.pickModal).show(); },
                async submitPick() { if (!this.selectedPlayer) return; this.loading = true; this.error = ''; try { const response = await fetch(this.pickUrl, { method: 'POST', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ tournament_player_id: this.selectedPlayer.id }) }); const payload = await response.json(); if (!response.ok) { this.error = payload.message || Object.values(payload.errors || {}).flat()[0] || 'The pick could not be completed.'; return; } const oldSelected = this.state.summary?.selected ?? 0; this.applyState(payload); bootstrap.Modal.getOrCreateInstance(this.$refs.pickModal).hide(); if (payload.summary?.selected > oldSelected) { this.celebrateSelection(payload); } } catch (error) { this.error = 'The request failed. Please try again.'; } finally { this.loading = false; } },
                async poll() { if (this.loading) return; try { const requestStartedAt = Date.now(); const response = await fetch(this.stateUrl, { headers: { 'Accept': 'application/json' } }); const responseReceivedAt = Date.now(); if (!response.ok) return; const payload = await response.json(); this.syncServerClock(payload, requestStartedAt, responseReceivedAt); if (payload.revision !== this.state.revision) { const oldSelected = this.state.summary?.selected ?? 0; this.applyState(payload); if (payload.summary?.selected > oldSelected) { this.celebrateSelection(payload); } } } catch (error) { this.error = 'Live connection interrupted. Retrying automatically.'; } },
                applyState(payload) { this.state = payload; this.timerDeadline = payload.timer?.expires_at ? Date.parse(payload.timer.expires_at) : null; this.syncServerClock(payload); this.syncTimer(); },
                celebrateSelection(payload) { const picks = payload.picks || []; const selectedPicks = picks.filter(p => p.status === 'selected'); if (selectedPicks.length === 0) return; selectedPicks.sort((a, b) => b.pick_number - a.pick_number); const lastPick = selectedPicks[0]; if (lastPick && lastPick.player) { this.lastPickedPlayerName = lastPick.player.full_name; this.lastPickedTeamName = lastPick.team?.name || 'New Team'; this.showCelebration = true; if (typeof confetti === 'function') { confetti({ particleCount: 180, spread: 100, origin: { y: 0.6 } }); } if (this.celebrationTimeout) clearTimeout(this.celebrationTimeout); this.celebrationTimeout = setTimeout(() => { this.showCelebration = false; }, 4000); } }
            };
        }
    </script>
</x-app-layout>
