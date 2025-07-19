<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Enums\SenderTypeEnum;
use App\Models\Reply;
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
use Tests\Helpers\TestingTraits\ReplyPolicyTestTrait;
use Tests\Helpers\TestingTraits\ReplyValidationTestTrait;
use Tests\TestCase;

class ReplyControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, ReplyValidationTestTrait, ReplyPolicyTestTrait;

    protected User $testUser;
    protected Ticket $testTicket;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('permission:cache-reset');
        // Ensure roles are created only once
        if (!Role::where('name', 'super_admin')->where('guard_name', 'api')->exists()) {
            Role::create(['name' => 'super_admin', 'guard_name' => 'api']);
        }
        if (!Role::where('name', 'user')->where('guard_name', 'api')->exists()) {
            Role::create(['name' => 'user', 'guard_name' => 'api']);
        }

        $this->testUser = User::factory()->create();
        $this->testTicket = Ticket::factory()->create(['user_id' => $this->testUser->id]);
    }

    //==================================================
    // Trait Implementation
    //==================================================

    protected function getIndexRoute(Ticket $ticket): string
    {
        // Correct route for shallow nested resource: tickets/{ticket}/replies
        return route('api.v1.tickets.replies.index', $ticket);
    }

    protected function getStoreRoute(Ticket $ticket): string
    {
        // Correct route for shallow nested resource: tickets/{ticket}/replies
        return route('api.v1.tickets.replies.store', $ticket);
    }

    protected function getShowRoute(Reply $reply): string
    {
        // Correct route for shallow resource: replies/{reply}
        return route('api.v1.replies.show', $reply);
    }

    protected function getUpdateRoute(Reply $reply): string
    {
        // Correct route for shallow resource: replies/{reply}
        return route('api.v1.replies.update', $reply);
    }

    //==================================================
    // Happy Path Tests
    //==================================================

    /**
     * Test if a user can successfully store a reply with valid data.
     */
    public function test_user_can_store_a_reply_with_valid_data(): void
    {
        Event::fake();
        $data = $this->getValidReplyData();
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute($this->testTicket), $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => $data['message'],
                'sender_type' => SenderTypeEnum::USER->value,
            ]);

        $this->assertDatabaseHas('replies', [
            'message' => $data['message'],
            'ticket_id' => $this->testTicket->id,
            'user_id' => $this->testUser->id,
        ]);
    }

    /**
     * Test if a user can successfully store a reply with an attachment.
     */
    public function test_user_can_store_a_reply_with_an_attachment(): void
    {
        Storage::fake('media');
        Event::fake();

        $data = $this->getValidReplyData();
        $data['attachment'] = UploadedFile::fake()->image('attachment.jpg');
        Sanctum::actingAs($this->testUser);
        $response = $this->postJson($this->getStoreRoute($this->testTicket), $data);

        $response->assertStatus(201);
        $reply = Reply::first();
        $this->assertNotNull($reply);
        $this->assertTrue($reply->hasMedia('attachments'));
        $this->assertCount(1, $reply->getMedia('attachments'));
    }
}
