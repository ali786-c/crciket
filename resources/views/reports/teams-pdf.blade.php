<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Teams & Captains · {{ $tournament->name }}</title>
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
    <h1>Teams & Captains</h1>
    <div class="meta">
        <strong>{{ $tournament->name }}</strong><br>
        <span class="muted">Generated {{ now()->format('d M Y H:i') }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Team Name</th>
                <th>Short Name</th>
                <th>Display Order</th>
                <th>Captain Name</th>
                <th>Captain Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tournament->teams->sortBy('display_order') as $index => $team)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $team->name }}</strong></td>
                    <td>{{ $team->short_name ?: '—' }}</td>
                    <td>{{ $team->display_order }}</td>
                    <td>{{ $team->activeCaptain?->user?->name ?: 'No Captain Assigned' }}</td>
                    <td>{{ $team->activeCaptain?->user?->email ?: '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
