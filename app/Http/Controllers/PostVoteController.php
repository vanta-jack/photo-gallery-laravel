<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostVote;
use App\Http\Requests\StorePostVoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * PostVoteController
 * 
 * Manages likes on posts (simple like/unlike system).
 * One vote per user per post enforced by database unique constraint.
 */
class PostVoteController extends Controller
{
    /**
     * Show vote form (or inline button on post.show)
     */
    public function create(Post $post): View
    {
        // Check if user already voted
        $existingVote = PostVote::where('post_id', $post->id)
            ->where('user_id', auth()->id())
            ->first();

        return view('votes.create', compact('post', 'existingVote'));
    }

    /**
     * Toggle vote (like/unlike)
     * 
     * If user already voted, remove vote (unlike)
     * If not voted, add vote (like)
     */
    public function store(StorePostVoteRequest $request, Post $post): RedirectResponse
    {
        // Check for existing vote
        $existingVote = PostVote::where('post_id', $post->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingVote) {
            // Unlike: remove vote
            $existingVote->delete();
            $message = 'Vote removed!';
        } else {
            // Like: add vote
            PostVote::create([
                'post_id' => $post->id,
                'user_id' => $request->user()->id,
            ]);
            $message = 'Post liked!';
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('status', $message);
    }

    /**
     * Delete vote (unlike)
     */
    public function destroy(Post $post, PostVote $vote): RedirectResponse
    {
        $this->authorize('delete', $vote);

        $vote->delete();

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Vote removed!');
    }
}
