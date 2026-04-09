<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
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
        $posts = Post::query()
            ->whereBelongsTo(request()->user())
            ->with([
                'user:id,first_name,last_name,profile_photo_id',
                'user.profilePhoto:id,path',
            ])
            ->withCount('votes')
            ->latest()
            ->paginate(15);

        $posts->getCollection()->transform(function (Post $post): Post {
            $post->setAttribute('description_html', $this->renderMarkdown($post->description));

            return $post;
        });

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
        $validated = $request->validated();

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
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
        $post->load([
            'user:id,first_name,last_name,profile_photo_id',
            'user.profilePhoto:id,path',
            'votes.user',
        ]);
        $post->setAttribute('description_html', $this->renderMarkdown($post->description));

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

    private function renderMarkdown(?string $description): ?string
    {
        if (! filled($description)) {
            return null;
        }

        return Str::markdown($description, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}
