<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class LoginController.
 *
 * Handles the user authentication process for the API.
 */
class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * Validates the incoming request data, attempts to authenticate the user,
     * and if successful, generates and returns an API token using Sanctum.
     *
     * @param \App\Http\Requests\V1\Auth\LoginRequest $request The request object containing validated user credentials.
     * @return \Illuminate\Http\JsonResponse A JSON response with the API token or an error message.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Retrieve the validated credentials from the request.
        $credentials = $request->validated();

        // Attempt to authenticate the user with the provided credentials.
        if (!Auth::attempt($credentials)) {
            // If authentication fails, return an error response.
            return response()->json(['message' => __('auth.failed')], 401);
        }

        // Get the authenticated user instance.
        $user = $request->user();

        // Create a new Sanctum token for the user.
        $token = $user->createToken('api-token')->plainTextToken;

        // Return a successful response with the token and user data.
        return response()->json([
            'message' => __('auth.login_successful'),
            'data' => [
                'token' => $token,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]
        ]);
    }
}
