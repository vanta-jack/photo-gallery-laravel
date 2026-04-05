<?php

namespace App\Policies;

use App\Models\User;

/**
 * UserPolicy
 * 
 * Users can update own profile, admin can update any.
 */
class UserPolicy
{
    public function update(User $currentUser, User $targetUser): bool
    {
        // User updating own profile OR admin
        return $currentUser->id === $targetUser->id || $currentUser->role === 'admin';
    }
}
