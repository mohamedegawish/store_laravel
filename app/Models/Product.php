<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'company_id', 'category_id', 'brand_id',
        'name', 'name_ar', 'name_en', 'slug',
        'description', 'description_ar', 'description_en',
        'short_description_ar', 'short_description_en',
        'images', 'sku', 'barcode',
        'pricing_type', 'unit_label', 'weight', 'tax_rate',
        'is_active', 'is_featured', 'is_trending', 'is_best_seller',
        'status', 'sort_order', 'views_count',
        'related_product_ids', 'attributes',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'is_featured'        => 'boolean',
        'is_trending'        => 'boolean',
        'is_best_seller'     => 'boolean',
        'images'             => 'array',
        'attributes'         => 'array',
        'related_product_ids'=> 'array',
        'tax_rate'           => 'decimal:2',
        'weight'             => 'decimal:3',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn ($m) => $m->name_en ?? $m->name ?? 'product')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    // ── Relationships ──────────────────────────────────────────────────
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', true);
    }

    public function defaultVariant(): HasMany
    {
        return $this->variants()->where('is_default', true);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    // ── Accessors ──────────────────────────────────────────────────────
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"name_{$locale}"} ?? $this->attributes['name'] ?? $this->name_ar ?? $this->name_en ?? '';
    }

    public function getDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"description_{$locale}"} ?? $this->attributes['description'] ?? '';
    }

    public function getMainImageAttribute(): ?string
    {
        $images = $this->images ?? [];
        return !empty($images) ? $images[0] : null;
    }

    public function getDefaultVariantAttribute(): ?ProductVariant
    {
        return $this->variants->firstWhere('is_default', true) ?? $this->variants->first();
    }

    public function getPriceAttribute(): ?float
    {
        return $this->defaultVariant?->effective_price;
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function isOnOffer(): bool
    {
        $variant = $this->defaultVariant;
        return $variant && $variant->hasActiveOffer();
    }

    // ── Scopes ─────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeBestSeller($query)
    {
        return $query->where('is_best_seller', true);
    }

    public function scopeInStock($query)
    {
        return $query->whereHas('variants', fn($q) => $q->where('stock_quantity', '>', 0)->where('is_active', true));
    }

    public function scopeLowStock($query, int $threshold = 5)
    {
        return $query->whereHas('variants', fn($q) => $q->where('stock_quantity', '<=', $threshold)->where('stock_quantity', '>', 0));
    }
}
