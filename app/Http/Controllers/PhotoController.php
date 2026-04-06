<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\User;
use App\Services\ImageProcessor;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
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
     * 2. Client has already cropped, resized, and converted to WebP
     * 3. Store the processed WebP image
     * 4. Create database record with file path
     * 5. Redirect with success message
     */
    public function store(StorePhotoRequest $request, ImageProcessor $imageProcessor): RedirectResponse
    {
        $uploaderId = $this->resolveUploaderId($request->user());

        // Get base64 WebP data from client-side processing
        $photoData = $request->input('photo');
        
        if (!is_string($photoData) || !str_starts_with($photoData, 'data:image/webp;base64,')) {
            throw ValidationException::withMessages([
                'photo' => 'Please select and process an image.',
            ]);
        }

        // Store the already-processed WebP image
        $path = $imageProcessor->process($photoData);
        
        $customTitle = trim((string) $request->input('title', ''));
        $title = $customTitle !== '' ? $customTitle : 'Cropped Photo';

        $photo = Photo::create([
            'user_id' => $uploaderId,
            'path' => $path,
            'title' => $title,
            'description' => $request->input('description'),
        ]);

        return redirect()
            ->route('photos.show', $photo)
            ->with('status', 'Photo uploaded successfully!');
    }

    /**
     * Resolve the uploader account used for incoming uploads.
     * Guest uploads are attributed to a reusable guest uploader account.
     */
    private function resolveUploaderId(?User $user): int
    {
        if ($user !== null) {
            return $user->id;
        }

        $guestUploader = User::query()
            ->where('role', 'guest')
            ->whereNull('email')
            ->first();

        if ($guestUploader !== null) {
            return $guestUploader->id;
        }

        return User::create([
            'role' => 'guest',
            'email' => null,
            'first_name' => 'Guest',
            'last_name' => 'Uploader',
            'password' => null,
            'profile_photo_id' => null,
        ])->id;
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
     * 
     * Authorization: Only photo owner or admin can edit
     * Policy check happens automatically via authorize()
     */
    public function edit(Photo $photo): View
    {
        // Throws AuthorizationException (403) if user can't update
        $this->authorize('update', $photo);

        return view('photos.edit', compact('photo'));
    }

    /**
     * Update the specified photo in storage.
     * 
     * Handles both metadata updates and optional file replacement.
     * If a new photo is uploaded, the old file is deleted to save space.
     * Client handles all image processing (WebP conversion, resizing, compression).
     */
    public function update(UpdatePhotoRequest $request, Photo $photo, ImageProcessor $imageProcessor): RedirectResponse
    {
        // Authorization - same as edit()
        $this->authorize('update', $photo);

        // Prepare data array for update
        $data = $request->validated();
        unset($data['photo']);

        // Check if a new photo was provided (base64 WebP from client)
        $photoData = $request->input('photo');
        
        if (is_string($photoData) && str_starts_with($photoData, 'data:image/webp;base64,')) {
            // Delete old file from storage to prevent orphaned files
            Storage::disk('public')->delete($photo->path);

            // Store the already-processed WebP image
            $data['path'] = $imageProcessor->process($photoData);
        }

        // Update the model
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
