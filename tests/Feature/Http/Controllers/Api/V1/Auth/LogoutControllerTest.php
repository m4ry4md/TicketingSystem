<?php

namespace Tests\Feature\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Define the route for the logout endpoint.
     * @return string
     */
    public function setRoute(): string
    {
        return route('api.v1.auth.logout');
    }

    /**
     * Ensure an authenticated user can successfully log out.
     */
    public function test_an_authenticated_user_can_logout_successfully(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson($this->setRoute());

        $response->assertOk()
            ->assertJson([
                'message' => __('auth.logged_out_successfully')
            ]);
    }

    /**
     * Unauthenticated users should not be able to log out.
     */
    public function test_an_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->postJson($this->setRoute());

        $response->assertUnauthorized();
    }
}
