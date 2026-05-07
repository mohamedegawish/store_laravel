@extends('layouts.store')
@section('title', 'الأسئلة الشائعة')
@section('content')

<div style="background:var(--surface-2);padding:48px 0 32px">
    <div class="container">
        <h1 style="font-size:2rem;font-weight:800;margin-bottom:8px">الأسئلة الشائعة</h1>
        <p style="color:var(--text-muted)">إجابات على أكثر الأسئلة شيوعاً</p>
    </div>
</div>

<div class="container" style="padding:48px 0;max-width:800px">
    @if($faqs->isEmpty())
    <div style="text-align:center;padding:60px;color:var(--text-muted)">
        <i class="fas fa-circle-question" style="font-size:3rem;opacity:.3;display:block;margin-bottom:16px"></i>
        <p>لا توجد أسئلة متاحة حالياً</p>
    </div>
    @else
    @php $categories = $faqs->pluck('category')->filter()->unique(); @endphp

    @if($categories->isNotEmpty())
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:32px">
        <button class="faq-filter active" data-cat="" onclick="filterFaqs(this, '')">الكل</button>
        @foreach($categories as $cat)
        <button class="faq-filter" data-cat="{{ $cat }}" onclick="filterFaqs(this, '{{ $cat }}')">{{ $cat }}</button>
        @endforeach
    </div>
    @endif

    <div id="faqList">
    @foreach($faqs as $faq)
    <div class="faq-item" data-cat="{{ $faq->category }}" style="background:var(--surface);border-radius:12px;margin-bottom:12px;border:1px solid var(--border);overflow:hidden">
        <button class="faq-question" onclick="toggleFaq(this)"
                style="width:100%;text-align:right;padding:18px 20px;background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;font-family:inherit;font-size:.95rem;font-weight:600;color:var(--text)">
            {{ app()->getLocale() === 'ar' ? $faq->question_ar : $faq->question_en }}
            <i class="fas fa-chevron-down" style="transition:.3s;color:var(--primary);flex-shrink:0;margin-right:12px"></i>
        </button>
        <div class="faq-answer" style="display:none;padding:0 20px 18px;color:var(--text-muted);line-height:1.8;border-top:1px solid var(--border)">
            <div style="padding-top:14px">
                {{ app()->getLocale() === 'ar' ? $faq->answer_ar : $faq->answer_en }}
            </div>
        </div>
    </div>
    @endforeach
    </div>
    @endif
</div>

@push('styles')
<style>
.faq-filter {
    padding: 7px 16px; border-radius: 20px; border: 1.5px solid var(--border);
    background: var(--surface); cursor: pointer; font-family: inherit;
    font-size: .84rem; font-weight: 600; color: var(--text-muted); transition: .2s;
}
.faq-filter:hover, .faq-filter.active {
    background: var(--primary); color: white; border-color: var(--primary);
}
.faq-question.open i { transform: rotate(180deg); }
</style>
@endpush

@push('scripts')
<script>
function toggleFaq(btn) {
    const answer = btn.nextElementSibling;
    const icon = btn.querySelector('i');
    if (answer.style.display === 'none') {
        answer.style.display = '';
        btn.classList.add('open');
    } else {
        answer.style.display = 'none';
        btn.classList.remove('open');
    }
}

function filterFaqs(btn, cat) {
    document.querySelectorAll('.faq-filter').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.faq-item').forEach(item => {
        item.style.display = (!cat || item.dataset.cat === cat) ? '' : 'none';
    });
}
</script>
@endpush
@endsection
