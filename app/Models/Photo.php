<?php

namespace App\Models;

use Database\Factories\PhotoFactory;
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
    /** @use HasFactory<PhotoFactory> */
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
     * Get milestones where this photo is linked as an attachment.
     */
    public function milestoneAttachments(): BelongsToMany
    {
        return $this->belongsToMany(Milestone::class)->withTimestamps();
    }

    /**
     * Get guestbook entries that feature this photo.
     */
    public function guestbookEntries(): HasMany
    {
        return $this->hasMany(GuestbookEntry::class);
    }

    /**
     * Get posts that feature this photo as the main image.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get guestbook entries where this photo is linked as an attachment.
     */
    public function guestbookEntryAttachments(): BelongsToMany
    {
        return $this->belongsToMany(GuestbookEntry::class)->withTimestamps();
    }

    /**
     * Get posts where this photo is linked as an attachment.
     */
    public function postAttachments(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }
}
