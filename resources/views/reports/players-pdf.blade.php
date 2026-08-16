<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Player Pool · {{ $tournament->name }}</title>
    <style>
        @page { margin: 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #153a2e; font-size: 11px; }
        h1 { margin: 0 0 4px; font-size: 22px; color: #075c46; }
        h2 { margin: 20px 0 8px; font-size: 15px; color: #075c46; border-bottom: 1px solid #d7e4dc; padding-bottom: 4px; }
        .muted { color: #6b7f75; }
        .meta { margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 14px; }
        th { background: #075c46; color: white; text-align: left; }
        th, td { padding: 8px 10px; border: 1px solid #d7e4dc; vertical-align: middle; }
        tr:nth-child(even) td { background: #f4f8f5; }
    </style>
</head>
<body>
    <div style="margin-bottom: 15px;">
        @if(isset($logoDataUri) && $logoDataUri)
            <img src="{{ $logoDataUri }}" style="width: 52px; height: 52px; object-fit: contain; vertical-align: middle; margin-right: 10px;">
        @endif
        <span style="font-size: 17px; font-weight: bold; color: #075c46; vertical-align: middle;">{{ $tournament->name }}</span>
    </div>
    <h1>Player Registrations & Approvals</h1>
    <div class="meta">
        @if($tournament->season_name)<strong>{{ $tournament->season_name }}</strong><br>@endif
        <span class="muted">{{ $tournament->venue ?: $tournament->location ?: 'Tournament report' }}{{ $tournament->city ? ', '.$tournament->city : '' }} · Generated {{ now()->format('d M Y H:i') }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th>Player Name</th>
                <th>Playing Role</th>
                <th>Email</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registrations as $index => $registration)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $registration->playerProfile?->full_name }}</strong></td>
                    <td>{{ $registration->playerProfile?->playing_role ?: '—' }}</td>
                    <td>{{ $registration->playerProfile?->user?->email ?: '—' }}</td>
                    <td>
                        @if ($registration->status === 'approved')
                            <span style="color: #0d5c3a; font-weight: bold;">Approved</span>
                        @elseif ($registration->status === 'rejected')
                            <span style="color: #a8233c; font-weight: bold;">Rejected</span>
                        @else
                            <span style="color: #b58900; font-weight: bold;">Pending</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
