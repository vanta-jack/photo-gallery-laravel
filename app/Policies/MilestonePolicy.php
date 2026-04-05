<?php

namespace App\Policies;

use App\Models\Milestone;
use App\Models\User;

/**
 * MilestonePolicy
 * 
 * Milestone owner or admin can modify/delete milestones.
 */
class MilestonePolicy
{
    public function update(User $user, Milestone $milestone): bool
    {
        return $user->id === $milestone->user_id || $user->role === 'admin';
    }

    public function delete(User $user, Milestone $milestone): bool
    {
        return $user->id === $milestone->user_id || $user->role === 'admin';
    }
}
