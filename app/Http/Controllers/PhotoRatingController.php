<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoRating;
use App\Http\Requests\StorePhotoRatingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * PhotoRatingController
 * 
 * Manages star ratings on photos (1-5 scale).
 * Users can rate once, then update or delete their rating.
 */
class PhotoRatingController extends Controller
{
    /**
     * Show rating form for photo
     */
    public function create(Photo $photo): View
    {
        // Check if user already rated this photo
        $existingRating = PhotoRating::where('photo_id', $photo->id)
            ->where('user_id', auth()->id())
            ->first();

        return view('ratings.create', compact('photo', 'existingRating'));
    }

    /**
     * Store or update rating
     * 
     * If user already rated, update existing rating instead of creating duplicate
     */
    public function store(StorePhotoRatingRequest $request, Photo $photo): RedirectResponse
    {
        // Find or create rating for this user+photo
        PhotoRating::updateOrCreate(
            [
                'photo_id' => $photo->id,
                'user_id' => $request->user()->id,
            ],
            [
                'rating' => $request->rating,
            ]
        );

        return redirect()
            ->route('photos.show', $photo)
            ->with('status', 'Rating saved!');
    }

    /**
     * Delete user's rating
     */
    public function destroy(Photo $photo, PhotoRating $rating): RedirectResponse
    {
        $this->authorize('delete', $rating);

        $rating->delete();

        return redirect()
            ->route('photos.show', $photo)
            ->with('status', 'Rating removed!');
    }
}
