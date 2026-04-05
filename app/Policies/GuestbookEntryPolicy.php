<?php

namespace App\Policies;

use App\Models\GuestbookEntry;
use App\Models\User;

/**
 * GuestbookEntryPolicy
 * 
 * Author (via post.user_id) or admin can modify/delete entries.
 */
class GuestbookEntryPolicy
{
    public function update(User $user, GuestbookEntry $entry): bool
    {
        // Load post to check authorship
        return $user->id === $entry->post->user_id || $user->role === 'admin';
    }

    public function delete(User $user, GuestbookEntry $entry): bool
    {
        return $user->id === $entry->post->user_id || $user->role === 'admin';
    }
}
