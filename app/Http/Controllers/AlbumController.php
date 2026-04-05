<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * AlbumController
 * 
 * Manages photo albums - collections of photos grouped by user.
 * Demonstrates: Eager loading, many-to-many relationships, privacy controls
 */
class AlbumController extends Controller
{
    /**
     * Display all albums
     * 
     * Public route: shows all public albums + user's own private ones
     * Eager load coverPhoto for thumbnails and user for attribution
     */
    public function index(): View
    {
        // Load public albums OR user's own albums
        $query = Album::with(['coverPhoto', 'user']);
        
        if (auth()->check()) {
            // Logged in: show public albums + own private albums
            $query->where(function($q) {
                $q->where('is_private', false)
                  ->orWhere('user_id', auth()->id());
            });
        } else {
            // Not logged in: only public albums
            $query->where('is_private', false);
        }
        
        $albums = $query->latest()->paginate(12);

        return view('albums.index', compact('albums'));
    }

    /**
     * Show album creation form
     */
    public function create(): View
    {
        return view('albums.create');
    }

    /**
     * Store new album
     * 
     * Associates album with authenticated user automatically
     */
    public function store(StoreAlbumRequest $request): RedirectResponse
    {
        $album = Album::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'is_private' => $request->boolean('is_private', false),
            'cover_photo_id' => $request->cover_photo_id,
        ]);

        return redirect()
            ->route('albums.show', $album)
            ->with('status', 'Album created successfully!');
    }

    /**
     * Display single album with its photos
     * 
     * Eager load photos with their users to avoid N+1 queries
     * Check privacy: deny access to private albums unless owner
     */
    public function show(Album $album): View|RedirectResponse
    {
        // Privacy check: private albums only visible to owner or admin
        if ($album->is_private) {
            if (!auth()->check() || 
                (auth()->id() !== $album->user_id && auth()->user()->role !== 'admin')) {
                return redirect()
                    ->route('albums.index')
                    ->with('error', 'This album is private.');
            }
        }

        // Load photos with their uploaders
        $album->load(['photos.user', 'user', 'coverPhoto']);

        return view('albums.show', compact('album'));
    }

    /**
     * Show edit form
     */
    public function edit(Album $album): View
    {
        // Throws 403 if user can't update
        $this->authorize('update', $album);

        return view('albums.edit', compact('album'));
    }

    /**
     * Update album
     */
    public function update(UpdateAlbumRequest $request, Album $album): RedirectResponse
    {
        $this->authorize('update', $album);

        $album->update($request->validated());

        return redirect()
            ->route('albums.show', $album)
            ->with('status', 'Album updated successfully!');
    }

    /**
     * Delete album
     * 
     * Note: Photos in album are NOT deleted (many-to-many relationship)
     * Only the album and its pivot table entries are removed
     */
    public function destroy(Album $album): RedirectResponse
    {
        $this->authorize('delete', $album);

        $album->delete();

        return redirect()
            ->route('albums.index')
            ->with('status', 'Album deleted successfully!');
    }
}
