<?php

namespace Tests\Feature\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class RegisterControllerTest.
 *
 * This class contains feature tests for the user registration process.
 * It covers success scenarios, validation rules, and edge cases for the registration API endpoint.
 */
class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Define the route for the registration endpoint.
     * @return string
     */
    public function setRoute(): string
    {
        return route('api.v1.auth.register');
    }

    /**
     * Provide a set of valid data for a registration request.
     * @return array
     */
    public function setValidRequestData(): array
    {
        return [
            'name' => 'maryam',
            'email' => 'maryam@test.com',
            'password' => 'maryam123456',
            'password_confirmation' => 'maryam123456',
        ];
    }

    /**
     * Ensure a new user can register successfully with valid credentials.
     */
    public function test_user_can_register_successfully_with_valid_data(): void
    {
        $requestData = $this->setValidRequestData();

        $response = $this->postJson($this->setRoute(), $requestData);

        $response->assertStatus(200)
            ->assertJson(['message' => __('auth.user_registered')]);

        $this->assertDatabaseHas('users', [
            'name' => $requestData['name'],
            'email' => $requestData['email'],
        ]);
    }

    /**
     * Ensure the user's password is properly hashed before being stored.
     */
    public function test_password_is_hashed_before_saving(): void
    {
        $requestData = $this->setValidRequestData();

        $this->postJson($this->setRoute(), $requestData);

        $user = User::where('email', $requestData['email'])->first();

        $this->assertNotNull($user);

        $this->assertNotEquals($requestData['password'], $user->password);

        $this->assertTrue(Hash::check($requestData['password'], $user->password));
    }

    /**
     * Authenticated users should be forbidden from accessing the registration endpoint.
     */
    public function test_user_cannot_access_register_route_if_already_logged_in(): void
    {
        $user = User::factory()->create();
        $requestData = $this->setValidRequestData();

        $response = $this->actingAs($user, 'api')->postJson($this->setRoute(), $requestData);

        $response->assertForbidden();
    }

    /**
     * Ensure the registration endpoint is rate-limited.
     */
    public function test_rate_limiter_for_register_is_applied(): void
    {
        $requestData = $this->setValidRequestData();
        $testIp = '127.0.0.1';

        // Send 10 successful requests from the same IP
        for ($i = 0; $i < 10; $i++) {
            $requestData['email'] = "maryam{$i}@test.com";
            $this->withServerVariables(['REMOTE_ADDR' => $testIp])
                ->postJson($this->setRoute(), $requestData)
                ->assertStatus(200);
        }

        // The 11th request from the same IP should be blocked
        $requestData['email'] = 'maryam10@test.com';
        $this->withServerVariables(['REMOTE_ADDR' => $testIp])
            ->postJson($this->setRoute(), $requestData)
            ->assertStatus(429)
            ->assertSee(__('limiter.to_many_attempts'));

        // Travel 1 minute into the future
        $this->travel(1)->minutes();

        // The next request should be allowed again
        $requestData['email'] = 'maryam11@test.com';
        $this->withServerVariables(['REMOTE_ADDR' => $testIp])
            ->postJson($this->setRoute(), $requestData)
            ->assertStatus(200);
    }
    /**
     * The name field must be present in the request.
     */
    public function test_name_field_is_required(): void
    {
        $response = $this->postJson($this->setRoute(), [
            'email' => 'maryam@test.com',
            'password' => 'maryam123456',
            'password_confirmation' => 'maryam123456',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * The name field must be a string.
     */
    public function test_name_must_be_a_string(): void
    {
        $requestData = $this->setValidRequestData();
        $requestData['name'] = 12345;

        $response = $this->postJson($this->setRoute(), $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * The name field must not exceed the maximum length.
     */
    public function test_name_cannot_be_longer_than_255_characters(): void
    {
        $requestData = $this->setValidRequestData();
        $requestData['name'] = Str::random(256);

        $response = $this->postJson($this->setRoute(), $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * The email field must be present in the request.
     */
    public function test_email_field_is_required(): void
    {
        $response = $this->postJson($this->setRoute(), [
            'name' => 'maryam',
            'password' => 'maryam123456',
            'password_confirmation' => 'maryam123456',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * The email field must be a string.
     */
    public function test_email_must_be_a_string(): void
    {
        $requestData = $this->setValidRequestData();
        $requestData['email'] = 12345;

        $response = $this->postJson($this->setRoute(), $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * The email field must contain a valid email format.
     */
    public function test_email_must_be_a_valid_email_address(): void
    {
        $requestData = $this->setValidRequestData();
        $requestData['email'] = 'invalid-email';

        $response = $this->postJson($this->setRoute(), $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * The email field must not exceed the maximum length.
     */
    public function test_email_cannot_be_longer_than_255_characters(): void
    {
        $requestData = $this->setValidRequestData();
        // Generates an email with a local part of 247 chars + @test.com = 256 chars total.
        $requestData['email'] = Str::random(247) . '@test.com';

        $response = $this->postJson($this->setRoute(), $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * The email must be unique and not already exist in the database.
     */
    public function test_email_must_be_unique(): void
    {
        $requestData = $this->setValidRequestData();

        // Create a user with the same email first.
        User::factory()->create(['email' => $requestData['email']]);

        $response = $this->postJson($this->setRoute(), $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * The password must be confirmed.
     */
    public function test_password_must_be_confirmed(): void
    {
        $requestData = $this->setValidRequestData();
        $requestData['password_confirmation'] = 'different-password';

        $response = $this->postJson($this->setRoute(), $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * The password must adhere to the application's default validation rules (e.g., minimum length).
     */
    public function test_password_must_meet_default_rules(): void
    {
        $requestData = $this->setValidRequestData();
        $requestData['password'] = 'short';
        $requestData['password_confirmation'] = 'short';

        $response = $this->postJson($this->setRoute(), $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * The password_confirmation field must match the password field.
     */
    public function test_password_confirmation_must_be_same_as_password(): void
    {
        $requestData = $this->setValidRequestData();
        $requestData['password_confirmation'] = 'different-password';

        $response = $this->postJson($this->setRoute(), $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * The password field must be present in the request.
     */
    public function test_password_field_is_required(): void
    {
        $response = $this->postJson($this->setRoute(), [
            'name' => 'maryam',
            'email' => 'maryam@test.com',
            'password_confirmation' => 'maryam123456',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * The password_confirmation field must be present in the request.
     */
    public function test_password_confirmation_field_is_required(): void
    {
        $response = $this->postJson($this->setRoute(), [
            'name' => 'maryam',
            'email' => 'maryam@test.com',
            'password' => 'maryam123456',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password_confirmation']);
    }
}
