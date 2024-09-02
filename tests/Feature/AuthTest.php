<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_fields(): void
    {
        Mail::fake();

        $fields = [
            'name' => 'Juicebox',
            'email' => 'test@juicebox.com.au',
            'password' => 'Abc12345!',
            'password_confirmation' => 'Abc12345!',
        ];

        $response = $this->postJson('/api/register', $fields);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                ],
            ])
            ->assertJson([
                'data' => [
                    'user' => [
                        'name' => $fields['name'],
                        'email' => $fields['email'],
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $fields['name'],
            'email' => $fields['email'],
        ]);

        Mail::assertSent(function (Mailable $mail) use ($fields) {
            return $mail->hasTo($fields['email']);
        });
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
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                ],
            ])
            ->assertJson([
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                ],
            ]);
    }

    public function test_user_can_not_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('Abc12345!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_user_can_logout_from_all_devices(): void
    {
        $user = User::factory()->create();
        $user->createToken('Device 1');
        $user->createToken('Device 2');

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout-all-devices');

        $response->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }
}
