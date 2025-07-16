<?php

namespace App\Traits;

/**
 * Provides helper methods for PHP 8.1+ backed enums.
 *
 * This trait allows for easy retrieval of enum names, values,
 * or a combined associative array, provides a convenient way to
 * validate enum values, and requires enums to define a human-readable label.
 */
trait EnumTrait
{
    /**
     * Get all of the case names for the enum.
     *
     * @return array<int, string>
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all of the case values for the enum.
     *
     * @return array<int, string|int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get an associative array of the enum's cases (value => name).
     *
     * @return array<string|int, string>
     */
    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }

    /**
     * Check if the given value is a valid enum case value.
     *
     * @param string|int $value The value to check.
     * @return bool True if the value is valid, false otherwise.
     */
    public static function isValidValue($value): bool
    {
        return in_array($value, self::values(), true);
    }

}
