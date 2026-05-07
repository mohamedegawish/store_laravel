@extends('layouts.admin')
@section('title', 'الخصائص')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">الخصائص</span>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">الخصائص وقيمها</h1>
        <p class="page-subtitle">إدارة خصائص المنتجات مثل اللون والحجم</p>
    </div>
    <button class="btn btn-primary" onclick="openAddAttrModal()"><i class="fas fa-plus"></i> إضافة خاصية</button>
</div>

<div style="display:grid;grid-template-columns:320px 1fr;gap:24px;align-items:start">

    {{-- Attributes list --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-list-check"></i> الخصائص</span></div>
        <div id="attrList">
        @forelse($attributes as $attr)
        <div class="attr-item {{ $loop->first ? 'active' : '' }}" data-id="{{ $attr->id }}" onclick="loadValues({{ $attr->id }}, '{{ addslashes($attr->name_ar) }}')">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;cursor:pointer;border-bottom:1px solid var(--border);transition:var(--transition)" onmouseover="this.style.background='var(--surface-2)'" onmouseout="if(!this.parentElement.classList.contains('active'))this.style.background=''">
                <div>
                    <div style="font-weight:600">{{ $attr->name_ar }}</div>
                    <div style="font-size:.78rem;color:var(--text-3)">{{ $attr->name_en }} &bull; {{ $attr->values_count }} قيمة</div>
                </div>
                <div style="display:flex;gap:4px">
                    <button class="btn btn-xs btn-outline" onclick="event.stopPropagation();openEditAttrModal({{ $attr->id }},'{{ addslashes($attr->name_ar) }}','{{ addslashes($attr->name_en) }}','{{ $attr->type }}')"><i class="fas fa-edit"></i></button>
                    <form method="POST" action="{{ route('admin.attributes.destroy', $attr) }}" onsubmit="return confirm('حذف الخاصية وجميع قيمها؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-outline" style="color:var(--danger)" onclick="event.stopPropagation()"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div style="padding:40px;text-align:center;color:var(--text-muted)"><i class="fas fa-tags" style="font-size:2rem;opacity:.3;display:block;margin-bottom:10px"></i>لا توجد خصائص</div>
        @endforelse
        </div>
    </div>

    {{-- Values panel --}}
    <div class="card" id="valuesPanel">
        <div class="card-header">
            <span class="card-title" id="valuesPanelTitle"><i class="fas fa-palette"></i> اختر خاصية لعرض قيمها</span>
            <button class="btn btn-sm btn-primary" id="addValueBtn" style="display:none" onclick="openAddValueModal()"><i class="fas fa-plus"></i> إضافة قيمة</button>
        </div>
        <div id="valuesList" style="padding:40px;text-align:center;color:var(--text-3)">
            <i class="fas fa-hand-pointer" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:12px"></i>
            اختر خاصية من القائمة على اليسار
        </div>
    </div>
</div>

{{-- Add/Edit Attribute Modal --}}
<div class="modal-backdrop" id="attrModal">
    <div class="modal">
        <h3 class="modal-title" id="attrModalTitle">إضافة خاصية جديدة</h3>
        <form id="attrForm" method="POST" action="{{ route('admin.attributes.store') }}">
            @csrf
            <input type="hidden" name="_method" id="attrFormMethod" value="POST">
            <div class="form-group" style="margin-top:20px">
                <label class="form-label">الاسم بالعربي <span class="required">*</span></label>
                <input type="text" name="name_ar" id="attrNameAr" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">الاسم بالإنجليزي <span class="required">*</span></label>
                <input type="text" name="name_en" id="attrNameEn" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">نوع العرض</label>
                <select name="type" id="attrType" class="form-control">
                    <option value="select">قائمة منسدلة</option>
                    <option value="color">لون</option>
                    <option value="size">حجم</option>
                    <option value="text">نص</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeAttrModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Value Modal --}}
<div class="modal-backdrop" id="valueModal">
    <div class="modal">
        <h3 class="modal-title">إضافة قيمة</h3>
        <form id="valueForm" method="POST">
            @csrf
            <div class="form-group" style="margin-top:20px">
                <label class="form-label">القيمة بالعربي <span class="required">*</span></label>
                <input type="text" name="value_ar" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">القيمة بالإنجليزي</label>
                <input type="text" name="value_en" class="form-control">
            </div>
            <div class="form-group" id="colorPickerWrap" style="display:none">
                <label class="form-label">اللون</label>
                <input type="color" name="color_code" class="form-control" style="height:42px;padding:4px">
            </div>
            <div class="form-group">
                <label class="form-label">الترتيب</label>
                <input type="number" name="sort_order" class="form-control" value="0" min="0">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeValueModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentAttrId = null;
let currentAttrType = 'select';

function loadValues(attrId, attrName) {
    currentAttrId = attrId;
    document.querySelectorAll('.attr-item').forEach(el => el.classList.remove('active'));
    document.querySelector(`.attr-item[data-id="${attrId}"]`)?.classList.add('active');
    document.getElementById('valuesPanelTitle').innerHTML = `<i class="fas fa-palette"></i> قيم: ${attrName}`;
    document.getElementById('addValueBtn').style.display = '';

    fetch(`/admin/attributes/${attrId}/values-list`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        currentAttrType = data.type || 'select';
        document.getElementById('valuesList').innerHTML = renderValues(data.values || []);
    })
    .catch(() => {
        document.getElementById('valuesList').innerHTML = '<div style="padding:20px;text-align:center;color:var(--danger)">خطأ في التحميل</div>';
    });
}

function renderValues(values) {
    if (!values.length) return '<div style="padding:40px;text-align:center;color:var(--text-3)">لا توجد قيم. أضف أولى القيم.</div>';
    return `<table class="table"><thead><tr><th>القيمة</th><th>الإنجليزي</th><th>اللون</th><th>الترتيب</th><th>إجراء</th></tr></thead><tbody>
        ${values.map(v => `<tr>
            <td style="font-weight:600">${v.value_ar}</td>
            <td style="color:var(--text-3)">${v.value_en || '—'}</td>
            <td>${v.color_code ? `<span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:${v.color_code};border:2px solid var(--border)"></span>` : '—'}</td>
            <td>${v.sort_order}</td>
            <td>
                <form method="POST" action="/admin/attributes/${currentAttrId}/values/${v.id}" style="display:inline" onsubmit="return confirm('حذف؟')">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-xs btn-outline" style="color:var(--danger)"><i class="fas fa-trash"></i></button>
                </form>
            </td>
        </tr>`).join('')}
    </tbody></table>`;
}

function openAddAttrModal() {
    document.getElementById('attrModalTitle').textContent = 'إضافة خاصية جديدة';
    document.getElementById('attrForm').action = '{{ route("admin.attributes.store") }}';
    document.getElementById('attrFormMethod').value = 'POST';
    document.getElementById('attrNameAr').value = '';
    document.getElementById('attrNameEn').value = '';
    document.getElementById('attrType').value = 'select';
    document.getElementById('attrModal').classList.add('open');
}

function openEditAttrModal(id, nameAr, nameEn, type) {
    document.getElementById('attrModalTitle').textContent = 'تعديل الخاصية';
    document.getElementById('attrForm').action = `/admin/attributes/${id}`;
    document.getElementById('attrFormMethod').value = 'PUT';
    document.getElementById('attrNameAr').value = nameAr;
    document.getElementById('attrNameEn').value = nameEn;
    document.getElementById('attrType').value = type;
    document.getElementById('attrModal').classList.add('open');
}

function closeAttrModal() { document.getElementById('attrModal').classList.remove('open'); }

function openAddValueModal() {
    if (!currentAttrId) return;
    document.getElementById('valueForm').action = `/admin/attributes/${currentAttrId}/values`;
    document.getElementById('colorPickerWrap').style.display = currentAttrType === 'color' ? '' : 'none';
    document.getElementById('valueModal').classList.add('open');
}

function closeValueModal() { document.getElementById('valueModal').classList.remove('open'); }

// Close modals on backdrop click
['attrModal','valueModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});

// Auto-load first attribute values
@if($attributes->isNotEmpty())
loadValues({{ $attributes->first()->id }}, '{{ addslashes($attributes->first()->name_ar) }}');
@endif
</script>
@endpush
@endsection
