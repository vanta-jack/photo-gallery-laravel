<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Milestone Model
 * 
 * Tracks important life events with optional photos, categorized by life stage.
 * Common use case: tracking child development (baby months, school years, etc.)
 */
#[Fillable(['user_id', 'photo_id', 'stage', 'label', 'description'])]
class Milestone extends Model
{
    /** @use HasFactory<\Database\Factories\MilestoneFactory> */
    use HasFactory;

    // Relationships

    /**
     * Get the user who created this milestone.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the photo representing this milestone.
     * 
     * Optional - milestone might be created before photo is available.
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }
}
