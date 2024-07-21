<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;

class PostTest extends TestCase
{
    use RefreshDatabase;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function testCreatePost()
    {
        $category = Category::factory()->create();
        $response = $this->postJson('/api/posts', [
            'title' => 'First Created Post',
            'body' => 'Post BODYY',
            'category_id' => $category->id,
        ]);
        $response->assertStatus(201)->assertJsonStructure(['message', 'post']);
    }

    public function testGetPosts()
    {
        Post::factory()->create(['user_id' => $this->user->id]);
        $response = $this->getJson('/api/posts');
        $response->assertStatus(200)->assertJsonStructure(['*' => ['id', 'title', 'body', 'category_id', 'user' => ['id', 'name', 'email']],]);
    }

    public function testUpdatePost()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $category = Category::factory()->create();
        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Updated First',
            'body' => 'Updated Body of firstt',
            'category_id' => $category->id,
        ]);
        $response->assertStatus(200)->assertJsonStructure(['message', 'post']);
    }

    public function testDeletePost()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $response = $this->deleteJson("/api/posts/{$post->id}");
        $response->assertStatus(200)
                ->assertJson(['message' => 'Post deleted successfully']);
    }
}
