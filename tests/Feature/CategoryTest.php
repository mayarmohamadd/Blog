<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Category;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        return $user;
    }

    public function test_user_can_create_category()
    {
        $this->authenticate();

        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'name', 'created_at', 'updated_at']);

        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
    }

    public function test_user_can_update_category()
    {
        $this->authenticate();

        $category = Category::factory()->create();

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['id', 'name', 'created_at', 'updated_at']);

        $this->assertDatabaseHas('categories', ['name' => 'Updated Category']);
    }

    public function test_user_can_delete_category()
    {
        $this->authenticate();

        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_user_can_view_all_categories()
    {
        $this->authenticate();

        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'created_at', 'updated_at']
                 ]);
    }

    public function test_user_can_view_single_category()
    {
        $this->authenticate();

        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure(['id', 'name', 'created_at', 'updated_at']);
    }
}
