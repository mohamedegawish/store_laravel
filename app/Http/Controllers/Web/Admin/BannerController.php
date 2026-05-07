<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('position')->orderBy('sort_order')->paginate(20);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_ar'       => 'nullable|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'subtitle_ar'    => 'nullable|string|max:500',
            'subtitle_en'    => 'nullable|string|max:500',
            'image'          => 'required|image|max:4096',
            'link'           => 'nullable|url|max:500',
            'button_text_ar' => 'nullable|string|max:100',
            'button_text_en' => 'nullable|string|max:100',
            'position'       => 'required|in:hero,sidebar,popup,section',
            'sort_order'     => 'nullable|integer',
            'is_active'      => 'nullable|boolean',
            'starts_at'      => 'nullable|date',
            'expires_at'     => 'nullable|date',
        ]);

        $data['image'] = $request->file('image')->store('banners', 'public');
        $data['is_active'] = $request->boolean('is_active', true);

        Banner::create($data);
        return redirect()->route('admin.banners.index')->with('success', 'تم إضافة البانر بنجاح');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title_ar'       => 'nullable|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'subtitle_ar'    => 'nullable|string|max:500',
            'subtitle_en'    => 'nullable|string|max:500',
            'image'          => 'nullable|image|max:4096',
            'link'           => 'nullable|url|max:500',
            'button_text_ar' => 'nullable|string|max:100',
            'button_text_en' => 'nullable|string|max:100',
            'position'       => 'required|in:hero,sidebar,popup,section',
            'sort_order'     => 'nullable|integer',
            'is_active'      => 'nullable|boolean',
            'starts_at'      => 'nullable|date',
            'expires_at'     => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image);
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'تم تحديث البانر بنجاح');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image);
        $banner->delete();
        return back()->with('success', 'تم حذف البانر بنجاح');
    }
}
