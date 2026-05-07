@extends('layouts.admin')
@section('title', 'رسائل التواصل')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">رسائل التواصل</span>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">رسائل التواصل</h1>
        <p class="page-subtitle">رسائل العملاء من صفحة "اتصل بنا"</p>
    </div>
</div>

<div class="card">
    <div class="filter-bar">
        <div class="search-wrap">
            <input type="text" id="searchInput" class="form-control" placeholder="بحث في الرسائل...">
            <i class="fas fa-magnifying-glass"></i>
        </div>
        <span style="color:var(--text-3);font-size:.85rem">{{ $messages->total() }} رسالة</span>
    </div>
    <div class="table-wrap">
        <table id="messagesTable">
            <thead>
                <tr>
                    <th>المرسل</th>
                    <th>الموضوع</th>
                    <th>الرسالة</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
            @forelse($messages as $msg)
            <tr style="{{ !($msg->is_read ?? false) ? 'background:var(--primary-soft)' : '' }}" id="msg-{{ $msg->id }}">
                <td>
                    <div style="font-weight:600">{{ $msg->name }}</div>
                    <div style="font-size:.78rem;color:var(--text-3)">
                        <a href="mailto:{{ $msg->email }}" style="color:var(--primary)">{{ $msg->email }}</a>
                    </div>
                    @if($msg->phone)
                    <div style="font-size:.78rem;color:var(--text-3)">{{ $msg->phone }}</div>
                    @endif
                </td>
                <td style="font-weight:600;max-width:200px">{{ $msg->subject }}</td>
                <td style="max-width:300px;color:var(--text-2)">
                    <span class="msg-preview">{{ Str::limit($msg->message, 80) }}</span>
                    <span class="msg-full" style="display:none">{{ $msg->message }}</span>
                </td>
                <td style="font-size:.82rem;color:var(--text-3);white-space:nowrap">
                    {{ $msg->created_at->format('d/m/Y') }}<br>
                    {{ $msg->created_at->format('H:i') }}
                </td>
                <td>
                    @if($msg->is_read ?? false)
                    <span class="badge badge-secondary">مقروءة</span>
                    @else
                    <span class="badge badge-primary">جديدة</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:4px">
                        <button class="btn btn-xs btn-outline" onclick="toggleMsg({{ $msg->id }})" title="عرض الرسالة">
                            <i class="fas fa-expand-alt"></i>
                        </button>
                        <a href="mailto:{{ $msg->email }}?subject=Re: {{ urlencode($msg->subject) }}" class="btn btn-xs btn-primary" title="رد">
                            <i class="fas fa-reply"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-3)">
                <i class="fas fa-comments" style="font-size:2rem;opacity:.3;display:block;margin-bottom:12px"></i>
                لا توجد رسائل
            </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($messages->hasPages())
    <div class="pagination-wrap">
        <span>{{ $messages->total() }} رسالة</span>
        {{ $messages->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
document.getElementById('searchInput')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#messagesTable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

function toggleMsg(id) {
    const row = document.getElementById('msg-' + id);
    const preview = row.querySelector('.msg-preview');
    const full = row.querySelector('.msg-full');
    if (full.style.display === 'none') {
        preview.style.display = 'none';
        full.style.display = '';
    } else {
        preview.style.display = '';
        full.style.display = 'none';
    }
}
</script>
@endpush
@endsection
