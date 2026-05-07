<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class StoreSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'homepage_layout'          => 'array',
        'banners'                  => 'array',
        'sections_config'          => 'array',
        'social_links'             => 'array',
        'show_low_stock_badges'    => 'boolean',
        'show_trending_badges'     => 'boolean',
        'enable_reviews'           => 'boolean',
        'enable_wishlist'          => 'boolean',
        'enable_newsletter'        => 'boolean',
        'maintenance_mode'         => 'boolean',
        'shipping_cost'            => 'decimal:2',
        'free_shipping_threshold'  => 'decimal:2',
        'tax_rate'                 => 'decimal:2',
        'min_order_amount'         => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getStoreNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"store_name_{$locale}"} ?? $this->store_name_ar ?? $this->store_name_en ?? config('app.name');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon ? asset('storage/' . $this->favicon) : null;
    }

    public function getSocialLink(string $platform): ?string
    {
        return $this->social_links[$platform] ?? null;
    }

    public function getSectionsConfig(): array
    {
        return $this->sections_config ?? [
            ['key' => 'hero',       'enabled' => true, 'order' => 1],
            ['key' => 'featured',   'enabled' => true, 'order' => 2],
            ['key' => 'categories', 'enabled' => true, 'order' => 3],
            ['key' => 'trending',   'enabled' => true, 'order' => 4],
            ['key' => 'banners',    'enabled' => true, 'order' => 5],
            ['key' => 'bestseller', 'enabled' => true, 'order' => 6],
        ];
    }

    public static function current(): ?self
    {
        return Cache::remember('store_settings', 3600, fn() => static::first());
    }

    public static function clearCache(): void
    {
        Cache::forget('store_settings');
    }
}
