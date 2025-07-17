<?php

namespace Tests\Feature\Http\Controllers\Admin\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;



/**
 * Feature tests for the admin LoginController.
 */
class AdminLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set the route for the login endpoint.
     * @return string
     */
    protected function setRoute(): string
    {
        return route('admin.auth.login');
    }

    /**
     * Create an admin for testing purposes.
     *
     * @param array $overrides
     * @return \App\Models\User
     */
    private function createAdminUser(array $overrides = []): User
    {
        return User::factory()->admin()->create(array_merge([
            'password' => Hash::make('password123'),
        ], $overrides));
    }

    /**
     * Create a regular (non-admin) user for testing purposes.
     *
     * @param array $overrides
     * @return \App\Models\User
     */
    private function createRegularUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'password' => Hash::make('password123'),
        ], $overrides));
    }

    /**
     * Test if an admin user can successfully log in with correct credentials.
     */
    public function test_admin_user_can_login_with_correct_credentials(): void
    {
        $user = $this->createAdminUser();

        $response = $this->postJson($this->setRoute(), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => ['token', 'user' => ['name', 'email', 'is_admin']]
        ]);
        $response->assertJsonFragment([
            'message' => __('auth.login_successful'),
            'is_admin' => true,
        ]);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => 'user',
            'name' => 'admin-token'
        ]);
    }

    /**
     * Test a non-admin user cannot login to the admin panel.
     */
    public function test_non_admin_user_cannot_login(): void
    {
        // Create a regular user who is not an admin
        $user = $this->createRegularUser();

        $response = $this->postJson($this->setRoute(), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => 'user',
            'name' => 'admin-token'
        ]);
        $response->assertJsonFragment(['message' => __('auth.unauthorized_access')]);
    }

    /**
     * Test that the generated token for an admin has the correct abilities.
     */
    public function test_admin_login_token_has_correct_abilities(): void
    {
        $user = $this->createAdminUser();

        $this->postJson($this->setRoute(), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertStatus(200);

        // Retrieve the token from the database to check its abilities
        $token = $user->tokens()->first();

        $this->assertNotNull($token, 'Token was not created for the user.');
        $this->assertTrue($token->can('admin-panel:view'));
        $this->assertFalse($token->can('some-other-ability'));
    }


    /**
     * Test if an already authenticated user is redirected.
     */
    public function test_user_cannot_access_login_route_if_already_logged_in(): void
    {
        $user = $this->createAdminUser();
        $this->actingAs($user, 'api');

        $response = $this->postJson($this->setRoute(), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertForbidden();
    }

    /**
     * Test the rate limiter based on IP address for the login route.
     */
    public function test_ip_is_rate_limited_after_too_many_login_attempts(): void
    {
        $maxAttempts = 10;
        $testIp = '127.0.0.1';

        for ($i = 0; $i < $maxAttempts; $i++) {
            $user = $this->createAdminUser();
            $response = $this->withServerVariables(['REMOTE_ADDR' => $testIp])
                ->postJson($this->setRoute(), [
                    'email' => $user->email,
                    'password' => 'wrong-password',
                ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }
        $newUser = $this->createAdminUser();

        $this->withServerVariables(['REMOTE_ADDR' => $testIp])->postJson($this->setRoute(), [
            'email' => $newUser->email,
            'password' => 'password123',
        ])->assertStatus(429)
            ->assertSee(__('limiter.to_many_attempts'));

        // Travel 1 minute into the future
        $this->travel(1)->minutes();

        $response = $this->withServerVariables(['REMOTE_ADDR' => $testIp])
            ->postJson($this->setRoute(), [
                'email' => $newUser->email,
                'password' => 'password123',
            ]);

        $response->assertStatus(200);
    }

    /**
     * Test the rate limiter based on the same email address.
     */
    public function test_email_is_rate_limited_after_too_many_login_attempts_on_same_email(): void
    {
        $user = $this->createAdminUser();
        $maxAttempts = 3;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.' . ($i + 1)])
                ->postJson($this->setRoute(), [
                    'email' => $user->email,
                    'password' => 'wrong-password',
                ]);

            $this->assertNotEquals(429, $response->getStatusCode());
        }

        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.100'])
            ->postJson($this->setRoute(), [
                'email' => $user->email,
                'password' => 'password123',
            ])->assertStatus(429)
            ->assertSee(__('limiter.to_many_attempts'));


        // Travel 1 minute into the future
        $this->travel(1)->minutes();

        $response = $this->postJson($this->setRoute(), [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $response->assertStatus(200);
    }


    /**
     * Test if a user cannot log in with an incorrect password.
     */
    public function test_user_cannot_login_with_incorrect_password(): void
    {
        $user = $this->createAdminUser();

        $response = $this->postJson($this->setRoute(), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => __('auth.failed')]);
    }

    /**
     * Test validation rule: email is required.
     */
    public function test_email_field_is_required(): void
    {
        $response = $this->postJson($this->setRoute(), ['password' => 'password123']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * Test validation rule: email must be a valid email address.
     */
    public function test_email_must_be_a_valid_email(): void
    {
        $response = $this->postJson($this->setRoute(), [
            'email' => 'invalid-email-format',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * Test validation rule: email must be a string.
     */
    public function test_email_must_be_a_string(): void
    {
        $response = $this->postJson($this->setRoute(), [
            'email' => 12345, // Not a string
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * The email field must not exceed the maximum length.
     */
    public function test_email_cannot_be_longer_than_255_characters(): void
    {
        // Generates an email with a local part of 247 chars + @test.com = 256 chars total.
        $wrongEmail = Str::random(247) . '@test.com';
        $response = $this->postJson($this->setRoute(), [
            'email' => $wrongEmail,
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test validation rule: password is required.
     */
    public function test_password_is_required(): void
    {
        $user = $this->createAdminUser();
        $response = $this->postJson($this->setRoute(), ['email' => $user->email]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    /**
     * The password must adhere to the application's default validation rules (e.g., minimum length).
     */
    public function test_password_must_meet_default_rules(): void
    {
        $response = $this->postJson($this->setRoute(), [
            'email' => 'maryam@test.com',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }


}
