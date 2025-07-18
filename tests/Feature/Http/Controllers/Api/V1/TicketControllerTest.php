<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Enums\SenderTypeEnum;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\Helpers\TestingTraits\TicketPolicyTestTrait;
use Tests\Helpers\TestingTraits\TicketValidationTestTrait;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, TicketValidationTestTrait, TicketPolicyTestTrait;

    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('permission:cache-reset');

        // Create roles needed for tests
        Role::create(['name' => 'super_admin', 'guard_name' => 'api']);
        Role::create(['name' => 'user', 'guard_name' => 'api']);

        $this->testUser = User::factory()->create();

    }

    //==================================================
    // Trait Implementation
    //==================================================

    protected function getIndexRoute(): string
    {
        return route('api.v1.tickets.index');
    }

    protected function getStoreRoute(): string
    {
        return route('api.v1.tickets.store');
    }

    protected function getShowRoute(Ticket $ticket): string
    {
        return route('api.v1.tickets.show', $ticket);
    }

    protected function getUpdateRoute(Ticket $ticket): string
    {
        return route('api.v1.tickets.update', $ticket);
    }

    //==================================================
    // Happy Path Tests
    //==================================================

    /**
     * Test if a user can successfully store a ticket with valid data.
     */
    public function test_user_can_store_a_ticket_with_valid_data(): void
    {
        Event::fake();
        $data = $this->getValidTicketData();
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute(), $data);

        $response->assertStatus(201)
          ->assertJsonFragment([
                'title' => $data['title'],
                'message' => $data['message'],
                'sender_type' => SenderTypeEnum::USER->value,
            ]);

        $this->assertDatabaseHas('tickets', [
            'title' => $data['title'],
            'user_id' => $this->testUser->id,
        ]);
    }

    /**
     * Test if a user can successfully store a ticket with an attachment.
     */
    public function test_user_can_store_a_ticket_with_an_attachment(): void
    {
        Storage::fake('media');
        Event::fake();

        $data = $this->getValidTicketData();
        $data['attachment'] = UploadedFile::fake()->image('attachment.jpg');
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute(), $data);

        $response->assertStatus(201);
        $ticket = Ticket::first();
        $this->assertNotNull($ticket);
        $this->assertTrue($ticket->hasMedia('attachments'));
        $this->assertCount(1, $ticket->getMedia('attachments'));
    }
}
