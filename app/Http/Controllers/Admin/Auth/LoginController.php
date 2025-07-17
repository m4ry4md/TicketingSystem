<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            Log::warning('Failed admin login attempt', [
                'email' => $credentials['email'],
                'ip_address' => $request->ip()
            ]);
            return response()->json(['message' => __('auth.failed')], 401);
        }

        // Get the authenticated user instance.
        $user = $request->user();
        if (!$user->is_admin) {
            Log::notice('Unauthorized admin access attempt by non-admin user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip()
            ]);
            return response()->json(['message' => __('auth.unauthorized_access')], 403);
        }

        // Create a new Sanctum token for the admin.
        $token = $user->createToken('admin-token', ['admin-panel:view'], now()->addDay())->plainTextToken;

        // Return a successful response with the token and user data.
        return response()->json([
            'message' => __('auth.login_successful'),
            'data' => [
                'token' => $token,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin,
                ]
            ]
        ]);
    }
}
