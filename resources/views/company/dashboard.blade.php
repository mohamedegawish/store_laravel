@extends('layouts.company')

@section('title', 'لوحة التحكم | ' . $company->company_name)

@section('breadcrumb')
    <span class="current">لوحة التحكم</span>
@endsection

@section('content')

{{-- ════════════════════════ PAGE HEADER ════════════════════════ --}}
<div class="page-header">
    <div>
        <h1 class="page-title">مرحباً، {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
        <p class="page-subtitle">نظرة عامة على أداء متجر <strong>{{ $company->company_name }}</strong></p>
    </div>
    <div class="btn-group">
        <a href="{{ auth()->user()->company?->slug ? route('front.home', $company->slug) : '#' }}"
           target="_blank" class="btn btn-outline-primary">
            <i class="fas fa-store"></i> عرض المتجر
        </a>
        <a href="{{ route('company.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة منتج
        </a>
    </div>
</div>

{{-- ════════════════════════ STAT CARDS ════════════════════════ --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">

    <div class="stat-card primary">
        <div class="stat-header">
            <span class="stat-label">إجمالي الطلبات</span>
            <span class="stat-icon primary"><i class="fas fa-cart-shopping"></i></span>
        </div>
        <div class="stat-value" data-count="{{ $totalOrders }}">0</div>
        <div class="stat-trend">
            <span class="badge badge-info" style="font-size:0.7rem;">{{ $pendingOrders }} قيد المراجعة</span>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <span class="stat-label">الطلبات المكتملة</span>
            <span class="stat-icon success"><i class="fas fa-circle-check"></i></span>
        </div>
        <div class="stat-value" data-count="{{ $completedOrders }}">0</div>
        <div class="stat-trend"><i class="fas fa-check"></i> تم التسليم</div>
    </div>

    <div class="stat-card accent">
        <div class="stat-header">
            <span class="stat-label">الإيرادات الكلية</span>
            <span class="stat-icon accent"><i class="fas fa-wallet"></i></span>
        </div>
        <div class="stat-value">{{ number_format($totalRevenue, 0) }}<small style="font-size:0.4em"> ج.م</small></div>
        <div class="stat-trend">
            <span style="color: {{ $growthRate >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                <i class="fas fa-arrow-{{ $growthRate >= 0 ? 'up' : 'down' }}-right"></i>
                {{ $growthRate > 0 ? '+' : '' }}{{ $growthRate }}% هذا الشهر
            </span>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <span class="stat-label">إيرادات هذا الشهر</span>
            <span class="stat-icon warning"><i class="fas fa-chart-line"></i></span>
        </div>
        <div class="stat-value">{{ number_format($monthlyRevenue, 0) }}<small style="font-size:0.4em"> ج.م</small></div>
        <div class="stat-trend"><i class="fas fa-calendar-days"></i> {{ now()->translatedFormat('F Y') }}</div>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <span class="stat-label">إجمالي المنتجات</span>
            <span class="stat-icon info"><i class="fas fa-box-open"></i></span>
        </div>
        <div class="stat-value" data-count="{{ $totalProducts }}">0</div>
        <div class="stat-trend">
            @if($outOfStockCount > 0)
                <span style="color:var(--danger);"><i class="fas fa-triangle-exclamation"></i> {{ $outOfStockCount }} نفذت كميتها</span>
            @else
                <i class="fas fa-check-circle" style="color:var(--success);"></i> المخزون جيد
            @endif
        </div>
    </div>

    <div class="stat-card" style="background: linear-gradient(135deg, #7C3AED, #4F46E5); color: white; border: none;">
        <div class="stat-header">
            <span class="stat-label" style="color:rgba(255,255,255,0.8);">متوسط قيمة الطلب</span>
            <span class="stat-icon" style="background:rgba(255,255,255,0.15); color: white;"><i class="fas fa-receipt"></i></span>
        </div>
        <div class="stat-value" style="color:white;">{{ number_format($avgOrderValue, 0) }}<small style="font-size:0.4em"> ج.م</small></div>
        <div class="stat-trend" style="color:rgba(255,255,255,0.7);"><i class="fas fa-equals"></i> لكل طلب مكتمل</div>
    </div>

</div>

{{-- ════════════════════════ SMART INSIGHTS ════════════════════════ --}}
<div class="card" style="margin-bottom:24px; background: linear-gradient(135deg, var(--primary-soft) 0%, var(--surface) 100%); border-color: var(--primary); border-width: 2px;">
    <div class="card-header" style="border-bottom: none; padding-bottom: 0;">
        <h3 class="card-title" style="color: var(--primary);">
            <i class="fas fa-wand-magic-sparkles"></i> رؤى ذكية
        </h3>
        <span style="font-size:0.8rem; color:var(--text-3); background:var(--primary-soft); padding:3px 10px; border-radius:20px;">مدعوم بالبيانات</span>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">

            {{-- Best Product --}}
            <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #D1FAE5; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink:0;">🏆</div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-3); margin-bottom: 3px;">أكثر المنتجات مبيعاً</div>
                    <div style="font-weight: 700; font-size: 0.9rem; color: var(--text);">
                        {{ $bestProduct?->name ?? 'لا توجد بيانات بعد' }}
                    </div>
                    @if($bestProduct)
                        <div style="font-size: 0.75rem; color: var(--success);">{{ $bestProduct->total_sold }} وحدة مباعة</div>
                    @endif
                </div>
            </div>

            {{-- Worst Product --}}
            <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #FEE2E2; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink:0;">📉</div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-3); margin-bottom: 3px;">يحتاج تحسيناً</div>
                    <div style="font-weight: 700; font-size: 0.9rem; color: var(--text);">
                        {{ $worstProduct?->name ?? 'لا توجد بيانات بعد' }}
                    </div>
                    @if($worstProduct)
                        <div style="font-size: 0.75rem; color: var(--danger);">{{ $worstProduct->total_sold }} وحدة فقط</div>
                    @endif
                </div>
            </div>

            {{-- Growth Rate --}}
            <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $growthRate >= 0 ? '#D1FAE5' : '#FEE2E2' }}; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink:0;">
                    {{ $growthRate >= 0 ? '📈' : '📉' }}
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-3); margin-bottom: 3px;">معدل النمو الشهري</div>
                    <div style="font-weight: 700; font-size: 1.1rem; color: {{ $growthRate >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                        {{ $growthRate > 0 ? '+' : '' }}{{ $growthRate }}%
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-3);">مقارنة بالشهر الماضي</div>
                </div>
            </div>

            {{-- Avg order value --}}
            <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #EDE9FE; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink:0;">💡</div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-3); margin-bottom: 3px;">متوسط قيمة الطلب</div>
                    <div style="font-weight: 700; font-size: 1.1rem; color: var(--text);">{{ number_format($avgOrderValue, 2) }} ج.م</div>
                    <div style="font-size: 0.75rem; color: var(--text-3);">من {{ $totalOrders }} طلب</div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ════════════════════════ CHARTS ════════════════════════ --}}
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 22px; margin-bottom: 24px;">

    {{-- Sales Line Chart --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-area"></i> الإيرادات الشهرية ({{ now()->year }})</h3>
            <a href="{{ route('company.reports.sales') }}" class="btn btn-sm btn-secondary">التقرير الكامل</a>
        </div>
        <div class="card-body" style="padding-top:10px;">
            <canvas id="salesChart" height="140"></canvas>
        </div>
    </div>

    {{-- Orders Doughnut --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-pie"></i> توزيع الطلبات</h3>
        </div>
        <div class="card-body" style="display:flex; flex-direction: column; align-items:center; justify-content:center;">
            <canvas id="ordersChart" height="200" style="max-width:200px;"></canvas>
            {{-- Legend --}}
            <div style="margin-top:16px; width:100%;">
                @php
                    $statusMap = ['pending'=>['قيد المراجعة','#F59E0B'], 'confirmed'=>['مؤكد','#3B82F6'], 'shipped'=>['مشحون','#8B5CF6'], 'delivered'=>['مكتمل','#10B981'], 'cancelled'=>['ملغي','#EF4444']];
                @endphp
                @foreach($statusMap as $key => [$label, $color])
                    @php $count = $ordersByStatus[$key] ?? 0; @endphp
                    @if($count > 0)
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; font-size:0.8rem;">
                        <span style="display:flex; align-items:center; gap:6px; color:var(--text-2);">
                            <span style="width:10px; height:10px; border-radius:50%; background:{{ $color }};"></span>
                            {{ $label }}
                        </span>
                        <strong>{{ $count }}</strong>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- ════════════════════════ LOW STOCK + RECENT ORDERS ════════════════════════ --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 22px; margin-bottom: 24px;">

    {{-- Low Stock Alerts --}}
    @if($lowStockProducts->count() > 0 || $outOfStockCount > 0)
    <div class="card" style="border-color: var(--warning);">
        <div class="card-header" style="background: rgba(245,158,11,0.06);">
            <h3 class="card-title" style="color:var(--warning);">
                <i class="fas fa-triangle-exclamation"></i> تنبيهات المخزون
            </h3>
            <span class="badge badge-warning">{{ $lowStockProducts->count() }} منتج</span>
        </div>
        <div class="card-body" style="padding:0;">
            @foreach($lowStockProducts as $p)
                @php $v = $p->variants->first(); @endphp
                <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-bottom:1px solid var(--border);">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:32px;height:32px;border-radius:6px;background:var(--warning-soft);display:flex;align-items:center;justify-content:center;color:var(--warning);overflow:hidden;flex-shrink:0;">
                            @if($p->images && count($p->images))
                                <img src="{{ asset('storage/'.$p->images[0]) }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i class="fas fa-image" style="font-size:0.8rem;"></i>
                            @endif
                        </div>
                        <span style="font-size:0.875rem; font-weight:600;">{{ $p->name }}</span>
                    </div>
                    <span class="badge badge-warning">{{ $v?->stock_quantity ?? 0 }} متبقي</span>
                </div>
            @endforeach
            @if($outOfStockCount > 0)
                <div style="padding:12px 20px; font-size:0.85rem; color:var(--danger); font-weight:600;">
                    <i class="fas fa-xmark-circle"></i> {{ $outOfStockCount }} منتج نفذت كميته تماماً
                </div>
            @endif
        </div>
        <div class="card-footer" style="text-align:center; padding:12px;">
            <a href="{{ route('company.products.index') }}?stock=low" class="btn btn-sm btn-warning" style="width:100%; justify-content:center;">إدارة المخزون</a>
        </div>
    </div>
    @else
    <div class="card" style="border-color:var(--success);">
        <div class="card-header" style="background: rgba(16,185,129,0.06);">
            <h3 class="card-title" style="color:var(--success);"><i class="fas fa-circle-check"></i> حالة المخزون</h3>
        </div>
        <div class="card-body" style="text-align:center; padding: 40px 20px;">
            <div style="font-size:3rem; margin-bottom:10px;">✅</div>
            <p style="font-weight:600; color:var(--success);">جميع المنتجات لديها مخزون كافٍ</p>
            <p style="font-size:0.85rem; color:var(--text-3); margin-top:5px;">لا توجد تنبيهات مخزون حالياً</p>
        </div>
    </div>
    @endif

    {{-- Recent Products --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-box-open"></i> أحدث المنتجات</h3>
            <a href="{{ route('company.products.index') }}" class="btn btn-sm btn-secondary">عرض الكل</a>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($recentProducts as $product)
                @php $v = $product->variants->first(); @endphp
                <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-bottom:1px solid var(--border);">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:36px;height:36px;border-radius:8px;background:var(--primary-soft);display:flex;align-items:center;justify-content:center;color:var(--primary);overflow:hidden;flex-shrink:0;">
                            @if($product->images && count($product->images))
                                <img src="{{ asset('storage/'.$product->images[0]) }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i class="fas fa-image" style="font-size:0.8rem;"></i>
                            @endif
                        </div>
                        <div>
                            <div style="font-weight:600; font-size:0.875rem;">{{ Str::limit($product->name, 28) }}</div>
                            <div style="font-size:0.75rem; color:var(--text-3);">{{ $product->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div style="text-align:left;">
                        <div style="font-weight:700; font-size:0.875rem; color:var(--primary);">{{ $v ? number_format($v->price, 0).'ج.م' : '—' }}</div>
                        <span class="badge badge-{{ $product->is_active ? 'success' : 'secondary' }}" style="font-size:0.65rem;">
                            {{ $product->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:40px; color:var(--text-3);">
                    <i class="fas fa-box-open" style="font-size:2rem; margin-bottom:10px; opacity:0.4;"></i>
                    <p>لا توجد منتجات بعد</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ════════════════════════ RECENT ORDERS TABLE ════════════════════════ --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-clock-rotate-left"></i> آخر الطلبات الواردة</h3>
        <a href="{{ route('company.orders.index') }}" class="btn btn-sm btn-secondary">عرض جميع الطلبات</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>العميل</th>
                    <th>التاريخ</th>
                    <th>الإجمالي</th>
                    <th>الدفع</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td style="font-weight:700; font-family:monospace; font-size:0.9rem;">#{{ $order->id }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:var(--primary-soft);display:flex;align-items:center;justify-content:center;color:var(--primary);font-weight:700;font-size:0.75rem;flex-shrink:0;">
                                {{ mb_substr($order->user?->name ?? 'ز', 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:0.85rem;">{{ $order->user?->name ?? 'زائر' }}</div>
                                <div style="font-size:0.72rem;color:var(--text-3);">{{ $order->user?->phone ?? $order->user?->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:0.8rem; color:var(--text-2);">
                        {{ $order->created_at->format('Y/m/d') }}<br>
                        <span style="color:var(--text-3);">{{ $order->created_at->format('H:i') }}</span>
                    </td>
                    <td style="font-weight:700; color:var(--accent);">{{ number_format($order->total_amount, 2) }} ج.م</td>
                    <td>
                        @php
                            $pColors = ['pending'=>'warning','paid'=>'success','failed'=>'danger','refunded'=>'secondary'];
                            $pLabels = ['pending'=>'انتظار','paid'=>'مدفوع','failed'=>'مرفوض','refunded'=>'مسترد'];
                        @endphp
                        <span class="badge badge-{{ $pColors[$order->payment_status ?? 'pending'] ?? 'secondary' }}">
                            {{ $pLabels[$order->payment_status ?? 'pending'] ?? ($order->payment_status ?? '—') }}
                        </span>
                    </td>
                    <td>
                        @php
                            $oColors = ['pending'=>'warning','confirmed'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                            $oLabels = ['pending'=>'قيد المراجعة','confirmed'=>'مؤكد','shipped'=>'مشحون','delivered'=>'مكتمل','cancelled'=>'ملغي'];
                        @endphp
                        <span class="badge badge-{{ $oColors[$order->status] ?? 'secondary' }}">
                            {{ $oLabels[$order->status] ?? $order->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('company.orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>لا توجد طلبات بعد</h3>
                            <p>ستظهر الطلبات هنا فور وصولها من عملائك.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Animated Counters ──────────────────────────────
document.querySelectorAll('[data-count]').forEach(el => {
    const target = parseInt(el.dataset.count) || 0;
    if (target === 0) { el.textContent = '0'; return; }
    let current = 0;
    const step = Math.max(1, Math.ceil(target / 50));
    const timer = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = current.toLocaleString('ar');
        if (current >= target) clearInterval(timer);
    }, 25);
});

// ── Sales Line Chart ──────────────────────────────
const salesCtx = document.getElementById('salesChart');
if (salesCtx) {
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'],
            datasets: [{
                label: 'الإيرادات (ج.م)',
                data: @json($monthlySales),
                borderColor: '#10B981',
                backgroundColor: (ctx) => {
                    const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 220);
                    gradient.addColorStop(0, 'rgba(16,185,129,0.25)');
                    gradient.addColorStop(1, 'rgba(16,185,129,0.01)');
                    return gradient;
                },
                borderWidth: 2.5,
                fill: true,
                tension: 0.45,
                pointRadius: 4,
                pointBackgroundColor: '#10B981',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// ── Orders Doughnut ───────────────────────────────
const ordersCtx = document.getElementById('ordersChart');
if (ordersCtx) {
    const statusData   = @json($ordersByStatus);
    const labels       = { pending:'قيد المراجعة', confirmed:'مؤكد', shipped:'مشحون', delivered:'مكتمل', cancelled:'ملغي' };
    const colors       = { pending:'#F59E0B', confirmed:'#3B82F6', shipped:'#8B5CF6', delivered:'#10B981', cancelled:'#EF4444' };
    const keys         = Object.keys(statusData);

    new Chart(ordersCtx, {
        type: 'doughnut',
        data: {
            labels: keys.map(k => labels[k] ?? k),
            datasets: [{
                data: keys.map(k => statusData[k]),
                backgroundColor: keys.map(k => colors[k] ?? '#94A3B8'),
                borderWidth: 2,
                borderColor: 'var(--surface)',
            }]
        },
        options: {
            responsive: true, cutout: '68%',
            plugins: { legend: { display: false } }
        }
    });
}
</script>
@endpush
