<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * PhotoController
 * 
 * Manages all photo-related operations in the gallery.
 * 
 * Key Laravel Concepts Demonstrated:
 * - Dependency Injection: Form Requests are auto-injected and validated
 * - Eloquent ORM: Database operations via Model methods
 * - File Storage: Using Laravel's filesystem abstraction
 * - Eager Loading: Preventing N+1 query problems
 * - Resourceful Routing: Standard CRUD method names
 */
class PhotoController extends Controller
{
    /**
     * Display a listing of photos.
     * 
     * Best Practice: Use pagination for large datasets.
     * Eager load 'user' to avoid N+1 queries when displaying uploader info.
     */
    public function index(): View
    {
        // with('user') loads the relationship in a single query
        // latest() orders by created_at descending
        // paginate(12) returns 12 items per page with pagination links
        $photos = Photo::with('user')
            ->latest()
            ->paginate(12);

        return view('photos.index', compact('photos'));
    }

    /**
     * Show the form for creating a new photo.
     */
    public function create(): View
    {
        return view('photos.create');
    }

    /**
     * Store a newly created photo in storage.
     * 
     * Flow:
     * 1. FormRequest validates input automatically before this method runs
     * 2. Store uploaded file using Laravel's Storage facade
     * 3. Create database record with file path
     * 4. Redirect with success message
     */
    public function store(StorePhotoRequest $request): RedirectResponse
    {
        // Store the uploaded file in the 'public' disk
        // Returns path like: "photos/abc123.jpg"
        // The 'public' disk is configured in config/filesystems.php
        $path = $request->file('photo')->store('photos', 'public');

        // Create the photo record
        // Using create() requires fillable fields in the Model (which Photo has)
        $photo = Photo::create([
            'user_id' => $request->user()->id, // Associate with authenticated user
            'path' => $path,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Redirect to the photo show page with a flash message
        return redirect()
            ->route('photos.show', $photo)
            ->with('status', 'Photo uploaded successfully!');
    }

    /**
     * Display the specified photo.
     * 
     * Eager load relationships needed for the view:
     * - user: Who uploaded it
     * - comments: Discussion thread
     * - ratings: For average rating calculation
     */
    public function show(Photo $photo): View
    {
        // Route Model Binding automatically fetches the Photo by ID
        // If not found, Laravel throws 404 automatically

        // Load relationships to prevent N+1 in the view
        $photo->load(['user', 'comments.user', 'ratings']);

        return view('photos.show', compact('photo'));
    }

    /**
     * Show the form for editing the specified photo.
     */
    public function edit(Photo $photo): View | RedirectResponse
    {
        // Authorization - used for both edit and update to ensure only owners/admins can modify. See Controller.php for authorize() method.
        try {
            $this->authorize('update', $photo);
        } catch (AuthorizationException $e) {
            return redirect()->route('photos.index')->with('error', 'You do not have permission to edit this photo.');
        }

        // Redirect instead of a 403
        if (!$this->user()->can('update', $photo)) {
            return redirect()->route('photos.index')->with('error', 'You do not have permission to edit this photo.');
        }

        return view('photos.edit', compact('photo'));
    }

    /**
     * Update the specified photo in storage.
     * 
     * Handles both metadata updates and optional file replacement.
     * If a new file is uploaded, the old file is deleted to save space.
     */
    public function update(UpdatePhotoRequest $request, Photo $photo): RedirectResponse
    {
        // Authorization - same as edit()
        $this->authorize('update', $photo);

        // Prepare data array for update
        $data = $request->validated();

        // Check if a new photo was uploaded
        if ($request->hasFile('photo')) {
            // Delete old file from storage to prevent orphaned files
            // Storage::delete() is safe even if file doesn't exist
            Storage::disk('public')->delete($photo->path);

            // Store new file
            $data['path'] = $request->file('photo')->store('photos', 'public');
        }

        // Update the model
        // update() only modifies fields present in $data
        $photo->update($data);

        return redirect()
            ->route('photos.show', $photo)
            ->with('status', 'Photo updated successfully!');
    }

    /**
     * Remove the specified photo from storage.
     * 
     * Cascading deletes:
     * - Database: Foreign keys with cascadeOnDelete() handle related records
     * - Storage: We must manually delete the file
     */
    public function destroy(Photo $photo): RedirectResponse
    {
        // Authorization
        $this->authorize('delete', $photo);

        // Delete file from storage
        Storage::disk('public')->delete($photo->path);

        // Delete database record
        // Related comments/ratings are auto-deleted via cascadeOnDelete in migrations
        $photo->delete();

        return redirect()
            ->route('photos.index')
            ->with('status', 'Photo deleted successfully!');
    }
}

