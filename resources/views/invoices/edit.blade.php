@extends('layouts.master')

@section('title', 'تعديل الفاتورة')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">تعديل الفاتورة #{{ $invoice->number }}</h2>
            <p class="text-muted">تحديث بيانات الفاتورة</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary rounded-xl">
            <i class="bi bi-arrow-right me-2"></i>رجوع للقائمة
        </a>
    </div>

    <form method="POST" action="{{ route('invoices.update', $invoice->id) }}" id="editInvoiceForm">
        @csrf
        @method('PUT')

        <!-- Client Information -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="fas fa-user me-2"></i>
                معلومات العميل
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">اختر العميل <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" class="form-control" id="clientSearchInput" 
                                   value="{{ $invoice->client->name }}" placeholder="ابحث عن عميل..." autocomplete="off">
                            <input type="hidden" name="client_id" id="selectedClientId" value="{{ $invoice->client_id }}" required>
                            <div id="clientDropdown" class="list-group position-absolute w-100 shadow" 
                                 style="display:none; z-index: 1000; max-height: 200px; overflow-y: auto;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control bg-light" id="clientEmail" 
                               value="{{ $invoice->client->email }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الهاتف</label>
                        <input type="text" class="form-control bg-light" id="clientPhone" 
                               value="{{ $invoice->client->phone }}" readonly>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label class="form-label">العنوان</label>
                        <textarea class="form-control bg-light" id="clientAddress" rows="2" readonly>{{ $invoice->client->address }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Information -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="fas fa-file-invoice me-2"></i>
                معلومات الفاتورة
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">رقم الفاتورة <span class="text-danger">*</span></label>
                        <input type="text" name="number" class="form-control" value="{{ $invoice->number }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">تاريخ الإصدار <span class="text-danger">*</span></label>
                        <input type="date" name="generation_date" class="form-control" 
                               value="{{ $invoice->generation_date }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">تاريخ الاستحقاق <span class="text-danger">*</span></label>
                        <input type="date" name="last_generation_date" class="form-control" 
                               value="{{ $invoice->last_generation_date }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نوع الخدمة <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" class="form-control" id="serviceSearchInput" 
                                   value="{{ $invoice->service->name ?? '' }}" placeholder="ابحث عن خدمة..." autocomplete="off">
                            <input type="hidden" name="service_id" id="selectedServiceId" value="{{ $invoice->service_id }}" required>
                            <div id="serviceDropdown" class="list-group position-absolute w-100 shadow" 
                                 style="display:none; z-index: 1000; max-height: 200px; overflow-y: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workforce Details -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="fas fa-users me-2"></i>
                تفاصيل العمالة
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">عدد العمال</label>
                        <input type="number" name="total_workers" id="total_workers" class="form-control worker-count" 
                               min="0" value="{{ $invoice->total_workers ?? 0 }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">أيام عمل العمال</label>
                        <input type="number" name="workers_days" id="workers_days" class="form-control work-days-input" 
                               min="0" value="{{ $invoice->workers_days ?? 0 }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">عدد المشرفين</label>
                        <input type="number" name="total_supervisors" id="total_supervisors" class="form-control worker-count" 
                               min="0" value="{{ $invoice->total_supervisors ?? 0 }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">أيام عمل المشرفين</label>
                        <input type="number" name="supervisors_days" id="supervisors_days" class="form-control work-days-input" 
                               min="0" value="{{ $invoice->supervisors_days ?? 0 }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">عدد المدراء</label>
                        <input type="number" name="total_managers" id="total_managers" class="form-control worker-count" 
                               min="0" value="{{ $invoice->total_managers ?? 0 }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">أيام عمل المدراء</label>
                        <input type="number" name="managers_days" id="managers_days" class="form-control work-days-input" 
                               min="0" value="{{ $invoice->managers_days ?? 0 }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">عدد المستخدمين</label>
                        <input type="number" name="total_users" id="total_users" class="form-control worker-count" 
                               min="0" value="{{ $invoice->total_users ?? 0 }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">أيام عمل المستخدمين</label>
                        <input type="number" name="users_days" id="users_days" class="form-control work-days-input" 
                               min="0" value="{{ $invoice->users_days ?? 0 }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">إجمالي العمالة</label>
                        <input type="text" id="total_workforce_display" class="form-control bg-light fw-bold" 
                               value="{{ ($invoice->total_workers ?? 0) + ($invoice->total_supervisors ?? 0) + ($invoice->total_managers ?? 0) + ($invoice->total_users ?? 0) }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">إجمالي أيام العمل</label>
                        <input type="text" id="total_work_days_display" class="form-control bg-light fw-bold" 
                               value="{{ (($invoice->total_workers ?? 0) * ($invoice->workers_days ?? 0)) + (($invoice->total_supervisors ?? 0) * ($invoice->supervisors_days ?? 0)) + (($invoice->total_managers ?? 0) * ($invoice->managers_days ?? 0)) + (($invoice->total_users ?? 0) * ($invoice->users_days ?? 0)) }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Details -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="fas fa-calculator me-2"></i>
                التفاصيل المالية
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">الأجر اليومي (ر.س) <span class="text-danger">*</span></label>
                        <input type="number" name="daily_rate" id="daily_rate" class="form-control" 
                               min="0" step="0.01" value="{{ $invoice->daily_rate }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">نسبة الضريبة (%) <span class="text-danger">*</span></label>
                        <input type="number" name="tax_rate" id="tax_rate" class="form-control" 
                               min="0" max="100" step="0.1" value="{{ $invoice->tax_rate }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">فرق المبلغ (ر.س)</label>
                        <input type="number" name="amount_difference" id="amount_difference" class="form-control" 
                               step="0.01" value="{{ $invoice->amount_difference ?? 0 }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">المبلغ قبل الضريبة (ر.س)</label>
                        <input type="text" id="subtotal_display" class="form-control bg-light fw-bold" 
                               value="{{ number_format($invoice->base_price, 0) }}" readonly>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">قيمة الضريبة (ر.س)</label>
                        <input type="text" id="tax_amount_display" class="form-control bg-light fw-bold" 
                               value="{{ number_format($invoice->tax_amount, 0) }}" readonly>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">المبلغ الإجمالي (ر.س)</label>
                        <input type="text" id="total_amount_display" class="form-control bg-light fw-bold text-success" 
                               value="{{ number_format($invoice->total_price, 0) }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment & Status -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="fas fa-credit-card me-2"></i>
                حالة السداد
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">حالة السداد <span class="text-danger">*</span></label>
                        <select name="payment_status" class="form-select" required>
                            <option value="pending" {{ $invoice->payment_status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="paid" {{ $invoice->payment_status == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                            <option value="overdue" {{ $invoice->payment_status == 'overdue' ? 'selected' : '' }}>متأخرة</option>
                            <option value="late" {{ $invoice->payment_status == 'late' ? 'selected' : '' }}>متأخرة (متابعة)</option>
                            <option value="cancelled" {{ $invoice->payment_status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاريخ السداد</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ $invoice->payment_date }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">حالة الفاتورة <span class="text-danger">*</span></label>
                        <select name="invoice_status" id="invoice_status" class="form-select" required onchange="toggleCustomStatus()">
                            <option value="">اختر حالة الفاتورة</option>
                            <option value="رواتب" {{ $invoice->invoice_status == 'رواتب' ? 'selected' : '' }}>رواتب</option>
                            <option value="عمولات" {{ $invoice->invoice_status == 'عمولات' ? 'selected' : '' }}>عمولات</option>
                            <option value="عمل اضافي" {{ $invoice->invoice_status == 'عمل اضافي' ? 'selected' : '' }}>عمل اضافي</option>
                            <option value="رواتب-احتضان قانوني" {{ $invoice->invoice_status == 'رواتب-احتضان قانوني' ? 'selected' : '' }}>رواتب-احتضان قانوني</option>
                            <option value="مصاريف قانونية- احتضان قانوني" {{ $invoice->invoice_status == 'مصاريف قانونية- احتضان قانوني' ? 'selected' : '' }}>مصاريف قانونية- احتضان قانوني</option>
                            <option value="يوزرات" {{ $invoice->invoice_status == 'يوزرات' ? 'selected' : '' }}>يوزرات</option>
                            <option value="ملغية" {{ $invoice->invoice_status == 'ملغية' ? 'selected' : '' }}>ملغية</option>
                            <option value="ملغية -احتضان قانوني" {{ $invoice->invoice_status == 'ملغية -احتضان قانوني' ? 'selected' : '' }}>ملغية -احتضان قانوني</option>
                            <option value="بروموتر" {{ $invoice->invoice_status == 'بروموتر' ? 'selected' : '' }}>بروموتر</option>
                            <option value="زيارة مستقلة" {{ $invoice->invoice_status == 'زيارة مستقلة' ? 'selected' : '' }}>زيارة مستقلة</option>
                            <option value="other">أخرى (أضف حالة جديدة)</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3" id="custom_status_container" style="display: none;">
                        <label class="form-label">الحالة المخصصة <span class="text-danger">*</span></label>
                        <input type="text" name="custom_status" class="form-control" placeholder="أدخل الحالة الجديدة للفاتورة">
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="fas fa-sticky-note me-2"></i>
                ملاحظات إضافية
            </div>
            <div class="card-body">
                <textarea name="notes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية حول الفاتورة...">{{ $invoice->notes }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3">
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary rounded-xl">إلغاء</a>
            <button type="submit" class="btn btn-primary rounded-xl">
                <i class="fas fa-save me-2"></i>حفظ التعديلات
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const clients = @json($clients);
    const services = @json($services);

    // Client search functionality
    const clientInput = document.getElementById('clientSearchInput');
    const selectedClientId = document.getElementById('selectedClientId');
    const clientDropdown = document.getElementById('clientDropdown');

    clientInput.addEventListener('input', function() {
        const search = this.value.toLowerCase();
        clientDropdown.innerHTML = '';

        if (search.length < 1) {
            clientDropdown.style.display = 'none';
            return;
        }

        const filtered = clients.filter(c => 
            c.name.toLowerCase().includes(search) || 
            (c.phone && c.phone.includes(search))
        );

        if (filtered.length > 0) {
            filtered.forEach(c => {
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'list-group-item list-group-item-action';
                item.textContent = c.name + (c.phone ? ' - ' + c.phone : '');
                item.onclick = (e) => {
                    e.preventDefault();
                    selectClient(c);
                };
                clientDropdown.appendChild(item);
            });
        }

        clientDropdown.style.display = 'block';
    });

    function selectClient(client) {
        clientInput.value = client.name;
        selectedClientId.value = client.id;
        document.getElementById('clientEmail').value = client.email || '';
        document.getElementById('clientPhone').value = client.phone || '';
        document.getElementById('clientAddress').value = client.address || '';
        clientDropdown.style.display = 'none';
    }

    // Service search functionality
    const serviceInput = document.getElementById('serviceSearchInput');
    const selectedServiceId = document.getElementById('selectedServiceId');
    const serviceDropdown = document.getElementById('serviceDropdown');

    serviceInput.addEventListener('input', function() {
        const search = this.value.toLowerCase();
        serviceDropdown.innerHTML = '';

        if (search.length < 1) {
            serviceDropdown.style.display = 'none';
            return;
        }

        const filtered = services.filter(s => s.name.toLowerCase().includes(search));

        if (filtered.length > 0) {
            filtered.forEach(s => {
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'list-group-item list-group-item-action';
                item.textContent = s.name;
                item.onclick = (e) => {
                    e.preventDefault();
                    selectService(s);
                };
                serviceDropdown.appendChild(item);
            });
        }

        serviceDropdown.style.display = 'block';
    });

    function selectService(service) {
        serviceInput.value = service.name;
        selectedServiceId.value = service.id;
        serviceDropdown.style.display = 'none';
    }

    // Workforce calculation
    const workersInput = document.getElementById('total_workers');
    const supervisorsInput = document.getElementById('total_supervisors');
    const managersInput = document.getElementById('total_managers');
    const usersInput = document.getElementById('total_users');
    const workforceDisplay = document.getElementById('total_workforce_display');

    function calculateTotalWorkforce() {
        const workers = parseInt(workersInput.value) || 0;
        const supervisors = parseInt(supervisorsInput.value) || 0;
        const managers = parseInt(managersInput.value) || 0;
        const users = parseInt(usersInput.value) || 0;

        const total = workers + supervisors + managers + users;
        workforceDisplay.value = total;

        const workersDays = parseInt(document.getElementById('workers_days')?.value) || 0;
        const supervisorsDays = parseInt(document.getElementById('supervisors_days')?.value) || 0;
        const managersDays = parseInt(document.getElementById('managers_days')?.value) || 0;
        const usersDays = parseInt(document.getElementById('users_days')?.value) || 0;

        const totalWorkDays = (workers * workersDays) + (supervisors * supervisorsDays) + 
                              (managers * managersDays) + (users * usersDays);
        
        const totalWorkDaysDisplay = document.getElementById('total_work_days_display');
        if (totalWorkDaysDisplay) {
            totalWorkDaysDisplay.value = totalWorkDays;
        }

        calculateFinancials();
    }

    // Financial calculation
    const dailyRateInput = document.getElementById('daily_rate');
    const taxRateInput = document.getElementById('tax_rate');
    const amountDiffInput = document.getElementById('amount_difference');

    const subtotalDisplay = document.getElementById('subtotal_display');
    const taxDisplay = document.getElementById('tax_amount_display');
    const totalDisplay = document.getElementById('total_amount_display');

    function calculateFinancials() {
        const workers = parseInt(workersInput.value) || 0;
        const supervisors = parseInt(supervisorsInput.value) || 0;
        const managers = parseInt(managersInput.value) || 0;
        const users = parseInt(usersInput.value) || 0;

        const workersDays = parseInt(document.getElementById('workers_days')?.value) || 0;
        const supervisorsDays = parseInt(document.getElementById('supervisors_days')?.value) || 0;
        const managersDays = parseInt(document.getElementById('managers_days')?.value) || 0;
        const usersDays = parseInt(document.getElementById('users_days')?.value) || 0;

        const totalManDays = (workers * workersDays) + (supervisors * supervisorsDays) + 
                             (managers * managersDays) + (users * usersDays);

        const dailyRate = parseFloat(dailyRateInput.value) || 0;
        const taxRate = parseFloat(taxRateInput.value) || 0;
        const amountDiff = parseFloat(amountDiffInput ? amountDiffInput.value : 0) || 0;

        const subtotal = totalManDays * dailyRate;
        const taxAmount = (subtotal * taxRate) / 100;
        const total = subtotal + taxAmount + amountDiff;

        subtotalDisplay.value = subtotal.toFixed(0);
        taxDisplay.value = taxAmount.toFixed(0);
        totalDisplay.value = total.toFixed(0);
    }

    // Event listeners
    [workersInput, supervisorsInput, managersInput, usersInput].forEach(input => {
        input.addEventListener('input', calculateTotalWorkforce);
    });

    const workDaysInputs = document.querySelectorAll('.work-days-input');
    workDaysInputs.forEach(input => {
        input.addEventListener('input', calculateTotalWorkforce);
    });

    [dailyRateInput, taxRateInput].forEach(input => {
        input.addEventListener('input', calculateFinancials);
    });
    if(amountDiffInput) amountDiffInput.addEventListener('input', calculateFinancials);

    // Custom Status Toggle
    function toggleCustomStatus() {
        const select = document.getElementById('invoice_status');
        const container = document.getElementById('custom_status_container');
        if (select.value === 'other') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    // Initialize
    calculateTotalWorkforce();
    calculateFinancials();
    toggleCustomStatus();
</script>
@endpush
