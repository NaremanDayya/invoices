@extends('layouts.master')

@section('title', 'الإعدادات')
@section('page_title', 'إعدادات النظام')
@section('page_subtitle', 'إدارة إعدادات النظام العامة')

@push('styles')
<style>
    .settings-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        border: 1px solid #edf2f7;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .settings-card h5 {
        font-weight: 700;
        margin-bottom: 20px;
        color: #2d3748;
        padding-bottom: 12px;
        border-bottom: 2px solid #edf2f7;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-control:focus {
        outline: none;
        border-color: #10a37f;
        box-shadow: 0 0 0 3px rgba(16, 163, 127, 0.1);
    }
    .form-text {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #718096;
    }
    .btn-save {
        background: #10a37f;
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-save:hover {
        background: #0d8968;
    }
    .alert {
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        border-radius: 8px;
        font-weight: 500;
    }
    .alert-success {
        background: #def7ec;
        color: #03543f;
        border: 1px solid #84e1bc;
    }
    .alert-error {
        background: #fde8e8;
        color: #9b1c1c;
        border: 1px solid #f8b4b4;
    }
    .info-box {
        background: #ebf8ff;
        border: 1px solid #90cdf4;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1.5rem;
    }
    .info-box h6 {
        color: #2c5282;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .info-box ul {
        margin: 0;
        padding-right: 1.5rem;
        color: #2d3748;
    }
    .info-box li {
        margin-bottom: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="settings-card">
            <h5><i class="bi bi-clock-history me-2"></i>إعداد أيام التأخير المسموح بها في صرف الرواتب</h5>

            <form id="salaryDelayForm" method="POST" action="{{ route('settings.update-salary-delay') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="accepted_salary_delay_days" class="form-label">
                        أيام التأخير المسموح بها بعد نهاية الشهر
                    </label>
                    <input
                        type="number"
                        class="form-control @error('accepted_salary_delay_days') is-invalid @enderror"
                        id="accepted_salary_delay_days"
                        name="accepted_salary_delay_days"
                        value="{{ old('accepted_salary_delay_days', $acceptedSalaryDelayDays) }}"
                        min="0"
                        max="365"
                        step="1"
                        required>
                    <small class="form-text">
                        إذا كانت القيمة 3، يبدأ احتساب التأخير اعتباراً من اليوم الرابع من الشهر التالي وتستمر الزيادة حتى يُصرف الراتب كاملاً.
                    </small>
                    @error('accepted_salary_delay_days')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-save me-2"></i>حفظ التغييرات
                    </button>
                    <span id="delaySaveStatus" class="text-muted"></span>
                </div>
            </form>

            <div class="info-box mt-4">
                <h6><i class="bi bi-info-circle me-2"></i>كيفية حساب تأخير الرواتب</h6>
                <ul>
                    <li>في نهاية كل شهر تُحسب رواتب الموظفين ويبدأ العد</li>
                    <li>يُسمح بعدد الأيام المحددة هنا قبل احتساب أي تأخير</li>
                    <li>مثال: إذا كانت القيمة 3، فإن يوم التأخير الأول يُحتسب في اليوم الرابع من الشهر التالي</li>
                    <li>يستمر عداد التأخير في الزيادة حتى يُصرف الراتب بالكامل</li>
                    <li>القيمة 0 تعني أن أي تأخير عن نهاية الشهر يُحتسب فوراً</li>
                </ul>
            </div>
        </div>

        <div class="settings-card">
            <h5><i class="bi bi-shield-check me-2"></i>إعدادات نظام حماية الأجور (WPS)</h5>
            
            <form id="wpsSettingsForm" method="POST" action="{{ route('settings.update-wps') }}">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="wps_max_percentage" class="form-label">
                        الحد الأقصى لنسبة WPS (%)
                    </label>
                    <input 
                        type="number" 
                        class="form-control @error('wps_max_percentage') is-invalid @enderror" 
                        id="wps_max_percentage" 
                        name="wps_max_percentage" 
                        value="{{ old('wps_max_percentage', $wpsMaxPercentage) }}"
                        min="0"
                        max="100"
                        step="0.01"
                        required>
                    <small class="form-text">
                        هذه النسبة تحدد الحد الأقصى المسموح به عند دفع الرواتب عبر نظام حماية الأجور (WPS)
                    </small>
                    @error('wps_max_percentage')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-save me-2"></i>حفظ التغييرات
                    </button>
                    <span id="saveStatus" class="text-muted"></span>
                </div>
            </form>

            <div class="info-box">
                <h6><i class="bi bi-info-circle me-2"></i>معلومات حول نظام حماية الأجور (WPS)</h6>
                <ul>
                    <li>نظام حماية الأجور هو نظام إلكتروني لمراقبة دفع الرواتب في الوقت المحدد</li>
                    <li>يتم تحديد نسبة مئوية من الراتب الصافي للموظف لتحويلها عبر WPS</li>
                    <li>النسبة المحددة هنا هي الحد الأقصى المسموح به في النظام</li>
                    <li>يمكن للموظفين الحصول على نسبة أقل، ولكن لا يمكن تجاوز هذا الحد</li>
                    <li>القيمة الافتراضية الموصى بها: 70%</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="settings-card">
            <h5><i class="bi bi-graph-up me-2"></i>إحصائيات الاستخدام</h5>
            <div class="mb-3">
                <p class="text-muted mb-1">النسبة الحالية</p>
                <p class="h3 text-success mb-0">{{ $wpsMaxPercentage }}%</p>
            </div>
            <hr>
            <div class="mb-3">
                <p class="text-muted mb-1">آخر تحديث</p>
                <p class="mb-0">{{ \App\Models\Setting::where('key', 'wps_max_percentage')->first()?->updated_at?->diffForHumans() ?? 'لم يتم التحديث بعد' }}</p>
            </div>
        </div>

        <div class="settings-card">
            <h5><i class="bi bi-lightbulb me-2"></i>نصائح</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <i class="bi bi-check-circle text-success me-2"></i>
                    قم بمراجعة النسبة بشكل دوري
                </li>
                <li class="mb-2">
                    <i class="bi bi-check-circle text-success me-2"></i>
                    تأكد من توافق النسبة مع اللوائح المحلية
                </li>
                <li class="mb-2">
                    <i class="bi bi-check-circle text-success me-2"></i>
                    أبلغ الموظفين بأي تغييرات في النسبة
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('salaryDelayForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    const saveStatus = document.getElementById('delaySaveStatus');
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>جاري الحفظ...';
    saveStatus.textContent = '';
    try {
        const response = await fetch(form.action, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ accepted_salary_delay_days: formData.get('accepted_salary_delay_days') })
        });
        const data = await response.json();
        if (data.success) {
            saveStatus.innerHTML = '<i class="bi bi-check-circle text-success me-1"></i><span class="text-success">تم الحفظ بنجاح</span>';
        } else {
            saveStatus.innerHTML = '<i class="bi bi-x-circle text-danger me-1"></i><span class="text-danger">فشل الحفظ</span>';
            alert(data.message || 'حدث خطأ');
        }
    } catch (error) {
        saveStatus.innerHTML = '<i class="bi bi-x-circle text-danger me-1"></i><span class="text-danger">خطأ في الاتصال</span>';
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>حفظ التغييرات';
    }
});

document.getElementById('wpsSettingsForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const saveStatus = document.getElementById('saveStatus');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>جاري الحفظ...';
    saveStatus.textContent = '';
    
    try {
        const response = await fetch(form.action, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                wps_max_percentage: formData.get('wps_max_percentage')
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            saveStatus.innerHTML = '<i class="bi bi-check-circle text-success me-1"></i><span class="text-success">تم الحفظ بنجاح</span>';
            
            // Show success alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success';
            alertDiv.innerHTML = '<i class="bi bi-check-circle me-2"></i>' + data.message;
            form.parentElement.insertBefore(alertDiv, form);
            
            setTimeout(() => alertDiv.remove(), 5000);
        } else {
            saveStatus.innerHTML = '<i class="bi bi-x-circle text-danger me-1"></i><span class="text-danger">فشل الحفظ</span>';
            alert(data.message || 'حدث خطأ أثناء الحفظ');
        }
    } catch (error) {
        console.error('Error saving settings:', error);
        saveStatus.innerHTML = '<i class="bi bi-x-circle text-danger me-1"></i><span class="text-danger">خطأ في الاتصال</span>';
        alert('حدث خطأ أثناء الحفظ: ' + error.message);
    } finally {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>حفظ التغييرات';
    }
});
</script>
@endpush
