<?php

use App\Http\Controllers\Api\V1\TicketController;
use App\Models\Ticket;
use Illuminate\Support\Facades\Route;

Route::middleware(['api','auth:sanctum','throttle:60,1'])
    ->prefix('api/v1')
    ->as('api.v1.')
    ->group(function () {

        Route::apiResource('tickets', TicketController::class)
            ->middleware(['throttle:user_send_ticket'])
    ->middlewareFor('index', ['can:viewAny,' . Ticket::class])
    ->middlewareFor('store', ['can:create,' . Ticket::class])
    ->middlewareFor('show', ['can:view,ticket'])
    ->middlewareFor('update', ['can:update,ticket'])
            ->except(['destroy']);


    });

