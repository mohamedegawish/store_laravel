<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::withCount('products')
            ->when($request->search, fn($q) => $q->where('name_ar', 'like', "%{$request->search}%")
                ->orWhere('name_en', 'like', "%{$request->search}%"))
            ->orderBy('sort_order')
            ->paginate(20)->withQueryString();

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar'          => 'required|string|max:255',
            'name_en'          => 'required|string|max:255',
            'description_ar'   => 'nullable|string',
            'description_en'   => 'nullable|string',
            'logo'             => 'nullable|image|max:2048',
            'is_active'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer|min:0',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'تم إضافة البراند بنجاح');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name_ar'          => 'required|string|max:255',
            'name_en'          => 'required|string|max:255',
            'description_ar'   => 'nullable|string',
            'description_en'   => 'nullable|string',
            'logo'             => 'nullable|image|max:2048',
            'is_active'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer|min:0',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('logo')) {
            if ($brand->logo) Storage::disk('public')->delete($brand->logo);
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'تم تحديث البراند بنجاح');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->exists()) {
            return back()->with('error', 'لا يمكن حذف براند مرتبط بمنتجات');
        }
        if ($brand->logo) Storage::disk('public')->delete($brand->logo);
        $brand->delete();
        return back()->with('success', 'تم حذف البراند بنجاح');
    }
}
