<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort_order')->paginate(20);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug'             => 'required|string|max:100|unique:pages,slug',
            'title_ar'         => 'required|string|max:255',
            'title_en'         => 'required|string|max:255',
            'content_ar'       => 'nullable|string',
            'content_en'       => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_published'     => 'nullable|boolean',
            'sort_order'       => 'nullable|integer',
        ]);
        $data['is_published'] = $request->boolean('is_published', true);
        Page::create($data);
        return redirect()->route('admin.pages.index')->with('success', 'تم إضافة الصفحة بنجاح');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title_ar'         => 'required|string|max:255',
            'title_en'         => 'required|string|max:255',
            'content_ar'       => 'nullable|string',
            'content_en'       => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_published'     => 'nullable|boolean',
            'sort_order'       => 'nullable|integer',
        ]);
        $data['is_published'] = $request->boolean('is_published', true);
        $page->update($data);
        return redirect()->route('admin.pages.index')->with('success', 'تم تحديث الصفحة بنجاح');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return back()->with('success', 'تم حذف الصفحة بنجاح');
    }

    // ── FAQs ──────────────────────────────────────────────────────────
    public function faqs()
    {
        $faqs = Faq::orderBy('sort_order')->paginate(20);
        return view('admin.pages.faqs', compact('faqs'));
    }

    public function storeFaq(Request $request)
    {
        $data = $request->validate([
            'question_ar' => 'required|string',
            'question_en' => 'required|string',
            'answer_ar'   => 'required|string',
            'answer_en'   => 'required|string',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        Faq::create($data);
        return back()->with('success', 'تم إضافة السؤال بنجاح');
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question_ar' => 'required|string',
            'question_en' => 'required|string',
            'answer_ar'   => 'required|string',
            'answer_en'   => 'required|string',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $faq->update($data);
        return back()->with('success', 'تم تحديث السؤال بنجاح');
    }

    public function destroyFaq(Faq $faq)
    {
        $faq->delete();
        return back()->with('success', 'تم حذف السؤال بنجاح');
    }
}
