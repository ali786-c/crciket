<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Something went wrong</title><?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?></head>
<body class="bg-light"><main class="min-vh-100 d-flex align-items-center justify-content-center p-4"><div class="cricket-surface p-5 text-center" style="max-width: 560px"><div class="text-success display-4 fw-bold">500</div><h1 class="h3 fw-bold mt-3">Something went wrong</h1><p class="text-secondary">The application could not complete that request. Please try again or contact the tournament administrator.</p><a href="<?php echo e(url('/')); ?>" class="btn btn-success">Return home</a></div></main></body>
</html>
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/errors/500.blade.php ENDPATH**/ ?>