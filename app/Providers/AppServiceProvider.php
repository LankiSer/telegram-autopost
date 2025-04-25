<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind GigaChatService
        $this->app->bind(\App\Services\GigaChatService::class, function ($app) {
            // Use authenticated user's credentials or fallback to first available
            $user = $app->make('auth')->user();
            $credentials = $user ? $user->gigaChatCredential : \App\Models\GigaChatCredential::first();
            
            return new \App\Services\GigaChatService($credentials);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
