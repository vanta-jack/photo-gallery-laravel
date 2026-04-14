<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\UpdateMilestoneRequest;
use App\Models\Album;
use App\Models\Milestone;
use App\Models\Photo;
use App\Models\User;
use App\Services\ImageProcessor;
use App\Services\PhotoAttachmentManager;
use App\Support\MarkdownRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * MilestoneController
 *
 * Manages milestones - life events tracking (baby months, school years, etc.).
 * Private by design: users only see their own milestones.
 */
class MilestoneController extends Controller
{
    /**
     * Display user's milestones
     *
     * Auth required: only show current user's milestones
     */
    public function index(): View
    {
        $user = auth()->user();

        $milestones = Milestone::query()
            ->whereBelongsTo($user)
            ->with('photo')
            ->latest()
            ->paginate(20);

        $milestones->getCollection()->transform(function (Milestone $milestone): Milestone {
            $milestone->setAttribute('description_html', MarkdownRenderer::toSafeHtml($milestone->description));

            return $milestone;
        });

        return view('milestones.index', compact('milestones'));
    }

    /**
     * Show milestone creation form
     */
    public function create(): View
    {
        $albums = collect();
        $userPhotos = collect();
        $curatedStages = Milestone::curatedStageOptions();

        $user = auth()->user();

        if ($user !== null) {
            $albums = Album::query()
                ->whereBelongsTo($user)
                ->orderByDesc('id')
                ->get(['id', 'title']);

            $userPhotos = Photo::query()
                ->whereBelongsTo($user)
                ->latest()
                ->get(['id', 'path', 'title']);
        }

        return view('milestones.create', compact('albums', 'userPhotos', 'curatedStages'));
    }

    /**
     * Store new milestone
     */
    public function store(
        StoreMilestoneRequest $request,
        ImageProcessor $imageProcessor,
        PhotoAttachmentManager $attachments,
    ): RedirectResponse {
        $validated = $request->validated();
        $user = $request->user();
        $selectedAlbum = $this->resolveSelectedAlbum($validated, $user);
        $uploadedPhotos = $attachments->storeUploadedPhotos(
            $attachments->extractPhotoPayloads($validated),
            $user,
            $imageProcessor,
            $validated['label'],
            $validated['description'] ?? null,
        );
        $existingPhotoIds = $this->resolveExistingPhotoIds($validated, $user->id, $attachments);
        $selectedPhotoId = $attachments->resolveMainPhotoId(
            $validated['main_photo_pick'] ?? null,
            $existingPhotoIds,
            $uploadedPhotos,
        );

        if ($selectedPhotoId === null) {
            throw ValidationException::withMessages([
                'photo_id' => 'Please select an existing photo or upload a new one.',
                'photo_ids' => 'Please select an existing photo or upload a new one.',
            ]);
        }

        $milestone = Milestone::create([
            'user_id' => $user->id,
            'photo_id' => $selectedPhotoId,
            'stage' => $this->resolveStage($validated),
            'label' => $validated['label'],
            'description' => $validated['description'] ?? null,
            'is_public' => (bool) ($validated['is_public'] ?? false),
        ]);

        $allPhotoIds = collect($existingPhotoIds)
            ->merge($uploadedPhotos->pluck('id')->map(static fn ($id): int => (int) $id))
            ->when($selectedPhotoId !== null, static fn ($ids) => $ids->push($selectedPhotoId))
            ->unique()
            ->values()
            ->all();

        $milestone->photos()->sync($allPhotoIds);
        $this->attachPhotosToAlbum($selectedAlbum, $allPhotoIds);

        return redirect()
            ->route('milestones.show', $milestone)
            ->with('status', 'Milestone created successfully!');
    }

    /**
     * Display single milestone
     */
    public function show(Milestone $milestone): View|RedirectResponse
    {
        // Privacy: only owner or admin can view
        if (auth()->id() !== $milestone->user_id && auth()->user()->role !== 'admin') {
            return redirect()
                ->route('milestones.index')
                ->with('error', 'You cannot view this milestone.');
        }

        $milestone->load('photo');
        $milestone->setAttribute('description_html', MarkdownRenderer::toSafeHtml($milestone->description));

        // Load spotlight photos (related milestone photos)
        $spotlightPhotos = $milestone->photos()->orderByDesc('id')->get();

        return view('milestones.show', compact('milestone', 'spotlightPhotos'));
    }

    /**
     * Show edit form
     */
    public function edit(Milestone $milestone): View
    {
        $this->authorize('update', $milestone);
        $milestone->loadMissing('photos');
        $curatedStages = Milestone::curatedStageOptions();

        $albums = Album::query()
            ->where('user_id', $milestone->user_id)
            ->orderByDesc('id')
            ->get(['id', 'title']);

        $userPhotos = Photo::query()
            ->where('user_id', $milestone->user_id)
            ->latest()
            ->get(['id', 'path', 'title']);

        return view('milestones.edit', compact('milestone', 'albums', 'userPhotos', 'curatedStages'));
    }

    /**
     * Update milestone
     */
    public function update(
        UpdateMilestoneRequest $request,
        Milestone $milestone,
        ImageProcessor $imageProcessor,
        PhotoAttachmentManager $attachments,
    ): RedirectResponse {
        $this->authorize('update', $milestone);

        $validated = $request->validated();
        $selectedAlbum = $this->resolveSelectedAlbum($validated, $request->user());
        $hasAttachmentInput = $request->exists('photo_ids')
            || $request->exists('main_photo_pick')
            || $request->exists('photo_id')
            || $request->filled('photo')
            || $request->filled('photos');

        $allPhotoIds = collect($milestone->photos()->pluck('photos.id')->all())
            ->push($milestone->photo_id)
            ->filter(static fn ($id): bool => is_numeric($id) && (int) $id > 0)
            ->map(static fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
        $selectedPhotoId = $milestone->photo_id;

        if ($hasAttachmentInput) {
            $uploadedPhotos = $attachments->storeUploadedPhotos(
                $attachments->extractPhotoPayloads($validated),
                $milestone->user,
                $imageProcessor,
                $validated['label'] ?? $milestone->label,
                $validated['description'] ?? $milestone->description,
            );
            $existingPhotoIds = $this->resolveExistingPhotoIds($validated, $milestone->user_id, $attachments);
            $selectedPhotoId = $attachments->resolveMainPhotoId(
                $validated['main_photo_pick'] ?? null,
                $existingPhotoIds,
                $uploadedPhotos,
            );

            $allPhotoIds = collect($existingPhotoIds)
                ->merge($uploadedPhotos->pluck('id')->map(static fn ($id): int => (int) $id))
                ->when($selectedPhotoId !== null, static fn ($ids) => $ids->push($selectedPhotoId))
                ->unique()
                ->values()
                ->all();
        }

        $data = $validated;
        unset($data['photo'], $data['photos'], $data['album_id'], $data['photo_ids'], $data['main_photo_pick'], $data['photo_id']);

        if (array_key_exists('stage', $data)) {
            $data['stage'] = $this->resolveStage($validated);
        }

        unset($data['stage_custom']);

        $data['photo_id'] = $selectedPhotoId;

        if (array_key_exists('is_public', $validated)) {
            $data['is_public'] = (bool) $validated['is_public'];
        }

        $milestone->update($data);

        if ($hasAttachmentInput) {
            $milestone->photos()->sync($allPhotoIds);
        }

        $this->attachPhotosToAlbum($selectedAlbum, $allPhotoIds);

        return redirect()
            ->route('milestones.show', $milestone)
            ->with('status', 'Milestone updated successfully!');
    }

    /**
     * Delete milestone
     */
    public function destroy(Milestone $milestone): RedirectResponse
    {
        $this->authorize('delete', $milestone);

        $milestone->delete();

        return redirect()
            ->route('milestones.index')
            ->with('status', 'Milestone deleted successfully!');
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
     * @param  array<int, int>  $photoIds
     */
    private function attachPhotosToAlbum(?Album $album, array $photoIds): void
    {
        if ($album === null) {
            return;
        }

        if ($photoIds === []) {
            return;
        }

        $album->photos()->syncWithoutDetaching($photoIds);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<int, int>
     */
    private function resolveExistingPhotoIds(array $validated, int $ownerId, PhotoAttachmentManager $attachments): array
    {
        $requestedIds = collect($validated['photo_ids'] ?? [])
            ->filter(static fn ($id): bool => $id !== null && $id !== '');

        if (isset($validated['photo_id']) && $validated['photo_id'] !== null && $validated['photo_id'] !== '') {
            $requestedIds->push((int) $validated['photo_id']);
        }

        $mainPick = $validated['main_photo_pick'] ?? null;
        if (is_string($mainPick) && str_starts_with($mainPick, 'existing:')) {
            $requestedIds->push((int) substr($mainPick, strlen('existing:')));
        }

        return $attachments->allowedExistingPhotoIds($requestedIds->all(), $ownerId);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function resolveStage(array $validated): string
    {
        if (($validated['stage'] ?? null) === 'custom') {
            return (string) ($validated['stage_custom'] ?? '');
        }

        return (string) $validated['stage'];
    }
}
