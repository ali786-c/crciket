@php
    $isEdit = isset($tournament);
    $value = fn (string $key, mixed $default = null): mixed => old($key, $isEdit ? data_get($tournament, $key, $default) : $default);
@endphp

<div class="row g-4">
    <div class="col-xl-8">
        <div class="cricket-surface p-4 p-lg-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="cricket-brand-mark"><i class="fa-solid fa-trophy"></i></span>
                <div>
                    <p class="cricket-kicker mb-1">Tournament identity</p>
                    <h2 class="h4 fw-bold mb-1">Competition profile</h2>
                    <p class="small text-secondary mb-0">The public-facing identity and location of this tournament.</p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="name" class="form-label">Tournament name</label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ $value('name') }}" placeholder="Lahore Premier Cup" required autofocus>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="season_name" class="form-label">Season</label>
                    <input id="season_name" name="season_name" type="text" class="form-control @error('season_name') is-invalid @enderror" value="{{ $value('season_name') }}" placeholder="2026 Season">
                    @error('season_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-7">
                    <label for="slug" class="form-label">URL slug</label>
                    <input id="slug" name="slug" type="text" class="form-control @error('slug') is-invalid @enderror" value="{{ $value('slug') }}" placeholder="lahore-premier-cup" required>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5">
                    <label for="timezone" class="form-label">Timezone</label>
                    <select id="timezone" name="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                        @foreach (timezone_identifiers_list() as $timezone)
                            <option value="{{ $timezone }}" @selected($value('timezone', 'Asia/Karachi') === $timezone)>{{ $timezone }}</option>
                        @endforeach
                    </select>
                    @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Description <span class="text-secondary fw-normal">Optional</span></label>
                    <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="A short note about this tournament...">{{ $value('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="venue" class="form-label">Venue</label>
                    <input id="venue" name="venue" type="text" class="form-control @error('venue') is-invalid @enderror" value="{{ $value('venue') }}" placeholder="Gaddafi Stadium">
                    @error('venue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="city" class="form-label">City</label>
                    <input id="city" name="city" type="text" class="form-control @error('city') is-invalid @enderror" value="{{ $value('city') }}" placeholder="Lahore">
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="location" class="form-label">Legacy location</label>
                    <input id="location" name="location" type="text" class="form-control @error('location') is-invalid @enderror" value="{{ $value('location') }}" placeholder="Punjab, Pakistan">
                    @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="starts_on" class="form-label">Tournament start date</label>
                    <input id="starts_on" name="starts_on" type="date" class="form-control @error('starts_on') is-invalid @enderror" value="{{ old('starts_on', $isEdit && $tournament->starts_on ? $tournament->starts_on->format('Y-m-d') : '') }}">
                    @error('starts_on')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="ends_on" class="form-label">Tournament end date</label>
                    <input id="ends_on" name="ends_on" type="date" class="form-control @error('ends_on') is-invalid @enderror" value="{{ old('ends_on', $isEdit && $tournament->ends_on ? $tournament->ends_on->format('Y-m-d') : '') }}">
                    @error('ends_on')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="cricket-surface p-4 p-lg-5 h-100">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="cricket-brand-mark"><i class="fa-solid fa-stopwatch"></i></span>
                <div>
                    <p class="cricket-kicker mb-1">Draft defaults</p>
                    <h2 class="h4 fw-bold mb-1">Operating rules</h2>
                    <p class="small text-secondary mb-0">Defaults used while building the draft.</p>
                </div>
            </div>
            <div class="mb-3">
                <label for="squad_size" class="form-label">Players per team</label>
                <input id="squad_size" name="squad_size" type="number" min="1" max="99" class="form-control @error('squad_size') is-invalid @enderror" value="{{ $value('squad_size', 3) }}" required>
                @error('squad_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label for="default_pick_duration" class="form-label">Default pick timer</label>
                <div class="input-group">
                    <input id="default_pick_duration" name="default_pick_duration" type="number" min="5" max="3600" class="form-control @error('default_pick_duration') is-invalid @enderror" value="{{ $value('default_pick_duration', 60) }}" required>
                    <span class="input-group-text">seconds</span>
                </div>
                @error('default_pick_duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label for="default_overs_per_innings" class="form-label">Default overs per innings</label>
                <div class="input-group">
                    <input id="default_overs_per_innings" name="default_overs_per_innings" type="number" min="1" max="100" class="form-control @error('default_overs_per_innings') is-invalid @enderror" value="{{ $value('default_overs_per_innings', '') }}">
                    <span class="input-group-text">overs</span>
                </div>
                @error('default_overs_per_innings')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="small text-secondary mt-2">Leave blank to use the selected cricket rule profile. Every new match can override this value.</div>
            </div>
            <div class="mb-4">
                <label for="cricket_rule_profile_id" class="form-label">Match format and cricket rules</label>
                <select id="cricket_rule_profile_id" name="cricket_rule_profile_id" class="form-select @error('cricket_rule_profile_id') is-invalid @enderror">
                    <option value="">Select rule profile later</option>
                    @foreach($ruleProfiles as $profile)
                        <option value="{{ $profile->id }}" @selected((string) $value('cricket_rule_profile_id', '') === (string) $profile->id)>{{ $profile->name }} · {{ $profile->overs_per_innings }} overs · XI {{ $profile->playing_xi_size }}</option>
                    @endforeach
                </select>
                @error('cricket_rule_profile_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="small text-secondary mt-2">This controls innings, overs, playing XI size, wickets, extras, and points. It locks after draft setup begins.</div>
            </div>
            <div class="form-check form-switch mb-4">
                <input type="hidden" name="is_public" value="0">
                <input id="is_public" name="is_public" type="checkbox" class="form-check-input" value="1" @checked((bool) $value('is_public', true))>
                <label for="is_public" class="form-check-label fw-bold">Visible on public pages</label>
                <div class="small text-secondary">Hide private drafts from the homepage and live center.</div>
            </div>
            <div class="cricket-surface-soft p-3 mb-4">
                <div class="small fw-bold mb-2"><i class="fa-solid fa-lock text-success me-2"></i>Workflow protection</div>
                <div class="small text-secondary">Squad size, default timer, match rules, and default overs become locked after draft setup begins. Admin can still move the tournament to any lifecycle status, and each match may use a custom over limit.</div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="cricket-surface p-4 p-lg-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="cricket-brand-mark"><i class="fa-solid fa-calendar-days"></i></span>
                <div>
                    <p class="cricket-kicker mb-1">Registration window</p>
                    <h2 class="h4 fw-bold mb-1">Control player sign-ups</h2>
                    <p class="small text-secondary mb-0">Leave blank when registration timing is managed outside the system.</p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="registration_opens_at" class="form-label">Registration opens</label>
                    <input id="registration_opens_at" name="registration_opens_at" type="datetime-local" class="form-control @error('registration_opens_at') is-invalid @enderror" value="{{ old('registration_opens_at', $isEdit && $tournament->registration_opens_at ? $tournament->registration_opens_at->format('Y-m-d\TH:i') : '') }}">
                    @error('registration_opens_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="registration_closes_at" class="form-label">Registration closes</label>
                    <input id="registration_closes_at" name="registration_closes_at" type="datetime-local" class="form-control @error('registration_closes_at') is-invalid @enderror" value="{{ old('registration_closes_at', $isEdit && $tournament->registration_closes_at ? $tournament->registration_closes_at->format('Y-m-d\TH:i') : '') }}">
                    @error('registration_closes_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="cricket-surface p-4 p-lg-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="cricket-brand-mark"><i class="fa-solid fa-images"></i></span>
                <div>
                    <p class="cricket-kicker mb-1">Branding</p>
                    <h2 class="h4 fw-bold mb-1">Logo and banner</h2>
                    <p class="small text-secondary mb-0">JPG, PNG or WebP. Logo up to 5 MB; banner up to 10 MB.</p>
                </div>
            </div>
            <div class="row g-4 align-items-end">
                <div class="col-md-6">
                    <label for="logo" class="form-label">Tournament logo</label>
                    <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp" class="form-control @error('logo') is-invalid @enderror">
                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if ($isEdit && $tournament->logo_path)
                        <div class="d-flex align-items-center gap-3 mt-3"><img src="{{ Storage::disk('public')->url($tournament->logo_path) }}" alt="Tournament logo" style="width:64px;height:64px;object-fit:cover;border-radius:16px;"><div class="form-check"><input id="remove_logo" name="remove_logo" type="checkbox" value="1" class="form-check-input"><label for="remove_logo" class="form-check-label small">Remove current logo</label></div></div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="banner" class="form-label">Tournament banner</label>
                    <input id="banner" name="banner" type="file" accept="image/jpeg,image/png,image/webp" class="form-control @error('banner') is-invalid @enderror">
                    @error('banner')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if ($isEdit && $tournament->banner_path)
                        <div class="d-flex align-items-center gap-3 mt-3"><img src="{{ Storage::disk('public')->url($tournament->banner_path) }}" alt="Tournament banner" style="width:120px;height:64px;object-fit:cover;border-radius:16px;"><div class="form-check"><input id="remove_banner" name="remove_banner" type="checkbox" value="1" class="form-check-input"><label for="remove_banner" class="form-check-label small">Remove current banner</label></div></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
