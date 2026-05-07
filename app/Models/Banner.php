<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title_ar', 'title_en', 'subtitle_ar', 'subtitle_en',
        'image', 'link', 'button_text_ar', 'button_text_en',
        'position', 'sort_order', 'is_active', 'starts_at', 'expires_at',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function getTitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"title_{$locale}"} ?? $this->title_ar ?? '';
    }

    public function getSubtitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"subtitle_{$locale}"} ?? $this->subtitle_ar ?? '';
    }

    public function getButtonTextAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"button_text_{$locale}"} ?? $this->button_text_ar ?? '';
    }

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) return false;
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->expires_at && $now->gt($this->expires_at)) return false;
        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()));
    }

    public function scopeForPosition($query, string $position)
    {
        return $query->where('position', $position);
    }
}
