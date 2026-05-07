@extends('store.account.layout')
@section('title', 'عناويني')

@section('account_content')

@if(session('success'))
<div style="background:#D1FAE5;border:1px solid #A7F3D0;border-radius:var(--radius);padding:14px 18px;margin-bottom:20px;color:#065F46;font-size:.88rem">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <h2 style="font-size:1.2rem;font-weight:800">عناويني</h2>
    <button onclick="document.getElementById('addAddressForm').style.display='block';this.style.display='none'" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> إضافة عنوان
    </button>
</div>

{{-- Add Form --}}
<div id="addAddressForm" style="display:none;background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;margin-bottom:20px">
    <h3 style="font-weight:700;margin-bottom:16px;font-size:.95rem">عنوان جديد</h3>
    <form method="POST" action="{{ route('store.account.addresses.store') }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            <div>
                <label style="font-size:.8rem;font-weight:600;display:block;margin-bottom:5px">التسمية</label>
                <input type="text" name="label" placeholder="مثال: المنزل، العمل" value="{{ old('label') }}"
                       style="width:100%;height:40px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 12px;font-family:var(--font);font-size:.85rem;outline:none">
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:600;display:block;margin-bottom:5px">الاسم <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                       style="width:100%;height:40px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 12px;font-family:var(--font);font-size:.85rem;outline:none">
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:600;display:block;margin-bottom:5px">الهاتف <span style="color:var(--danger)">*</span></label>
                <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                       style="width:100%;height:40px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 12px;font-family:var(--font);font-size:.85rem;outline:none">
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:600;display:block;margin-bottom:5px">المدينة <span style="color:var(--danger)">*</span></label>
                <input type="text" name="city" value="{{ old('city') }}" required
                       style="width:100%;height:40px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 12px;font-family:var(--font);font-size:.85rem;outline:none">
            </div>
            <div style="grid-column:span 2">
                <label style="font-size:.8rem;font-weight:600;display:block;margin-bottom:5px">العنوان <span style="color:var(--danger)">*</span></label>
                <input type="text" name="address_line1" value="{{ old('address_line1') }}" required placeholder="الشارع، المبنى، الشقة"
                       style="width:100%;height:40px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 12px;font-family:var(--font);font-size:.85rem;outline:none">
            </div>
            <div style="grid-column:span 2">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.85rem">
                    <input type="checkbox" name="is_default" value="1"> تعيين كعنوان افتراضي
                </label>
            </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:16px">
            <button type="submit" class="btn btn-primary btn-sm">حفظ العنوان</button>
            <button type="button" onclick="document.getElementById('addAddressForm').style.display='none'" class="btn btn-ghost btn-sm">إلغاء</button>
        </div>
    </form>
</div>

{{-- Address List --}}
@forelse($addresses as $address)
<div style="background:#fff;border:1.5px solid {{ $address->is_default ? 'var(--primary)' : 'var(--border)' }};border-radius:var(--radius);padding:20px;margin-bottom:12px">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                <span style="font-weight:700;font-size:.92rem">{{ $address->label ?? $address->name }}</span>
                @if($address->is_default)
                <span style="background:var(--primary-light);color:var(--primary);font-size:.72rem;font-weight:700;padding:2px 8px;border-radius:99px">افتراضي</span>
                @endif
            </div>
            <div style="font-size:.85rem;color:var(--text-muted);line-height:1.8">
                <div>{{ $address->name }} — {{ $address->phone }}</div>
                <div>{{ $address->address_line1 }}، {{ $address->city }}</div>
                @if($address->state)<div>{{ $address->state }}</div>@endif
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0">
            @if(!$address->is_default)
            <form method="POST" action="{{ route('store.account.addresses.default', $address) }}">
                @csrf <button type="submit" class="btn btn-ghost btn-sm" title="تعيين كافتراضي"><i class="fas fa-check"></i></button>
            </form>
            @endif
            <form method="POST" action="{{ route('store.account.addresses.destroy', $address) }}" onsubmit="return confirm('حذف العنوان؟')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger)" title="حذف"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </div>
</div>
@empty
<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:40px;text-align:center;color:var(--text-muted)">
    <i class="fas fa-map-marker-alt" style="font-size:2.5rem;opacity:.2;margin-bottom:12px"></i>
    <p>لا توجد عناوين محفوظة</p>
</div>
@endforelse
@endsection
