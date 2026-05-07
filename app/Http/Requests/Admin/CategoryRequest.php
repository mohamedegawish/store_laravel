<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name_ar'          => 'required|string|max:255',
            'name_en'          => 'required|string|max:255',
            'parent_id'        => 'nullable|exists:categories,id',
            'description_ar'   => 'nullable|string',
            'description_en'   => 'nullable|string',
            'image'            => 'nullable|image|max:2048',
            'icon'             => 'nullable|string|max:100',
            'is_active'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer|min:0',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }
}
