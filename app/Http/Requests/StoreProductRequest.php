<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            // Core product fields
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_image' => ['nullable', 'string', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],

            // Shared JSON attributes (e.g. brand, material, certification)
            'attributes' => ['nullable', 'array'],

            // Variants — at least one required
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.sku' => ['required', 'string', 'max:100', 'distinct', 'unique:product_variants,sku'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.stock_quantity' => ['required', 'integer', 'min:0'],

            // Per-variant JSON attributes (e.g. size, color, weight)
            'variants.*.variant_attributes' => ['nullable', 'array'],
        ];
    }

    /**
     * Custom human-readable attribute names for error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'variants.*.sku' => 'variant SKU',
            'variants.*.price' => 'variant price',
            'variants.*.stock_quantity' => 'variant stock quantity',
            'variants.*.variant_attributes' => 'variant attributes',
        ];
    }
}
