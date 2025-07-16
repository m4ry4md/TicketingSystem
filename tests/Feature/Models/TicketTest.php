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
 * Feature tests for the Ticket model.
 */
class TicketTest extends TestCase
{
    use ModelsTestTrait;

    /**
     * Specifies the model class name for the testing trait.
     *
     * @return string
     */
    protected function model(): string
    {
        return Ticket::class;
    }

    /**
     * Test the relationship to ensure a ticket belongs to a user.
     *
     * @return void
     */
    public function test_belongs_to_user_relationship(): void
    {
        $this->assertBelongsToRelationship(User::class, 'user');
    }

    /**
     * Test the relationship to ensure a ticket can have multiple replies.
     *
     * @return void
     */
    public function test_has_many_replies_relationship(): void
    {
        $this->assertHasManyRelationship(Reply::class, 'replies');
    }

    /**
     * Test that the 'uuid' column is correctly configured and holds a UUID value.
     * This is crucial as it's used for route model binding.
     *
     * @return void
     */
    public function test_uuid_column_is_uuid(): void
    {
        $this->assertColumnIsUuid('uuid');
    }

    /**
     * Test that the model's route key name is correctly set to 'uuid'.
     *
     * @return void
     */
    public function test_uses_uuid_for_route_key_name(): void
    {
        $this->assertRouteKeyNameIs('uuid');
    }


}
