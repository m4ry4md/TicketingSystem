<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AttachmentController;
use App\Livewire\Admin\Tickets\ShowTicket;
use App\Livewire\Admin\Tickets\TicketsList;
use App\Models\Ticket;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');


    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', TicketsList::class)->name('index')->middleware('can:viewAny,' . Ticket::class);
        Route::get('/{ticket}', ShowTicket::class)->name('show')->middleware('can:view,ticket');
    });


});

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });
});

Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');


Route::get('attachments/{media}', [AttachmentController::class, 'show'])->name('attachments.show');
