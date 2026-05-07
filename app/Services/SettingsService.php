<?php

namespace App\Services;

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsService
{
    public function get(): StoreSetting
    {
        return StoreSetting::current() ?? new StoreSetting([
            'store_name_ar'   => 'متجري',
            'store_name_en'   => 'My Store',
            'primary_color'   => '#6C3FC5',
            'secondary_color' => '#EFE9FA',
            'accent_color'    => '#10B981',
            'currency'        => 'SAR',
            'currency_symbol' => 'ر.س',
        ]);
    }

    public function update(array $data, array $files = []): StoreSetting
    {
        $settings = StoreSetting::first() ?? new StoreSetting();

        // Handle file uploads
        foreach (['logo', 'favicon', 'cover_image'] as $field) {
            if (!empty($files[$field])) {
                $oldPath = $settings->$field;
                $settings->$field = $files[$field]->store("store/{$field}s", 'public');
                if ($oldPath) Storage::disk('public')->delete($oldPath);
            }
        }

        // Handle social links JSON
        if (isset($data['social_links']) && is_array($data['social_links'])) {
            $settings->social_links = array_filter($data['social_links']);
            unset($data['social_links']);
        }

        $settings->fill($data);
        $settings->save();

        StoreSetting::clearCache();

        return $settings;
    }

    public function getCurrencySymbol(): string
    {
        return $this->get()->currency_symbol ?? 'ر.س';
    }

    public function formatPrice(float $price): string
    {
        $symbol = $this->getCurrencySymbol();
        return number_format($price, 2) . ' ' . $symbol;
    }
}
