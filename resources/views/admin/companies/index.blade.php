@extends('layouts.admin')

@section('title', 'إدارة الشركات | المدير العام')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">الشركات</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">إدارة الشركات</h1>
        <p class="page-subtitle">{{ $companies->total() }} شركة مسجلة في النظام</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة شركة
        </a>
    </div>
</div>

<div class="card">
    {{-- ===== FILTER BAR ===== --}}
    <form method="GET" action="{{ route('admin.companies.index') }}" id="filterForm">
        <div class="filter-bar">
            <div class="search-wrap">
                <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو البريد..." value="{{ request('search') }}">
                <i class="fas fa-magnifying-glass"></i>
            </div>

            <select name="status" class="form-control" onchange="document.getElementById('filterForm').submit()">
                <option value="">جميع الحالات</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>نشطة</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>قيد المراجعة</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>معطلة</option>
            </select>

            <select name="sort" class="form-control" onchange="document.getElementById('filterForm').submit()">
                <option value="created_at" {{ request('sort') === 'created_at'   ? 'selected' : '' }}>الأحدث أولاً</option>
                <option value="company_name" {{ request('sort') === 'company_name' ? 'selected' : '' }}>الاسم أبجدياً</option>
                <option value="status"     {{ request('sort') === 'status'       ? 'selected' : '' }}>الحالة</option>
            </select>

            @if(request()->hasAny(['search','status','sort']))
                <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-rotate"></i> إعادة ضبط
                </a>
            @endif

            <div style="margin-right:auto; margin-left:0; display:flex; gap:6px;">
                {{-- view toggle --}}
                <div class="view-toggle">
                    <button type="button" class="view-btn active" id="btnTable" onclick="setView('table')"><i class="fas fa-list"></i></button>
                    <button type="button" class="view-btn" id="btnCard"  onclick="setView('card')"> <i class="fas fa-grip"></i></button>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-magnifying-glass"></i> بحث</button>
            </div>
        </div>
    </form>

    {{-- ===== TABLE VIEW ===== --}}
    <div id="tableView">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الشركة</th>
                        <th>المدير</th>
                        <th>البريد الإلكتروني</th>
                        <th>الحالة</th>
                        <th>تاريخ الإضافة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                    <tr>
                        <td style="color:var(--text-3); font-size:0.8rem;">{{ $company->id }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-soft);display:flex;align-items:center;justify-content:center;color:var(--primary);flex-shrink:0;overflow:hidden;">
                                    @if($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <i class="fas fa-building"></i>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:700;">{{ $company->company_name }}</div>
                                    <div style="font-size:0.75rem;color:var(--text-3);">{{ $company->chairman_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $company->manager_name }}</td>
                        <td style="font-size:0.85rem; direction:ltr; text-align:right;">{{ $company->company_email }}</td>
                        <td>
                            <span class="badge badge-{{ $company->statusColor() }}">
                                <i class="fas fa-circle" style="font-size:0.45rem;"></i>
                                {{ $company->statusLabel() }}
                            </span>
                        </td>
                        <td style="font-size:0.8rem;color:var(--text-3);">
                            {{ $company->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-icon btn-secondary btn-xs" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-icon btn-outline-primary btn-xs" title="تعديل">
                                    <i class="fas fa-pen"></i>
                                </a>

                                {{-- Toggle status --}}
                                <form action="{{ route('admin.companies.toggleStatus', $company) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-icon btn-xs {{ $company->isActive() ? 'btn-warning' : 'btn-success' }}"
                                        title="{{ $company->isActive() ? 'تعطيل' : 'تفعيل' }}">
                                        <i class="fas {{ $company->isActive() ? 'fa-ban' : 'fa-circle-check' }}"></i>
                                    </button>
                                </form>

                                <button
                                    class="btn btn-icon btn-danger btn-xs"
                                    title="حذف"
                                    onclick="confirmDelete('{{ route('admin.companies.destroy', $company) }}', '{{ $company->company_name }}')">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-building"></i>
                                <h3>لا توجد شركات</h3>
                                <p>لم يتم العثور على شركات تطابق معايير البحث</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== CARD VIEW ===== --}}
    <div id="cardView" style="display:none;">
        <div class="company-cards" style="padding:20px;">
            @forelse($companies as $company)
            <div class="company-card">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="company-card-logo">
                        @if($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="">
                        @else
                            <i class="fas fa-building"></i>
                        @endif
                    </div>
                    <div>
                        <div class="company-card-name">{{ $company->company_name }}</div>
                        <span class="badge badge-{{ $company->statusColor() }}">{{ $company->statusLabel() }}</span>
                    </div>
                </div>
                <div>
                    <div class="company-card-meta"><i class="fas fa-user-tie"></i> {{ $company->manager_name }}</div>
                    <div class="company-card-meta"><i class="fas fa-envelope"></i> {{ $company->company_email }}</div>
                    @if($company->hotline)
                    <div class="company-card-meta"><i class="fas fa-phone"></i> {{ $company->hotline }}</div>
                    @endif
                </div>
                <div class="company-card-actions">
                    <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-secondary btn-xs"><i class="fas fa-eye"></i> عرض</a>
                    <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-outline-primary btn-xs"><i class="fas fa-pen"></i> تعديل</a>
                    <button class="btn btn-danger btn-xs" onclick="confirmDelete('{{ route('admin.companies.destroy', $company) }}', '{{ $company->company_name }}')">
                        <i class="fas fa-trash-can"></i>
                    </button>
                </div>
            </div>
            @empty
                <div class="empty-state" style="grid-column: 1 / -1; width: 100%;">
                    <i class="fas fa-building"></i>
                    <h3>لا توجد شركات</h3>
                    <p>لم يتم العثور على شركات تطابق معايير البحث</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($companies->hasPages())
    <div class="pagination-wrap">
        <span>عرض {{ $companies->firstItem() }}–{{ $companies->lastItem() }} من {{ $companies->total() }}</span>
        <div class="pagination">
            {{ $companies->links() }}
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    function setView(v) {
        const tableView = document.getElementById('tableView');
        const cardView  = document.getElementById('cardView');
        const btnTable  = document.getElementById('btnTable');
        const btnCard   = document.getElementById('btnCard');
        if (v === 'table') {
            tableView.style.display = '';
            cardView.style.display  = 'none';
            btnTable.classList.add('active');
            btnCard.classList.remove('active');
        } else {
            tableView.style.display = 'none';
            cardView.style.display  = '';
            btnCard.classList.add('active');
            btnTable.classList.remove('active');
        }
        localStorage.setItem('companiesView', v);
    }

    // restore view preference
    const savedView = localStorage.getItem('companiesView');
    if (savedView) setView(savedView);
</script>
@endpush

