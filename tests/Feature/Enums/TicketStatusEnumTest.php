<?php

namespace Tests\Feature\Enums;

use App\Enums\TicketStatusEnum;
use Tests\Helpers\TestingTraits\EnumsTestTrait;
use Tests\TestCase;

/**
 * Tests the TicketStatusEnum to ensure all cases have valid and corresponding translations.
 */
class TicketStatusEnumTest extends TestCase
{
    use EnumsTestTrait;

    /**
     * Specifies the enum class that this test case is responsible for testing.
     *
     * @return string The fully qualified class name of the enum.
     */
    protected function enumClass(): string
    {
        return TicketStatusEnum::class;
    }

    /**
     * Defines the translation prefix used for the enum's labels in the language files.
     *
     * @return string The translation key prefix.
     */
    protected function translationPrefix(): string
    {
        return 'enums/ticket_status';
    }
}
