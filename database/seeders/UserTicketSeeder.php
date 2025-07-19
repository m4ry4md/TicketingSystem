<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Sequence;

class UserTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory()
            ->count(3)
            ->user()
            ->state(new Sequence(
                ['name' => 'Test User 1', 'email' => 'user1@example.com'],
                ['name' => 'Test User 2', 'email' => 'user2@example.com'],
                ['name' => 'Test User 3', 'email' => 'user3@example.com'],
            ))
            ->create([
                'password' => Hash::make('password'),
            ])
            ->each(function (User $user) {
                Ticket::factory()
                    ->count(rand(2, 5))
                    ->create([
                        'user_id' => $user->id,
                    ]);
            });
    }
}
