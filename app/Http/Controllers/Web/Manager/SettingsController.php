<?php

namespace App\Http\Controllers\Web\Manager;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = Setting::allAsArray();

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'site_name'          => 'nullable|string|max:255',
            'site_email'         => 'nullable|email|max:255',
            'site_phone'         => 'nullable|string|max:30',
            'site_address'       => 'nullable|string|max:500',
            'pagination_limit'   => 'nullable|integer|min:5|max:100',
            'allow_registration' => 'nullable|boolean',
        ]);

        $group = 'general';
        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::set($key, $value, $group);
        }

        return back()->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}
