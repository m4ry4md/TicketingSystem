<?php

namespace Tests\Helpers\TestingTraits;

use App\Models\Reply;
use App\Models\Ticket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

trait ReplyValidationTestTrait
{
    /**
     * Define the route for the store endpoint.
     * @param Ticket $ticket
     * @return string
     */
    abstract protected function getStoreRoute(Ticket $ticket): string;

    /**
     * Define the route for the update endpoint.
     * @param Reply $reply
     * @return string
     */
    abstract protected function getUpdateRoute(Reply $reply): string;

    /**
     * Provide a set of valid data for a store request.
     * @return array
     */
    protected function getValidReplyData(): array
    {
        return [
            'message' => 'This is a test reply message.',
        ];
    }

    //==================================================
    // Store Validation Tests
    //==================================================

    public function test_message_is_required_for_storing_reply(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        $data = $this->getValidReplyData();
        unset($data['message']);
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute($ticket), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('message');
    }

    public function test_message_must_be_a_string_for_storing_reply(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        $data = $this->getValidReplyData();
        $data['message'] = 12345;
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute($ticket), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('message');
    }

    public function test_message_cannot_be_longer_than_5000_characters_for_storing_reply(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        $data = $this->getValidReplyData();
        $data['message'] = str_repeat('a', 5001);
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute($ticket), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('message');
    }

    public function test_attachment_must_be_a_file_for_storing_reply(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        $data = $this->getValidReplyData();
        $data['attachment'] = 'not-a-file';
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute($ticket), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('attachment');
    }

    public function test_attachment_must_have_valid_mimes_for_storing_reply(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        Storage::fake('media');
        $data = $this->getValidReplyData();
        $data['attachment'] = UploadedFile::fake()->create('document.txt', 100, 'text/plain');
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute($ticket), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('attachment');
    }

    public function test_attachment_cannot_exceed_max_size_for_storing_reply(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        Storage::fake('media');
        $data = $this->getValidReplyData();
        $data['attachment'] = UploadedFile::fake()->create('large-file.jpg', 6000, 'image/jpeg');
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute($ticket), $data);

        $response->assertStatus(422)->assertJsonValidationErrors('attachment');
    }

    //==================================================
    // Update Validation Tests
    //==================================================

    public function test_message_must_be_a_string_for_updating_reply(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        $reply = Reply::factory()->create(['ticket_id' => $ticket->id, 'user_id' => $this->testUser->id]);

        Sanctum::actingAs($this->testUser);
        $response = $this->putJson($this->getUpdateRoute($reply), ['message' => 12345]);

        $response->assertStatus(422)->assertJsonValidationErrors('message');
    }

    public function test_message_cannot_be_longer_than_5000_characters_for_updating_reply(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
        $reply = Reply::factory()->create(['ticket_id' => $ticket->id, 'user_id' => $this->testUser->id]);

        Sanctum::actingAs($this->testUser);
        $response = $this->putJson($this->getUpdateRoute($reply), ['message' => str_repeat('a', 5001)]);

        $response->assertStatus(422)->assertJsonValidationErrors('message');
    }
}
