<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * PostController
 * 
 * Manages user posts (blog-style entries).
 * Posts support markdown in description field.
 */
class PostController extends Controller
{
    /**
     * Display all posts
     * 
     * Public route: anyone can read posts
     * Eager load author and vote counts
     */
    public function index(): View
    {
        $posts = Post::with('user')
            ->withCount('votes') // Adds votes_count attribute
            ->latest()
            ->paginate(15);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show post creation form
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Store new post
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post created successfully!');
    }

    /**
     * Display single post with votes
     */
    public function show(Post $post): View
    {
        // Load author and votes with voters
        $post->load(['user', 'votes.user']);

        return view('posts.show', compact('post'));
    }

    /**
     * Show edit form
     */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update post
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post updated successfully!');
    }

    /**
     * Delete post
     * 
     * Votes are cascade-deleted via foreign key constraints
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('status', 'Post deleted successfully!');
    }
}
