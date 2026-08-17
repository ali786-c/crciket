<x-app-layout>
    <x-slot name="header"><div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3"><div><p class="cricket-kicker mb-2">Tournament setup</p><h1 class="display-6 fw-bold mb-2">Player approvals</h1><p class="text-secondary mb-0">{{ $tournament->name }} · Build the pool captains can actually pick from.</p></div><div class="d-flex gap-2"><a href="{{ route('admin.tournaments.players.pdf', $tournament) }}" class="btn btn-outline-success"><i class="fa-solid fa-file-pdf me-2"></i>Download PDF</a><a href="{{ route('admin.tournaments.show', $tournament) }}" class="btn btn-light"><i class="fa-solid fa-arrow-left me-2"></i>Tournament workspace</a></div></div></x-slot>

    <div class="container pb-5">@if (session('status'))<div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>@endif @if ($errors->any())<div class="alert alert-danger border-0 shadow-sm"><strong>CSV import could not be completed.</strong><ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4"><div><p class="cricket-kicker mb-1">Registration queue</p><h2 class="h3 fw-bold mb-0">Review players</h2></div><span class="badge text-bg-light">{{ $registrations->total() }} registrations</span></div>
        <div class="cricket-surface p-3 p-lg-4 mb-4"><div class="row align-items-center g-4"><div class="col-lg-7"><p class="cricket-kicker mb-1">Bulk onboarding & Manual Entry</p><h3 class="h4 fw-bold mb-2">Add players to pool</h3><p class="text-secondary mb-0">Upload a CSV template or add a player manually. Existing players are matched by phone number instead of duplicated.</p></div><div class="col-lg-5"><div class="d-flex flex-wrap justify-content-lg-end gap-2 mb-3"><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPlayerModal"><i class="fa-solid fa-plus me-2"></i>Add Player Manually</button><a href="{{ route('admin.players.import.template') }}" class="btn btn-outline-primary"><i class="fa-solid fa-download me-2"></i>Download demo CSV</a></div><form method="POST" action="{{ route('admin.tournaments.players.import', $tournament) }}" enctype="multipart/form-data" class="d-flex flex-column flex-sm-row gap-2">@csrf<input type="file" name="players_csv" accept=".csv,text/csv" class="form-control" required><button class="btn btn-outline-success flex-shrink-0" type="submit"><i class="fa-solid fa-upload me-2"></i>Upload CSV</button></form></div></div><div class="small text-secondary mt-3">Required columns: <code>full_name, phone, location, playing_role</code>. Supported roles: Batter, Bowler, All-rounder, Wicketkeeper. Maximum 500 rows per upload.</div></div>
        <div class="cricket-surface p-3 p-lg-4">
            <!-- Filter Bar -->
            <form method="GET" action="{{ route('admin.tournaments.players.index', $tournament) }}" class="row g-2 mb-4 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label small fw-bold text-secondary">Search by Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Type player name..." value="{{ $currentSearch }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="location" class="form-label small fw-bold text-secondary">Location</label>
                    <select name="location" id="location" class="form-select">
                        <option value="">All Locations</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" @selected($currentLocation === $loc)>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label small fw-bold text-secondary">Sort by</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="latest" @selected($currentSort === 'latest')>Latest Joined</option>
                        <option value="name_asc" @selected($currentSort === 'name_asc')>Name: A to Z</option>
                        <option value="name_desc" @selected($currentSort === 'name_desc')>Name: Z to A</option>
                        <option value="location_asc" @selected($currentSort === 'location_asc')>Location: A to Z</option>
                        <option value="location_desc" @selected($currentSort === 'location_desc')>Location: Z to A</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-grow-1"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                    @if($currentSearch || $currentLocation || $currentSort !== 'latest')
                        <a href="{{ route('admin.tournaments.players.index', $tournament) }}" class="btn btn-outline-secondary" title="Reset filters"><i class="fa-solid fa-arrow-rotate-left"></i></a>
                    @endif
                </div>
            </form>

            @if ($registrations->isEmpty())
                <div class="p-5 text-center">
                    <span class="cricket-brand-mark mb-4"><i class="fa-solid fa-user-check"></i></span>
                    @if($currentSearch || $currentLocation)
                        <h3 class="h4 fw-bold text-white">No players match the criteria</h3>
                        <p class="text-secondary mb-0">Try adjusting your filters or search query.</p>
                    @else
                        <h3 class="h4 fw-bold text-white">The queue is clear</h3>
                        <p class="text-secondary mb-0">No player registrations are waiting for review.</p>
                    @endif
                </div>
            @else
                <div class="vstack gap-2">
                    @foreach ($registrations as $registration)
                        <div class="cricket-surface-soft p-3 p-lg-4">
                            <div class="row align-items-center g-3">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="cricket-brand-mark flex-shrink-0"><i class="fa-solid fa-user"></i></span>
                                        <div>
                                            <div class="fw-bold text-white">{{ $registration->playerProfile?->full_name }}</div>
                                            <div class="small text-secondary">{{ $registration->playerProfile?->user?->email }}</div>
                                            <div class="small text-white-50 mt-1"><i class="fa-solid fa-phone me-1"></i>{{ $registration->playerProfile?->phone ?: 'No phone' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-2">
                                    <div class="small text-secondary mb-1">Playing role</div>
                                    <div class="fw-bold text-white">{{ $registration->playerProfile?->playing_role ?: 'Not set' }}</div>
                                </div>
                                <div class="col-6 col-lg-2">
                                    <div class="small text-secondary mb-1">City</div>
                                    <div class="fw-bold text-white">{{ $registration->playerProfile?->city ?: 'Not set' }}</div>
                                </div>
                                <div class="col-6 col-lg-2">
                                    <div class="small text-secondary mb-1">Status</div>
                                    <span class="badge {{ $registration->status === 'approved' ? 'text-bg-success' : ($registration->status === 'rejected' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ ucfirst($registration->status) }}</span>
                                </div>
                                <div class="col-6 col-lg-2">
                                    <div class="d-flex justify-content-lg-end gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPlayerModal{{ $registration->id }}"><i class="fa-solid fa-pen me-1"></i>Edit</button>
                                        @if ($registration->status !== 'approved')
                                            <form method="POST" action="{{ route('admin.tournaments.players.approve', [$tournament, $registration]) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-success" type="submit"><i class="fa-solid fa-check me-1"></i>Approve</button>
                                            </form>
                                        @endif
                                        @if ($registration->status !== 'rejected')
                                            <form method="POST" action="{{ route('admin.tournaments.players.reject', [$tournament, $registration]) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-light text-danger" type="submit"><i class="fa-solid fa-xmark me-1"></i>Reject</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
        
        <div class="modal fade text-dark" id="editPlayerModal{{ $registration->id }}" tabindex="-1" aria-labelledby="editPlayerModalLabel{{ $registration->id }}" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg"><form method="POST" action="{{ route('admin.tournaments.players.update', [$tournament, $registration]) }}">@csrf @method('PUT')<div class="modal-header border-0 p-4 pb-0"><h5 class="modal-title fw-bold text-dark" id="editPlayerModalLabel{{ $registration->id }}">Edit Player Profile</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body p-4 text-start"><div class="mb-3"><label for="edit_full_name_{{ $registration->id }}" class="form-label small fw-bold text-dark">Full Name</label><input type="text" class="form-control" id="edit_full_name_{{ $registration->id }}" name="full_name" value="{{ $registration->playerProfile?->full_name }}" required></div><div class="mb-3"><label for="edit_phone_{{ $registration->id }}" class="form-label small fw-bold text-dark">Phone Number</label><input type="text" class="form-control" id="edit_phone_{{ $registration->id }}" name="phone" value="{{ $registration->playerProfile?->phone }}" required></div><div class="mb-3"><label for="edit_email_{{ $registration->id }}" class="form-label small fw-bold text-dark">Email Address</label><input type="email" class="form-control" id="edit_email_{{ $registration->id }}" name="email" value="{{ $registration->playerProfile?->user?->email }}" required></div><div class="mb-3"><label for="edit_location_{{ $registration->id }}" class="form-label small fw-bold text-dark">City/Location</label><input type="text" class="form-control" id="edit_location_{{ $registration->id }}" name="location" value="{{ $registration->playerProfile?->city }}" required></div><div class="mb-3"><label for="edit_playing_role_{{ $registration->id }}" class="form-label small fw-bold text-dark">Playing Role</label><select class="form-select" id="edit_playing_role_{{ $registration->id }}" name="playing_role" required>@foreach(['Batter', 'Bowler', 'All-rounder', 'Wicketkeeper'] as $role)<option value="{{ $role }}" @selected($registration->playerProfile?->playing_role === $role)>{{ $role }}</option>@endforeach</select></div><div class="mb-3"><label for="edit_status_{{ $registration->id }}" class="form-label small fw-bold text-dark">Registration Status</label><select class="form-select" id="edit_status_{{ $registration->id }}" name="status" required>@foreach(['approved' => 'Approved', 'rejected' => 'Rejected', 'pending' => 'Pending'] as $statusVal => $statusLabel)<option value="{{ $statusVal }}" @selected($registration->status === $statusVal)>{{ $statusLabel }}</option>@endforeach</select></div></div><div class="modal-footer border-0 p-4 pt-0"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save Changes <i class="fa-solid fa-floppy-disk ms-1"></i></button></div></form></div></div></div>
        @endforeach</div><div class="mt-4">{{ $registrations->links() }}</div>@endif</div>

        <div class="modal fade text-dark" id="createPlayerModal" tabindex="-1" aria-labelledby="createPlayerModalLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg"><form method="POST" action="{{ route('admin.tournaments.players.store', $tournament) }}">@csrf<div class="modal-header border-0 p-4 pb-0"><h5 class="modal-title fw-bold text-dark" id="createPlayerModalLabel">Add Player Manually</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body p-4 text-start"><div class="mb-3"><label for="create_full_name" class="form-label small fw-bold text-dark">Full Name</label><input type="text" class="form-control" id="create_full_name" name="full_name" required></div><div class="mb-3"><label for="create_phone" class="form-label small fw-bold text-dark">Phone Number</label><input type="text" class="form-control" id="create_phone" name="phone" required placeholder="e.g. +923001234567"></div><div class="mb-3"><label for="create_email" class="form-label small fw-bold text-dark">Email Address (Optional)</label><input type="email" class="form-control" id="create_email" name="email" placeholder="e.g. player@domain.com"><div class="form-text small text-muted">If left blank, a clean unique email will be auto-generated automatically.</div></div><div class="mb-3"><label for="create_location" class="form-label small fw-bold text-dark">City/Location</label><input type="text" class="form-control" id="create_location" name="location" required></div><div class="mb-3"><label for="create_playing_role" class="form-label small fw-bold text-dark">Playing Role</label><select class="form-select" id="create_playing_role" name="playing_role" required><option value="">Select role</option><option value="Batter">Batter</option><option value="Bowler">Bowler</option><option value="All-rounder">All-rounder</option><option value="Wicketkeeper">Wicketkeeper</option></select></div></div><div class="modal-footer border-0 p-4 pt-0"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Add Player <i class="fa-solid fa-plus ms-1"></i></button></div></form></div></div></div>
    </div>
</x-app-layout>
