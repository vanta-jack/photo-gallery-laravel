<?php

namespace App\Http\Requests;

use App\Models\Album;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * StorePhotoRequest
 *
 * Handles validation for creating a new photo.
 * Using a Form Request allows us to:
 * - Keep the controller clean and focused on flow control
 * - Reuse validation rules across multiple endpoints
 * - Automatically authorize the request before validation
 */
class StorePhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * In a real application, you would check policies here.
     * For now, we allow all authenticated users.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Rules explained:
     * - 'photo': Client-processed base64 image string
     *   - WebP preferred
     *   - JPEG/PNG accepted when WebP encoding is unavailable on device
     * - 'title': Optional string, max 255 chars
     * - 'description': Optional text
     */
    public function rules(): array
    {
        if (! $this->routeIs('photos.store')) {
            return [
                'photo' => ['required', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/'],
                'title' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
            ];
        }

        return [
            'photo' => ['nullable', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/', 'required_without:photos'],
            'photos' => ['nullable', 'array', 'min:1', 'required_without:photo'],
            'photos.*' => ['required', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
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
                if (! $this->routeIs('photos.store')) {
                    return;
                }

                if (! $this->filled('album_id')) {
                    return;
                }

                if ($this->user() === null) {
                    $validator->errors()->add('album_id', 'Please sign in to select an album.');

                    return;
                }

                $albumId = (int) $this->input('album_id');
                $ownsAlbum = Album::query()
                    ->whereKey($albumId)
                    ->where('user_id', $this->user()->id)
                    ->exists();

                if (! $ownsAlbum) {
                    $validator->errors()->add('album_id', 'Please select one of your albums.');
                }
            },
        ];
    }

    /**
     * Custom validation messages for upload failures.
     */
    public function messages(): array
    {
        return [
            'photo.required' => 'Please select and process an image before uploading.',
            'photo.regex' => 'Photo must be a processed WebP, PNG, or JPEG image.',
            'photos.required' => 'Please select at least one image before uploading.',
            'photos.array' => 'Please upload photos as a valid image list.',
            'photos.min' => 'Please select at least one image before uploading.',
            'photos.*.required' => 'Each selected photo must include image data.',
            'photos.*.regex' => 'Each selected photo must be a processed WebP, PNG, or JPEG image.',
        ];
    }

    /**
     * Customize attribute names for error messages.
     * Makes errors more user-friendly: "The photo must be an image."
     */
    public function attributes(): array
    {
        return [
            'photo' => 'photo',
            'photos' => 'photos',
            'photos.*' => 'photo',
            'album_id' => 'album',
        ];
    }
}
