<?php
    $isEdit = isset($tournament);
    $value = fn (string $key, mixed $default = null): mixed => old($key, $isEdit ? data_get($tournament, $key, $default) : $default);
?>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="cricket-surface p-4 p-lg-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="cricket-brand-mark"><i class="fa-solid fa-trophy"></i></span>
                <div>
                    <p class="cricket-kicker mb-1">Tournament identity</p>
                    <h2 class="h4 fw-bold mb-1">Competition profile</h2>
                    <p class="small text-secondary mb-0">The public-facing identity and location of this tournament.</p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="name" class="form-label">Tournament name</label>
                    <input id="name" name="name" type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($value('name')); ?>" placeholder="Lahore Premier Cup" required autofocus>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-4">
                    <label for="season_name" class="form-label">Season</label>
                    <input id="season_name" name="season_name" type="text" class="form-control <?php $__errorArgs = ['season_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($value('season_name')); ?>" placeholder="2026 Season">
                    <?php $__errorArgs = ['season_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-7">
                    <label for="slug" class="form-label">URL slug</label>
                    <input id="slug" name="slug" type="text" class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($value('slug')); ?>" placeholder="lahore-premier-cup" required>
                    <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-5">
                    <label for="timezone" class="form-label">Timezone</label>
                    <select id="timezone" name="timezone" class="form-select <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <?php $__currentLoopData = timezone_identifiers_list(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timezone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($timezone); ?>" <?php if($value('timezone', 'Asia/Karachi') === $timezone): echo 'selected'; endif; ?>><?php echo e($timezone); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Description <span class="text-secondary fw-normal">Optional</span></label>
                    <textarea id="description" name="description" rows="4" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="A short note about this tournament..."><?php echo e($value('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-4">
                    <label for="venue" class="form-label">Venue</label>
                    <input id="venue" name="venue" type="text" class="form-control <?php $__errorArgs = ['venue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($value('venue')); ?>" placeholder="Gaddafi Stadium">
                    <?php $__errorArgs = ['venue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-4">
                    <label for="city" class="form-label">City</label>
                    <input id="city" name="city" type="text" class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($value('city')); ?>" placeholder="Lahore">
                    <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-4">
                    <label for="location" class="form-label">Legacy location</label>
                    <input id="location" name="location" type="text" class="form-control <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($value('location')); ?>" placeholder="Punjab, Pakistan">
                    <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6">
                    <label for="starts_on" class="form-label">Tournament start date</label>
                    <input id="starts_on" name="starts_on" type="date" class="form-control <?php $__errorArgs = ['starts_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('starts_on', $isEdit && $tournament->starts_on ? $tournament->starts_on->format('Y-m-d') : '')); ?>">
                    <?php $__errorArgs = ['starts_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6">
                    <label for="ends_on" class="form-label">Tournament end date</label>
                    <input id="ends_on" name="ends_on" type="date" class="form-control <?php $__errorArgs = ['ends_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('ends_on', $isEdit && $tournament->ends_on ? $tournament->ends_on->format('Y-m-d') : '')); ?>">
                    <?php $__errorArgs = ['ends_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="cricket-surface p-4 p-lg-5 h-100">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="cricket-brand-mark"><i class="fa-solid fa-stopwatch"></i></span>
                <div>
                    <p class="cricket-kicker mb-1">Draft defaults</p>
                    <h2 class="h4 fw-bold mb-1">Operating rules</h2>
                    <p class="small text-secondary mb-0">Defaults used while building the draft.</p>
                </div>
            </div>
            <div class="mb-3">
                <label for="squad_size" class="form-label">Players per team</label>
                <input id="squad_size" name="squad_size" type="number" min="1" max="99" class="form-control <?php $__errorArgs = ['squad_size'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($value('squad_size', 3)); ?>" required>
                <?php $__errorArgs = ['squad_size'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="mb-4">
                <label for="default_pick_duration" class="form-label">Default pick timer</label>
                <div class="input-group">
                    <input id="default_pick_duration" name="default_pick_duration" type="number" min="5" max="3600" class="form-control <?php $__errorArgs = ['default_pick_duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($value('default_pick_duration', 60)); ?>" required>
                    <span class="input-group-text">seconds</span>
                </div>
                <?php $__errorArgs = ['default_pick_duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="mb-4">
                <label for="default_overs_per_innings" class="form-label">Default overs per innings</label>
                <div class="input-group">
                    <input id="default_overs_per_innings" name="default_overs_per_innings" type="number" min="1" max="100" class="form-control <?php $__errorArgs = ['default_overs_per_innings'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($value('default_overs_per_innings', '')); ?>">
                    <span class="input-group-text">overs</span>
                </div>
                <?php $__errorArgs = ['default_overs_per_innings'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="small text-secondary mt-2">Leave blank to use the selected cricket rule profile. Every new match can override this value.</div>
            </div>
            <div class="mb-4">
                <label for="cricket_rule_profile_id" class="form-label">Match format and cricket rules</label>
                <select id="cricket_rule_profile_id" name="cricket_rule_profile_id" class="form-select <?php $__errorArgs = ['cricket_rule_profile_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="">Select rule profile later</option>
                    <?php $__currentLoopData = $ruleProfiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($profile->id); ?>" <?php if((string) $value('cricket_rule_profile_id', '') === (string) $profile->id): echo 'selected'; endif; ?>><?php echo e($profile->name); ?> · <?php echo e($profile->overs_per_innings); ?> overs · XI <?php echo e($profile->playing_xi_size); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['cricket_rule_profile_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="small text-secondary mt-2">This controls innings, overs, playing XI size, wickets, extras, and points. It locks after draft setup begins.</div>
            </div>
            <div class="form-check form-switch mb-4">
                <input type="hidden" name="is_public" value="0">
                <input id="is_public" name="is_public" type="checkbox" class="form-check-input" value="1" <?php if((bool) $value('is_public', true)): echo 'checked'; endif; ?>>
                <label for="is_public" class="form-check-label fw-bold">Visible on public pages</label>
                <div class="small text-secondary">Hide private drafts from the homepage and live center.</div>
            </div>
            <div class="cricket-surface-soft p-3 mb-4">
                <div class="small fw-bold mb-2"><i class="fa-solid fa-lock text-success me-2"></i>Workflow protection</div>
                <div class="small text-secondary">Squad size, default timer, match rules, and default overs become locked after draft setup begins. Admin can still move the tournament to any lifecycle status, and each match may use a custom over limit.</div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="cricket-surface p-4 p-lg-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="cricket-brand-mark"><i class="fa-solid fa-calendar-days"></i></span>
                <div>
                    <p class="cricket-kicker mb-1">Registration window</p>
                    <h2 class="h4 fw-bold mb-1">Control player sign-ups</h2>
                    <p class="small text-secondary mb-0">Leave blank when registration timing is managed outside the system.</p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="registration_opens_at" class="form-label">Registration opens</label>
                    <input id="registration_opens_at" name="registration_opens_at" type="datetime-local" class="form-control <?php $__errorArgs = ['registration_opens_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('registration_opens_at', $isEdit && $tournament->registration_opens_at ? $tournament->registration_opens_at->format('Y-m-d\TH:i') : '')); ?>">
                    <?php $__errorArgs = ['registration_opens_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6">
                    <label for="registration_closes_at" class="form-label">Registration closes</label>
                    <input id="registration_closes_at" name="registration_closes_at" type="datetime-local" class="form-control <?php $__errorArgs = ['registration_closes_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('registration_closes_at', $isEdit && $tournament->registration_closes_at ? $tournament->registration_closes_at->format('Y-m-d\TH:i') : '')); ?>">
                    <?php $__errorArgs = ['registration_closes_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="cricket-surface p-4 p-lg-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="cricket-brand-mark"><i class="fa-solid fa-images"></i></span>
                <div>
                    <p class="cricket-kicker mb-1">Branding</p>
                    <h2 class="h4 fw-bold mb-1">Logo and banner</h2>
                    <p class="small text-secondary mb-0">JPG, PNG or WebP. Logo up to 5 MB; banner up to 10 MB.</p>
                </div>
            </div>
            <div class="row g-4 align-items-end">
                <div class="col-md-6">
                    <label for="logo" class="form-label">Tournament logo</label>
                    <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp" class="form-control <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php if($isEdit && $tournament->logo_path): ?>
                        <div class="d-flex align-items-center gap-3 mt-3"><img src="<?php echo e(Storage::disk('public')->url($tournament->logo_path)); ?>" alt="Tournament logo" style="width:64px;height:64px;object-fit:cover;border-radius:16px;"><div class="form-check"><input id="remove_logo" name="remove_logo" type="checkbox" value="1" class="form-check-input"><label for="remove_logo" class="form-check-label small">Remove current logo</label></div></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="banner" class="form-label">Tournament banner</label>
                    <input id="banner" name="banner" type="file" accept="image/jpeg,image/png,image/webp" class="form-control <?php $__errorArgs = ['banner'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['banner'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php if($isEdit && $tournament->banner_path): ?>
                        <div class="d-flex align-items-center gap-3 mt-3"><img src="<?php echo e(Storage::disk('public')->url($tournament->banner_path)); ?>" alt="Tournament banner" style="width:120px;height:64px;object-fit:cover;border-radius:16px;"><div class="form-check"><input id="remove_banner" name="remove_banner" type="checkbox" value="1" class="form-check-input"><label for="remove_banner" class="form-check-label small">Remove current banner</label></div></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/admin/tournaments/_form.blade.php ENDPATH**/ ?>