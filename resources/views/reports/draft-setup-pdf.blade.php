<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rounds & Picks Setup · {{ $tournament->name }}</title>
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
    <h1>Draft Rounds & Picks Configuration</h1>
    <div class="meta">
        <strong>{{ $tournament->name }}</strong><br>
        <span class="muted">Generated {{ now()->format('d M Y H:i') }}</span>
    </div>
    
    @foreach ($draft->rounds as $round)
        <h2>{{ $round->name ?: 'Round ' . $round->round_number }}</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Pick #</th>
                    <th>Assigned Team</th>
                    <th style="width: 25%;">Timer Duration</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($round->picks->sortBy('pick_number') as $pick)
                    <tr>
                        <td><strong>#{{ $pick->pick_number }}</strong></td>
                        <td>{{ $pick->team?->name ?: 'Unassigned' }}</td>
                        <td>{{ $pick->pick_duration }} seconds</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>
