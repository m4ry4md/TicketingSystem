<?php

namespace Database\Factories;

use App\Enums\SenderTypeEnum;
use App\Enums\TicketStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(6),
            'message' => $this->faker->paragraphs(3, true),
            'status' => TicketStatusEnum::OPEN->value,
            'sender_type' => SenderTypeEnum::USER->value,
        ];

    }

    public function open(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TicketStatusEnum::OPEN->value,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TicketStatusEnum::CLOSED->value,
        ]);
    }

    public function sentByUser(): static
    {
        return $this->state(fn(array $attributes) => [
            'sender_type' => SenderTypeEnum::USER->value,
        ]);
    }

    public function sentByAdmin(): static
    {
        return $this->state(fn(array $attributes) => [
            'sender_type' => SenderTypeEnum::USER->value,
        ]);
    }
}
