<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;
use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use App\Services\ImageProcessor;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
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
        $user = request()->user();

        $photos = Photo::query()
            ->whereBelongsTo($user)
            ->with('user')
            ->latest()
            ->paginate(12);

        $ownedPhotos = Photo::query()
            ->whereBelongsTo($user)
            ->latest()
            ->get(['id', 'path', 'title', 'description', 'created_at']);

        return view('photos.index', compact('photos', 'ownedPhotos'));
    }

    /**
     * Show the form for creating a new photo.
     */
    public function create(): View
    {
        $albums = collect();

        if (request()->user() !== null) {
            $albums = Album::query()
                ->whereBelongsTo(request()->user())
                ->orderByDesc('id')
                ->get(['id', 'title']);
        }

        return view('photos.create', compact('albums'));
    }

    /**
     * Store a newly created photo in storage.
     *
     * Flow:
     * 1. FormRequest validates input automatically before this method runs
     * 2. Client has already cropped and resized image
     * 3. Store the processed image file (WebP preferred, PNG/JPEG fallback)
     * 4. Create database record with file path
     * 5. Redirect with success message
     */
    public function store(StorePhotoRequest $request, ImageProcessor $imageProcessor): RedirectResponse
    {
        $validated = $request->validated();
        $uploaderId = $this->resolveUploaderId($request->user());
        $selectedAlbum = $this->resolveSelectedAlbum($validated, $request->user());
        $photoPayloads = $this->extractPhotoPayloads($validated);

        if ($photoPayloads === []) {
            throw ValidationException::withMessages([
                'photo' => 'Please select and process an image.',
            ]);
        }

        $uploadedPhotos = new EloquentCollection;
        $failedUploads = 0;
        $customTitle = trim((string) $request->input('title', ''));
        $title = $customTitle !== '' ? $customTitle : 'Photo';

        foreach ($photoPayloads as $photoData) {
            if (! is_string($photoData) || ! $this->hasSupportedClientImageData($photoData)) {
                $failedUploads++;

                continue;
            }

            $photo = Photo::create([
                'user_id' => $uploaderId,
                'path' => $imageProcessor->process($photoData),
                'title' => $title,
                'description' => $request->input('description'),
            ]);

            if ($selectedAlbum !== null) {
                $selectedAlbum->photos()->syncWithoutDetaching([$photo->id]);
            }

            $uploadedPhotos->push($photo);
        }

        if ($uploadedPhotos->isEmpty()) {
            throw ValidationException::withMessages([
                'photo' => 'None of the selected files could be uploaded.',
            ]);
        }

        if ($uploadedPhotos->count() === 1 && count($photoPayloads) === 1 && $failedUploads === 0) {
            return redirect()
                ->route('photos.show', $uploadedPhotos->first())
                ->with('status', 'Photo uploaded successfully!');
        }

        $successCount = $uploadedPhotos->count();
        $statusMessage = sprintf(
            '%d photo%s uploaded successfully!',
            $successCount,
            $successCount === 1 ? '' : 's',
        );

        $redirect = redirect()
            ->route('photos.index')
            ->with('status', $statusMessage);

        if ($failedUploads > 0) {
            $redirect->with('error', sprintf('%d photo%s could not be uploaded.', $failedUploads, $failedUploads === 1 ? '' : 's'));
        }

        return $redirect;
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
     * Validate client image payload format.
     */
    private function hasSupportedClientImageData(string $photoData): bool
    {
        return preg_match('/^data:image\/(webp|png|jpeg|jpg);base64,/', $photoData) === 1;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<int, string>
     */
    private function extractPhotoPayloads(array $validated): array
    {
        if (isset($validated['photos']) && is_array($validated['photos'])) {
            return array_values(array_filter(
                $validated['photos'],
                static fn ($photo): bool => is_string($photo) && $photo !== '',
            ));
        }

        if (isset($validated['photo']) && is_string($validated['photo']) && $validated['photo'] !== '') {
            return [$validated['photo']];
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function resolveSelectedAlbum(array $validated, ?User $user): ?Album
    {
        if (! isset($validated['album_id']) || $user === null) {
            return null;
        }

        return Album::query()
            ->whereKey((int) $validated['album_id'])
            ->whereBelongsTo($user)
            ->first();
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

        // Check if a new photo was provided (base64 image from client).
        $photoData = $request->input('photo');

        if (is_string($photoData) && $this->hasSupportedClientImageData($photoData)) {
            // Delete old file from storage to prevent orphaned files
            Storage::disk('public')->delete($photo->path);

            // Store the already-processed image and keep path in DB.
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
