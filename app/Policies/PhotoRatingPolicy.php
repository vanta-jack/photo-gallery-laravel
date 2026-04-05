<?php

namespace App\Policies;

use App\Models\PhotoRating;
use App\Models\User;

/**
 * PhotoRatingPolicy
 * 
 * Users can only modify their own ratings.
 */
class PhotoRatingPolicy
{
    public function update(User $user, PhotoRating $rating): bool
    {
        return $user->id === $rating->user_id || $user->role === 'admin';
    }

    public function delete(User $user, PhotoRating $rating): bool
    {
        return $user->id === $rating->user_id || $user->role === 'admin';
    }
}
