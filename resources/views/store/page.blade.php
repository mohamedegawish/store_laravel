@extends('layouts.store')
@section('title', app()->getLocale() === 'ar' ? $page->title_ar : $page->title_en)
@section('content')

<div style="background:var(--surface-2);padding:48px 0 32px">
    <div class="container">
        <h1 style="font-size:2rem;font-weight:800;margin-bottom:8px">
            {{ app()->getLocale() === 'ar' ? $page->title_ar : $page->title_en }}
        </h1>
    </div>
</div>

<div class="container" style="padding:48px 0;max-width:900px">
    <div style="background:var(--surface);border-radius:16px;padding:36px;border:1px solid var(--border);line-height:1.9;color:var(--text-2)">
        {!! app()->getLocale() === 'ar' ? $page->content_ar : ($page->content_en ?? $page->content_ar) !!}
    </div>
</div>
@endsection
