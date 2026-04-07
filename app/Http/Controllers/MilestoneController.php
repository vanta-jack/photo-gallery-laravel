<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\UpdateMilestoneRequest;
use App\Models\Album;
use App\Models\Milestone;
use App\Models\Photo;
use App\Models\User;
use App\Services\ImageProcessor;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
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
            $milestone->setAttribute('description_html', $this->renderMarkdown($milestone->description));

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
    public function store(StoreMilestoneRequest $request, ImageProcessor $imageProcessor): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();
        $selectedAlbum = $this->resolveSelectedAlbum($validated, $user);
        $photoPayloads = $this->extractPhotoPayloads($validated);
        $uploadedPhotos = $this->storeUploadedPhotos(
            $photoPayloads,
            $user,
            $imageProcessor,
            $validated['label'],
            $validated['description'] ?? null,
        );

        $selectedPhotoId = $this->resolveSelectedPhotoId($validated, $uploadedPhotos);

        if ($selectedPhotoId === null) {
            throw ValidationException::withMessages([
                'photo_id' => 'Please select an existing photo or upload a new one.',
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

        $this->attachPhotosToAlbum($selectedAlbum, $uploadedPhotos, $selectedPhotoId);

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
        $milestone->setAttribute('description_html', $this->renderMarkdown($milestone->description));

        return view('milestones.show', compact('milestone'));
    }

    /**
     * Show edit form
     */
    public function edit(Milestone $milestone): View
    {
        $this->authorize('update', $milestone);
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
    public function update(UpdateMilestoneRequest $request, Milestone $milestone, ImageProcessor $imageProcessor): RedirectResponse
    {
        $this->authorize('update', $milestone);

        $validated = $request->validated();
        $selectedAlbum = $this->resolveSelectedAlbum($validated, $request->user());
        $photoPayloads = $this->extractPhotoPayloads($validated);
        $uploadedPhotos = $this->storeUploadedPhotos(
            $photoPayloads,
            $request->user(),
            $imageProcessor,
            $validated['label'] ?? $milestone->label,
            $validated['description'] ?? $milestone->description,
        );

        $selectedPhotoId = $this->resolveSelectedPhotoId($validated, $uploadedPhotos);

        $data = $validated;
        unset($data['photo'], $data['photos'], $data['album_id']);

        if (array_key_exists('stage', $data)) {
            $data['stage'] = $this->resolveStage($validated);
        }

        unset($data['stage_custom']);

        if (array_key_exists('photo_id', $data) && $data['photo_id'] === null) {
            unset($data['photo_id']);
        }

        $shouldSetPhotoId = $uploadedPhotos->isNotEmpty()
            || (array_key_exists('photo_id', $validated) && $validated['photo_id'] !== null);

        if ($selectedPhotoId !== null && $shouldSetPhotoId) {
            $data['photo_id'] = $selectedPhotoId;
        }

        if (array_key_exists('is_public', $validated)) {
            $data['is_public'] = (bool) $validated['is_public'];
        }

        $milestone->update($data);

        $albumPhotoId = $selectedPhotoId ?? $milestone->photo_id;
        $this->attachPhotosToAlbum($selectedAlbum, $uploadedPhotos, $albumPhotoId);

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
     * @param  array<int, string>  $photoPayloads
     */
    private function storeUploadedPhotos(
        array $photoPayloads,
        User $user,
        ImageProcessor $imageProcessor,
        string $label,
        ?string $description,
    ): EloquentCollection {
        $uploadedPhotos = new EloquentCollection;

        if ($photoPayloads === []) {
            return $uploadedPhotos;
        }

        $totalUploads = count($photoPayloads);

        foreach ($photoPayloads as $index => $photoData) {
            if (! is_string($photoData) || ! $this->hasSupportedClientImageData($photoData)) {
                continue;
            }

            $uploadedPhotos->push(Photo::create([
                'user_id' => $user->id,
                'path' => $imageProcessor->process($photoData),
                'title' => $this->resolvePhotoTitle($label, $index, $totalUploads),
                'description' => $description,
            ]));
        }

        if ($uploadedPhotos->isEmpty()) {
            throw ValidationException::withMessages([
                'photo' => 'None of the selected files could be uploaded.',
            ]);
        }

        return $uploadedPhotos;
    }

    private function resolvePhotoTitle(string $label, int $index, int $totalUploads): string
    {
        $baseTitle = trim($label) !== '' ? $label : 'Milestone Photo';
        $title = $totalUploads > 1 ? sprintf('%s (%d)', $baseTitle, $index + 1) : $baseTitle;

        return Str::limit($title, 255, '');
    }

    private function resolveSelectedPhotoId(array $validated, EloquentCollection $uploadedPhotos): ?int
    {
        if (isset($validated['photo_id']) && $validated['photo_id'] !== null) {
            return (int) $validated['photo_id'];
        }

        return $uploadedPhotos->first()?->id;
    }

    private function attachPhotosToAlbum(?Album $album, EloquentCollection $uploadedPhotos, ?int $selectedPhotoId): void
    {
        if ($album === null) {
            return;
        }

        $photoIds = $uploadedPhotos->pluck('id')->when(
            $selectedPhotoId !== null,
            fn ($collection) => $collection->push($selectedPhotoId),
        )->unique()->values()->all();

        if ($photoIds === []) {
            return;
        }

        $album->photos()->syncWithoutDetaching($photoIds);
    }

    private function hasSupportedClientImageData(string $photoData): bool
    {
        return preg_match('/^data:image\/(webp|png|jpeg|jpg);base64,/', $photoData) === 1;
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
