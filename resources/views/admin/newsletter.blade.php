@extends('layouts.admin')
@section('title', 'النشرة البريدية')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">النشرة البريدية</span>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">النشرة البريدية</h1>
        <p class="page-subtitle">قائمة المشتركين في النشرة البريدية</p>
    </div>
    <a href="#" class="btn btn-secondary" onclick="exportCSV()"><i class="fas fa-file-csv"></i> تصدير CSV</a>
</div>

<div class="card">
    <div class="filter-bar">
        <div class="search-wrap">
            <input type="text" id="searchInput" class="form-control" placeholder="بحث بالبريد الإلكتروني...">
            <i class="fas fa-magnifying-glass"></i>
        </div>
        <span style="color:var(--text-3);font-size:.85rem">{{ $subscribers->total() }} مشترك</span>
    </div>
    <div class="table-wrap">
        <table id="subscribersTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>البريد الإلكتروني</th>
                    <th>تاريخ الاشتراك</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
            @forelse($subscribers as $i => $sub)
            <tr>
                <td style="color:var(--text-3)">{{ $subscribers->firstItem() + $i }}</td>
                <td>
                    <a href="mailto:{{ $sub->email }}" style="color:var(--primary);text-decoration:none;font-weight:600">
                        {{ $sub->email }}
                    </a>
                </td>
                <td style="font-size:.85rem;color:var(--text-3)">
                    {{ $sub->subscribed_at ? \Carbon\Carbon::parse($sub->subscribed_at)->format('d/m/Y H:i') : $sub->created_at->format('d/m/Y H:i') }}
                </td>
                <td>
                    <span class="badge badge-{{ ($sub->is_active ?? true) ? 'success' : 'secondary' }}">
                        {{ ($sub->is_active ?? true) ? 'نشط' : 'ملغي' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;padding:40px;color:var(--text-3)">
                <i class="fas fa-envelope-open" style="font-size:2rem;opacity:.3;display:block;margin-bottom:12px"></i>
                لا يوجد مشتركون بعد
            </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($subscribers->hasPages())
    <div class="pagination-wrap">
        <span>{{ $subscribers->total() }} مشترك</span>
        {{ $subscribers->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
document.getElementById('searchInput')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#subscribersTable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

function exportCSV() {
    const rows = [['#', 'البريد الإلكتروني', 'تاريخ الاشتراك', 'الحالة']];
    document.querySelectorAll('#subscribersTable tbody tr').forEach(tr => {
        const cells = tr.querySelectorAll('td');
        if (cells.length >= 4) {
            rows.push([cells[0].textContent.trim(), cells[1].textContent.trim(), cells[2].textContent.trim(), cells[3].textContent.trim()]);
        }
    });
    const csv = rows.map(r => r.map(c => `"${c.replace(/"/g,'""')}"`).join(',')).join('\n');
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'subscribers_' + new Date().toISOString().slice(0,10) + '.csv';
    link.click();
}
</script>
@endpush
@endsection
