<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\TestingTraits\ModelsTestTrait;
use Tests\TestCase;

class UserTest extends TestCase
{
    use ModelsTestTrait;

    protected function model(): string
    {
       return User::class;
    }
    /**
     * Test that the 'role' column has a default value of 'user'.
     *
     * @return void
     */
    public function test_role_column_has_default_user_value(): void
    {
        $this->assertColumnHasDefaultValue('role', 'user');
    }
}
