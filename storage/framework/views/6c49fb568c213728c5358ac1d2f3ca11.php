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
     <?php $__env->slot('header', null, []); ?> <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3"><div><p class="cricket-kicker mb-2">API governance</p><h1 class="display-6 fw-bold mb-2">API clients</h1><p class="text-secondary mb-0">Register and control web, mobile and internal applications.</p></div><div class="d-flex gap-2"><a href="<?php echo e(route('super-admin.dashboard')); ?>" class="btn btn-light">Control plane</a><a href="<?php echo e(route('super-admin.api-clients.create')); ?>" class="btn btn-success"><i class="fa-solid fa-plus me-2"></i>Register client</a></div></div> <?php $__env->endSlot(); ?>
    <div class="container pb-5"><?php if(session('status')): ?><div class="alert alert-success border-0"><?php echo e(session('status')); ?></div><?php endif; ?><div class="cricket-surface overflow-hidden"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Client</th><th>Platform</th><th>Version</th><th>Rate limit</th><th>Status</th><th>Last seen</th><th></th></tr></thead><tbody><?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr><td><strong><?php echo e($client->name); ?></strong><div class="small text-secondary"><?php echo e($client->slug); ?></div></td><td><?php echo e(ucfirst($client->platform)); ?></td><td><?php echo e($client->version ?: '—'); ?></td><td><?php echo e($client->rate_limit_per_minute); ?>/min</td><td><span class="badge <?php echo e($client->is_active ? 'text-bg-success' : 'text-bg-secondary'); ?>"><?php echo e($client->is_active ? 'Active' : 'Disabled'); ?></span></td><td class="text-secondary"><?php echo e($client->last_seen_at?->format('d M Y H:i') ?: 'Never'); ?></td><td class="text-end"><form method="POST" action="<?php echo e(route('super-admin.api-clients.toggle', $client)); ?>"><?php echo csrf_field(); ?><button class="btn btn-sm <?php echo e($client->is_active ? 'btn-outline-danger' : 'btn-outline-success'); ?>" type="submit"><?php echo e($client->is_active ? 'Disable' : 'Activate'); ?></button></form></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="7" class="text-center py-5 text-secondary">No API clients registered yet.</td></tr><?php endif; ?></tbody></table></div></div><div class="mt-3"><?php echo e($clients->links()); ?></div></div>
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
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/super-admin/api-clients/index.blade.php ENDPATH**/ ?>