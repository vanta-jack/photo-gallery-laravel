<?php

namespace App\Policies;

use App\Models\PhotoComment;
use App\Models\User;

/**
 * PhotoCommentPolicy
 * 
 * Comment author or admin can modify/delete comments.
 */
class PhotoCommentPolicy
{
    public function update(User $user, PhotoComment $comment): bool
    {
        return $user->id === $comment->user_id || $user->role === 'admin';
    }

    public function delete(User $user, PhotoComment $comment): bool
    {
        return $user->id === $comment->user_id || $user->role === 'admin';
    }
}
