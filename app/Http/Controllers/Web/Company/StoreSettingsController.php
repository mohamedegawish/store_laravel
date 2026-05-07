<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreSettingsController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;

        if (! $company) {
            abort(403, 'لا توجد شركة مرتبطة بهذا الحساب.');
        }

        $settings = $company->storeSetting()->firstOrCreate(
            ['company_id' => $company->id],
            [
                'primary_color'          => '#6C3FC5',
                'secondary_color'        => '#EFE9FA',
                'font_family'            => 'Cairo',
                'theme_mode'             => 'light',
                'show_low_stock_badges'  => true,
                'show_trending_badges'   => true,
                'banners'                => [],
                'homepage_layout'        => ['hero', 'categories', 'featured', 'offers'],
            ]
        );

        return view('company.settings.edit', compact('settings', 'company'));
    }

    public function update(Request $request)
    {
        $company  = auth()->user()->company;
        $settings = $company->storeSetting;

        $validated = $request->validate([
            'primary_color'         => 'required|string|max:20',
            'secondary_color'       => 'required|string|max:20',
            'font_family'           => 'required|string|max:50',
            'theme_mode'            => 'required|in:light,dark,system',
            'show_low_stock_badges' => 'nullable|boolean',
            'show_trending_badges'  => 'nullable|boolean',
            'banner_images.*'       => 'nullable|image|max:3072',
            'banner_titles.*'       => 'nullable|string|max:255',
            'banner_buttons.*'      => 'nullable|string|max:100',
            'banner_urls.*'         => 'nullable|string|max:255',
        ]);

        $validated['show_low_stock_badges'] = $request->has('show_low_stock_badges');
        $validated['show_trending_badges']  = $request->has('show_trending_badges');

        // Handle Banners
        $existingBanners = $settings->banners ?? [];
        $bannerTitles    = $request->input('banner_titles', []);
        $bannerButtons   = $request->input('banner_buttons', []);
        $bannerUrls      = $request->input('banner_urls', []);

        // Remove deleted banners
        $keepIndexes = $request->input('keep_banners', []);
        $newBanners  = [];
        foreach ($existingBanners as $i => $banner) {
            if (in_array((string) $i, $keepIndexes)) {
                $newBanners[] = $banner;
            } else {
                // Delete image
                if (! empty($banner['image'])) {
                    Storage::disk('public')->delete($banner['image']);
                }
            }
        }

        // Add new banners from uploaded files
        if ($request->hasFile('banner_images')) {
            foreach ($request->file('banner_images') as $k => $file) {
                $path = $file->store('banners', 'public');
                $newBanners[] = [
                    'image'  => $path,
                    'title'  => $bannerTitles[$k] ?? '',
                    'button' => $bannerButtons[$k] ?? '',
                    'url'    => $bannerUrls[$k] ?? '#',
                ];
            }
        }

        $settings->update([
            'primary_color'          => $validated['primary_color'],
            'secondary_color'        => $validated['secondary_color'],
            'font_family'            => $validated['font_family'],
            'theme_mode'             => $validated['theme_mode'],
            'show_low_stock_badges'  => $validated['show_low_stock_badges'],
            'show_trending_badges'   => $validated['show_trending_badges'],
            'banners'                => $newBanners,
        ]);

        return redirect()->route('company.settings.edit')
            ->with('success', 'تم حفظ إعدادات المتجر بنجاح ✨');
    }
}
