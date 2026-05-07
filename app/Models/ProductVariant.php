<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'name_ar', 'name_en', 'sku', 'barcode',
        'price', 'cost', 'discount', 'offer_price', 'offer_starts_at', 'offer_ends_at',
        'stock_quantity', 'min_stock_alert', 'weight', 'image',
        'is_default', 'is_active', 'sort_order', 'variant_attributes',
    ];

    protected $casts = [
        'price'              => 'decimal:2',
        'cost'               => 'decimal:2',
        'discount'           => 'decimal:2',
        'offer_price'        => 'decimal:2',
        'offer_starts_at'    => 'datetime',
        'offer_ends_at'      => 'datetime',
        'is_default'         => 'boolean',
        'is_active'          => 'boolean',
        'variant_attributes' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductVariantAttribute::class, 'variant_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"name_{$locale}"} ?? $this->name_ar ?? $this->name_en ?? '';
    }

    public function hasActiveOffer(): bool
    {
        if (!$this->offer_price) return false;
        $now    = now();
        $starts = $this->offer_starts_at;
        $ends   = $this->offer_ends_at;
        return ($starts === null || $now->gte($starts)) && ($ends === null || $now->lte($ends));
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->hasActiveOffer() ? (float) $this->offer_price : (float) $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (!$this->hasActiveOffer() || !$this->price) return 0;
        return (int) round((($this->price - $this->offer_price) / $this->price) * 100);
    }

    public function getProfitAttribute(): ?float
    {
        if ($this->cost === null) return null;
        return $this->effective_price - (float) $this->cost;
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= ($this->min_stock_alert ?? 5);
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }
}
