@extends('layouts.admin')
@section('title', 'الأسئلة الشائعة')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">الأسئلة الشائعة</span>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">الأسئلة الشائعة</h1>
        <p class="page-subtitle">إدارة الأسئلة والأجوبة المتكررة</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('addFaqForm').style.display=''"><i class="fas fa-plus"></i> إضافة سؤال</button>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
@endif

{{-- Add FAQ form --}}
<div class="card" id="addFaqForm" style="display:none;margin-bottom:24px">
    <div class="card-header"><span class="card-title"><i class="fas fa-plus-circle"></i> إضافة سؤال جديد</span></div>
    <form method="POST" action="{{ route('admin.faqs.store') }}">
        @csrf
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">السؤال بالعربي <span class="required">*</span></label>
                    <textarea name="question_ar" class="form-control" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">السؤال بالإنجليزي <span class="required">*</span></label>
                    <textarea name="question_en" class="form-control" rows="2" required></textarea>
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">الإجابة بالعربي <span class="required">*</span></label>
                    <textarea name="answer_ar" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">الإجابة بالإنجليزي <span class="required">*</span></label>
                    <textarea name="answer_en" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">الفئة</label>
                    <input type="text" name="category" class="form-control" placeholder="الطلبات، الشحن، الدفع...">
                </div>
                <div class="form-group">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control" value="0" min="0">
                </div>
            </div>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:600">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" style="width:18px;height:18px" checked>
                تفعيل السؤال
            </label>
        </div>
        <div class="card-footer" style="display:flex;gap:10px;justify-content:flex-end">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('addFaqForm').style.display='none'">إلغاء</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
        </div>
    </form>
</div>

{{-- FAQs list --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>السؤال</th><th>الفئة</th><th>الحالة</th><th>الترتيب</th><th>إجراءات</th></tr>
            </thead>
            <tbody>
            @forelse($faqs as $faq)
            <tr>
                <td>
                    <div style="font-weight:600">{{ Str::limit($faq->question_ar, 80) }}</div>
                    <div style="font-size:.78rem;color:var(--text-3)">{{ Str::limit($faq->answer_ar, 100) }}</div>
                </td>
                <td>
                    @if($faq->category)
                    <span class="badge badge-info">{{ $faq->category }}</span>
                    @else
                    <span style="color:var(--text-3)">—</span>
                    @endif
                </td>
                <td><span class="badge badge-{{ $faq->is_active ? 'success' : 'secondary' }}">{{ $faq->is_active ? 'نشط' : 'مخفي' }}</span></td>
                <td>{{ $faq->sort_order }}</td>
                <td>
                    <div style="display:flex;gap:6px">
                        <button class="btn btn-xs btn-outline" onclick="toggleEditFaq({{ $faq->id }})"><i class="fas fa-edit"></i></button>
                        <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('حذف السؤال؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline" style="color:var(--danger)"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            {{-- Inline edit row --}}
            <tr id="editRow-{{ $faq->id }}" style="display:none;background:var(--surface-2)">
                <td colspan="5">
                    <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" style="padding:16px">
                        @csrf @method('PUT')
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">السؤال ع</label>
                                <textarea name="question_ar" class="form-control" rows="2" required>{{ $faq->question_ar }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">السؤال en</label>
                                <textarea name="question_en" class="form-control" rows="2" required>{{ $faq->question_en }}</textarea>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">الإجابة ع</label>
                                <textarea name="answer_ar" class="form-control" rows="3" required>{{ $faq->answer_ar }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">الإجابة en</label>
                                <textarea name="answer_en" class="form-control" rows="3" required>{{ $faq->answer_en }}</textarea>
                            </div>
                        </div>
                        <div class="form-grid" style="margin-bottom:12px">
                            <input type="text" name="category" class="form-control" value="{{ $faq->category }}" placeholder="الفئة">
                            <input type="number" name="sort_order" class="form-control" value="{{ $faq->sort_order }}" placeholder="الترتيب">
                        </div>
                        <div style="display:flex;gap:10px;align-items:center">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" style="width:16px;height:16px" {{ $faq->is_active ? 'checked' : '' }}>
                                نشط
                            </label>
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> حفظ</button>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="toggleEditFaq({{ $faq->id }})">إلغاء</button>
                        </div>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-3)">لا توجد أسئلة شائعة</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($faqs->hasPages())
    <div class="pagination-wrap">
        <span>{{ $faqs->total() }} سؤال</span>
        {{ $faqs->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function toggleEditFaq(id) {
    const row = document.getElementById('editRow-' + id);
    row.style.display = row.style.display === 'none' ? '' : 'none';
}
</script>
@endpush
@endsection
