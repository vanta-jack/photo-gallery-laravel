<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        // Best Practice: Use policies for complex authorization
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Rules explained:
     * - 'photo': Must be an uploaded file, image type, max 5MB
     * - 'title': Required string, max 255 chars
     * - 'description': Optional text
     */
    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'max:5120'], // 5MB max
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Customize attribute names for error messages.
     * Makes errors more user-friendly: "The photo must be an image."
     */
    public function attributes(): array
    {
        return [
            'photo' => 'photo file',
        ];
    }
}