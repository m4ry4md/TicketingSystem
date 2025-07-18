<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/auth')
                ->group(base_path('routes/auth.php'));

            Route::middleware(['api', 'throttle:60,1','auth:sanctum', 'isAdmin', 'abilities:admin-panel:view'])
                ->prefix('api/admin')
                ->group(base_path('routes/admin.php'));


        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'apiGuest' => \App\Http\Middleware\ApiGuest::class,
            'isAdmin' => \App\Http\Middleware\IsAdmin::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
