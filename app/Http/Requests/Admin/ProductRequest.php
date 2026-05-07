<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name_ar'               => 'required|string|max:255',
            'name_en'               => 'required|string|max:255',
            'category_id'           => 'required|exists:categories,id',
            'brand_id'              => 'nullable|exists:brands,id',
            'short_description_ar'  => 'nullable|string|max:500',
            'short_description_en'  => 'nullable|string|max:500',
            'description_ar'        => 'nullable|string',
            'description_en'        => 'nullable|string',
            'sku'                   => 'nullable|string|max:100',
            'barcode'               => 'nullable|string|max:100',
            'pricing_type'          => 'required|in:per_piece,per_kg,per_gram,per_liter,per_package',
            'unit_label'            => 'nullable|string|max:50',
            'weight'                => 'nullable|numeric|min:0',
            'tax_rate'              => 'nullable|numeric|min:0|max:100',
            'status'                => 'required|in:active,inactive,draft',
            'is_featured'           => 'nullable|boolean',
            'is_trending'           => 'nullable|boolean',
            'is_best_seller'        => 'nullable|boolean',
            'sort_order'            => 'nullable|integer|min:0',
            'meta_title'            => 'nullable|string|max:255',
            'meta_description'      => 'nullable|string|max:500',
            'images'                => 'nullable|array',
            'images.*'              => 'nullable|image|max:4096',
            // Main / default variant
            'price'                 => 'required|numeric|min:0',
            'cost'                  => 'nullable|numeric|min:0',
            'offer_price'           => 'nullable|numeric|min:0',
            'offer_starts_at'       => 'nullable|date',
            'offer_ends_at'         => 'nullable|date|after:offer_starts_at',
            'stock_quantity'        => 'required|integer|min:0',
            'min_stock_alert'       => 'nullable|integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'name_ar'        => 'اسم المنتج (عربي)',
            'name_en'        => 'اسم المنتج (إنجليزي)',
            'category_id'    => 'التصنيف',
            'price'          => 'السعر',
            'stock_quantity' => 'الكمية',
        ];
    }
}
