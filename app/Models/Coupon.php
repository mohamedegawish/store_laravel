<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name_ar', 'name_en', 'description_ar', 'description_en',
        'type', 'value', 'min_order_amount', 'max_discount_amount',
        'usage_limit', 'used_count', 'user_limit',
        'product_ids', 'category_ids',
        'is_active', 'starts_at', 'expires_at',
    ];

    protected $casts = [
        'value'               => 'decimal:2',
        'min_order_amount'    => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'product_ids'         => 'array',
        'category_ids'        => 'array',
        'is_active'           => 'boolean',
        'starts_at'           => 'datetime',
        'expires_at'          => 'datetime',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"name_{$locale}"} ?? $this->name_ar ?? $this->name_en ?? '';
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->expires_at && $now->gt($this->expires_at)) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function calculateDiscount(float $orderTotal): float
    {
        $discount = $this->type === 'percentage'
            ? $orderTotal * ($this->value / 100)
            : (float) $this->value;

        if ($this->max_discount_amount) {
            $discount = min($discount, (float) $this->max_discount_amount);
        }

        return min($discount, $orderTotal);
    }
}
