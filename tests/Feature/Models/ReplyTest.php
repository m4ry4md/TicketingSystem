<?php

namespace Tests\Feature\Models;

use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;
use Tests\Helpers\TestingTraits\ModelsTestTrait;
use Tests\TestCase;

/**
 * Feature tests for the Reply model.
 */
class ReplyTest extends TestCase
{
    use ModelsTestTrait;

    /**
     * Specifies the model class name for the testing trait.
     *
     * @return string
     */
    protected function model(): string
    {
        return Reply::class;
    }

    /**
     * Test the relationship to ensure a reply belongs to a user.
     *
     * @return void
     */
    public function test_belongs_to_user_relationship(): void
    {
        $this->assertBelongsToRelationship(User::class, 'user');
    }

    /**
     * Test the relationship to ensure a reply belongs to a ticket.
     *
     * @return void
     */
    public function test_belongs_to_ticket_relationship(): void
    {
        $this->assertBelongsToRelationship(Ticket::class, 'ticket');
    }
}
