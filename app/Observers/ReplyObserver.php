<?php

namespace App\Observers;

use App\Models\Reply;
use Illuminate\Support\Facades\Cache;

class ReplyObserver
{
    /**
     * Handle the Reply "created" event.
     */
    public function created(Reply $reply): void
    {
        $this->clearTicketCaches($reply);
    }

    /**
     * Handle the Reply "updated" event.
     */
    public function updated(Reply $reply): void
    {
        $this->clearTicketCaches($reply);
    }

    /**
     * Handle the Reply "deleted" event.
     */
    public function deleted(Reply $reply): void
    {
        $this->clearTicketCaches($reply);
    }

    /**
     * Clears the caches for the parent ticket.
     */
    protected function clearTicketCaches(Reply $reply): void
    {
        // When a reply is added/changed, the ticket list is affected.
        Cache::forget('tickets:all');
        if ($reply->ticket) {
            Cache::forget("tickets:user:{$reply->ticket->user_id}");
        }
    }
}
