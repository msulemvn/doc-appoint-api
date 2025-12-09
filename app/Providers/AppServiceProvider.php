<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\JWTGuard;

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
        Auth::extend('jwt', fn ($app, $name, array $config) => new JWTGuard(
            $app['tymon.jwt'],
            $app['auth']->createUserProvider($config['provider']),
            $app['request']
        ));
    }
}
