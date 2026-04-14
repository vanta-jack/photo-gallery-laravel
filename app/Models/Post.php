<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Post Model
 *
 * User-generated content entries, similar to blog posts or status updates.
 * Supports markdown in description field and can receive votes from users.
 */
#[Fillable(['user_id', 'title', 'description', 'photo_id'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    // Relationships

    /**
     * Get the user who authored this post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all votes for this post.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PostVote::class);
    }

    /**
     * Get the guestbook entry if this post is a guestbook post.
     *
     * One-to-one: A post can be at most one guestbook entry.
     */
    public function guestbookEntry(): HasOne
    {
        return $this->hasOne(GuestbookEntry::class);
    }

    /**
     * Get the main photo for this post.
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    /**
     * Get all photos linked to this post.
     */
    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class)->withTimestamps();
    }
}
