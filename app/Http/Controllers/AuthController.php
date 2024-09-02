<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Create a user
     *
     * Register a new user and send a welcome email once created.
     *
     * @unauthenticated
     */
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        SendWelcomeEmail::dispatch($user);

        $token = $user->createToken($request->email);

        $response = [
            'user' => $user,
            'token' => $token->plainTextToken,
        ];

        /**
         * The created user.
         *
         * @status 201
         */
        return AuthResource::make($response)->response()->setStatusCode(201);
    }

    /**
     * Login a user
     *
     * Logs user into the system
     *
     * @unauthenticated
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $user->createToken($request->email);

        $response = [
            'user' => $user,
            'token' => $token->plainTextToken,
        ];

        /**
         * The logged in user.
         *
         * @status 201
         */
        return AuthResource::make($response)->response()->setStatusCode(201);
    }

    /**
     * Logout a user
     *
     * Revokes the current user's token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    /**
     * Logout all devices
     *
     * Revokes all of the current user's tokens
     */
    public function logoutAllDevices(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->noContent();
    }
}
