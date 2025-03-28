<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function ($request) {
            if ($request->user() && $request->user()->role === 'admin') {
                return Limit::none();
            }
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
