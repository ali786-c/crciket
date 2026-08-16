<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }} · {{ $report['tournament']->name }}</title>
    <style>
        @page { margin: 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #153a2e; font-size: 11px; }
        h1 { margin: 0 0 4px; font-size: 22px; color: #075c46; }
        h2 { margin: 20px 0 8px; font-size: 15px; color: #075c46; border-bottom: 1px solid #d7e4dc; padding-bottom: 4px; }
        p { margin: 4px 0; }
        .muted { color: #6b7f75; }
        .meta { margin-bottom: 18px; }
        .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .brand img { width: 52px; height: 52px; object-fit: contain; }
        .brand-name { font-size: 17px; font-weight: bold; color: #075c46; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 14px; }
        th { background: #075c46; color: white; text-align: left; }
        th, td { padding: 6px 7px; border: 1px solid #d7e4dc; vertical-align: top; }
        tr:nth-child(even) td { background: #f4f8f5; }
        .stat { display: inline-block; width: 30%; margin: 0 2% 8px 0; padding: 9px; background: #eef6f0; border-radius: 5px; }
        .stat strong { display: block; font-size: 16px; color: #075c46; }
        .small { font-size: 9px; }
    </style>
</head>
<body>
    <div class="brand">@if($report['logo_data_uri'])<img src="{{ $report['logo_data_uri'] }}" alt="">@endif<div class="brand-name">{{ $report['tournament']->name }}</div></div>
    <h1>{{ $title }}</h1>
    <div class="meta">@if($report['tournament']->season_name)<strong>{{ $report['tournament']->season_name }}</strong><br>@endif<span class="muted">{{ $report['tournament']->venue ?: $report['tournament']->location ?: 'Tournament report' }}{{ $report['tournament']->city ? ', '.$report['tournament']->city : '' }} · Generated {{ now()->format('d M Y H:i') }}</span></div>

    @if ($type === 'summary')
        <h2>Tournament summary</h2>
        @foreach ($report['summary'] as $key => $value)
            <div class="stat"><span class="muted">{{ ucwords(str_replace('_', ' ', $key)) }}</span><strong>{{ is_scalar($value) ? ($value ?? '—') : '—' }}</strong></div>
        @endforeach
        <p class="muted">Venue: {{ $report['tournament']->venue ?: $report['tournament']->location ?: 'Not set' }}{{ $report['tournament']->city ? ', '.$report['tournament']->city : '' }}</p>
    @elseif ($type === 'history')
        <h2>Draft history</h2>
        <table><thead><tr><th>Pick</th><th>Round</th><th>Team</th><th>Player</th><th>Role</th><th>Status</th>@if($report['audience'] === 'admin')<th>Source</th>@endif</tr></thead><tbody>
        @foreach ($report['history'] as $pick)<tr><td>{{ $pick['pick_number'] }}</td><td>{{ $pick['round'] }}</td><td>{{ $pick['team'] }}</td><td>{{ $pick['player'] ?: '—' }}</td><td>{{ $pick['playing_role'] ?: '—' }}</td><td>{{ ucfirst($pick['status']) }}</td>@if($report['audience'] === 'admin')<td>{{ $pick['selected_by'] ?: '—' }}</td>@endif</tr>@endforeach
        </tbody></table>
    @elseif ($type === 'squads')
        <h2>Team squad report</h2>
        @foreach ($report['team_squads'] as $squad)
            <h3>{{ $squad['team'] }} ({{ $squad['selected_count'] }})</h3>
            <table><thead><tr><th>Pick</th><th>Player</th><th>Playing role</th></tr></thead><tbody>@foreach($squad['players'] as $player)<tr><td>{{ $player['pick_number'] }}</td><td>{{ $player['player'] }}</td><td>{{ $player['playing_role'] ?: '—' }}</td></tr>@endforeach</tbody></table>
        @endforeach
    @elseif ($type === 'registrations')
        <h2>Player registration report</h2>
        <table><thead><tr><th>Player</th><th>Role</th><th>Email</th><th>Status</th><th>Reviewed by</th><th>Reviewed at</th></tr></thead><tbody>@foreach($report['registrations'] as $registration)<tr><td>{{ $registration['player'] }}</td><td>{{ $registration['role'] ?: '—' }}</td><td>{{ $registration['email'] }}</td><td>{{ ucfirst($registration['status']) }}</td><td>{{ $registration['reviewed_by'] ?: '—' }}</td><td>{{ $registration['reviewed_at'] ?: '—' }}</td></tr>@endforeach</tbody></table>
    @elseif ($type === 'timer')
        <h2>Timer report</h2>
        @foreach ($report['timer'] as $key => $value)<div class="stat"><span class="muted">{{ ucwords(str_replace('_', ' ', $key)) }}</span><strong>{{ $value }}</strong></div>@endforeach
    @elseif ($type === 'audit')
        <h2>Audit report</h2>
        <table><thead><tr><th>Action</th><th>User</th><th>Timestamp</th><th>IP address</th><th>User agent</th></tr></thead><tbody>@foreach($report['audit_logs'] as $log)<tr><td>{{ $log['action'] }}</td><td>{{ $log['user'] ?: 'System' }}</td><td>{{ $log['created_at'] }}</td><td>{{ $log['ip_address'] ?: '—' }}</td><td class="small">{{ $log['user_agent'] ?: '—' }}</td></tr>@endforeach</tbody></table>
    @endif
</body>
</html>
