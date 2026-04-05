<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\GuestbookEntry;
use App\Http\Requests\StoreGuestbookEntryRequest;
use App\Http\Requests\UpdateGuestbookEntryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * GuestbookEntryController
 * 
 * Manages guestbook entries - visitor messages with optional photos.
 * Each entry creates a Post (for title/description) and GuestbookEntry (for photo link).
 */
class GuestbookEntryController extends Controller
{
    /**
     * Display all guestbook entries
     * 
     * Public route: anyone can read guestbook
     */
    public function index(): View
    {
        // Load entries with their posts and photos
        $entries = GuestbookEntry::with(['post.user', 'photo'])
            ->latest()
            ->paginate(20);

        return view('guestbook.index', compact('entries'));
    }

    /**
     * Show form to create guestbook entry
     */
    public function create(): View
    {
        return view('guestbook.create');
    }

    /**
     * Store new guestbook entry
     * 
     * Creates Post first, then GuestbookEntry linking to it
     */
    public function store(StoreGuestbookEntryRequest $request): RedirectResponse
    {
        // Create the underlying post
        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Create guestbook entry linking to post
        $entry = GuestbookEntry::create([
            'post_id' => $post->id,
            'photo_id' => $request->photo_id,
        ]);

        return redirect()
            ->route('guestbook.index')
            ->with('status', 'Guestbook entry added!');
    }

    /**
     * Show edit form
     */
    public function edit(GuestbookEntry $guestbook): View
    {
        $this->authorize('update', $guestbook);

        // Load post for form pre-population
        $guestbook->load('post');

        return view('guestbook.edit', compact('guestbook'));
    }

    /**
     * Update guestbook entry
     * 
     * Updates the underlying Post and GuestbookEntry
     */
    public function update(UpdateGuestbookEntryRequest $request, GuestbookEntry $guestbook): RedirectResponse
    {
        $this->authorize('update', $guestbook);

        // Update post if title/description changed
        if ($request->has('title') || $request->has('description')) {
            $guestbook->post->update([
                'title' => $request->input('title', $guestbook->post->title),
                'description' => $request->input('description', $guestbook->post->description),
            ]);
        }

        // Update photo if changed
        if ($request->has('photo_id')) {
            $guestbook->update(['photo_id' => $request->photo_id]);
        }

        return redirect()
            ->route('guestbook.index')
            ->with('status', 'Guestbook entry updated!');
    }

    /**
     * Delete guestbook entry
     * 
     * Deletes both GuestbookEntry and underlying Post
     */
    public function destroy(GuestbookEntry $guestbook): RedirectResponse
    {
        $this->authorize('delete', $guestbook);

        // Delete post (cascade deletes guestbook entry via foreign key)
        $guestbook->post->delete();

        return redirect()
            ->route('guestbook.index')
            ->with('status', 'Guestbook entry deleted!');
    }
}
