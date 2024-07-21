<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRegister()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'user']);
    }

    public function testUserCanLogin()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'user']);
    }

    public function testUserCanLogout()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->postJson('/api/logout', [], ['Authorization' => "Bearer $token"]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'User successfully signed out']);
    }

    public function testTokenCanBeRefreshed()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->postJson('/api/refresh', [], ['Authorization' => "Bearer $token"]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'user']);
    }
}
