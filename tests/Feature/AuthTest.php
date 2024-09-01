<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_fields(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Juicebox',
            'email' => 'test@juicebox.com.au',
            'password' => 'Abc12345!',
            'password_confirmation' => 'Abc12345!',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'token',
            ])
            ->assertJson([
                'user' => [
                    'name' => 'Juicebox',
                    'email' => 'test@juicebox.com.au',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Juicebox',
            'email' => 'test@juicebox.com.au',
        ]);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('Abc12345!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'Abc12345!',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'token',
            ])
            ->assertJson([
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken($user->email);

        $response = $this->postJson('/api/logout', [], [
            'Authorization' => 'Bearer '.$token->plainTextToken,
        ]);

        $response->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    public function test_user_can_logout_from_all_devices(): void
    {
        $user = User::factory()->create();

        $token1 = $user->createToken('Device 1');

        $user->createToken('Device 2');

        $response = $this->postJson('/api/logout-all-devices', [], [
            'Authorization' => 'Bearer '.$token1->plainTextToken,
        ]);

        $response->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }
}
