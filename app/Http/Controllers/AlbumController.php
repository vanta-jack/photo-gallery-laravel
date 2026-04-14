<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Models\Album;
use App\Models\Photo;
use App\Services\ImageProcessor;
use App\Services\PhotoAttachmentManager;
use App\Support\MarkdownRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
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
        $albums = Album::query()
            ->with(['coverPhoto', 'user'])
            ->whereBelongsTo(request()->user())
            ->orderByDesc('is_favorite')
            ->latest()
            ->paginate(12);

        $albums->getCollection()->transform(function (Album $album): Album {
            $album->setAttribute('description_html', MarkdownRenderer::toSafeHtml($album->description));

            return $album;
        });

        return view('albums.index', compact('albums'));
    }

    /**
     * Show album creation form
     */
    public function create(): View
    {
        $userPhotos = auth()->user()?->photos()->latest()->get() ?? collect();

        return view('albums.create', compact('userPhotos'));
    }

    /**
     * Store new album
     *
     * Associates album with authenticated user automatically
     */
    public function store(
        StoreAlbumRequest $request,
        ImageProcessor $imageProcessor,
        PhotoAttachmentManager $attachments,
    ): RedirectResponse {
        $validated = $request->validated();
        $existingPhotoIds = $attachments->allowedExistingPhotoIds($validated['photo_ids'] ?? [], $request->user()->id);
        $uploadedPhotos = $attachments->storeUploadedPhotos(
            $attachments->extractPhotoPayloads($validated),
            $request->user(),
            $imageProcessor,
            $validated['title'] ?? 'Album Photo',
            $validated['description'] ?? null,
        );
        $mainPhotoPick = $validated['main_photo_pick'] ?? null;
        $coverPhotoId = $attachments->resolveMainPhotoId(
            $mainPhotoPick,
            $existingPhotoIds,
            $uploadedPhotos,
        );
        $legacyCoverPhotoId = isset($validated['cover_photo_id']) ? (int) $validated['cover_photo_id'] : null;

        if (
            ($mainPhotoPick === null || $mainPhotoPick === '')
            && $legacyCoverPhotoId !== null
            && in_array($legacyCoverPhotoId, $existingPhotoIds, true)
        ) {
            $coverPhotoId = $legacyCoverPhotoId;
        }

        $allPhotoIds = collect($existingPhotoIds)
            ->merge($uploadedPhotos->pluck('id')->map(static fn ($id): int => (int) $id))
            ->when($coverPhotoId !== null, static fn ($ids) => $ids->push($coverPhotoId))
            ->unique()
            ->values()
            ->all();

        $album = Album::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_private' => $request->boolean('is_private'),
            'cover_photo_id' => $coverPhotoId,
        ]);

        $album->photos()->sync($allPhotoIds);

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
            if (! auth()->check() ||
                (auth()->id() !== $album->user_id && auth()->user()->role !== 'admin')) {
                return redirect()
                    ->route('home')
                    ->with('error', 'This album is private.');
            }
        }

        // Load photos with their uploaders, ratings, and comments
        $album->load(['photos.user', 'photos.ratings', 'photos.comments', 'user', 'coverPhoto']);
        $album->setAttribute('description_html', MarkdownRenderer::toSafeHtml($album->description));

        return view('albums.show', compact('album'));
    }

    /**
     * Show edit form
     */
    public function edit(Album $album): View
    {
        // Throws 403 if user can't update
        $this->authorize('update', $album);
        $album->loadMissing('photos');

        // Get all photos belonging to the authenticated user
        // Exclude photos already in the album to show separately
        $userPhotos = $album->user->photos()
            ->whereNotIn('photos.id', $album->photos->pluck('id'))
            ->latest()
            ->get();

        return view('albums.edit', compact('album', 'userPhotos'));
    }

    /**
     * Update album
     *
     * Syncs photos via pivot table using photo_ids array from form
     */
    public function update(
        UpdateAlbumRequest $request,
        Album $album,
        ImageProcessor $imageProcessor,
        PhotoAttachmentManager $attachments,
    ): RedirectResponse {
        $this->authorize('update', $album);
        $validated = $request->validated();
        $hasAttachmentInput = $request->exists('photo_ids')
            || $request->exists('main_photo_pick')
            || $request->exists('cover_photo_id')
            || $request->filled('photo')
            || $request->filled('photos');
        $coverPhotoId = $album->cover_photo_id;

        if ($hasAttachmentInput) {
            $existingPhotoIds = $attachments->allowedExistingPhotoIds($validated['photo_ids'] ?? [], $album->user_id);
            $uploadedPhotos = $attachments->storeUploadedPhotos(
                $attachments->extractPhotoPayloads($validated),
                $album->user,
                $imageProcessor,
                $validated['title'] ?? $album->title ?? 'Album Photo',
                $validated['description'] ?? $album->description,
            );
            $mainPhotoPick = $validated['main_photo_pick'] ?? null;
            $coverPhotoId = $attachments->resolveMainPhotoId(
                $mainPhotoPick,
                $existingPhotoIds,
                $uploadedPhotos,
            );
            $legacyCoverPhotoId = isset($validated['cover_photo_id']) ? (int) $validated['cover_photo_id'] : null;

            if (
                ($mainPhotoPick === null || $mainPhotoPick === '')
                && $legacyCoverPhotoId !== null
                && in_array($legacyCoverPhotoId, $existingPhotoIds, true)
            ) {
                $coverPhotoId = $legacyCoverPhotoId;
            }

            $allPhotoIds = collect($existingPhotoIds)
                ->merge($uploadedPhotos->pluck('id')->map(static fn ($id): int => (int) $id))
                ->when($coverPhotoId !== null, static fn ($ids) => $ids->push($coverPhotoId))
                ->unique()
                ->values()
                ->all();

            $album->photos()->sync($allPhotoIds);
        }

        $album->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_private' => $request->boolean('is_private'),
            'cover_photo_id' => $coverPhotoId,
        ]);

        return redirect()
            ->route('albums.show', $album)
            ->with('status', 'Album updated successfully!');
    }

    /**
     * Upload a photo from the album create modal and return JSON metadata.
     */
    public function storePhotoForCreate(StorePhotoRequest $request, ImageProcessor $imageProcessor): JsonResponse
    {
        $validated = $request->validated();

        $customTitle = trim((string) ($validated['title'] ?? ''));
        $title = $customTitle !== '' ? $customTitle : 'Photo';

        $photo = Photo::create([
            'user_id' => $request->user()->id,
            'path' => $imageProcessor->process($validated['photo']),
            'title' => $title,
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'message' => 'Photo uploaded successfully.',
            'photo' => [
                'id' => $photo->id,
                'title' => $photo->title,
                'path' => $photo->path,
                'url' => Storage::url($photo->path),
            ],
        ], 201);
    }

    /**
     * Upload a photo from the album edit modal and return JSON metadata.
     */
    public function storePhoto(StorePhotoRequest $request, Album $album, ImageProcessor $imageProcessor): JsonResponse
    {
        $this->authorize('update', $album);
        $validated = $request->validated();

        $customTitle = trim((string) ($validated['title'] ?? ''));
        $title = $customTitle !== '' ? $customTitle : 'Photo';

        $photo = Photo::create([
            'user_id' => $album->user_id,
            'path' => $imageProcessor->process($validated['photo']),
            'title' => $title,
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'message' => 'Photo uploaded successfully.',
            'photo' => [
                'id' => $photo->id,
                'title' => $photo->title,
                'path' => $photo->path,
                'url' => Storage::url($photo->path),
            ],
        ], 201);
    }

    /**
     * Delete album
     *
     * By default: Only album and pivot entries are deleted (photos remain)
     * Optional: If delete_photos=1, also delete all photos in the album
     */
    public function destroy(Album $album): RedirectResponse
    {
        $this->authorize('delete', $album);

        // Check if user wants to delete photos as well
        $deletePhotos = request()->boolean('delete_photos', false);

        if ($deletePhotos) {
            // Delete all photos in the album
            $album->photos()->each(function ($photo) {
                $photo->delete();
            });
        }

        $album->delete();

        $message = $deletePhotos
            ? 'Album and all photos deleted successfully!'
            : 'Album deleted successfully!';

        return redirect()
            ->route('albums.index')
            ->with('status', $message);
    }

    /**
     * Batch delete albums
     *
     * Validates ownership of all albums before deletion
     */
    public function batchDelete(): RedirectResponse
    {
        $validated = request()->validate([
            'album_ids' => 'required|array|min:1',
            'album_ids.*' => 'integer|exists:albums,id',
        ]);

        $albums = Album::whereIn('id', $validated['album_ids'])->get();

        // Verify user owns all selected albums
        foreach ($albums as $album) {
            $this->authorize('delete', $album);
        }

        $count = $albums->count();
        Album::whereIn('id', $validated['album_ids'])->delete();

        return redirect()
            ->route('albums.index')
            ->with('status', "{$count} album(s) deleted successfully!");
    }

    /**
     * Batch update visibility (is_private)
     *
     * Validates ownership of all albums before updating
     */
    public function batchUpdateVisibility(): RedirectResponse
    {
        $validated = request()->validate([
            'album_ids' => 'required|array|min:1',
            'album_ids.*' => 'integer|exists:albums,id',
            'is_private' => 'required|boolean',
        ]);

        $albums = Album::whereIn('id', $validated['album_ids'])->get();

        // Verify user owns all selected albums
        foreach ($albums as $album) {
            $this->authorize('update', $album);
        }

        Album::whereIn('id', $validated['album_ids'])
            ->update(['is_private' => $validated['is_private']]);

        $visibility = $validated['is_private'] ? 'private' : 'public';
        $count = $albums->count();

        return redirect()
            ->route('albums.index')
            ->with('status', "{$count} album(s) set to {$visibility}!");
    }

    /**
     * Batch update favorite status
     *
     * Validates ownership of all albums before updating
     */
    public function batchUpdateFavorite(): RedirectResponse
    {
        $validated = request()->validate([
            'album_ids' => 'required|array|min:1',
            'album_ids.*' => 'integer|exists:albums,id',
            'is_favorite' => 'required|boolean',
        ]);

        $albums = Album::whereIn('id', $validated['album_ids'])->get();

        // Verify user owns all selected albums
        foreach ($albums as $album) {
            $this->authorize('update', $album);
        }

        Album::whereIn('id', $validated['album_ids'])
            ->update(['is_favorite' => $validated['is_favorite']]);

        $status = $validated['is_favorite'] ? 'favorited' : 'unfavorited';
        $count = $albums->count();

        return redirect()
            ->route('albums.index')
            ->with('status', "{$count} album(s) {$status}!");
    }
}
