<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;

/**
 * AlbumPolicy
 * 
 * Authorization logic for album operations.
 * Owner or admin can modify/delete albums.
 */
class AlbumPolicy
{
    /**
     * Only album owner or admin can update
     */
    public function update(User $user, Album $album): bool
    {
        return $user->id === $album->user_id || $user->role === 'admin';
    }

    /**
     * Only album owner or admin can delete
     */
    public function delete(User $user, Album $album): bool
    {
        return $user->id === $album->user_id || $user->role === 'admin';
    }
}
