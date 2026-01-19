<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Create a personal access token if credentials match, logout previous
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createToken(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // It would be unsafe to say whether an email or password is registered here
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Only let 1 token persist per account
        $user->tokens()->where('name', (string) $user->id)->delete();

        $token = $user->createToken((string) $user->id)->plainTextToken;

        return response()->json([
            'message' => "You've logged in successfully.",
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout user by deleting their personal access token(s)
     *
     * @return void
     */
    public function deleteToken(Request $request): JsonResponse
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => "You've logged out successfully."
        ]);
    }
}
