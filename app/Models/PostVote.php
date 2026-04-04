<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PostVote Model
 * 
 * Simple upvote system for posts (like Reddit upvotes or Facebook likes).
 * Composite unique constraint ensures each user can only vote once per post.
 */
#[Fillable(['post_id', 'user_id'])]
class PostVote extends Model
{
    /** @use HasFactory<\Database\Factories\PostVoteFactory> */
    use HasFactory;

    // Relationships

    /**
     * Get the post being voted on.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user who cast this vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
