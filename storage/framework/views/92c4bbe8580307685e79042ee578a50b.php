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
     <?php $__env->slot('header', null, []); ?> <div><p class="cricket-kicker mb-2">API governance</p><h1 class="display-6 fw-bold mb-2">Register API client</h1><p class="text-secondary mb-0">Create a governed application record before connecting a mobile build.</p></div> <?php $__env->endSlot(); ?>
    <div class="container pb-5"><?php if($errors->any()): ?><div class="alert alert-danger border-0"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?><form method="POST" action="<?php echo e(route('super-admin.api-clients.store')); ?>" class="cricket-surface p-4 p-lg-5"><div class="row g-4"><div class="col-md-6"><label class="form-label fw-semibold">Client name</label><input class="form-control" name="name" value="<?php echo e(old('name')); ?>" placeholder="Cricket Draft Android App" required></div><div class="col-md-6"><label class="form-label fw-semibold">Slug</label><input class="form-control" name="slug" value="<?php echo e(old('slug')); ?>" placeholder="cricket-draft-android" required></div><div class="col-md-4"><label class="form-label fw-semibold">Platform</label><select class="form-select" name="platform" required><?php $__currentLoopData = ['android','ios','web','internal','other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $platform): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($platform); ?>" <?php if(old('platform') === $platform): echo 'selected'; endif; ?>><?php echo e(ucfirst($platform)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div><div class="col-md-4"><label class="form-label fw-semibold">App version</label><input class="form-control" name="version" value="<?php echo e(old('version')); ?>" placeholder="1.0.0"></div><div class="col-md-4"><label class="form-label fw-semibold">Requests per minute</label><input class="form-control" name="rate_limit_per_minute" type="number" min="10" max="10000" value="<?php echo e(old('rate_limit_per_minute', 120)); ?>" required></div><div class="col-12"><label class="form-label fw-semibold">Notes</label><textarea class="form-control" name="notes" rows="4"><?php echo e(old('notes')); ?></textarea></div></div><div class="d-flex justify-content-end gap-2 mt-4"><a href="<?php echo e(route('super-admin.api-clients.index')); ?>" class="btn btn-light">Cancel</a><button type="submit" class="btn btn-success">Register API client</button></div><?php echo csrf_field(); ?></form></div>
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
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/super-admin/api-clients/create.blade.php ENDPATH**/ ?>