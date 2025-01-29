<?php

namespace App\Http\Requests\UserPreference;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'preferred_sources' => ['sometimes', 'array'],
            'preferred_sources.*' => ['required', 'integer', 'exists:sources,id'],
            'preferred_categories' => ['sometimes', 'array'],
            'preferred_categories.*' => ['required', 'integer', 'exists:categories,id'],
            'preferred_authors' => ['sometimes', 'array'],
            'preferred_authors.*' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'preferred_sources.*.exists' => 'One or more selected sources do not exist.',
            'preferred_categories.*.exists' => 'One or more selected categories do not exist.',
            'preferred_authors.*.max' => 'Author names cannot exceed 255 characters.',
        ];
    }
}
