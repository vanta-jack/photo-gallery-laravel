<?php

namespace App\Http\Requests;

use App\Models\Album;
use App\Models\Milestone;
use App\Models\Photo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * StoreMilestoneRequest
 * 
 * Validates creating a milestone (life event tracker).
 */
class StoreMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * stage: Must be one of the defined enum values
     * label: Required descriptor (e.g., "Month 3", "Grade 2")
     * description: Optional narrative
     * photo_id/photo/photos: Must include at least one photo
     */
    public function rules(): array
    {
        return [
            'stage' => ['required', 'string', Rule::in([...Milestone::curatedStageValues(), 'custom'])],
            'stage_custom' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn (): bool => $this->input('stage') === 'custom')],
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_public' => ['nullable', 'boolean'],
            'photo_id' => ['nullable', 'integer', 'exists:photos,id', 'required_without_all:photo,photos'],
            'photo' => ['nullable', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/', 'required_without_all:photo_id,photos'],
            'photos' => ['nullable', 'array', 'min:1', 'required_without_all:photo_id,photo'],
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
            'photo_id.required_without_all' => 'Please select an existing photo or upload a new one.',
            'photo.required_without_all' => 'Please select an existing photo or upload a new one.',
            'photos.required_without_all' => 'Please select an existing photo or upload a new one.',
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
        $this->merge([
            'is_public' => $this->boolean('is_public'),
            'stage_custom' => is_string($this->input('stage_custom'))
                ? trim($this->input('stage_custom'))
                : $this->input('stage_custom'),
        ]);
    }
}
