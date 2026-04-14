<?php

namespace App\Models;

use Database\Factories\GuestbookEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * GuestbookEntry Model
 *
 * Represents guestbook entries - visitor messages with optional photos.
 * Each entry is backed by a Post for content (title, description, votes).
 */
#[Fillable(['post_id', 'photo_id'])]
class GuestbookEntry extends Model
{
    /** @use HasFactory<GuestbookEntryFactory> */
    use HasFactory;

    // Relationships

    /**
     * Get the post this guestbook entry is based on.
     *
     * One-to-one: Each guestbook entry links to exactly one post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the optional photo attached to this guestbook entry.
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    /**
     * Get all photos linked to this guestbook entry.
     */
    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class)->withTimestamps();
    }
}
