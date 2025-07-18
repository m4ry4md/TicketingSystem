<?php

namespace Database\Factories;

use App\Enums\SenderTypeEnum;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reply>
 */
class ReplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'message' => $this->faker->paragraph(2),
            'sender_type' => SenderTypeEnum::ADMIN->value,
        ];
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
