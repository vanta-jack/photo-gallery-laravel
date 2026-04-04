<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PhotoComment Model
 * 
 * User comments on photos for discussion and feedback.
 */
#[Fillable(['photo_id', 'user_id', 'body'])]
class PhotoComment extends Model
{
    /** @use HasFactory<\Database\Factories\PhotoCommentFactory> */
    use HasFactory;

    // Relationships

    /**
     * Get the photo being commented on.
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    /**
     * Get the user who wrote this comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
