<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthTest extends TestCase
{
    #[DataProvider('invalidRegistrationDataProvider')]
    public function test_user_cannot_register_with_invalid_fields($input, $expectedErrors): void
    {
        $response = $this->postJson('/api/register', $input);

        $response->assertStatus(422)
            ->assertJsonValidationErrors($expectedErrors);
    }

    /**
     * Data provider for test_user_cannot_register_with_invalid_fields
     */
    public static function invalidRegistrationDataProvider(): array
    {
        return [
            'missing name' => [
                'input' => [
                    'email' => 'test@juicebox.com.au',
                    'password' => 'Abc12345!',
                    'password_confirmation' => 'Abc12345!',
                ],
                'expectedErrors' => ['name' => 'The name field is required.'],
            ],
            'invalid email' => [
                'input' => [
                    'name' => 'Juicebox',
                    'email' => 'invalid-email',
                    'password' => 'Abc12345!',
                    'password_confirmation' => 'Abc12345!',
                ],
                'expectedErrors' => ['email' => 'The email field must be a valid email address.'],
            ],
            'passwords do not match' => [
                'input' => [
                    'name' => 'Juicebox',
                    'email' => 'test@juicebox.com.au',
                    'password' => 'Abc12345!',
                    'password_confirmation' => 'WrongPassword!',
                ],
                'expectedErrors' => ['password' => 'The password field confirmation does not match.'],
            ],
            'short password' => [
                'input' => [
                    'name' => 'Juicebox',
                    'email' => 'test@juicebox.com.au',
                    'password' => 'abc',
                    'password_confirmation' => 'abc',
                ],
                'expectedErrors' => ['password' => 'The password field must be at least 8 characters.'],
            ],
            'password with no symbol' => [
                'input' => [
                    'name' => 'Juicebox',
                    'email' => 'test@juicebox.com.au',
                    'password' => 'ABC12345',
                    'password_confirmation' => 'ABC12345',
                ],
                'expectedErrors' => ['password' => 'The password field must contain at least one symbol.'],
            ],
            'password with no capital letter' => [
                'input' => [
                    'name' => 'Juicebox',
                    'email' => 'test@juicebox.com.au',
                    'password' => 'abc12345!',
                    'password_confirmation' => 'abc12345!',
                ],
                'expectedErrors' => ['password' => 'The password field must contain at least one uppercase and one lowercase letter.'],
            ],
        ];
    }
}
