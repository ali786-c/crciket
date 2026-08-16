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
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3"><div><p class="cricket-kicker mb-2">Identity governance</p><h1 class="display-6 fw-bold mb-2">Users and roles</h1><p class="text-secondary mb-0">Review every platform identity, inspect activity, manage role boundaries, and revoke API access when required.</p></div><a href="<?php echo e(route('super-admin.dashboard')); ?>" class="btn btn-light"><i class="fa-solid fa-arrow-left me-2"></i>Control plane</a></div>
     <?php $__env->endSlot(); ?>
    <div class="container pb-5">
        <div class="row g-3 mb-4">
            <?php $__currentLoopData = ['super_admin' => 'Super Admins', 'admin' => 'Administrators', 'captain' => 'Captains', 'player' => 'Players']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-xl-3"><a href="<?php echo e(route('super-admin.users.index', ['role' => $role])); ?>" class="cricket-surface p-4 h-100 text-decoration-none d-block"><div class="small text-secondary"><?php echo e($label); ?></div><div class="display-6 fw-bold text-dark"><?php echo e($roleCounts[$role] ?? 0); ?></div><div class="small text-success mt-2">Filter this role <i class="fa-solid fa-arrow-right ms-1"></i></div></a></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if(session('status')): ?><div class="alert alert-success border-0 shadow-sm"><?php echo e(session('status')); ?></div><?php endif; ?>
        <?php if($errors->any()): ?><div class="alert alert-danger border-0 shadow-sm"><?php echo e($errors->first()); ?></div><?php endif; ?>
        <div class="cricket-surface p-4 mb-4">
            <form method="GET" class="row g-2 align-items-end"><div class="col-lg-6"><label class="form-label small text-secondary">Search user</label><input type="search" name="search" value="<?php echo e($search); ?>" class="form-control" placeholder="Name or email"></div><div class="col-lg-3"><label class="form-label small text-secondary">Role</label><select name="role" class="form-select"><option value="">All roles</option><?php $__currentLoopData = ['super_admin' => 'Super Admin', 'admin' => 'Administrator', 'captain' => 'Captain', 'player' => 'Player']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($role); ?>" <?php if($selectedRole === $role): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div><div class="col-lg-3 d-flex gap-2"><button class="btn btn-success flex-grow-1"><i class="fa-solid fa-filter me-2"></i>Apply filters</button><a href="<?php echo e(route('super-admin.users.index')); ?>" class="btn btn-light">Reset</a></div></form>
        </div>
        <div class="cricket-surface overflow-hidden"><div class="d-flex justify-content-between align-items-center gap-3 p-4 border-bottom"><div><p class="cricket-kicker mb-1">Directory</p><h2 class="h4 fw-bold mb-0">Platform identities</h2></div><span class="small text-secondary"><?php echo e($users->total()); ?> total matches</span></div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>User</th><th>Role</th><th>API sessions</th><th>Audit events</th><th>Joined</th><th class="text-end">Action</th></tr></thead><tbody><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr><td><div class="fw-semibold"><?php echo e($user->name); ?></div><div class="small text-secondary"><?php echo e($user->email); ?></div></td><td><span class="badge <?php echo e($user->getRoleNames()->first() === 'super_admin' ? 'text-bg-dark' : 'text-bg-light'); ?>"><?php echo e(str_replace('_', ' ', ucfirst($user->getRoleNames()->first() ?: 'member'))); ?></span></td><td><?php echo e($user->tokens_count); ?></td><td><?php echo e($user->audit_logs_count); ?></td><td class="text-secondary"><?php echo e($user->created_at?->format('d M Y')); ?></td><td class="text-end"><a href="<?php echo e(route('super-admin.users.show', $user)); ?>" class="btn btn-sm btn-outline-success">Inspect</a></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="6" class="text-center py-5 text-secondary">No identities match the selected filters.</td></tr><?php endif; ?></tbody></table></div></div><div class="mt-3"><?php echo e($users->links()); ?></div>
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
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/super-admin/users/index.blade.php ENDPATH**/ ?>