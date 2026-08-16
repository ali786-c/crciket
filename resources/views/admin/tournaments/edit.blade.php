<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
            <div>
                <p class="cricket-kicker mb-2">Tournament configuration</p>
                <h1 class="display-6 fw-bold mb-2">Edit {{ $tournament->name }}</h1>
                <p class="text-secondary mb-0">Update public identity, registration timing, visibility, branding, and draft defaults.</p>
            </div>
            <a href="{{ route('admin.tournaments.show', $tournament) }}" class="btn btn-light"><i class="fa-solid fa-arrow-left me-2"></i>Back to workspace</a>
        </div>
    </x-slot>

    <div class="container pb-5">
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">Please review the highlighted fields and try again.</div>
        @endif
        <form method="POST" action="{{ route('admin.tournaments.update', $tournament) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.tournaments._form', ['tournament' => $tournament])
            <div class="d-flex flex-column flex-md-row gap-2 mt-4">
                <button type="submit" class="btn btn-success btn-lg"><i class="fa-solid fa-floppy-disk me-2"></i>Save configuration</button>
                <a href="{{ route('admin.tournaments.show', $tournament) }}" class="btn btn-light btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
