@extends('layouts.admin')
@section('title', 'الكوبونات')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">الكوبونات</span>
@endsection
@section('content')

<div class="page-header">
    <div><h1 class="page-title">الكوبونات</h1><p class="page-subtitle">{{ $coupons->count() }} كوبون</p></div>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة كوبون</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>الكود</th><th>النوع</th><th>القيمة</th><th>الاستخدامات</th><th>الصلاحية</th><th>الحالة</th><th>إجراءات</th></tr></thead>
            <tbody>
            @forelse($coupons as $coupon)
            <tr>
                <td>
                    <div style="font-weight:700;font-family:monospace;font-size:1rem;color:var(--primary)">{{ $coupon->code }}</div>
                    <div style="font-size:.78rem;color:var(--text-muted)">{{ $coupon->name_ar }}</div>
                </td>
                <td style="font-size:.85rem">{{ $coupon->type === 'fixed' ? 'مبلغ ثابت' : 'نسبة مئوية' }}</td>
                <td style="font-weight:700">{{ $coupon->type === 'fixed' ? $coupon->value.' ر.س' : $coupon->value.'%' }}</td>
                <td style="font-size:.85rem">{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                <td style="font-size:.82rem;color:var(--text-muted)">
                    @if($coupon->expires_at)
                    {{ $coupon->expires_at->format('d/m/Y') }}
                    @if($coupon->expires_at->isPast())<span style="color:var(--danger)"> (منتهي)</span>@endif
                    @else
                    بدون انتهاء
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ $coupon->is_active ? 'success' : 'secondary' }}">{{ $coupon->is_active ? 'نشط' : 'معطل' }}</span>
                </td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-xs btn-outline"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('حذف الكوبون؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline" style="color:var(--danger)"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">لا توجد كوبونات</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
