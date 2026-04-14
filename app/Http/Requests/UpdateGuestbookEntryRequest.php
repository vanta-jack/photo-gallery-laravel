<?php

namespace App\Http\Requests;

use App\Models\Photo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * UpdateGuestbookEntryRequest
 *
 * Validates updating a guestbook entry.
 */
class UpdateGuestbookEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
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
                $photoIds = collect($this->input('photo_ids', []))
                    ->map(static fn ($id): int => (int) $id)
                    ->filter(static fn (int $id): bool => $id > 0)
                    ->values();

                if ($photoIds->isEmpty()) {
                    return;
                }

                $guestbook = $this->route('guestbook');
                $ownerId = (int) ($guestbook?->post?->user_id ?? $this->user()?->id ?? 0);
                if ($ownerId <= 0) {
                    $validator->errors()->add('photo_ids', 'Please select valid photos.');

                    return;
                }

                $ownedCount = Photo::query()
                    ->where('user_id', $ownerId)
                    ->whereIn('id', $photoIds)
                    ->count();

                if ($ownedCount !== $photoIds->count()) {
                    $validator->errors()->add('photo_ids', 'Please select only available owner photos.');
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
