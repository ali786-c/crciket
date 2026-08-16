<?php

namespace App\Providers;

use Illuminate\Foundation\Vite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('api-auth', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));

        if (app()->environment('production') && (bool) config('app.debug')) {
            throw new \RuntimeException('Production deployments must set APP_DEBUG=false.');
        }

        if ((bool) env('APP_FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        if ((bool) env('APP_RELATIVE_ASSETS', false)) {
            app(Vite::class)->createAssetPathsUsing(
                fn (string $path): string => '/'.ltrim($path, '/')
            );
        }
    }
}
