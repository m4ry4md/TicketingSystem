<?php

namespace Tests\Helpers\TestingTraits;

use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

trait ReplyPolicyTestTrait
{
    /**
     * Define the route for the index endpoint.
     * @param Ticket $ticket
     * @return string
     */
    abstract protected function getIndexRoute(Ticket $ticket): string;

    /**
     * Define the route for the store endpoint.
     * @param Ticket $ticket
     * @return string
     */
    abstract protected function getStoreRoute(Ticket $ticket): string;

    /**
     * Define the route for the show endpoint.
     * @param Reply $reply
     * @return string
     */
    abstract protected function getShowRoute(Reply $reply): string;

    /**
     * Define the route for the update endpoint.
     * @param Reply $reply
     * @return string
     */
    abstract protected function getUpdateRoute(Reply $reply): string;

    //==================================================
    // Access Control / Policy Tests
    //==================================================

    public function test_unauthenticated_user_cannot_view_replies(): void
    {
        $ticket = Ticket::factory()->create();
        $this->getJson($this->getIndexRoute($ticket))->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_create_a_reply(): void
    {
        $ticket = Ticket::factory()->create();
        $this->postJson($this->getStoreRoute($ticket), [])->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_view_a_reply(): void
    {
        $reply = Reply::factory()->create();
        $this->getJson($this->getShowRoute($reply))->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_update_a_reply(): void
    {
        $reply = Reply::factory()->create();
        $this->putJson($this->getUpdateRoute($reply), [])->assertUnauthorized();
    }

    public function test_user_can_view_replies_for_their_own_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);
        Reply::factory()->count(3)->create(['ticket_id' => $ticket->id, 'user_id' => $user->id]);

        Sanctum::actingAs($user);

        $this->getJson($this->getIndexRoute($ticket))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_cannot_view_replies_for_another_users_ticket(): void
    {
        $user = User::factory()->create();
        $otherUserTicket = Ticket::factory()->create(); // Belongs to another user
        Reply::factory()->count(2)->create(['ticket_id' => $otherUserTicket->id]);

        Sanctum::actingAs($user);

        $this->getJson($this->getIndexRoute($otherUserTicket))->assertForbidden();
    }


    public function test_user_can_view_their_own_reply(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);
        $reply = Reply::factory()->create(['ticket_id' => $ticket->id, 'user_id' => $user->id]);

        Sanctum::actingAs($user);

        $this->getJson($this->getShowRoute($reply))
            ->assertOk()
            ->assertJsonFragment(['id' => $reply->id]);
    }


    public function test_user_cannot_view_another_users_reply(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherUserTicket = Ticket::factory()->create(['user_id' => $otherUser->id]);
        $otherUserReply = Reply::factory()->create(['ticket_id' => $otherUserTicket->id, 'user_id' => $otherUser->id]);

        Sanctum::actingAs($user);

        $this->getJson($this->getShowRoute($otherUserReply))->assertForbidden();
    }

    public function test_user_can_update_their_own_reply(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);
        $reply = Reply::factory()->create(['ticket_id' => $ticket->id, 'user_id' => $user->id]);
        $updateData = ['message' => 'Updated Message'];

        Sanctum::actingAs($user);

        $this->putJson($this->getUpdateRoute($reply), $updateData)
            ->assertOk()
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'message' => 'Updated Message',
        ]);
    }

    public function test_user_cannot_update_another_users_reply(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherUserTicket = Ticket::factory()->create(['user_id' => $otherUser->id]);
        $otherUserReply = Reply::factory()->create(['ticket_id' => $otherUserTicket->id, 'user_id' => $otherUser->id]);

        Sanctum::actingAs($user);

        $this->putJson($this->getUpdateRoute($otherUserReply), ['message' => 'Malicious Update'])->assertForbidden();
    }

    public function test_super_admin_can_view_any_reply(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'api')->first();
        $admin = User::factory()->create();
        $admin->assignRole($superAdminRole);

        $otherUser = User::factory()->create();
        $otherUserTicket = Ticket::factory()->create(['user_id' => $otherUser->id]);
        $otherUserReply = Reply::factory()->create(['ticket_id' => $otherUserTicket->id, 'user_id' => $otherUser->id]);

        Sanctum::actingAs($admin, ['*']);

        $this->getJson($this->getShowRoute($otherUserReply))
            ->assertOk()
            ->assertJsonFragment(['id' => $otherUserReply->id]);
    }

    public function test_super_admin_can_update_any_reply(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'api')->first();
        $admin = User::factory()->create();
        $admin->assignRole($superAdminRole);

        $otherUser = User::factory()->create();
        $otherUserTicket = Ticket::factory()->create(['user_id' => $otherUser->id]);
        $otherUserReply = Reply::factory()->create(['ticket_id' => $otherUserTicket->id, 'user_id' => $otherUser->id]);
        $updateData = ['message' => 'Admin Update'];

        Sanctum::actingAs($admin);

        $this->putJson($this->getUpdateRoute($otherUserReply), $updateData)
            ->assertOk()
            ->assertJsonFragment($updateData);
    }
}
