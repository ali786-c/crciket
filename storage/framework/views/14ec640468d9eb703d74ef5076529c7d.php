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
     <?php $__env->slot('header', null, []); ?> <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3"><div><p class="cricket-kicker mb-2">Schedule management</p><h1 class="display-6 fw-bold mb-2">Fixtures & schedule</h1><p class="text-secondary mb-0"><?php echo e($tournament->name); ?> · Plan every match before match day.</p></div><div class="d-flex gap-2"><a href="<?php echo e(route('admin.tournaments.show', $tournament)); ?>" class="btn btn-light"><i class="fa-solid fa-arrow-left me-2"></i>Tournament</a><a href="<?php echo e(route('admin.tournaments.fixtures.create', $tournament)); ?>" class="btn btn-success"><i class="fa-solid fa-calendar-plus me-2"></i>Create fixture</a></div></div> <?php $__env->endSlot(); ?>
    <div class="container pb-5">
        <?php if(session('status')): ?><div class="alert alert-success border-0 shadow-sm"><?php echo e(session('status')); ?></div><?php endif; ?>
        <?php if($errors->any()): ?><div class="alert alert-danger border-0 shadow-sm"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>
        <div class="cricket-surface p-3 p-lg-4 mb-4"><div class="d-flex flex-wrap justify-content-between align-items-center gap-3"><div><p class="cricket-kicker mb-1">Match calendar</p><h2 class="h3 fw-bold mb-1">Upcoming and completed fixtures</h2><p class="text-secondary mb-0">Create an operational match from a scheduled fixture when squads are ready.</p></div><span class="badge text-bg-light"><?php echo e($fixtures->total()); ?> fixtures</span></div></div>
        <?php if($fixtures->isEmpty()): ?>
            <div class="cricket-surface p-5 text-center"><span class="cricket-brand-mark mb-4"><i class="fa-solid fa-calendar-days"></i></span><h3 class="h4 fw-bold">No fixtures yet</h3><p class="text-secondary mb-4">Start building the tournament schedule by adding the first match.</p><a href="<?php echo e(route('admin.tournaments.fixtures.create', $tournament)); ?>" class="btn btn-success">Create first fixture</a></div>
        <?php else: ?>
            <div class="vstack gap-3"><?php $__currentLoopData = $fixtures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fixture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $statusTransitions = match ($fixture->status) {
                        'scheduled' => ['postponed', 'cancelled'],
                        'postponed' => ['scheduled', 'cancelled'],
                        'in_progress' => ['completed', 'cancelled'],
                        default => [],
                    };
                ?>
                <div class="cricket-surface p-3 p-lg-4"><div class="row align-items-center g-4"><div class="col-lg-5"><div class="d-flex align-items-center gap-3"><span class="cricket-brand-mark flex-shrink-0"><i class="fa-solid fa-calendar-day"></i></span><div><div class="small text-secondary mb-1"><?php if($fixture->round_name): ?><?php echo e($fixture->round_name); ?> · <?php endif; ?> Match <?php echo e($fixture->match_number ?: $fixture->id); ?></div><h3 class="h5 fw-bold mb-1"><?php echo e($fixture->homeTeam->name); ?> <span class="text-secondary fw-normal">vs</span> <?php echo e($fixture->awayTeam->name); ?></h3><div class="small text-secondary"><?php echo e($fixture->venue ?: 'Venue TBC'); ?><?php echo e($fixture->city ? ', '.$fixture->city : ''); ?></div></div></div></div><div class="col-6 col-lg-3"><div class="small text-secondary mb-1">Scheduled</div><div class="fw-semibold"><?php echo e($fixture->scheduled_at->setTimezone($fixture->timezone)->format('d M Y, H:i')); ?></div><div class="small text-secondary"><?php echo e($fixture->timezone); ?></div></div><div class="col-6 col-lg-2"><span class="badge <?php echo e($fixture->status === 'scheduled' ? 'text-bg-primary' : ($fixture->status === 'in_progress' ? 'text-bg-warning' : ($fixture->status === 'completed' ? 'text-bg-success' : ($fixture->status === 'cancelled' ? 'text-bg-danger' : 'text-bg-secondary')))); ?>"><?php echo e(str_replace('_', ' ', ucfirst($fixture->status))); ?></span></div><div class="col-lg-2"><div class="d-flex justify-content-lg-end flex-wrap gap-2"><?php if($fixture->match): ?><a href="<?php echo e(route('admin.tournaments.matches.show', [$tournament, $fixture->match])); ?>" class="btn btn-sm btn-success">Open match</a><?php elseif(in_array($fixture->status, ['scheduled', 'postponed'], true)): ?><form method="POST" action="<?php echo e(route('admin.tournaments.fixtures.create-match', [$tournament, $fixture])); ?>"><?php echo csrf_field(); ?><button type="submit" class="btn btn-sm btn-success">Create match</button></form><?php endif; ?> <?php if(!$fixture->match): ?><a href="<?php echo e(route('admin.tournaments.fixtures.edit', [$tournament, $fixture])); ?>" class="btn btn-sm btn-light">Edit</a><?php endif; ?></div></div></div><?php if(count($statusTransitions)): ?><div class="border-top mt-3 pt-3 d-flex flex-wrap justify-content-between align-items-center gap-2"><span class="small text-secondary">Schedule status control</span><div class="d-flex gap-2"><?php $__currentLoopData = $statusTransitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nextStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><form method="POST" action="<?php echo e(route('admin.tournaments.fixtures.status', [$tournament, $fixture])); ?>"><?php echo csrf_field(); ?><input type="hidden" name="status" value="<?php echo e($nextStatus); ?>"><button type="submit" class="btn btn-sm <?php echo e($nextStatus === 'cancelled' ? 'btn-outline-danger' : 'btn-outline-secondary'); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $nextStatus))); ?></button></form><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div><?php endif; ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
            <div class="mt-4"><?php echo e($fixtures->links()); ?></div>
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
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/admin/fixtures/index.blade.php ENDPATH**/ ?>