<?php

namespace Tests\Helpers\TestingTraits;

use Illuminate\Support\Facades\Lang;

trait EnumsTestTrait
{
    /**
     * The enum class being tested.
     * This method must be implemented by the test class.
     *
     * @return string The fully qualified class name of the Enum.
     */
    abstract protected function enumClass(): string;

    /**
     * The translation key prefix for the enum labels (e.g., 'auth.actions').
     * This method must be implemented by the test class.
     * The final key will be constructed as "prefix.case_value".
     *
     * @return string The translation string prefix.
     */
    abstract protected function translationPrefix(): string;

    /**
     * It ensures that each enum case has a valid and existing translation label.
     *
     * @return void
     */
    public function test_all_enum_cases_have_valid_labels_and_translations(): void
    {
        $enumClass = $this->enumClass();
        $prefix = $this->translationPrefix();

        // Ensure the enum class has cases to test.
        $this->assertNotEmpty($enumClass::cases(), "The enum class {$enumClass} has no cases to test.");

        foreach ($enumClass::cases() as $case) {
            // Construct the expected translation key based on the defined prefix and case value.
            $expectedTranslationKey = $prefix . '.' . $case->value;

            // 1. Assert that a translation exists for the constructed key.
            $this->assertTrue(
                Lang::has($expectedTranslationKey),
                "Translation missing for enum case [{$case->name}]. Expected key: [{$expectedTranslationKey}]"
            );

            // 2. Assert that the translated label is not an empty string.
            $this->assertNotEmpty(
                __($expectedTranslationKey),
                "Translated label for enum case [{$case->name}] is empty."
            );
        }
    }
}
