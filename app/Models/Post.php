<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Post Model
 * 
 * User-generated content entries, similar to blog posts or status updates.
 * Supports markdown in description field and can receive votes from users.
 */
#[Fillable(['user_id', 'title', 'description'])]
class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
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
}
