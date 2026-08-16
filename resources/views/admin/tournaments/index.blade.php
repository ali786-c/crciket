<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3"><div><p class="cricket-kicker mb-2">Admin command center</p><h1 class="display-6 fw-bold mb-2">Tournament portfolio</h1><p class="text-secondary mb-0">Create, configure, and operate every cricket draft from one place.</p></div><a href="{{ route('admin.tournaments.create') }}" class="btn btn-success"><i class="fa-solid fa-plus me-2"></i>Create tournament</a></div>
    </x-slot>

    <div class="container pb-5">
        @if (session('status'))<div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>@endif
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4"><div class="d-flex align-items-center gap-2"><span class="badge text-bg-success">{{ $tournaments->total() }} total</span><span class="small text-secondary">Most recently updated first</span></div><div class="small text-secondary"><i class="fa-solid fa-circle-info me-1"></i>Status is controlled from each tournament workspace</div></div>

        @if ($tournaments->isEmpty())
            <div class="cricket-surface p-5 text-center"><span class="cricket-brand-mark mb-4"><i class="fa-solid fa-trophy"></i></span><h2 class="h3 fw-bold">Your portfolio is empty</h2><p class="text-secondary mb-4">Create the first tournament and start shaping the draft room.</p><a href="{{ route('admin.tournaments.create') }}" class="btn btn-success">Create first tournament <i class="fa-solid fa-arrow-right ms-2"></i></a></div>
        @else
            <div class="vstack gap-3">
                @foreach ($tournaments as $tournament)
                    <div class="cricket-surface p-3 p-lg-4"><div class="row align-items-center g-3"><div class="col-lg-5"><div class="d-flex align-items-center gap-3">@if($tournament->logo_path)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($tournament->logo_path) }}" alt="{{ $tournament->name }} logo" style="width:52px;height:52px;object-fit:cover;border-radius:16px;">@else<span class="cricket-brand-mark flex-shrink-0"><i class="fa-solid fa-trophy"></i></span>@endif<div><h2 class="h5 fw-bold mb-1">{{ $tournament->name }}</h2><div class="small text-secondary">{{ $tournament->season_name ?: 'Season not set' }} · /{{ $tournament->slug }}</div><div class="small text-secondary">{{ $tournament->venue ?: $tournament->location ?: 'Venue not set' }}{{ $tournament->city ? ', '.$tournament->city : '' }}</div></div></div></div><div class="col-6 col-lg-2"><div class="small text-secondary mb-1">Status</div><span class="badge text-bg-{{ $tournament->status === 'live' ? 'success' : 'light' }}">{{ ucfirst($tournament->status) }}</span><span class="badge {{ $tournament->is_public ? 'text-bg-success' : 'text-bg-secondary' }} ms-1">{{ $tournament->is_public ? 'Public' : 'Private' }}</span></div><div class="col-6 col-lg-2"><div class="small text-secondary mb-1">Squad size</div><div class="fw-bold">{{ $tournament->squad_size }} <span class="fw-normal text-secondary">players</span></div></div><div class="col-6 col-lg-2"><div class="small text-secondary mb-1">Pick timer</div><div class="fw-bold">{{ $tournament->default_pick_duration }} <span class="fw-normal text-secondary">sec</span></div></div><div class="col-6 col-lg-1 text-lg-end"><a href="{{ route('admin.tournaments.show', $tournament) }}" class="btn btn-light btn-sm rounded-circle p-0" style="width:2.5rem;height:2.5rem;" aria-label="Open tournament"><i class="fa-solid fa-arrow-up-right-from-square text-success"></i></a></div></div></div>
                @endforeach
            </div>
            <div class="mt-4">{{ $tournaments->links() }}</div>
        @endif
    </div>
</x-app-layout>
