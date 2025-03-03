<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

#[Group('Authentication')]
class LoginController extends Controller
{
    /**
     * Login
     *
     * Exchange username and password for a bearer token.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($request->only('username', 'password'))) {
            return response()->json([
                'message' => "Username or password doesn't match.",
            ], 401);
        }

        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'token' => $user->createToken('token-name')->plainTextToken,
            'user' => new UserResource($user),
        ]);
    }
}
