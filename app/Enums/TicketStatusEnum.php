<?php

namespace App\Enums;

use App\Traits\EnumTrait;

/**
 * Defines the possible statuses for a support ticket.
 */
enum TicketStatusEnum: string
{
    use EnumTrait;

    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case CLOSED = 'closed';

    /**
     * Get the human-readable, localized label for the enum case.
     *
     * It uses Laravel's translation helper to fetch the label
     * from the language files.
     *
     * @return string
     */
    public function label(): string
    {
        return __('enums.ticket_status.' . $this->value);
    }
}
