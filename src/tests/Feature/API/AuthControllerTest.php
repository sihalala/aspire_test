<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // create a user
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('test123456')
        ]);

    }

    public function test_raise_validation_error_when_required_field_empty()
    {
        $response = $this->json('POST', config('app.url') . '/api/register', [
            'name' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => ''
        ], ['Accept' => 'application/json']);
        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => [
                'name',
                'email',
                'password',
                'confirm_password'
            ]]);
    }

    public function test_can_register_user()
    {
        $response = $this->json('POST',config('app.url').'/api/register', [
            'name' => 'haho',
            'email' => 'haho@test.com',
            'password' => 'test123456',
            'confirm_password' => 'test123456'
        ],['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'name',
                'email',
                'token'
            ]])
            ->assertJson(['message' => 'User has been created']);
    }

    public function test_wrong_username_or_password()
    {
        $response = $this->json('POST', config('app.url').'/api/login', [
            'email' => 'wrong@test.com',
            'password' => '2321321321'
        ],['Accept' => 'application/json']);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Username or password did not match']);
    }

    public function test_can_login_successfully()
    {
        $response = $this->json('POST', config('app.url').'/api/login', [
            'email' =>'test@test.com',
            'password' => 'test123456',
        ],['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['message' => 'User Logged in'])
            ->assertJsonStructure(['data'=>[
                'id',
                'name',
                'email',
                'is_admin',
                'token'
            ]]);
    }

}
