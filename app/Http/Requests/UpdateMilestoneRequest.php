<?php

namespace App\Http\Requests;

use App\Models\Album;
use App\Models\Milestone;
use App\Models\Photo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * UpdateMilestoneRequest
 *
 * Validates updating a milestone.
 */
class UpdateMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        /** @var Milestone|null $milestone */
        $milestone = $this->route('milestone');
        $ownerId = $milestone?->user_id ?? $this->user()?->id;

        return [
            'stage' => ['sometimes', 'string', Rule::in([...Milestone::curatedStageValues(), 'custom'])],
            'stage_custom' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn (): bool => $this->input('stage') === 'custom')],
            'label' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_public' => ['sometimes', 'boolean'],
            'photo_id' => [
                'nullable',
                'integer',
                Rule::exists('photos', 'id')->where(static fn ($query) => $query->where('user_id', $ownerId)),
            ],
            'photo_ids' => ['nullable', 'array'],
            'photo_ids.*' => ['integer', 'exists:photos,id'],
            'main_photo_pick' => ['nullable', 'string', 'regex:/^(existing:\d+|upload:\d+)$/'],
            'photo' => ['nullable', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/'],
            'photos' => ['nullable', 'array', 'min:1'],
            'photos.*' => ['required', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/'],
            'album_id' => ['nullable', 'integer'],
        ];
    }

    /**
     * Perform additional validation checks.
     *
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $user = $this->user();
                $milestone = $this->route('milestone');
                $photoIds = collect($this->input('photo_ids', []))
                    ->map(static fn ($id): int => (int) $id)
                    ->filter(static fn (int $id): bool => $id > 0)
                    ->values();
                $hasIncomingPhoto = $photoIds->isNotEmpty() || $this->filled('photo') || $this->filled('photos');

                if ($milestone?->photo_id === null && ! $hasIncomingPhoto) {
                    $validator->errors()->add('photo_id', 'Please select an existing photo or upload a new one.');
                    $validator->errors()->add('photo_ids', 'Please select an existing photo or upload a new one.');
                }

                if ($photoIds->isNotEmpty()) {
                    $ownedCount = Photo::query()
                        ->where('user_id', $user?->id)
                        ->whereIn('id', $photoIds)
                        ->count();

                    if ($ownedCount !== $photoIds->count()) {
                        $validator->errors()->add('photo_ids', 'Please select only your own photos.');
                    }
                }

                if (! $this->filled('album_id')) {
                    return;
                }

                if ($user === null) {
                    $validator->errors()->add('album_id', 'Please sign in to select an album.');

                    return;
                }

                $albumId = (int) $this->input('album_id');
                $ownsAlbum = Album::query()
                    ->whereKey($albumId)
                    ->where('user_id', $user->id)
                    ->exists();

                if (! $ownsAlbum) {
                    $validator->errors()->add('album_id', 'Please select one of your albums.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'stage_custom.required' => 'Please provide a custom life stage.',
            'photo.regex' => 'Photo must be a processed WebP, PNG, or JPEG image.',
            'photos.min' => 'Please select at least one image before uploading.',
            'photos.*.required' => 'Each selected photo must include image data.',
            'photos.*.regex' => 'Each selected photo must be a processed WebP, PNG, or JPEG image.',
        ];
    }

    public function attributes(): array
    {
        return [
            'photo_ids' => 'photos',
            'photo_ids.*' => 'photo',
            'photo' => 'photo',
            'photos' => 'photos',
            'photos.*' => 'photo',
            'album_id' => 'album',
        ];
    }

    protected function prepareForValidation(): void
    {
        $prepared = [];

        if ($this->has('stage_custom') && is_string($this->input('stage_custom'))) {
            $prepared['stage_custom'] = trim($this->input('stage_custom'));
        }

        if ($this->has('is_public')) {
            $prepared['is_public'] = $this->boolean('is_public');
        }

        $photoId = $this->input('photo_id');
        if (
            ($photoId !== null && $photoId !== '')
            && (! $this->has('photo_ids') || ! is_array($this->input('photo_ids')))
        ) {
            $prepared['photo_ids'] = [(int) $photoId];
        }

        if (($photoId !== null && $photoId !== '') && ! $this->filled('main_photo_pick')) {
            $prepared['main_photo_pick'] = 'existing:'.(int) $photoId;
        }

        if ($prepared !== []) {
            $this->merge($prepared);
        }
    }
}
