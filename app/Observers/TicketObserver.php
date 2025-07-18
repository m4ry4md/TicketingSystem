<?php

namespace App\Observers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        $this->clearCaches($ticket);
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        $this->clearCaches($ticket);
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        $this->clearCaches($ticket);
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        $this->clearCaches($ticket);
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        $this->clearCaches($ticket);
    }

    /**
     * Clears all relevant caches for the ticket.
     */
    protected function clearCaches(Ticket $ticket): void
    {
        Cache::forget('tickets:all');
        Cache::forget("tickets:user:{$ticket->user_id}");
    }
}
