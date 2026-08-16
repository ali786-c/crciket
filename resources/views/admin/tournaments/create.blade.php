<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="cricket-kicker mb-2">Tournament setup</p>
            <h1 class="display-6 fw-bold mb-2">Create a new tournament</h1>
            <p class="text-secondary mb-0">Configure the competition identity, public visibility, registration window, branding, and draft defaults.</p>
        </div>
    </x-slot>

    <div class="container pb-5">
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">Please review the highlighted fields and try again.</div>
        @endif
        <form method="POST" action="{{ route('admin.tournaments.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.tournaments._form')
            <div class="d-flex flex-column flex-md-row gap-2 mt-4">
                <button type="submit" class="btn btn-success btn-lg"><i class="fa-solid fa-plus me-2"></i>Create tournament</button>
                <a href="{{ route('admin.tournaments.index') }}" class="btn btn-light btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
