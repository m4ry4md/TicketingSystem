<?php

namespace Tests\Helpers\TestingTraits;

use App\Models\Ticket;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

trait TicketPolicyTestTrait
{
    /**
     * Define the route for the index endpoint.
     * @return string
     */
    abstract protected function getIndexRoute(): string;

    /**
     * Define the route for the store endpoint.
     * @return string
     */
    abstract protected function getStoreRoute(): string;

    /**
     * Define the route for the show endpoint.
     * @param Ticket $ticket
     * @return string
     */
    abstract protected function getShowRoute(Ticket $ticket): string;

    /**
     * Define the route for the update endpoint.
     * @param Ticket $ticket
     * @return string
     */
    abstract protected function getUpdateRoute(Ticket $ticket): string;

    //==================================================
    // Access Control / Policy Tests
    //==================================================

    public function test_unauthenticated_user_cannot_view_tickets(): void
    {
        $this->getJson($this->getIndexRoute())->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_create_a_ticket(): void
    {
        $this->postJson($this->getStoreRoute(), [])->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_view_a_ticket(): void
    {
        $ticket = Ticket::factory()->create();
        $this->getJson($this->getShowRoute($ticket))->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_update_a_ticket(): void
    {
        $ticket = Ticket::factory()->create();
        $this->putJson($this->getUpdateRoute($ticket), [])->assertUnauthorized();
    }

    public function test_authenticated_user_can_view_their_own_tickets_only(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $userTickets = Ticket::factory()->count(3)->create(['user_id' => $user->id]);
        $otherUserTicket = Ticket::factory()->create(['user_id' => $otherUser->id]);

        Sanctum::actingAs($user);

        $response = $this->getJson($this->getIndexRoute());

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment(['id' => $userTickets->first()->uuid])
            ->assertJsonMissing(['id' => $otherUserTicket->uuid]);
    }

    public function test_user_can_view_their_own_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $this->getJson($this->getShowRoute($ticket))
            ->assertOk()
            ->assertJsonFragment(['id' => $ticket->uuid]);
    }

    public function test_user_cannot_view_another_users_ticket(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherUserTicket = Ticket::factory()->create(['user_id' => $otherUser->id]);

        Sanctum::actingAs($user);

        $this->getJson($this->getShowRoute($otherUserTicket))->assertForbidden();
    }

    public function test_user_can_update_their_own_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);
        $updateData = ['title' => 'Updated Title'];

        Sanctum::actingAs($user);

        $this->putJson($this->getUpdateRoute($ticket), $updateData)
            ->assertOk()
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_user_cannot_update_another_users_ticket(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherUserTicket = Ticket::factory()->create(['user_id' => $otherUser->id]);

        Sanctum::actingAs($user);

        $this->putJson($this->getUpdateRoute($otherUserTicket), ['title' => 'Malicious Update'])->assertForbidden();
    }

    public function test_super_admin_can_view_any_ticket(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'api')->first();
        $admin = User::factory()->create();
        $admin->assignRole($superAdminRole); // Assuming you use a role package

        $otherUser = User::factory()->create();
        $otherUserTicket = Ticket::factory()->create(['user_id' => $otherUser->id]);

        Sanctum::actingAs($admin, ['*']);

       $this->getJson($this->getShowRoute($otherUserTicket))
            ->assertOk()
            ->assertJsonFragment(['id' => $otherUserTicket->uuid]);
    }

    public function test_super_admin_can_update_any_ticket(): void
    {

        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'api')->first();
        $admin = User::factory()->create();
        $admin->assignRole($superAdminRole); // Assuming you use a role package

        $otherUser = User::factory()->create();
        $otherUserTicket = Ticket::factory()->create(['user_id' => $otherUser->id]);
        $updateData = ['title' => 'Admin Update'];

        Sanctum::actingAs($admin);

        $this->putJson($this->getUpdateRoute($otherUserTicket), $updateData)
            ->assertOk()
            ->assertJsonFragment($updateData);
    }
}
