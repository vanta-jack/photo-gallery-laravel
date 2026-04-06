<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 * 
 * Represents users in the photo gallery application with three roles: guest, user, admin.
 * Supports authentication, photo ownership, albums, posts, CV/professional profile, and social interactions.
 */
#[Fillable([
    'role',
    'email',
    'first_name',
    'last_name',
    'password',
    'profile_photo_id',
    'bio',
    'phone',
    'phone_public',
    'linkedin',
    'academic_history',
    'professional_experience',
    'skills',
    'certifications',
    'orcid_id',
    'github',
    'other_links',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'phone_public' => 'boolean',
            'academic_history' => 'array',
            'professional_experience' => 'array',
            'skills' => 'array',
            'certifications' => 'array',
            'other_links' => 'array',
        ];
    }

    // Relationships

    /**
     * Get the profile photo for the user.
     * 
     * A user may have one profile photo (nullable).
     */
    public function profilePhoto(): BelongsTo
    {
        return $this->belongsTo(Photo::class, 'profile_photo_id');
    }

    /**
     * Get all photos uploaded by the user.
     * 
     * One user can upload many photos.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Get all albums created by the user.
     */
    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    /**
     * Get all milestones tracked by the user.
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    /**
     * Get all posts created by the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get all photo ratings submitted by the user.
     */
    public function photoRatings(): HasMany
    {
        return $this->hasMany(PhotoRating::class);
    }

    /**
     * Get all photo comments submitted by the user.
     */
    public function photoComments(): HasMany
    {
        return $this->hasMany(PhotoComment::class);
    }

    /**
     * Get all post votes cast by the user.
     */
    public function postVotes(): HasMany
    {
        return $this->hasMany(PostVote::class);
    }
}
