<?php if (isset($component)) { $__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.public-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="container py-5"><div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><p class="cricket-kicker mb-1"><?php echo e($tournament->name); ?></p><h1 class="display-6 fw-bold mb-2">Match center</h1><p class="text-secondary mb-0">Follow the tournament schedule and open live scorecards as matches begin.</p></div><a href="<?php echo e(route('public.standings', $tournament)); ?>" class="btn btn-outline-primary"><i class="fa-solid fa-ranking-star me-2"></i>Points table</a></div>
        <?php if($fixtures->isEmpty()): ?><div class="cricket-surface p-5 text-center"><span class="cricket-brand-mark mb-4"><i class="fa-solid fa-calendar-days"></i></span><h2 class="h4 fw-bold">Schedule coming soon</h2><p class="text-secondary mb-0">The tournament schedule has not been published yet.</p></div><?php else: ?><div class="vstack gap-3"><?php $__currentLoopData = $fixtures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fixture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="cricket-surface p-3 p-lg-4"><div class="row align-items-center g-4"><div class="col-lg-5"><div class="small text-secondary mb-1"><?php echo e($fixture->round_name ?: 'Tournament fixture'); ?> · Match <?php echo e($fixture->match_number ?: $fixture->id); ?></div><h2 class="h4 fw-bold mb-1"><?php echo e($fixture->homeTeam->name); ?> <span class="text-secondary fw-normal">vs</span> <?php echo e($fixture->awayTeam->name); ?></h2><div class="small text-secondary"><i class="fa-solid fa-location-dot me-1"></i><?php echo e($fixture->venue ?: 'Venue TBC'); ?><?php echo e($fixture->city ? ', '.$fixture->city : ''); ?></div></div><div class="col-6 col-lg-3"><div class="small text-secondary">Scheduled</div><div class="fw-semibold"><?php echo e($fixture->scheduled_at->setTimezone($fixture->timezone)->format('d M Y, H:i')); ?></div><div class="small text-secondary"><?php echo e($fixture->timezone); ?></div></div><div class="col-6 col-lg-2"><span class="badge <?php echo e($fixture->status === 'in_progress' ? 'text-bg-success' : ($fixture->status === 'completed' ? 'text-bg-primary' : ($fixture->status === 'cancelled' ? 'text-bg-danger' : 'text-bg-light'))); ?>"><?php echo e(str_replace('_', ' ', ucfirst($fixture->status))); ?></span></div><div class="col-lg-2 text-lg-end"><?php if($fixture->match && in_array($fixture->match->status, ['live', 'completed', 'result_pending', 'approved'], true)): ?><a href="<?php echo e(route('public.matches.show', $fixture->match)); ?>" class="btn btn-sm btn-success"><i class="fa-solid fa-chart-line me-1"></i>Scorecard</a><?php elseif($fixture->match): ?><span class="small text-secondary">Match preparation</span><?php else: ?><span class="small text-secondary">Upcoming</span><?php endif; ?></div></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b)): ?>
<?php $attributes = $__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b; ?>
<?php unset($__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b)): ?>
<?php $component = $__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b; ?>
<?php unset($__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/public/matches/index.blade.php ENDPATH**/ ?>