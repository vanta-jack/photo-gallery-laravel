<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StorePostVoteRequest
 * 
 * Validates voting on a post.
 * Simple like/unlike system (no upvote/downvote).
 */
class StorePostVoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * No additional validation needed - vote is just user+post
     */
    public function rules(): array
    {
        return [];
    }
}
