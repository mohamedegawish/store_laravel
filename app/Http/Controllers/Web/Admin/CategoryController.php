<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('products')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name_ar', 'like', "%{$request->search}%")
                  ->orWhere('name_en', 'like', "%{$request->search}%");
            }))
            ->when($request->parent_id, fn($q) => $q->where('parent_id', $request->parent_id))
            ->orderBy('sort_order')
            ->paginate(20)->withQueryString();

        $roots = Category::roots()->orderBy('name_ar')->get();

        return view('admin.categories.index', compact('query', 'roots'));
    }

    public function create()
    {
        $parents = Category::roots()->where('is_active', true)->orderBy('name_ar')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function edit(Category $category)
    {
        $parents = Category::roots()->where('is_active', true)->where('id', '!=', $category->id)->orderBy('name_ar')->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($category->image) Storage::disk('public')->delete($category->image);
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'لا يمكن حذف تصنيف يحتوي على منتجات');
        }

        if ($category->image) Storage::disk('public')->delete($category->image);
        $category->delete();

        return back()->with('success', 'تم حذف التصنيف بنجاح');
    }
}
