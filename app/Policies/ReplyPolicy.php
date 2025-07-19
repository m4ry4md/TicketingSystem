<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;

class ReplyPolicy
{
    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return bool|null
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super_admin', 'api')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return bool
     */
    public function viewAny(User $user, Ticket $ticket): bool
    {
        return $user->can('view', $ticket);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reply  $reply
     * @return bool
     */
    public function view(User $user, Reply $reply): bool
    {
        return $user->can('view', $reply->ticket);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return bool
     */
    public function create(User $user, Ticket $ticket): bool
    {
        return $user->can('reply_to_tickets') || $user->id === $ticket->user_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reply  $reply
     * @return bool
     */
    public function update(User $user, Reply $reply): bool
    {
        return $user->can('reply_to_tickets') || $user->id === $reply->user_id;
    }
}
