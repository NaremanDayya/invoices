@extends('layouts.master')

@section('title', 'إضافة فاتورة جديدة')

@section('content')
    <div class="container-fluid px-4">
        <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm" class="space-y-6">
            @csrf

            <!-- Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                <div class="mb-4 lg:mb-0">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-plus-circle text-emerald-500 mr-2"></i>
                        إضافة فاتورة جديدة
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">املأ المعلومات أدناه لإنشاء فاتورة جديدة</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('invoices.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-right ml-2"></i>
                        رجوع للقائمة
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save ml-2"></i>
                        حفظ الفاتورة
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Client Information -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user text-emerald-500 ml-2"></i>
                        معلومات العميل
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">اختر العميل <span class="text-red-500">*</span></label>
                            <select name="client_id" class="form-select" required id="clientSelect">
                                <option value="">-- اختر العميل --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}"
                                            data-email="{{ $client->email }}"
                                            data-phone="{{ $client->phone }}"
                                            data-address="{{ $client->address }}">
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn-outline mt-3" data-bs-toggle="modal" data-bs-target="#addClientModal">
                                <i class="fas fa-plus ml-1"></i>إضافة عميل جديد
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-input bg-gray-50" id="clientEmail" readonly>
                            </div>
                            <div>
                                <label class="form-label">الهاتف</label>
                                <input type="text" class="form-input bg-gray-50" id="clientPhone" readonly>
                            </div>
                            <div class="md:col-span-2">
                                <label class="form-label">العنوان</label>
                                <textarea class="form-input bg-gray-50" id="clientAddress" rows="2" readonly></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Information -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-invoice text-emerald-500 ml-2"></i>
                        معلومات الفاتورة
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">رقم الفاتورة <span class="text-red-500">*</span></label>
                                <input type="text" name="number" class="form-input" value="{{ $invoiceNumber }}" required>
                            </div>
                            <div>
                                <label class="form-label">تاريخ الإصدار <span class="text-red-500">*</span></label>
                                <input type="date" name="generation_date" class="form-input" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="form-label">تاريخ الاستحقاق <span class="text-red-500">*</span></label>
                                <input type="date" name="last_generation_date" class="form-input" value="{{ now()->addDays(30)->format('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="form-label">نوع الخدمة <span class="text-red-500">*</span></label>
                                <select name="service_id" class="form-select" required id="serviceSelect">
                                    <option value="">-- اختر الخدمة --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn-outline mt-3" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                                    <i class="fas fa-plus ml-1"></i>إضافة خدمة جديدة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workforce Details -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users text-emerald-500 ml-2"></i>
                    تفاصيل العمالة
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label class="form-label">عدد العمال <span class="text-red-500">*</span></label>
                            <input type="number" name="total_workers" id="total_workers" class="form-input" min="0" value="0" required>
                        </div>
                        <div>
                            <label class="form-label">عدد المشرفين <span class="text-red-500">*</span></label>
                            <input type="number" name="total_supervisors" id="total_supervisors" class="form-input" min="0" value="0" required>
                        </div>
                        <div>
                            <label class="form-label">عدد المدراء <span class="text-red-500">*</span></label>
                            <input type="number" name="total_managers" id="total_managers" class="form-input" min="0" value="0" required>
                        </div>
                        <div>
                            <label class="form-label">عدد المستخدمين <span class="text-red-500">*</span></label>
                            <input type="number" name="total_users" id="total_users" class="form-input" min="0" value="0" required>
                        </div>
                    </div>

                    <!-- Total Workers Display -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 max-w-xs">
                        <label class="form-label text-blue-700 dark:text-blue-300 font-semibold">إجمالي العمالة</label>
                        <input type="text" id="total_workforce_display" class="form-input bg-white dark:bg-gray-800 text-blue-700 dark:text-blue-300 font-bold text-lg" value="0" readonly>
                    </div>
                </div>
            </div>

            <!-- Financial Details -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-calculator text-emerald-500 ml-2"></i>
                    التفاصيل المالية
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="form-label">أيام العمل <span class="text-red-500">*</span></label>
                            <input type="number" name="work_days" id="work_days" class="form-input" min="1" value="1" required>
                        </div>
                        <div>
                            <label class="form-label">الأجر اليومي (﷼) <span class="text-red-500">*</span></label>
                            <input type="number" name="daily_rate" id="daily_rate" class="form-input" min="0" step="0.01" value="0" required>
                        </div>
                        <div>
                            <label class="form-label">نسبة الضريبة (%) <span class="text-red-500">*</span></label>
                            <input type="number" name="tax_rate" id="tax_rate" class="form-input" min="0" max="100" step="0.1" value="15" required>
                        </div>
                    </div>

                    <!-- Financial Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <label class="form-label text-blue-700 dark:text-blue-300">المبلغ قبل الضريبة (﷼)</label>
                            <input type="text" id="subtotal_display" class="form-input bg-white dark:bg-gray-800 text-blue-700 dark:text-blue-300 font-semibold" value="0.00" readonly>
                        </div>
                        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                            <label class="form-label text-orange-700 dark:text-orange-300">قيمة الضريبة (﷼)</label>
                            <input type="text" id="tax_amount_display" class="form-input bg-white dark:bg-gray-800 text-orange-700 dark:text-orange-300 font-semibold" value="0.00" readonly>
                        </div>
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4">
                            <label class="form-label text-emerald-700 dark:text-emerald-300">المبلغ الإجمالي (﷼)</label>
                            <input type="text" id="total_amount_display" class="form-input bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 font-semibold" value="0.00" readonly>
                        </div>
                    </div>

{{--                    <div class="md:w-1/3">--}}
{{--                        <label class="form-label">فرق المبلغ (﷼)</label>--}}
{{--                        <input type="number" name="amount_difference" id="amount_difference" class="form-input" step="0.01" value="0">--}}
{{--                    </div>--}}
                </div>
            </div>

            <!-- Payment & Status -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-credit-card text-emerald-500 ml-2"></i>
                    حالة السداد
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">حالة السداد <span class="text-red-500">*</span></label>
                            <select name="payment_status" class="form-select" required>
                                <option value="pending">قيد الانتظار</option>
                                <option value="paid">مدفوعة</option>
                                <option value="overdue">متأخرة</option>
                                <option value="late">متأخرة (متابعة)</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">تاريخ السداد</label>
                            <input type="date" name="payment_date" class="form-input">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">حالة الفاتورة <span class="text-red-500">*</span></label>
                            <select name="invoice_status" id="invoice_status" class="form-select" required onchange="toggleCustomStatus()">
                                <option value="">اختر حالة الفاتورة</option>
                                <option value="رواتب">رواتب</option>
                                <option value="عمولات">عمولات</option>
                                <option value="عمل اضافي">عمل اضافي</option>
                                <option value="رواتب-احتضان قانوني">رواتب-احتضان قانوني</option>
                                <option value="مصاريف قانونية- احتضان قانوني">مصاريف قانونية- احتضان قانوني</option>
                                <option value="يوزرات">يوزرات</option>
                                <option value="ملغية">ملغية</option>
                                <option value="ملغية -احتضان قانوني">ملغية -احتضان قانوني</option>
                                <option value="بروموتر">بروموتر</option>
                                <option value="زيارة مستقلة">زيارة مستقلة</option>
                                <option value="other">أخرى (أضف حالة جديدة)</option>
                            </select>
                            @error('invoice_status')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="custom_status_container" style="display: none;">
                            <label class="form-label">الحالة المخصصة <span class="text-red-500">*</span></label>
                            <input type="text"
                                   name="custom_status"
                                   class="form-control @error('custom_status') is-invalid @enderror"
                                   placeholder="أدخل الحالة الجديدة للفاتورة">
                            @error('custom_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-sticky-note text-emerald-500 ml-2"></i>
                    ملاحظات إضافية
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-input" rows="3" placeholder="أي ملاحظات إضافية حول الفاتورة..."></textarea>
                </div>
            </div>
        </form>
    </div>
    <!-- Add Client Modal -->
    <div class="modal fade" id="addClientModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة عميل جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addClientForm">
                    <div class="modal-body space-y-4">
                        <div>
                            <label class="form-label">اسم العميل <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="form-input" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">الهاتف</label>
                                <input type="text" name="phone" class="form-input">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">العنوان</label>
                            <textarea name="address" class="form-input" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn-primary">حفظ العميل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة خدمة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addServiceForm">
                    <div class="modal-body space-y-4">
                        <div>
                            <label class="form-label">اسم الخدمة <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-input" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn-primary">حفظ الخدمة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function toggleCustomStatus() {
                const invoiceStatus = document.getElementById('invoice_status');
                const customStatusContainer = document.getElementById('custom_status_container');

                if (invoiceStatus.value === 'other') {
                    customStatusContainer.style.display = 'block';
                } else {
                    customStatusContainer.style.display = 'none';
                }
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                toggleCustomStatus();
            });
        </script>

        <script>
            // Update client info when client is selected
            document.getElementById('clientSelect').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                document.getElementById('clientEmail').value = selectedOption.getAttribute('data-email') || '';
                document.getElementById('clientPhone').value = selectedOption.getAttribute('data-phone') || '';
                document.getElementById('clientAddress').value = selectedOption.getAttribute('data-address') || '';
            });

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

                // Trigger financial calculation
                calculateFinancials();
            }

            // Financial calculation
            const workDaysInput = document.getElementById('work_days');
            const dailyRateInput = document.getElementById('daily_rate');
            const taxRateInput = document.getElementById('tax_rate');
            const amountDiffInput = document.getElementById('amount_difference');

            const subtotalDisplay = document.getElementById('subtotal_display');
            const taxDisplay = document.getElementById('tax_amount_display');
            const totalDisplay = document.getElementById('total_amount_display');

            function calculateFinancials() {
                const totalWorkforce = parseInt(workforceDisplay.value) || 0;
                const workDays = parseInt(workDaysInput.value) || 0;
                const dailyRate = parseFloat(dailyRateInput.value) || 0;
                const taxRate = parseFloat(taxRateInput.value) || 0;
                const amountDiff = parseFloat(amountDiffInput.value) || 0;

                const subtotal = totalWorkforce * workDays * dailyRate;
                const taxAmount = (subtotal * taxRate) / 100;
                const total = subtotal + taxAmount + amountDiff;

                subtotalDisplay.value = subtotal.toFixed(2);
                taxDisplay.value = taxAmount.toFixed(2);
                totalDisplay.value = total.toFixed(2);
            }

            // Event listeners for workforce inputs
            [workersInput, supervisorsInput, managersInput, usersInput].forEach(input => {
                input.addEventListener('input', calculateTotalWorkforce);
            });

            // Event listeners for financial inputs
            [workDaysInput, dailyRateInput, taxRateInput, amountDiffInput].forEach(input => {
                input.addEventListener('input', calculateFinancials);
            });

            // Initialize calculations
            calculateTotalWorkforce();
            calculateFinancials();

            // Inline create: Clients
            const addClientForm = document.getElementById('addClientForm');
            addClientForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(addClientForm);
                try {
                    const resp = await fetch("{{ route('invoices.add-client') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });
                    const data = await resp.json();
                    if (data.success) {
                        // append to select and select it
                        const clientSelect = document.getElementById('clientSelect');
                        const opt = document.createElement('option');
                        opt.value = data.client.id;
                        opt.textContent = data.client.name;
                        opt.setAttribute('data-email', data.client.email || '');
                        opt.setAttribute('data-phone', data.client.phone || '');
                        opt.setAttribute('data-address', data.client.address || '');
                        clientSelect.appendChild(opt);
                        clientSelect.value = data.client.id;
                        clientSelect.dispatchEvent(new Event('change'));

                        // reset form
                        addClientForm.reset();

                        // close modal
                        const modalEl = document.getElementById('addClientModal');
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.hide();

                        // notify
                        if (window.toastr) toastr.success(data.message || 'تم إضافة العميل بنجاح');
                    } else {
                        if (window.toastr) toastr.error(data.message || 'تعذر إضافة العميل');
                    }
                } catch (err) {
                    if (window.toastr) toastr.error('حدث خطأ أثناء إضافة العميل');
                    console.error(err);
                }
            });

            // Inline create: Services
            const addServiceForm = document.getElementById('addServiceForm');
            addServiceForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(addServiceForm);
                try {
                    const resp = await fetch("{{ route('invoices.add-service') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });
                    const data = await resp.json();
                    if (data.success) {
                        // append to select and select it
                        const serviceSelect = document.getElementById('serviceSelect');
                        const opt = document.createElement('option');
                        opt.value = data.service.id;
                        opt.textContent = data.service.name;
                        serviceSelect.appendChild(opt);
                        serviceSelect.value = data.service.id;

                        // reset form
                        addServiceForm.reset();

                        // close modal
                        const modalEl = document.getElementById('addServiceModal');
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.hide();

                        // notify
                        if (window.toastr) toastr.success(data.message || 'تم إضافة الخدمة بنجاح');
                    } else {
                        if (window.toastr) toastr.error(data.message || 'تعذر إضافة الخدمة');
                    }
                } catch (err) {
                    if (window.toastr) toastr.error('حدث خطأ أثناء إضافة الخدمة');
                    console.error(err);
                }
            });
        </script>
    @endpush

    <style>
        .card {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700;
        }

        .card-header {
            @apply bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-6 py-4 rounded-t-xl border-b border-emerald-400;
        }

        .card-body {
            @apply p-6;
        }

        .form-label {
            @apply block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2;
        }

        .form-input {
            @apply w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-white transition-all duration-200 shadow-sm;
        }

        .form-select {
            @apply w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-white transition-all duration-200 shadow-sm;
        }

        .btn-primary {
            @apply bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 shadow-lg;
        }

        .btn-secondary {
            @apply bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-lg;
        }

        .btn-outline {
            @apply border-2 border-emerald-500 text-emerald-500 hover:bg-emerald-500 hover:text-white px-4 py-2 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2;
        }

        .modal-header {
            @apply bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-t-xl;
        }
    </style>
@endsection
