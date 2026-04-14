<?php

namespace App\Http\Requests;

use App\Models\Photo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * StorePostRequest
 *
 * Validates creating a new post (blog-style entry).
 */
class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Title required, description supports markdown
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'photo_id' => ['nullable', 'integer', 'exists:photos,id'],
            'photo_ids' => ['nullable', 'array'],
            'photo_ids.*' => ['integer', 'exists:photos,id'],
            'main_photo_pick' => ['nullable', 'string', 'regex:/^(existing:\d+|upload:\d+)$/'],
            'photo' => ['nullable', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/'],
            'photos' => ['nullable', 'array', 'min:1'],
            'photos.*' => ['required', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/'],
        ];
    }

    /**
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $user = $this->user();
                if ($user === null) {
                    return;
                }

                $photoIds = collect($this->input('photo_ids', []))
                    ->map(static fn ($id): int => (int) $id)
                    ->filter(static fn (int $id): bool => $id > 0)
                    ->values();

                if ($photoIds->isEmpty()) {
                    return;
                }

                $ownedCount = Photo::query()
                    ->whereBelongsTo($user)
                    ->whereIn('id', $photoIds)
                    ->count();

                if ($ownedCount !== $photoIds->count()) {
                    $validator->errors()->add('photo_ids', 'Please select only your own photos.');
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $prepared = [];
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
