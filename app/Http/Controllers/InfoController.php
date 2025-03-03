<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Authentication')]
class InfoController extends Controller
{
    /**
     * Info
     *
     * Retrieve the authenticated user.
     */
    public function __invoke(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
