<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel for admin panel updates
Broadcast::channel('tickets', function ($user) {
    return $user->hasRole(['support', 'super_admin']);
});

// Private channel for user ticket updates
Broadcast::channel('tickets.{ticketId}', function ($user, $ticketId) {
    $ticket = \App\Models\Ticket::find($ticketId);
    return $user->id === $ticket->user_id;
});
