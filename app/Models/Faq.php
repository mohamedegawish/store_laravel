<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'question_ar', 'question_en', 'answer_ar', 'answer_en',
        'category', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getQuestionAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"question_{$locale}"} ?? $this->question_ar ?? '';
    }

    public function getAnswerAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"answer_{$locale}"} ?? $this->answer_ar ?? '';
    }
}
