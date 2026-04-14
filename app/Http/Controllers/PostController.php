<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Photo;
use App\Models\Post;
use App\Services\ImageProcessor;
use App\Services\PhotoAttachmentManager;
use App\Support\MarkdownRenderer;
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
     * Display authenticated user's posts.
     */
    public function index(): View
    {
        return view('posts.index');
    }

    /**
     * Show post creation form
     */
    public function create(): View
    {
        $userPhotos = Photo::query()
            ->whereBelongsTo(request()->user())
            ->latest()
            ->get(['id', 'path', 'title']);

        return view('posts.create', compact('userPhotos'));
    }

    /**
     * Store new post
     */
    public function store(
        StorePostRequest $request,
        ImageProcessor $imageProcessor,
        PhotoAttachmentManager $attachments,
    ): RedirectResponse {
        $validated = $request->validated();
        $existingPhotoIds = $attachments->allowedExistingPhotoIds($validated['photo_ids'] ?? [], $request->user()->id);
        $uploadedPhotos = $attachments->storeUploadedPhotos(
            $attachments->extractPhotoPayloads($validated),
            $request->user(),
            $imageProcessor,
            $validated['title'] ?? 'Post Photo',
            $validated['description'] ?? null,
        );
        $mainPhotoId = $attachments->resolveMainPhotoId(
            $validated['main_photo_pick'] ?? null,
            $existingPhotoIds,
            $uploadedPhotos,
        );

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'photo_id' => $mainPhotoId,
        ]);

        $allPhotoIds = collect($existingPhotoIds)
            ->merge($uploadedPhotos->pluck('id')->map(static fn ($id): int => (int) $id))
            ->when($mainPhotoId !== null, static fn ($ids) => $ids->push($mainPhotoId))
            ->unique()
            ->values()
            ->all();

        if ($allPhotoIds !== []) {
            $post->photos()->sync($allPhotoIds);
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post created successfully!');
    }

    /**
     * Display single post with votes
     */
    public function show(Post $post): View
    {
        $post->load([
            'user:id,first_name,last_name,profile_photo_id',
            'user.profilePhoto:id,path',
            'votes.user',
            'photo:id,path,title',
            'photos:id,path,title',
        ]);
        $post->setAttribute('description_html', MarkdownRenderer::toSafeHtml($post->description));

        $mainPhoto = $post->photo ?? $post->photos->first();
        $attachmentPhotos = $post->photos
            ->when($mainPhoto !== null, static fn ($photos) => $photos->prepend($mainPhoto))
            ->unique('id')
            ->values();

        return view('posts.show', compact('post', 'mainPhoto', 'attachmentPhotos'));
    }

    /**
     * Show edit form
     */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);
        $post->loadMissing('photos');

        $userPhotos = Photo::query()
            ->whereBelongsTo($post->user)
            ->latest()
            ->get(['id', 'path', 'title']);

        return view('posts.edit', compact('post', 'userPhotos'));
    }

    /**
     * Update post
     */
    public function update(
        UpdatePostRequest $request,
        Post $post,
        ImageProcessor $imageProcessor,
        PhotoAttachmentManager $attachments,
    ): RedirectResponse {
        $this->authorize('update', $post);
        $validated = $request->validated();
        $hasAttachmentInput = $request->exists('photo_ids')
            || $request->exists('main_photo_pick')
            || $request->exists('photo_id')
            || $request->filled('photo')
            || $request->filled('photos');

        $data = $validated;
        unset($data['photo'], $data['photos'], $data['photo_ids'], $data['main_photo_pick'], $data['photo_id']);

        if ($hasAttachmentInput) {
            $existingPhotoIds = $attachments->allowedExistingPhotoIds($validated['photo_ids'] ?? [], $post->user_id);
            $uploadedPhotos = $attachments->storeUploadedPhotos(
                $attachments->extractPhotoPayloads($validated),
                $post->user,
                $imageProcessor,
                $validated['title'] ?? $post->title ?? 'Post Photo',
                $validated['description'] ?? $post->description,
            );
            $mainPhotoId = $attachments->resolveMainPhotoId(
                $validated['main_photo_pick'] ?? null,
                $existingPhotoIds,
                $uploadedPhotos,
            );
            $data['photo_id'] = $mainPhotoId;

            $allPhotoIds = collect($existingPhotoIds)
                ->merge($uploadedPhotos->pluck('id')->map(static fn ($id): int => (int) $id))
                ->when($mainPhotoId !== null, static fn ($ids) => $ids->push($mainPhotoId))
                ->unique()
                ->values()
                ->all();

            $post->photos()->sync($allPhotoIds);
        }

        $post->update($data);

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
