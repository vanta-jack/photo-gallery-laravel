<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Album Model
 * 
 * Represents collections of photos grouped by the user.
 * Albums can be public or private, and have an optional cover photo.
 */
#[Fillable(['user_id', 'cover_photo_id', 'title', 'description', 'is_private'])]
class Album extends Model
{
    /** @use HasFactory<\Database\Factories\AlbumFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
        ];
    }

    // Relationships

    /**
     * Get the user who owns this album.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cover photo for this album.
     * 
     * Optional featured photo displayed as the album thumbnail.
     */
    public function coverPhoto(): BelongsTo
    {
        return $this->belongsTo(Photo::class, 'cover_photo_id');
    }

    /**
     * Get all photos in this album.
     * 
     * Many-to-many: An album contains many photos, and photos can be in many albums.
     */
    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class);
    }
}
