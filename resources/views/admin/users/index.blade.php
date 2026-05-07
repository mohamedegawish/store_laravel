@extends('layouts.admin')

@section('title', 'إدارة المستخدمين | المدير العام')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">المستخدمون</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">إدارة المستخدمين</h1>
        <p class="page-subtitle">{{ $users->total() }} مستخدم مسجل في النظام</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> إضافة مستخدم
    </a>
</div>

<div class="card">
    <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
        <div class="filter-bar">
            <div class="search-wrap">
                <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو البريد..." value="{{ request('search') }}">
                <i class="fas fa-magnifying-glass"></i>
            </div>

            <select name="role" class="form-control" onchange="document.getElementById('filterForm').submit()">
                <option value="">جميع الأدوار</option>
                <option value="super_admin"   {{ request('role') === 'super_admin'   ? 'selected' : '' }}>مدير عام</option>
                <option value="company_admin" {{ request('role') === 'company_admin' ? 'selected' : '' }}>مدير شركة</option>
                <option value="user"          {{ request('role') === 'user'          ? 'selected' : '' }}>مستخدم عادي</option>
            </select>

            <select name="sort" class="form-control" onchange="document.getElementById('filterForm').submit()">
                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>الأحدث أولاً</option>
                <option value="name"       {{ request('sort') === 'name'       ? 'selected' : '' }}>الاسم أبجدياً</option>
                <option value="email"      {{ request('sort') === 'email'      ? 'selected' : '' }}>البريد الإلكتروني</option>
                <option value="role"       {{ request('sort') === 'role'       ? 'selected' : '' }}>الدور</option>
            </select>

            @if(request()->hasAny(['search','role','sort']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-rotate"></i> إعادة ضبط
                </a>
            @endif

            <button type="submit" class="btn btn-primary btn-sm" style="margin-right:auto;">
                <i class="fas fa-magnifying-glass"></i> بحث
            </button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المستخدم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الدور</th>
                    <th>الشركة</th>
                    <th>تاريخ التسجيل</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $roleMap = [
                        'super_admin'   => ['label' => 'مدير عام',    'class' => 'primary'],
                        'company_admin' => ['label' => 'مدير شركة',   'class' => 'warning'],
                        'user'          => ['label' => 'مستخدم',      'class' => 'secondary'],
                    ];
                    $r = $roleMap[$user->role] ?? ['label' => $user->role, 'class' => 'secondary'];
                @endphp
                <tr>
                    <td style="color:var(--text-3); font-size:0.8rem;">{{ $user->id }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:white;font-size:0.85rem;font-weight:700;flex-shrink:0;">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight:700;">{{ $user->name }}</div>
                                <div style="font-size:0.75rem;color:var(--text-3);">ID: {{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:0.85rem; direction:ltr; text-align:right;">{{ $user->email }}</td>
                    <td>
                        <span class="badge badge-{{ $r['class'] }}">{{ $r['label'] }}</span>
                    </td>
                    <td style="font-size:0.85rem;">
                        {{ $user->company?->company_name ?? '—' }}
                    </td>
                    <td style="font-size:0.8rem;color:var(--text-3);">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-icon btn-secondary btn-xs" title="عرض">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-icon btn-outline-primary btn-xs" title="تعديل">
                                <i class="fas fa-pen"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <button
                                class="btn btn-icon btn-danger btn-xs"
                                title="حذف"
                                onclick="confirmDelete('{{ route('admin.users.destroy', $user) }}', '{{ $user->name }}')">
                                <i class="fas fa-trash-can"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h3>لا يوجد مستخدمون</h3>
                            <p>لم يتم العثور على مستخدمين تطابق معايير البحث</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="pagination-wrap">
        <span>عرض {{ $users->firstItem() }}–{{ $users->lastItem() }} من {{ $users->total() }}</span>
        <div class="pagination">{{ $users->links() }}</div>
    </div>
    @endif
</div>

@endsection

