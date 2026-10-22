<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Token issuance for non-browser API clients (mobile apps, external POS
 * hardware). The web login flow authenticates via session instead and
 * mints its own Passport token in LoginController — this endpoint exists
 * so the same credentials work for either integration path.
 */
class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::once($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('api-client')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'branch_id' => $user->branch_id,
            ],
        ]);
    }

    public function logout(): JsonResponse
    {
        $token = Auth::user()?->token();
        $token?->revoke();

        return response()->json(['message' => 'Logged out.']);
    }
}
