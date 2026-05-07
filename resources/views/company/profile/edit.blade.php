@extends('layouts.company')

@section('title', 'ملف الشركة | إدارة المتجر')

@section('breadcrumb')
    <a href="{{ route('company.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">ملف الشركة</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">ملف الشركة 🏢</h1>
        <p class="page-subtitle">تحديث المعلومات الأساسية، شعار الشركة، وبيانات التواصل.</p>
    </div>
</div>

<form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">

        {{-- ── Logo & Basic Info ── --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">المعلومات الأساسية</h3></div>
            <div class="card-body">
                
                {{-- Logo --}}
                <div style="display:flex; flex-direction:column; align-items:center; margin-bottom:24px; gap:12px;">
                    <div style="width:120px; height:120px; border-radius:50%; border:2px dashed var(--border); overflow:hidden; position:relative; display:flex; align-items:center; justify-content:center; background:var(--surface-2); cursor:pointer;" onclick="document.getElementById('logoInput').click()">
                        @if($company->logo)
                            <img src="{{ asset('storage/'.$company->logo) }}" id="logoPreview" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <img src="" id="logoPreview" style="width:100%; height:100%; object-fit:cover; display:none;">
                            <div id="logoPlaceholder" style="text-align:center; color:var(--text-3);">
                                <i class="fas fa-camera" style="font-size:1.5rem; margin-bottom:5px;"></i><br>
                                <span style="font-size:0.75rem;">تغيير الشعار</span>
                            </div>
                        @endif
                    </div>
                    <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none;" onchange="previewLogo(this)">
                    <div class="form-hint">يُفضل رفع صورة بخلفية شفافة (PNG) بحجم لا يتجاوز 2MB.</div>
                    @error('logo') <div style="color:var(--danger); font-size:0.8rem;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">اسم الشركة <span class="required">*</span></label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $company->company_name) }}" required>
                    @error('company_name') <div style="color:var(--danger); font-size:0.8rem; margin-top:5px;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">اسم رئيس مجلس الإدارة</label>
                    <input type="text" name="chairman_name" class="form-control" value="{{ old('chairman_name', $company->chairman_name) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">اسم المدير العام</label>
                    <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name', $company->manager_name) }}">
                </div>
            </div>
        </div>

        {{-- ── Contact Info ── --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">بيانات التواصل والإدارة</h3></div>
            <div class="card-body">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني للشركة</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $company->company_email) }}" dir="ltr">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">رقم المدير المباشر</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="text" name="manager_phone" class="form-control" value="{{ old('manager_phone', $company->manager_phone) }}" dir="ltr">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">الخط الساخن (المبيعات)</label>
                        <div class="input-with-icon">
                            <i class="fas fa-headset"></i>
                            <input type="text" name="hotline" class="form-control" value="{{ old('hotline', $company->hotline) }}" dir="ltr">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">رقم الواتساب</label>
                        <div class="input-with-icon">
                            <i class="fab fa-whatsapp" style="color: #25D366;"></i>
                            <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $company->whatsapp) }}" dir="ltr" placeholder="05xxxxxxxx">
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-top:20px;">
                    <label class="form-label">الموقع الإلكتروني</label>
                    <div class="input-with-icon">
                        <i class="fas fa-globe"></i>
                        <input type="url" name="website" class="form-control" value="{{ old('website', $company->website) }}" dir="ltr" placeholder="https://">
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Addresses & Hours ── --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">العناوين ومواعيد العمل</h3></div>
            <div class="card-body">
                
                <div class="form-group">
                    <label class="form-label">عنوان المصنع / المقر الرئيسي <span class="required">*</span></label>
                    <textarea name="factory_address" class="form-control" rows="2" required>{{ old('factory_address', $company->factory_address) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">رابط خرائط جوجل للمقر (Location URL)</label>
                    <div class="input-with-icon">
                        <i class="fas fa-map-marker-alt" style="color:#EF4444;"></i>
                        <input type="url" name="location_url" class="form-control" value="{{ old('location_url', $company->location_url) }}" dir="ltr" placeholder="https://maps.google.com/...">
                    </div>
                </div>

                <div class="form-group" style="margin-top:24px;">
                    <label class="form-label">أوقات وساعات العمل</label>
                    <input type="text" name="company_working_hours" class="form-control" value="{{ old('company_working_hours', $company->company_working_hours) }}" placeholder="مثال: من الأحد إلى الخميس، 9 صباحاً - 5 مساءً">
                </div>

            </div>
        </div>

    </div>

    {{-- Bottom Actions --}}
    <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
        <a href="{{ route('company.dashboard') }}" class="btn btn-secondary">إلغاء</a>
        <button type="submit" class="btn btn-primary" style="padding:10px 30px;"><i class="fas fa-save"></i> حفظ التحديثات</button>
    </div>

</form>

@endsection

@push('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            preview.src = e.target.result;
            preview.style.display = 'block';
            
            const placeholder = document.getElementById('logoPlaceholder');
            if(placeholder) placeholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
