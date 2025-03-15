<?php

namespace App\Http\Controllers;

use App\Http\Resources\NoteResource;
use App\Http\Resources\NoteSimpleResource;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class NoteController extends Controller
{
    /**
     * List
     *
     * Get a list of notes
     *
     * @return LengthAwarePaginator<NoteSimpleResource>
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            /**
             * @default 1
             */
            'page' => 'integer|min:1',
            /**
             *  search for notes by title or content
             */
            'search' => 'sometimes|string',
            'title' => 'sometimes|string',
            'user_id' => 'sometimes|integer|exists:users,id',
            /**
             * @default id
             */
            'sort_by' => 'sometimes|string|in:id,title,created_at',
            /**
             * @default asc
             */
            'sort_dir' => 'sometimes|string|in:asc,desc',
        ]);

        if (! $request->user()->is_admin) {
            $request->merge(['user_id' => $request->user()->id]);
        }

        $notes = Note::query()
            ->when($request->has('is_public'), fn ($query, $isPublic) => $query->where('is_public', $isPublic))
            ->when($request->user_id, fn ($query, $userId) => $query->where('user_id', $userId)->orWhere('is_public', true))
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('content', 'like', "%$search%");
            })
            ->when($request->title, fn ($query, $title) => $query->where('title', 'like', $title))
            ->paginate($request->input('per_page', 10));

        return NoteSimpleResource::collection($notes);
    }

    /**
     * Show
     *
     * Retrieve a note
     */
    public function show(Request $request, Note $note): NoteResource
    {
        abort_if(! $note->is_public && ! $request->user()->is_admin && $request->user()->id !== $note->user_id, 401);

        $note->load('user');

        return new NoteResource($note);
    }

    /**
     * Store
     *
     * Create a new note
     */
    public function store(Request $request): NoteResource
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            /**
             * @default false
             */
            'is_public' => 'sometimes|boolean',
            /**
             * Default to the authenticated user
             */
            'user_id' => 'sometimes|integer|exists:users,id',
        ]);

        $validated['user_id'] = $request->integer('user_id', $request->user()->id);

        abort_if(! $request->user()->is_admin && $request->user()->id !== $validated['user_id'], 401);

        $note = Note::create($validated);

        $note->refresh()->load('user');

        return new NoteResource($note);
    }

    /**
     * Update
     *
     * Update a note
     */
    public function update(Request $request, Note $note): NoteResource
    {
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'content' => 'sometimes|string',
            'is_public' => 'sometimes|boolean',
        ]);

        abort_if(! $request->user()->is_admin && $request->user()->id !== $note->user_id, 401);

        $note->update($validated);

        return new NoteResource($note);
    }

    /**
     * Destroy
     *
     * Delete a note
     */
    public function destroy(Request $request, Note $note): Response
    {
        abort_if(! $request->user()->is_admin && $request->user()->id !== $note->user_id, 401);

        $note->delete();

        return response()->noContent();
    }
}
