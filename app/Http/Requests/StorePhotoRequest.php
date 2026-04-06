<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'photo' => ['required', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
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
        ];
    }
}
