<?php

namespace App\Http\Requests;

use App\Models\Album;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateAlbumRequest
 *
 * Validates updating an existing album.
 * All fields are optional since user might update just one field.
 */
class UpdateAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        $album = $this->route('album');

        return $album instanceof Album
            && $this->user() !== null
            && $this->user()->can('update', $album);
    }

    public function rules(): array
    {
        /** @var Album|null $album */
        $album = $this->route('album');
        $ownerId = $album?->user_id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_private' => ['nullable', 'boolean'],
            'cover_photo_id' => [
                'nullable',
                'integer',
                Rule::exists('photos', 'id')->where(static fn ($query) => $query->where('user_id', $ownerId)),
            ],
            'photo_ids' => ['nullable', 'array'],
            'photo_ids.*' => [
                'integer',
                Rule::exists('photos', 'id')->where(static fn ($query) => $query->where('user_id', $ownerId)),
            ],
            'main_photo_pick' => ['nullable', 'string', 'regex:/^(existing:\d+|upload:\d+)$/'],
            'photo' => ['nullable', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/'],
            'photos' => ['nullable', 'array', 'min:1'],
            'photos.*' => ['required', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $prepared = [];
        $coverPhotoId = $this->input('cover_photo_id');

        if (
            ($coverPhotoId !== null && $coverPhotoId !== '')
            && (! $this->has('photo_ids') || ! is_array($this->input('photo_ids')))
        ) {
            $prepared['photo_ids'] = [(int) $coverPhotoId];
        }

        if (($coverPhotoId !== null && $coverPhotoId !== '') && ! $this->filled('main_photo_pick')) {
            $prepared['main_photo_pick'] = 'existing:'.(int) $coverPhotoId;
        }

        if ($prepared !== []) {
            $this->merge($prepared);
        }
    }
}
