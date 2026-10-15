<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UserOptionController extends Controller
{
    /**
     * Minimal id + name list of users in the caller's organization, for form dropdowns.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        Gate::allowIf($user->hasRole('super_admin', 'property_manager'));

        $request->validate([
            'role' => ['required', Rule::in(['landlord', 'property_manager'])],
        ]);

        $users = User::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->whereHas('roles', fn($q) => $q->where('name', $request->input('role')))
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['data' => $users]);
    }
}
