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
            <div>
                <p class="cricket-kicker mb-2">Tournament configuration</p>
                <h1 class="display-6 fw-bold mb-2">Edit <?php echo e($tournament->name); ?></h1>
                <p class="text-secondary mb-0">Update public identity, registration timing, visibility, branding, and draft defaults.</p>
            </div>
            <a href="<?php echo e(route('admin.tournaments.show', $tournament)); ?>" class="btn btn-light"><i class="fa-solid fa-arrow-left me-2"></i>Back to workspace</a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="container pb-5">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger border-0 shadow-sm">Please review the highlighted fields and try again.</div>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('admin.tournaments.update', $tournament)); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('admin.tournaments._form', ['tournament' => $tournament], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="d-flex flex-column flex-md-row gap-2 mt-4">
                <button type="submit" class="btn btn-success btn-lg"><i class="fa-solid fa-floppy-disk me-2"></i>Save configuration</button>
                <a href="<?php echo e(route('admin.tournaments.show', $tournament)); ?>" class="btn btn-light btn-lg">Cancel</a>
            </div>
        </form>
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
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/admin/tournaments/edit.blade.php ENDPATH**/ ?>