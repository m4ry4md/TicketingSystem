<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes

Route::prefix('v1')->as('api.v1.auth.')
    ->group(function () {
        Route::middleware('apiGuest')->group(function () {
            Route::post('/register', [\App\Http\Controllers\Api\V1\Auth\RegisterController::class, 'register'])
                ->middleware(['throttle:register'])
                ->name('register');

            Route::post('/login', [\App\Http\Controllers\Api\V1\Auth\LoginController::class, 'login'])
                ->middleware(['throttle:login', 'throttle:login_with_same_email'])
                ->name('login');
        });

        Route::post('/logout', \App\Http\Controllers\Api\V1\Auth\LogoutController::class)
            ->middleware('auth:sanctum')
            ->name('logout');

    });

//admin auth route
Route::post('/admin/login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'login'])
    ->middleware(['apiGuest', 'throttle:login', 'throttle:login_with_same_email'])
    ->name('admin.auth.login');





