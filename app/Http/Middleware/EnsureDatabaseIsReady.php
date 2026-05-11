<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EnsureDatabaseIsReady
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check if database connection is working
            \DB::connection()->getPdo();
            
            // Check if users table exists
            if (!Schema::hasTable('users')) {
                // Try to run migrations
                try {
                    \Artisan::call('migrate', ['--force' => true]);
                } catch (\Exception $e) {
                    // If migrations fail, continue with error handling
                    \Log::error('Migration failed: ' . $e->getMessage());
                }
            }
            
            return $next($request);
        } catch (\Exception $e) {
            // If database connection fails, log and continue
            \Log::error('Database connection failed: ' . $e->getMessage());
            
            // For authentication routes, try to handle gracefully
            if ($request->is('login') || $request->is('register')) {
                return response()->view('errors.database', [], 500);
            }
            
            return $next($request);
        }
    }
}
