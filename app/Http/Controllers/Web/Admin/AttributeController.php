<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::withCount('values')->orderBy('sort_order')->get();
        return view('admin.attributes.index', compact('attributes'));
    }

    public function valuesList(Attribute $attribute)
    {
        $values = $attribute->values()->orderBy('sort_order')->get();
        return response()->json(['type' => $attribute->type, 'values' => $values]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar'    => 'required|string|max:255',
            'name_en'    => 'required|string|max:255',
            'type'       => 'required|in:select,color,size,text',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $attribute = Attribute::create($data);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'attribute' => $attribute]);
        }
        return back()->with('success', 'تمت إضافة الخاصية');
    }

    public function update(Request $request, Attribute $attribute)
    {
        $data = $request->validate([
            'name_ar'    => 'required|string|max:255',
            'name_en'    => 'required|string|max:255',
            'type'       => 'required|in:select,color,size,text',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $attribute->update($data);
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'تم تعديل الخاصية');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return back()->with('success', 'تم حذف الخاصية');
    }

    // ── Values ─────────────────────────────────────────────────────────
    public function storeValue(Request $request, Attribute $attribute)
    {
        $data = $request->validate([
            'value_ar'   => 'required|string|max:255',
            'value_en'   => 'nullable|string|max:255',
            'color_code' => 'nullable|string|max:10',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $value = $attribute->values()->create($data);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'value' => $value]);
        }
        return back()->with('success', 'تمت إضافة القيمة');
    }

    public function destroyValue(Attribute $attribute, AttributeValue $value)
    {
        $value->delete();
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'تم حذف القيمة');
    }
}
