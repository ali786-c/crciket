<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Page not found</title><?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?></head>
<body class="bg-light"><main class="min-vh-100 d-flex align-items-center justify-content-center p-4"><div class="cricket-surface p-5 text-center" style="max-width: 560px"><div class="text-success display-4 fw-bold">404</div><h1 class="h3 fw-bold mt-3">Page not found</h1><p class="text-secondary">The page you requested does not exist or is no longer available.</p><a href="<?php echo e(url('/')); ?>" class="btn btn-success">Return home</a></div></main></body>
</html>
<?php /**PATH C:\Users\Muhammad Aliyan\Downloads\cricket-draft-source\resources\views/errors/404.blade.php ENDPATH**/ ?>