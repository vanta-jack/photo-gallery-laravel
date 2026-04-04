<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PhotoRating Model
 * 
 * Stores 1-5 star ratings for photos.
 * Composite unique constraint ensures each user can only rate a photo once.
 */
#[Fillable(['photo_id', 'user_id', 'rating'])]
class PhotoRating extends Model
{
    /** @use HasFactory<\Database\Factories\PhotoRatingFactory> */
    use HasFactory;

    // Relationships

    /**
     * Get the photo being rated.
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    /**
     * Get the user who submitted this rating.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
