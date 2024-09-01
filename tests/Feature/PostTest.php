<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_get_posts_with_pagination(): void
    {
        $user = User::factory()->create();
        Post::factory(18)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'slug',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(10, 'data')
            ->assertJsonFragment([
                'total' => 18,
                'per_page' => 10,
                'current_page' => 1,
                'last_page' => 2,
            ]);

        $response = $this->getJson('/api/posts?page=2');

        $response->assertStatus(200)
            ->assertJsonCount(8, 'data')
            ->assertJsonFragment([
                'total' => 18,
                'per_page' => 10,
                'current_page' => 2,
                'last_page' => 2,
            ]);
    }

    public function test_user_can_post_a_new_post(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'content',
                'slug',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => $response['title'],
            'content' => $response['content'],
            'slug' => $response['slug'],
        ]);
    }

    public function test_user_can_get_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'content',
                'slug',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'slug' => $post->slug,
            ]);
    }

    public function test_user_can_update_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'content',
                'slug',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $response['title'],
            'content' => $response['content'],
            'slug' => $response['slug'],
        ]);
    }

    public function test_user_can_delete_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_user_can_not_delete_a_post_that_they_did_not_create(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(
            ['user_id' => User::factory()->create()->id]
        );

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
        ]);
    }
}
