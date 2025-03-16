<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Authentication')]
class RegisterController extends Controller
{
    /**
     * Register
     *
     * Create a new user and return a bearer token.
     *
     * @unauthenticated
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users|min:3',
            'password' => 'required|string|min:6',
            'name' => 'required|string|min:3',
        ]);

        $user = User::create($validated);
        $user->refresh();

        return response()->json([
            'token' => $user->createToken('token-name')->plainTextToken,
            'user' => new UserResource($user),
        ]);
    }
}
