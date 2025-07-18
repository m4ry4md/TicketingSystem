<?php

namespace App\Enums;

use App\Traits\EnumTrait;

/**
 * Defines the type of the sender for a ticket or a reply.
 */
enum SenderTypeEnum: string
{
    use EnumTrait;

    case USER = 'user';
    case ADMIN = 'admin';
    case SYSTEM = 'system';

    /**
     * Get the human-readable, localized label for the enum case.
     *
     * @return string
     */
    public function label(): string
    {
        return __('enums.sender_type.' . $this->value);
    }
}
