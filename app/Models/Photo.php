<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Photo Model
 * 
 * Represents uploaded photos with metadata, ratings, comments, and album associations.
 * Central model of the photo gallery application.
 */
#[Fillable(['user_id', 'path', 'title', 'description'])]
class Photo extends Model
{
    /** @use HasFactory<\Database\Factories\PhotoFactory> */
    use HasFactory;

    // Relationships

    /**
     * Get the user who uploaded this photo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all ratings for this photo.
     * 
     * Used to calculate average rating for display.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(PhotoRating::class);
    }

    /**
     * Get all comments for this photo.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(PhotoComment::class);
    }

    /**
     * Get all albums this photo belongs to.
     * 
     * Many-to-many: A photo can be in multiple albums.
     */
    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class);
    }

    /**
     * Get milestones that feature this photo.
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    /**
     * Get guestbook entries that feature this photo.
     */
    public function guestbookEntries(): HasMany
    {
        return $this->hasMany(GuestbookEntry::class);
    }
}
