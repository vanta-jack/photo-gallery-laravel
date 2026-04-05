<?php

namespace App\Policies;

use App\Models\PostVote;
use App\Models\User;

/**
 * PostVotePolicy
 * 
 * Users can only delete their own votes.
 */
class PostVotePolicy
{
    public function delete(User $user, PostVote $vote): bool
    {
        return $user->id === $vote->user_id || $user->role === 'admin';
    }
}
