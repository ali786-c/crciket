<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> <div><p class="cricket-kicker mb-2">Captain workspace</p><h1 class="display-6 fw-bold mb-2">Your draft rooms.</h1><p class="text-secondary mb-0">See your assigned teams, watch the tournament state, and be ready when the clock starts.</p></div> <?php $__env->endSlot(); ?>

    <div class="container pb-5">
        <div class="cricket-pitch-panel p-4 p-lg-5 mb-4"><div class="row align-items-center g-4"><div class="col-lg-8"><p class="cricket-kicker mb-2">Captain briefing</p><h2 class="h2 fw-bold mb-2">Your call is the only call that matters on your turn.</h2><p class="text-white-50 mb-0">The server checks your team assignment, the active pick, player approval, and the timer before accepting every selection.</p></div><div class="col-lg-4 text-lg-end"><i class="fa-solid fa-shield-halved" style="font-size:5rem; color:var(--cricket-lime); opacity:.85;"></i></div></div></div>
        <div class="d-flex align-items-center justify-content-between gap-3 mb-4"><div><p class="cricket-kicker mb-2">Assignments</p><h2 class="h3 fw-bold mb-1">Your tournaments</h2><p class="text-secondary mb-0">Only active assignments appear here.</p></div><span class="badge text-bg-light"><?php echo e($tournaments->count()); ?> assigned</span></div>
        <?php if($tournaments->isEmpty()): ?>
            <div class="cricket-surface p-5 text-center"><span class="cricket-brand-mark mb-4"><i class="fa-solid fa-hourglass-half"></i></span><h3 class="h4 fw-bold">No captain assignment yet</h3><p class="text-secondary mb-0">An administrator will assign you to a team before draft day.</p></div>
        <?php else: ?>
            <div class="row g-4">
                <?php $__currentLoopData = $tournaments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tournament): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $teamNames = $assignments->filter(fn ($assignment) => $assignment->team?->tournament_id === $tournament->id)->pluck('team.name')->filter()->join(', ');
                        $captainReport = $reports[$tournament->id] ?? null;
                    ?>
                    <div class="col-lg-6"><div class="cricket-surface p-4 p-lg-5 h-100 d-flex flex-column"><div class="d-flex align-items-start justify-content-between gap-3 mb-4"><span class="cricket-brand-mark"><i class="fa-solid fa-users"></i></span><span class="badge text-bg-<?php echo e($tournament->status === 'live' ? 'success' : 'light'); ?>"><?php echo e(ucfirst($tournament->status)); ?></span></div><div class="small text-secondary mb-1">Assigned team<?php echo e($teamNames ? '' : 's'); ?></div><h3 class="h3 fw-bold mb-2"><?php echo e($teamNames ?: 'Team assignment'); ?></h3><p class="text-secondary mb-4"><?php echo e($tournament->name); ?></p><?php if($captainReport): ?><div class="cricket-surface-soft p-3 mb-4"><div class="d-flex align-items-center justify-content-between gap-2 mb-3"><div><div class="small text-secondary">Selected players</div><div class="fw-bold"><?php echo e($captainReport['team_squads']->first()['selected_count'] ?? 0); ?> selected</div></div><a href="<?php echo e(route('captain.reports.pdf', [$tournament, 'squads'])); ?>" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-download me-1"></i>Download team PDF</a></div><div class="vstack gap-2"><?php $__empty_1 = true; $__currentLoopData = ($captainReport['team_squads']->first()['players'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $player): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><div class="bg-white rounded-3 p-2 d-flex align-items-center justify-content-between"><span class="fw-bold text-truncate"><?php echo e($player['player']); ?></span><span class="small text-secondary ms-2"><?php echo e($player['playing_role'] ?: 'Unassigned'); ?></span></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><div class="small text-secondary">No player selected yet.</div><?php endif; ?></div></div><?php endif; ?><div class="cricket-surface-soft p-3 mb-4"><div class="d-flex justify-content-between small"><span class="text-secondary">Tournament state</span><strong><?php echo e(ucfirst($tournament->status)); ?></strong></div><div class="d-flex justify-content-between small mt-2"><span class="text-secondary">Draft room</span><strong><?php echo e($tournament->draft ? ucfirst($tournament->draft->status) : 'Not configured'); ?></strong></div></div><div class="mt-auto"><?php if($tournament->status === 'live' && $tournament->draft): ?><a href="<?php echo e(route('captain.draft.show', $tournament)); ?>" class="btn btn-success w-100"><i class="fa-solid fa-tower-broadcast me-2"></i>Open live draft room</a><?php else: ?><div class="small text-secondary"><i class="fa-solid fa-clock me-2 text-success"></i>Waiting for the administrator to start the live draft.</div><?php endif; ?></div></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/captain/dashboard.blade.php ENDPATH**/ ?>