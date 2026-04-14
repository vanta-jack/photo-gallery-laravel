<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestbookEntryRequest;
use App\Http\Requests\UpdateGuestbookEntryRequest;
use App\Models\GuestbookEntry;
use App\Models\Photo;
use App\Models\Post;
use App\Services\ImageProcessor;
use App\Services\PhotoAttachmentManager;
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
        return view('guestbook.index');
    }

    /**
     * Show form to create guestbook entry
     */
    public function create(): View
    {
        $userPhotos = collect();

        if (auth()->user() !== null) {
            $userPhotos = Photo::query()
                ->whereBelongsTo(auth()->user())
                ->latest()
                ->get(['id', 'path', 'title']);
        }

        return view('guestbook.create', compact('userPhotos'));
    }

    /**
     * Store new guestbook entry
     *
     * Creates Post first, then GuestbookEntry linking to it
     */
    public function store(
        StoreGuestbookEntryRequest $request,
        ImageProcessor $imageProcessor,
        PhotoAttachmentManager $attachments,
    ): RedirectResponse {
        $validated = $request->validated();
        $photoPayloads = $attachments->extractPhotoPayloads($validated);
        $uploadedPhotos = $attachments->storeUploadedPhotos(
            $photoPayloads,
            $request->user(),
            $imageProcessor,
            $validated['title'] ?? 'Guestbook Photo',
            $validated['description'] ?? null,
        );

        $existingPhotoIds = $request->user() !== null
            ? $attachments->allowedExistingPhotoIds($validated['photo_ids'] ?? [], $request->user()->id)
            : [];

        $mainPhotoId = $attachments->resolveMainPhotoId(
            $validated['main_photo_pick'] ?? null,
            $existingPhotoIds,
            $uploadedPhotos,
        );

        $post = Post::create([
            'user_id' => $request->user()?->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
        ]);

        $entry = GuestbookEntry::create([
            'post_id' => $post->id,
            'photo_id' => $mainPhotoId,
        ]);

        $allPhotoIds = collect($existingPhotoIds)
            ->merge($uploadedPhotos->pluck('id')->map(static fn ($id): int => (int) $id))
            ->unique()
            ->values()
            ->all();

        if ($allPhotoIds !== []) {
            $entry->photos()->sync($allPhotoIds);
        }

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

        $guestbook->load(['post.user', 'photos']);
        $owner = $guestbook->post?->user ?? request()->user();
        $userPhotos = collect();

        if ($owner !== null) {
            $userPhotos = Photo::query()
                ->whereBelongsTo($owner)
                ->latest()
                ->get(['id', 'path', 'title']);
        }

        return view('guestbook.edit', compact('guestbook', 'userPhotos'));
    }

    /**
     * Update guestbook entry
     *
     * Updates the underlying Post and GuestbookEntry
     */
    public function update(
        UpdateGuestbookEntryRequest $request,
        GuestbookEntry $guestbook,
        ImageProcessor $imageProcessor,
        PhotoAttachmentManager $attachments,
    ): RedirectResponse {
        $this->authorize('update', $guestbook);
        $validated = $request->validated();
        $guestbook->loadMissing('post.user');
        $mainPhotoPick = $validated['main_photo_pick'] ?? null;

        $postData = [];
        if (array_key_exists('title', $validated)) {
            $postData['title'] = $validated['title'];
        }
        if (array_key_exists('description', $validated)) {
            $postData['description'] = $validated['description'];
        }
        if ($postData !== []) {
            $guestbook->post->update($postData);
        }

        $hasAttachmentInput = $request->exists('photo_ids')
            || $request->exists('photo_id')
            || $request->filled('photo')
            || $request->filled('photos')
            || ($request->has('main_photo_pick') && $request->input('main_photo_pick') === '');

        if ($hasAttachmentInput) {
            $photoPayloads = $attachments->extractPhotoPayloads($validated);
            $uploader = $guestbook->post?->user ?? $request->user();
            $uploadedPhotos = $attachments->storeUploadedPhotos(
                $photoPayloads,
                $uploader,
                $imageProcessor,
                $validated['title'] ?? $guestbook->post->title ?? 'Guestbook Photo',
                $validated['description'] ?? $guestbook->post->description,
            );

            $ownerId = (int) ($guestbook->post?->user_id ?? $request->user()?->id ?? 0);
            $existingPhotoIds = $ownerId > 0
                ? $attachments->allowedExistingPhotoIds($validated['photo_ids'] ?? [], $ownerId)
                : [];

            $mainPhotoId = $attachments->resolveMainPhotoId(
                $mainPhotoPick,
                $existingPhotoIds,
                $uploadedPhotos,
            );

            $allPhotoIds = collect($existingPhotoIds)
                ->merge($uploadedPhotos->pluck('id')->map(static fn ($id): int => (int) $id))
                ->unique()
                ->values()
                ->all();

            $guestbook->photos()->sync($allPhotoIds);

            if ($mainPhotoId !== null) {
                $guestbook->update(['photo_id' => $mainPhotoId]);
            } elseif ($allPhotoIds === []) {
                $guestbook->update(['photo_id' => null]);
            } else {
                $guestbook->update(['photo_id' => $allPhotoIds[0]]);
            }
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
