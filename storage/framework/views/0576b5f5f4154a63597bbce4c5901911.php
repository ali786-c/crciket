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
            <div><p class="cricket-kicker mb-2">Admin command center</p><h1 class="display-6 fw-bold mb-2">Good to see you, <?php echo e(explode(' ', auth()->user()->name)[0]); ?>.</h1><p class="text-secondary mb-0">Run the tournament from setup to the final pick with a clear view of every moving part.</p></div>
            <div class="d-flex gap-2"><a href="<?php echo e(route('admin.tournaments.create')); ?>" class="btn btn-success"><i class="fa-solid fa-plus me-2"></i>New tournament</a><a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-light"><i class="fa-solid fa-users-gear me-2"></i>Manage users</a></div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="container pb-5">
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3"><div class="cricket-surface p-4 h-100"><div class="d-flex justify-content-between align-items-start mb-4"><span class="cricket-brand-mark"><i class="fa-solid fa-trophy"></i></span><span class="small text-secondary fw-bold">ALL TIME</span></div><div class="text-secondary small mb-1">Total tournaments</div><div class="cricket-stat-value"><?php echo e($tournamentCount); ?></div><div class="small text-secondary mt-2">Your tournament portfolio</div></div></div>
            <div class="col-md-6 col-xl-3"><div class="cricket-surface p-4 h-100"><div class="d-flex justify-content-between align-items-start mb-4"><span class="cricket-brand-mark"><i class="fa-solid fa-users"></i></span><span class="small text-secondary fw-bold">PLATFORM</span></div><div class="text-secondary small mb-1">Registered accounts</div><div class="cricket-stat-value"><?php echo e($userCount); ?></div><div class="small text-secondary mt-2">Players, captains, admins</div></div></div>
            <div class="col-md-6 col-xl-3"><div class="cricket-surface p-4 h-100"><div class="d-flex justify-content-between align-items-start mb-4"><span class="cricket-brand-mark"><i class="fa-solid fa-signal"></i></span><span class="badge text-bg-success">Active</span></div><div class="text-secondary small mb-1">Active tournaments</div><div class="cricket-stat-value"><?php echo e($activeTournaments->count()); ?></div><div class="small text-secondary mt-2">Registration through live</div></div></div>
            <div class="col-md-6 col-xl-3"><div class="cricket-pitch-panel p-4 h-100"><div class="d-flex justify-content-between align-items-start mb-4"><span class="text-white fs-3"><i class="fa-solid fa-bolt"></i></span><span class="small text-white-50 fw-bold">NEXT MOVE</span></div><div class="small text-white-50 mb-1">Keep the room ready</div><div class="h4 fw-bold mb-2">Configure pick order</div><div class="small text-white-50">Assign exact numbers to teams before going live.</div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8"><div class="cricket-surface p-4 p-lg-5 h-100"><div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4"><div><p class="cricket-kicker mb-2">Tournament portfolio</p><h2 class="h3 fw-bold mb-1">Active tournaments</h2><p class="text-secondary mb-0">A quick view of everything currently in motion.</p></div><a href="<?php echo e(route('admin.tournaments.index')); ?>" class="btn btn-light btn-sm">View all <i class="fa-solid fa-arrow-right ms-1"></i></a></div>
                    <?php if($activeTournaments->isEmpty()): ?>
                        <div class="cricket-surface-soft p-4 text-center"><i class="fa-solid fa-trophy text-success fs-2 mb-3"></i><h3 class="h5 fw-bold">No active tournaments yet</h3><p class="text-secondary mb-3">Create a tournament to start building your draft room.</p><a href="<?php echo e(route('admin.tournaments.create')); ?>" class="btn btn-success">Create tournament</a></div>
                    <?php else: ?>
                        <div class="vstack gap-2"><?php $__currentLoopData = $activeTournaments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tournament): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a href="<?php echo e(route('admin.tournaments.show', $tournament)); ?>" class="text-decoration-none"><div class="cricket-surface-soft p-3 d-flex flex-column flex-md-row align-items-md-center gap-3"><span class="cricket-brand-mark flex-shrink-0"><i class="fa-solid fa-trophy"></i></span><div class="flex-grow-1"><div class="fw-bold text-dark"><?php echo e($tournament->name); ?></div><div class="small text-secondary"><?php echo e($tournament->location ?: 'Location not set'); ?> · <?php echo e($tournament->squad_size); ?> players per team</div></div><div class="d-flex align-items-center gap-3"><span class="badge text-bg-<?php echo e($tournament->status === 'live' ? 'success' : 'light'); ?>"><?php echo e(ucfirst($tournament->status)); ?></span><i class="fa-solid fa-arrow-right text-success"></i></div></div></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
                    <?php endif; ?>
                </div></div>
            <div class="col-xl-4"><div class="cricket-surface p-4 p-lg-5 h-100"><p class="cricket-kicker mb-2">Recommended flow</p><h2 class="h3 fw-bold mb-4">Your next moves</h2><div class="vstack gap-4"><div class="d-flex gap-3"><span class="badge text-bg-success rounded-circle p-2">1</span><div><div class="fw-bold">Build the tournament</div><div class="small text-secondary">Set squad size, dates, and the default timer.</div></div></div><div class="d-flex gap-3"><span class="badge text-bg-success rounded-circle p-2">2</span><div><div class="fw-bold">Prepare the teams</div><div class="small text-secondary">Add teams and assign one active captain to each.</div></div></div><div class="d-flex gap-3"><span class="badge text-bg-success rounded-circle p-2">3</span><div><div class="fw-bold">Open the room</div><div class="small text-secondary">Approve players, configure picks, and start live control.</div></div></div></div><div class="cricket-surface-soft p-3 mt-5"><div class="small text-secondary mb-1">Need to manage access?</div><a href="<?php echo e(route('admin.users.index')); ?>" class="fw-bold text-decoration-none">Open user management <i class="fa-solid fa-arrow-right ms-1"></i></a></div></div></div>
        </div>
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
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>