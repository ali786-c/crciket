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
     <?php $__env->slot('header', null, []); ?> <div><p class="cricket-kicker text-success mb-1">Player account</p><h2 class="h3 fw-bold mb-0">Player profile</h2></div> <?php $__env->endSlot(); ?>
    <div class="container py-4"><div class="row justify-content-center"><div class="col-xl-8"><div class="cricket-surface p-4 p-lg-5">
        <?php if(session('status')): ?><div class="alert alert-success"><?php echo e(session('status')); ?></div><?php endif; ?>
        <form method="POST" action="<?php echo e(route('player.profile.update')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label" for="full_name">Full name</label><input class="form-control" id="full_name" name="full_name" value="<?php echo e(old('full_name', $profile?->full_name)); ?>" required></div>
                <div class="col-md-4"><label class="form-label" for="phone">Phone</label><input class="form-control" id="phone" name="phone" value="<?php echo e(old('phone', $profile?->phone)); ?>"></div>
                <div class="col-md-6"><label class="form-label" for="city">City</label><input class="form-control" id="city" name="city" value="<?php echo e(old('city', $profile?->city)); ?>"></div>
                <div class="col-md-6"><label class="form-label" for="playing_role">Playing role</label><select class="form-select" id="playing_role" name="playing_role" required><option value="">Select playing role</option><?php $__currentLoopData = ['Batter', 'Bowler', 'All-rounder', 'Wicketkeeper']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($role); ?>" <?php if(old('playing_role', $profile?->playing_role) === $role): echo 'selected'; endif; ?>><?php echo e($role); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><div class="form-text">This role appears beside your name for captains during the live draft.</div></div>
                <div class="col-md-6"><label class="form-label" for="batting_style">Batting style</label><input class="form-control" id="batting_style" name="batting_style" value="<?php echo e(old('batting_style', $profile?->batting_style)); ?>"></div>
                <div class="col-md-6"><label class="form-label" for="bowling_style">Bowling style</label><input class="form-control" id="bowling_style" name="bowling_style" value="<?php echo e(old('bowling_style', $profile?->bowling_style)); ?>"></div>
                <div class="col-12"><label class="form-label" for="bio">Bio</label><textarea class="form-control" id="bio" name="bio" rows="4"><?php echo e(old('bio', $profile?->bio)); ?></textarea></div>
            </div>
            <div class="d-flex justify-content-end mt-4"><button class="btn btn-success" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Save profile</button></div>
        </form>
    </div></div></div></div>
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
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/player/profile.blade.php ENDPATH**/ ?>