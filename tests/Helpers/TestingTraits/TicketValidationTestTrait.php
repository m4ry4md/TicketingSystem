<?php

namespace Tests\Helpers\TestingTraits;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

trait TicketValidationTestTrait
{
    /**
     * Define the route for the store endpoint.
     * This method must be implemented by the test class.
     * @return string
     */
    abstract protected function getStoreRoute(): string;

    /**
     * Define the route for the update endpoint.
     * This method must be implemented by the test class.
     * @param Ticket $ticket
     * @return string
     */
    abstract protected function getUpdateRoute(Ticket $ticket): string;

    /**
     * Provide a set of valid data for a store request.
     * @return array
     */
    protected function getValidTicketData(): array
    {
        return [
            'title' => 'Test Ticket Title',
            'message' => 'This is a test message for the ticket.',
        ];
    }

    //==================================================
    // Store Validation Tests
    //==================================================

    public function test_title_is_required_for_storing_ticket(): void
    {
        $data = $this->getValidTicketData();
        unset($data['title']);
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute(), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('title');
    }

    public function test_title_must_be_a_string_for_storing_ticket(): void
    {
        $data = $this->getValidTicketData();
        $data['title'] = 12345;
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute(), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('title');
    }

    public function test_title_cannot_be_longer_than_255_characters_for_storing_ticket(): void
    {
        $data = $this->getValidTicketData();
        $data['title'] = str_repeat('a', 256);
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute(), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('title');
    }

    public function test_message_is_required_for_storing_ticket(): void
    {
        $data = $this->getValidTicketData();
        unset($data['message']);
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute(), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('message');
    }

    public function test_message_must_be_a_string_for_storing_ticket(): void
    {
        $data = $this->getValidTicketData();
        $data['message'] = 12345;
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute(), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('message');
    }

    public function test_message_cannot_be_longer_than_5000_characters_for_storing_ticket(): void
    {
        $data = $this->getValidTicketData();
        $data['message'] = str_repeat('a', 5001);
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute(), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('message');
    }

    public function test_attachment_must_be_a_file(): void
    {
        $data = $this->getValidTicketData();
        $data['attachment'] = 'not-a-file';
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute(), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('attachment');
    }

    public function test_attachment_must_have_valid_mimes(): void
    {
        Storage::fake('media');
        $data = $this->getValidTicketData();
        $data['attachment'] = UploadedFile::fake()->create('document.txt', 100, 'text/plain');
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute(), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('attachment');
    }

    public function test_attachment_cannot_exceed_max_size(): void
    {
        Storage::fake('media');
        $data = $this->getValidTicketData();
        $data['attachment'] = UploadedFile::fake()->create('large-file.jpg', 6000, 'image/jpeg');
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute(), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('attachment');
    }

    //==================================================
    // Update Validation Tests
    //==================================================

    public function test_title_must_be_a_string_for_updating_ticket(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        Sanctum::actingAs($this->testUser);
        $response = $this->putJson($this->getUpdateRoute($ticket), ['title' => 12345]);

        $response->assertStatus(422)->assertJsonValidationErrors('title');
    }

    public function test_title_cannot_be_longer_than_255_characters_for_updating_ticket(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        Sanctum::actingAs($this->testUser);
        $response = $this->putJson($this->getUpdateRoute($ticket), ['title' => str_repeat('a', 256)]);

        $response->assertStatus(422)->assertJsonValidationErrors('title');
    }

    public function test_message_must_be_a_string_for_updating_ticket(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        Sanctum::actingAs($this->testUser);
        $response = $this->putJson($this->getUpdateRoute($ticket), ['message' => 12345]);

        $response->assertStatus(422)->assertJsonValidationErrors('message');
    }

    public function test_message_cannot_be_longer_than_5000_characters_for_updating_ticket(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        Sanctum::actingAs($this->testUser);
        $response = $this->putJson($this->getUpdateRoute($ticket), ['message' => str_repeat('a', 5001)]);

        $response->assertStatus(422)->assertJsonValidationErrors('message');
    }
}
