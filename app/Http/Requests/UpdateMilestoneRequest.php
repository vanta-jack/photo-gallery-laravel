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
        return [
            'stage' => ['sometimes', 'string', Rule::in([...Milestone::curatedStageValues(), 'custom'])],
            'stage_custom' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn (): bool => $this->input('stage') === 'custom')],
            'label' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_public' => ['sometimes', 'boolean'],
            'photo_id' => ['nullable', 'integer', 'exists:photos,id'],
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
                $hasIncomingPhoto = $this->filled('photo_id') || $this->filled('photo') || $this->filled('photos');

                if ($milestone?->photo_id === null && ! $hasIncomingPhoto) {
                    $validator->errors()->add('photo_id', 'Please select an existing photo or upload a new one.');
                }

                if ($this->filled('photo_id')) {
                    $photoId = (int) $this->input('photo_id');
                    $ownsPhoto = Photo::query()
                        ->whereKey($photoId)
                        ->where('user_id', $user?->id)
                        ->exists();

                    if (! $ownsPhoto) {
                        $validator->errors()->add('photo_id', 'Please select one of your photos.');
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
            'photo_id' => 'photo',
            'photo' => 'photo',
            'photos' => 'photos',
            'photos.*' => 'photo',
            'album_id' => 'album',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('stage_custom') && is_string($this->input('stage_custom'))) {
            $this->merge([
                'stage_custom' => trim($this->input('stage_custom')),
            ]);
        }

        if ($this->has('is_public')) {
            $this->merge([
                'is_public' => $this->boolean('is_public'),
            ]);
        }
    }
}
