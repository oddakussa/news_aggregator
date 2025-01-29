<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class ListArticleRequest extends FormRequest
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
            'source_id' => ['sometimes', 'integer', 'exists:sources,id'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'author' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['sometimes', 'date', 'before_or_equal:end_date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['sometimes', 'string', 'in:published_at'],
            'sort_direction' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Get the default values for the request.
     */
    public function defaults(): array
    {
        return [
            'per_page' => 15,
            'sort_by' => 'published_at',
            'sort_direction' => 'desc',
        ];
    }
}
