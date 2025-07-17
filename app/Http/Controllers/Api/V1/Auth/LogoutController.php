<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class LogoutController.
 *
 * Handles the user logout process for the API.
 */
class LogoutController extends Controller
{
    /**
     * Invalidate the user's current API token (logout).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {

        $user = Auth::user();

        // Revoke the token that was used to authenticate the current request...
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => __('auth.logged_out_successfully')
        ]);
    }
}
