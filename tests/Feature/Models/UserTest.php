<?php

namespace Tests\Feature\Models;

use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\TestingTraits\ModelsTestTrait;
use Tests\TestCase;

/**
 * Feature tests for the User model.
 */
class UserTest extends TestCase
{
    use ModelsTestTrait;

    /**
     * Specifies the model class name for the testing trait.
     *
     * @return string
     */
    protected function model(): string
    {
        return User::class;
    }
    /**
     * Test that the 'role' column has a default value of 'user'.
     *
     * @return void
     */
    public function test_role_column_has_default_user_value(): void
    {
        $this->assertColumnHasDefaultValue('role', 'user');
    }

    /**
     * A user can have multiple tickets.
     *
     * @return void
     */
    public function test_has_many_tickets_relationship(): void
    {
        $this->assertHasManyRelationship(Ticket::class, 'tickets');
    }

    /**
     * A user can have multiple replies.
     *
     * @return void
     */
    public function test_has_many_replies_relationship(): void
    {
        $this->assertHasManyRelationship(Reply::class, 'replies');
    }
}
