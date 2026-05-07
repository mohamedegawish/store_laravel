<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'store_name_ar'           => 'nullable|string|max:255',
            'store_name_en'           => 'nullable|string|max:255',
            'logo'                    => 'nullable|image|max:2048',
            'favicon'                 => 'nullable|image|max:512',
            'cover_image'             => 'nullable|image|max:4096',
            'primary_color'           => 'nullable|string|max:10',
            'secondary_color'         => 'nullable|string|max:10',
            'accent_color'            => 'nullable|string|max:10',
            'theme_mode'              => 'nullable|in:light,dark,system',
            'font_family'             => 'nullable|string|max:100',
            'phone'                   => 'nullable|string|max:30',
            'email'                   => 'nullable|email|max:255',
            'address_ar'              => 'nullable|string|max:500',
            'address_en'              => 'nullable|string|max:500',
            'social_links'            => 'nullable|array',
            'social_links.*'          => 'nullable|string|max:255',
            'meta_title'              => 'nullable|string|max:255',
            'meta_description'        => 'nullable|string|max:500',
            'footer_text_ar'          => 'nullable|string',
            'footer_text_en'          => 'nullable|string',
            'hero_title_ar'           => 'nullable|string|max:255',
            'hero_title_en'           => 'nullable|string|max:255',
            'hero_subtitle_ar'        => 'nullable|string',
            'hero_subtitle_en'        => 'nullable|string',
            'currency'                => 'nullable|string|max:10',
            'currency_symbol'         => 'nullable|string|max:10',
            'shipping_cost'           => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'tax_rate'                => 'nullable|numeric|min:0|max:100',
            'min_order_amount'        => 'nullable|numeric|min:0',
            'enable_reviews'          => 'nullable|boolean',
            'enable_wishlist'         => 'nullable|boolean',
            'enable_newsletter'       => 'nullable|boolean',
            'maintenance_mode'        => 'nullable|boolean',
            'default_locale'          => 'nullable|in:ar,en',
            'sections_config'         => 'nullable|array',
        ];
    }
}
