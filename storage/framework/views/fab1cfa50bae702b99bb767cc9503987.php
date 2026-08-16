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
     <?php $__env->slot('header', null, []); ?> 
        <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
            <div><p class="cricket-kicker mb-2">Admin reporting</p><h1 class="display-6 fw-bold mb-2"><?php echo e($report['tournament']->name); ?> reports</h1><p class="text-secondary mb-0">Complete operational reporting for draft history, squads, registrations, timers, and audit activity.</p></div>
            <a href="<?php echo e(route('admin.tournaments.show', $report['tournament'])); ?>" class="btn btn-light"><i class="fa-solid fa-arrow-left me-2"></i>Tournament workspace</a>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="container pb-5">
        <div class="row g-3 mb-4">
            <?php $__currentLoopData = [['teams','Teams'],['registered_players','Registered players'],['approved_players','Approved players'],['selected_players','Selected players'],['total_picks','Total picks'],['completed_picks','Completed picks']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$key,$label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-lg-2"><div class="cricket-surface p-3 h-100"><div class="small text-secondary"><?php echo e($label); ?></div><div class="cricket-stat-value mt-2"><?php echo e($report['summary'][$key]); ?></div></div></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="cricket-surface p-4 p-lg-5 mb-4"><div class="d-flex align-items-center justify-content-between gap-3 mb-4"><div><p class="cricket-kicker mb-2">Download center</p><h2 class="h3 fw-bold mb-1">Operational reports</h2><p class="text-secondary mb-0">Each PDF contains the complete admin-level data for its category.</p></div><i class="fa-solid fa-file-pdf text-danger fs-2"></i></div><div class="row g-3"><?php $__currentLoopData = $reportTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-md-6 col-lg-4"><div class="cricket-surface-soft p-3 d-flex align-items-center justify-content-between gap-3"><div><div class="fw-bold"><?php echo e($label); ?></div><div class="small text-secondary">PDF download</div></div><a href="<?php echo e(route('admin.tournaments.reports.pdf', [$report['tournament'], $type])); ?>" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-download me-1"></i>PDF</a></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div>
        <div class="row g-4"><div class="col-xl-7"><div class="cricket-surface p-4 h-100"><h2 class="h4 fw-bold mb-3">Team squads</h2><div class="vstack gap-3"><?php $__currentLoopData = $report['team_squads']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $squad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="cricket-surface-soft p-3"><div class="d-flex justify-content-between"><strong><?php echo e($squad['team']); ?></strong><span class="badge text-bg-success"><?php echo e($squad['selected_count']); ?></span></div><div class="small text-secondary mt-2"><?php $__empty_1 = true; $__currentLoopData = $squad['players']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $player): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php echo e($player['player']); ?> · <?php echo e($player['playing_role'] ?: 'Unassigned'); ?><?php if(!$loop->last): ?>, <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?> No players selected yet. <?php endif; ?></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div></div><div class="col-xl-5"><div class="cricket-surface p-4 h-100"><h2 class="h4 fw-bold mb-3">Timer health</h2><div class="vstack gap-3"><?php $__currentLoopData = $report['timer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="d-flex justify-content-between border-bottom pb-2"><span class="text-secondary"><?php echo e(ucwords(str_replace('_',' ',$key))); ?></span><strong><?php echo e($value); ?></strong></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div></div></div>
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
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>