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
     <?php $__env->slot('header', null, []); ?> <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3"><div><p class="cricket-kicker mb-2">Schedule management</p><h1 class="display-6 fw-bold mb-2">Create fixture</h1><p class="text-secondary mb-0"><?php echo e($tournament->name); ?> · Schedule the next competition match.</p></div><a href="<?php echo e(route('admin.tournaments.fixtures.index', $tournament)); ?>" class="btn btn-light"><i class="fa-solid fa-arrow-left me-2"></i>Schedule</a></div> <?php $__env->endSlot(); ?>
    <div class="container pb-5"><div class="mb-4"><p class="cricket-kicker mb-1">Fixture details</p><h2 class="h3 fw-bold mb-1">Set the match appointment</h2><p class="text-secondary mb-0">Both teams must be active members of this tournament.</p></div><?php echo $__env->make('admin.fixtures._form', ['action' => route('admin.tournaments.fixtures.store', $tournament), 'method' => 'POST'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
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
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/admin/fixtures/create.blade.php ENDPATH**/ ?>