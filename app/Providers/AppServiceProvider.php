<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        
        // Auto-run migrations if database is connected but tables don't exist
        try {
            if (\DB::connection()->getPdo()) {
                if (!\Schema::hasTable('users')) {
                    \Artisan::call('migrate', ['--force' => true]);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't break the application
            \Log::error('Auto-migration failed: ' . $e->getMessage());
        }
    }
}