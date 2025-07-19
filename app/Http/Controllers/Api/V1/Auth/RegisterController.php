<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * Class RegisterController.
 *
 * Handles the user registration process for the API.
 */
class RegisterController extends Controller
{
    /**
     * Handle a new user registration request.
     *
     * Validates the incoming request data using RegisterRequest, creates a new user
     * with the provided credentials, and returns a success message upon completion.
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request The request object containing validated user data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating successful registration.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Retrieve the validated input data from the request.
        $validated = $request->validated();

        // Create a new user record in the database.
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // Hash the password for security.
        ]);

        // Return a successful response.
        return response()->json(['message' => __('auth.user_registered')]);
    }
}
