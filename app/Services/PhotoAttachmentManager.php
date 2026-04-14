<?php

namespace App\Services;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Validation\ValidationException;

class PhotoAttachmentManager
{
    /**
     * @param  array<string, mixed>  $validated
     * @return array<int, string>
     */
    public function extractPhotoPayloads(array $validated): array
    {
        if (isset($validated['photos']) && is_array($validated['photos'])) {
            $photos = array_values(array_filter(
                $validated['photos'],
                static fn ($photo): bool => is_string($photo) && $photo !== '',
            ));

            if ($photos !== []) {
                return $photos;
            }
        }

        if (isset($validated['photo']) && is_string($validated['photo']) && $validated['photo'] !== '') {
            return [$validated['photo']];
        }

        return [];
    }

    /**
     * @param  array<int, string>  $photoPayloads
     */
    public function storeUploadedPhotos(
        array $photoPayloads,
        ?User $user,
        ImageProcessor $imageProcessor,
        string $baseTitle = 'Photo',
        ?string $description = null,
    ): EloquentCollection {
        $uploadedPhotos = new EloquentCollection;

        if ($photoPayloads === []) {
            return $uploadedPhotos;
        }

        $uploaderId = $this->resolveUploaderId($user);
        $totalUploads = count($photoPayloads);

        foreach ($photoPayloads as $index => $photoData) {
            if (! is_string($photoData) || ! $this->hasSupportedClientImageData($photoData)) {
                continue;
            }

            $title = $totalUploads > 1 ? sprintf('%s (%d)', $baseTitle, $index + 1) : $baseTitle;

            $uploadedPhotos->push(Photo::create([
                'user_id' => $uploaderId,
                'path' => $imageProcessor->process($photoData),
                'title' => mb_substr(trim($title) !== '' ? $title : 'Photo', 0, 255),
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

    /**
     * @param  array<int, int>  $existingPhotoIds
     */
    public function resolveMainPhotoId(
        ?string $mainPick,
        array $existingPhotoIds,
        EloquentCollection $uploadedPhotos,
    ): ?int {
        if (is_string($mainPick) && str_starts_with($mainPick, 'existing:')) {
            $id = (int) substr($mainPick, 9);

            return in_array($id, $existingPhotoIds, true) ? $id : null;
        }

        if (is_string($mainPick) && str_starts_with($mainPick, 'upload:')) {
            $index = (int) substr($mainPick, 7);
            $photo = $uploadedPhotos->values()->get($index);

            return $photo?->id;
        }

        if ($existingPhotoIds !== []) {
            return $existingPhotoIds[0];
        }

        return $uploadedPhotos->first()?->id;
    }

    /**
     * @param  array<int, mixed>  $requestedIds
     * @return array<int, int>
     */
    public function allowedExistingPhotoIds(array $requestedIds, int $ownerId): array
    {
        $ids = collect($requestedIds)
            ->map(static fn ($id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        return Photo::query()
            ->where('user_id', $ownerId)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    private function hasSupportedClientImageData(string $photoData): bool
    {
        return preg_match('/^data:image\/(webp|png|jpeg|jpg);base64,/', $photoData) === 1;
    }

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
}
