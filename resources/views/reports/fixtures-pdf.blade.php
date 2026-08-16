<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fixtures & Schedule · {{ $tournament->name }}</title>
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
    <h1>Fixtures & Schedule</h1>
    <div class="meta">
        <strong>{{ $tournament->name }}</strong><br>
        <span class="muted">Generated {{ now()->format('d M Y H:i') }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Match #</th>
                <th>Round</th>
                <th>Home Team</th>
                <th>Away Team</th>
                <th>Venue / City</th>
                <th>Scheduled At</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fixtures->sortBy('match_number') as $fixture)
                <tr>
                    <td><strong>#{{ $fixture->match_number }}</strong></td>
                    <td>{{ $fixture->round_name ?: 'Round ' . $fixture->round_number }}</td>
                    <td>{{ $fixture->homeTeam?->name }}</td>
                    <td>{{ $fixture->awayTeam?->name }}</td>
                    <td>{{ $fixture->venue ?: '—' }}{{ $fixture->city ? ', ' . $fixture->city : '' }}</td>
                    <td>{{ $fixture->scheduled_at?->format('d M Y H:i') }} ({{ $fixture->timezone }})</td>
                    <td>
                        @if ($fixture->status === 'completed')
                            <span style="color: #0d5c3a; font-weight: bold;">Completed</span>
                        @elseif ($fixture->status === 'in_progress')
                            <span style="color: #0056b3; font-weight: bold;">In Progress</span>
                        @elseif ($fixture->status === 'cancelled')
                            <span style="color: #a8233c; font-weight: bold;">Cancelled</span>
                        @else
                            <span style="color: #6c757d;">{{ ucfirst($fixture->status) }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
