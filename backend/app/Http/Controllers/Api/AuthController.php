<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        // One generic message for every failure, so attackers can't tell
        // whether an email exists or whether the account is disabled.
        if (! $user || ! Hash::check($request->password, $user->password) || ! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user'  => $this->userPayload($user),
            ],
            'message' => 'Logged in.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->userPayload($request->user()),
        ]);
    }

    private function userPayload(User $user): array
    {
        $user->load(['roles:id,name,label', 'organization:id,name,slug']);

        return [
            'id'           => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
            'phone'        => $user->phone,
            'roles'        => $user->roles->pluck('name'),
            'organization' => $user->organization,
        ];
    }
}
