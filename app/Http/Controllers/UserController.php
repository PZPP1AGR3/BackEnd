<?php

namespace App\Http\Controllers;

use App\Enum\Role;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rules\Enum;

class UserController extends Controller
{
    /**
     * List
     *
     * Retrieve a list of users.
     *
     * @return LengthAwarePaginator<UserResource>
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        abort_if(! $request->user()->is_admin, 403);

        $request->validate([
            'page' => 'integer|min:1',
            'search' => 'sometimes|string',
            'sort_by' => 'sometimes|string|in:id,name,role,created_at',
            'sort_dir' => 'sometimes|string|in:asc,desc',
            'role' => ['sometimes', new Enum(Role::class)],
        ]);

        $users = User::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->sort_by, function ($query) use ($request) {
                $query->orderBy($request->sort_by, $request->sort_dir === 'desc' ? 'desc' : 'asc');
            })
            ->when($request->role, function ($query) use ($request) {
                $query->where('role', $request->role);
            })
            ->paginate($request->input('per_page', 10));

        return UserResource::collection($users);
    }

    /**
     * Show
     *
     * Retrieve a user.
     */
    public function show(Request $request, User $user): UserResource
    {
        $isAdmin = $request->user()->is_admin;
        abort_if($user->id != $request->user()->id && ! $isAdmin, 403);

        return new UserResource($user);
    }

    /**
     * Update
     *
     * Update a user.
     */
    public function update(Request $request, User $user): UserResource
    {
        $isAdmin = $request->user()->is_admin;
        abort_if($user->id != $request->user()->id && ! $isAdmin, 403);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'role' => ['sometimes', new Enum(Role::class)],
        ]);

        if (! $isAdmin) {
            unset($validated['role']);
        }

        $user->update($validated);

        return new UserResource($user);
    }

    /**
     * Destroy
     *
     * Delete a user.
     */
    public function destroy(Request $request, User $user): Response
    {
        abort_if(! $request->user()->is_admin, 403);

        $user->delete();

        return response()->noContent();
    }
}
