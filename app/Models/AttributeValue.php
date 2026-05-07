<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value_ar', 'value_en', 'color_code', 'sort_order'];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function getValueAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"value_{$locale}"} ?? $this->value_ar ?? $this->value_en ?? '';
    }
}
