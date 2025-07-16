<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('v1')->as('api.v1.auth.')
    ->middleware('apiGuest')
    ->group(function () {

    Route::post('/register', [\App\Http\Controllers\Api\V1\Auth\RegisterController::class, 'register'])->name('register');

});
