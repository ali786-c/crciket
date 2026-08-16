<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($title); ?> · <?php echo e($report['tournament']->name); ?></title>
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
    <div class="brand"><?php if($report['logo_data_uri']): ?><img src="<?php echo e($report['logo_data_uri']); ?>" alt=""><?php endif; ?><div class="brand-name"><?php echo e($report['tournament']->name); ?></div></div>
    <h1><?php echo e($title); ?></h1>
    <div class="meta"><?php if($report['tournament']->season_name): ?><strong><?php echo e($report['tournament']->season_name); ?></strong><br><?php endif; ?><span class="muted"><?php echo e($report['tournament']->venue ?: $report['tournament']->location ?: 'Tournament report'); ?><?php echo e($report['tournament']->city ? ', '.$report['tournament']->city : ''); ?> · Generated <?php echo e(now()->format('d M Y H:i')); ?></span></div>

    <?php if($type === 'summary'): ?>
        <h2>Tournament summary</h2>
        <?php $__currentLoopData = $report['summary']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="stat"><span class="muted"><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></span><strong><?php echo e(is_scalar($value) ? ($value ?? '—') : '—'); ?></strong></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <p class="muted">Venue: <?php echo e($report['tournament']->venue ?: $report['tournament']->location ?: 'Not set'); ?><?php echo e($report['tournament']->city ? ', '.$report['tournament']->city : ''); ?></p>
    <?php elseif($type === 'history'): ?>
        <h2>Draft history</h2>
        <table><thead><tr><th>Pick</th><th>Round</th><th>Team</th><th>Player</th><th>Role</th><th>Status</th><?php if($report['audience'] === 'admin'): ?><th>Source</th><?php endif; ?></tr></thead><tbody>
        <?php $__currentLoopData = $report['history']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pick): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($pick['pick_number']); ?></td><td><?php echo e($pick['round']); ?></td><td><?php echo e($pick['team']); ?></td><td><?php echo e($pick['player'] ?: '—'); ?></td><td><?php echo e($pick['playing_role'] ?: '—'); ?></td><td><?php echo e(ucfirst($pick['status'])); ?></td><?php if($report['audience'] === 'admin'): ?><td><?php echo e($pick['selected_by'] ?: '—'); ?></td><?php endif; ?></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody></table>
    <?php elseif($type === 'squads'): ?>
        <h2>Team squad report</h2>
        <?php $__currentLoopData = $report['team_squads']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $squad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <h3><?php echo e($squad['team']); ?> (<?php echo e($squad['selected_count']); ?>)</h3>
            <table><thead><tr><th>Pick</th><th>Player</th><th>Playing role</th></tr></thead><tbody><?php $__currentLoopData = $squad['players']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $player): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($player['pick_number']); ?></td><td><?php echo e($player['player']); ?></td><td><?php echo e($player['playing_role'] ?: '—'); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tbody></table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php elseif($type === 'registrations'): ?>
        <h2>Player registration report</h2>
        <table><thead><tr><th>Player</th><th>Role</th><th>Email</th><th>Status</th><th>Reviewed by</th><th>Reviewed at</th></tr></thead><tbody><?php $__currentLoopData = $report['registrations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($registration['player']); ?></td><td><?php echo e($registration['role'] ?: '—'); ?></td><td><?php echo e($registration['email']); ?></td><td><?php echo e(ucfirst($registration['status'])); ?></td><td><?php echo e($registration['reviewed_by'] ?: '—'); ?></td><td><?php echo e($registration['reviewed_at'] ?: '—'); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tbody></table>
    <?php elseif($type === 'timer'): ?>
        <h2>Timer report</h2>
        <?php $__currentLoopData = $report['timer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="stat"><span class="muted"><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></span><strong><?php echo e($value); ?></strong></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php elseif($type === 'audit'): ?>
        <h2>Audit report</h2>
        <table><thead><tr><th>Action</th><th>User</th><th>Timestamp</th><th>IP address</th><th>User agent</th></tr></thead><tbody><?php $__currentLoopData = $report['audit_logs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($log['action']); ?></td><td><?php echo e($log['user'] ?: 'System'); ?></td><td><?php echo e($log['created_at']); ?></td><td><?php echo e($log['ip_address'] ?: '—'); ?></td><td class="small"><?php echo e($log['user_agent'] ?: '—'); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tbody></table>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/reports/pdf.blade.php ENDPATH**/ ?>