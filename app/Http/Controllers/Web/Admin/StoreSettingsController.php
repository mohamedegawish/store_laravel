<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSettingsRequest;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class StoreSettingsController extends Controller
{
    public function __construct(private SettingsService $settingsService) {}

    public function index()
    {
        $settings = $this->settingsService->get();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(StoreSettingsRequest $request)
    {
        $data  = $request->validated();
        $files = [];

        foreach (['logo', 'favicon', 'cover_image'] as $field) {
            if ($request->hasFile($field)) {
                $files[$field] = $request->file($field);
            }
            unset($data[$field]);
        }

        // Booleans
        foreach (['enable_reviews', 'enable_wishlist', 'enable_newsletter', 'maintenance_mode'] as $bool) {
            $data[$bool] = $request->boolean($bool);
        }

        $this->settingsService->update($data, $files);

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    public function updateSections(Request $request)
    {
        $request->validate(['sections_config' => 'required|array']);
        $this->settingsService->update(['sections_config' => $request->sections_config]);
        return response()->json(['success' => true]);
    }
}
