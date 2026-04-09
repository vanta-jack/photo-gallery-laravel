<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoCommentRequest;
use App\Http\Requests\UpdatePhotoCommentRequest;
use App\Models\Photo;
use App\Models\PhotoComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * PhotoCommentController
 *
 * Manages comments on photos.
 * Nested resource: comments belong to photos
 */
class PhotoCommentController extends Controller
{
    /**
     * Show form to add comment to photo
     */
    public function create(Photo $photo): View
    {
        return view('comments.create', compact('photo'));
    }

    /**
     * Store new comment on photo
     */
    public function store(StorePhotoCommentRequest $request, Photo $photo): RedirectResponse
    {
        $validated = $request->validated();

        PhotoComment::create([
            'photo_id' => $photo->id,
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return redirect()
            ->route('photos.show', $photo)
            ->with('status', 'Comment added!');
    }

    /**
     * Show edit form for comment
     */
    public function edit(Photo $photo, PhotoComment $comment): View
    {
        $this->authorize('update', $comment);

        return view('comments.edit', compact('photo', 'comment'));
    }

    /**
     * Update comment
     */
    public function update(UpdatePhotoCommentRequest $request, Photo $photo, PhotoComment $comment): RedirectResponse
    {
        $this->authorize('update', $comment);

        $comment->update($request->validated());

        return redirect()
            ->route('photos.show', $photo)
            ->with('status', 'Comment updated!');
    }

    /**
     * Delete comment
     */
    public function destroy(Photo $photo, PhotoComment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return redirect()
            ->route('photos.show', $photo)
            ->with('status', 'Comment deleted!');
    }
}
