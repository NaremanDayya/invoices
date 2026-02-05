@extends('layouts.master')

@section('title', 'إضافة فاتورة جديدة')

@section('content')
    <div class="container-fluid px-4 py-6">
        <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm" class="space-y-8">
            @csrf

            <!-- Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                <div class="mb-4 lg:mb-0">
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-500">
                            <i class="fas fa-plus-circle mr-2 text-emerald-600"></i>
                            إضافة فاتورة جديدة
                        </span>
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-2 text-lg">أدخل تفاصيل الفاتورة بدقة لضمان المعالجة الصحيحة</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('invoices.index') }}" class="btn-secondary flex items-center justify-center">
                        <i class="fas fa-arrow-right ml-2 group-hover:-translate-x-1 transition-transform"></i>
                        رجوع للقائمة
                    </a>
                    <button type="submit" class="btn-primary flex items-center justify-center shadow-emerald-500/20">
                        <i class="fas fa-save ml-2"></i>
                        حفظ الفاتورة
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="xl:col-span-2 space-y-8">
                    <!-- Client & Service Info -->
                    <div class="card overflow-hidden">
                        <div class="card-header bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 border-b border-slate-200 dark:border-slate-700 p-6">
                            <h3 class="flex items-center text-lg font-bold text-slate-800 dark:text-white">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 ml-3">
                                    <i class="fas fa-user-tag text-xl"></i>
                                </div>
                                المعلومات الأساسية
                            </h3>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="form-label">العميل <span class="text-red-500">*</span></label>
                                    <div class="flex gap-2">
                                        <select name="client_id" class="form-select flex-1" required id="clientSelect">
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
                                        <button type="button" class="btn-icon" data-bs-toggle="modal" data-bs-target="#addClientModal" title="إضافة عميل جديد">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <!-- Quick Info Client -->
                                    <div id="clientQuickInfo" class="hidden mt-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl text-sm text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-700">
                                        <div class="flex items-center mb-2"><i class="fas fa-phone w-6 text-emerald-500"></i> <span id="clientPhoneDisplay"></span></div>
                                        <div class="flex items-center"><i class="fas fa-map-marker-alt w-6 text-emerald-500"></i> <span id="clientAddressDisplay"></span></div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="form-label">الخدمة <span class="text-red-500">*</span></label>
                                    <div class="flex gap-2">
                                        <select name="service_id" class="form-select flex-1" required id="serviceSelect">
                                            <option value="">-- اختر الخدمة --</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn-icon" data-bs-toggle="modal" data-bs-target="#addServiceModal" title="إضافة خدمة جديدة">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Workforce Details -->
                    <div class="card overflow-hidden">
                        <div class="card-header bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 border-b border-slate-200 dark:border-slate-700 p-6">
                            <h3 class="flex items-center text-lg font-bold text-slate-800 dark:text-white">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 ml-3">
                                    <i class="fas fa-users-cog text-xl"></i>
                                </div>
                                تفاصيل العمالة وأيام العمل
                            </h3>
                        </div>
                        <div class="p-8">
                            <div class="overflow-x-auto">
                                <table class="w-full text-right">
                                    <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-700">
                                        <th class="py-3 px-4 font-bold text-slate-600 dark:text-slate-400 w-1/4">النوع</th>
                                        <th class="py-3 px-4 font-bold text-slate-600 dark:text-slate-400 w-1/4">العدد</th>
                                        <th class="py-3 px-4 font-bold text-slate-600 dark:text-slate-400 w-1/4">أيام العمل</th>
                                        <th class="py-3 px-4 font-bold text-slate-600 dark:text-slate-400 w-1/4">الإجمالي (مان-داي)</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @php
                                        $types = [
                                            ['key' => 'workers', 'label' => 'العمال', 'icon' => 'fa-hard-hat'],
                                            ['key' => 'supervisors', 'label' => 'المشرفين', 'icon' => 'fa-user-tie'],
                                            ['key' => 'managers', 'label' => 'المدراء', 'icon' => 'fa-briefcase'],
                                            ['key' => 'users', 'label' => 'المستخدمين', 'icon' => 'fa-users'],
                                        ];
                                    @endphp
                                    @foreach($types as $type)
                                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="py-4 px-4">
                                                <div class="flex items-center">
                                                    <i class="fas {{ $type['icon'] }} ml-3 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $type['label'] }}</span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <input type="number" name="total_{{ $type['key'] }}" class="form-input text-center workforce-count" data-type="{{ $type['key'] }}" min="0" value="0" required>
                                            </td>
                                            <td class="py-4 px-4">
                                                <input type="number" name="{{ $type['key'] }}_days" class="form-input text-center workforce-days" data-type="{{ $type['key'] }}" min="0" value="0" required>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="bg-slate-100 dark:bg-slate-700 rounded-lg py-2 px-3 text-center font-bold text-slate-700 dark:text-slate-300" id="total_{{ $type['key'] }}_mandays">0</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-slate-50 dark:bg-slate-800/80 font-bold">
                                        <td class="py-4 px-4 text-emerald-600">المجموع الكلي</td>
                                        <td class="py-4 px-4 text-center text-emerald-600 text-lg" id="grand_total_count">0</td>
                                        <td class="py-4 px-4 text-center text-slate-400">-</td>
                                        <td class="py-4 px-4 text-center text-emerald-600 text-lg" id="grand_total_mandays">0</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Financials & Dates) -->
                <div class="space-y-8">
                    <!-- Dates & Status -->
                    <div class="card">
                        <div class="card-header bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 border-b border-slate-200 dark:border-slate-700 p-6">
                            <h3 class="flex items-center text-lg font-bold text-slate-800 dark:text-white">
                                <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 ml-3">
                                    <i class="fas fa-calendar-alt text-xl"></i>
                                </div>
                                التواريخ والحالة
                            </h3>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="form-label">رقم الفاتورة <span class="text-red-500">*</span></label>
                                <input type="text" name="number" class="form-input bg-slate-50 font-mono text-left" value="{{ $invoiceNumber }}" required readonly>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label text-xs">تاريخ الإصدار</label>
                                    <input type="date" name="generation_date" class="form-input text-sm" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div>
                                    <label class="form-label text-xs">تاريخ الاستحقاق</label>
                                    <input type="date" name="last_generation_date" class="form-input text-sm" value="{{ now()->addDays(30)->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">حالة السداد</label>
                                <select name="payment_status" class="form-select status-select">
                                    <option value="pending" class="text-yellow-600">🕘 قيد الانتظار</option>
                                    <option value="paid" class="text-emerald-600">✅ مدفوعة</option>
                                    <option value="overdue" class="text-red-600">❌ متأخرة</option>
                                    <option value="late" class="text-orange-600">⚠️ متأخرة (متابعة)</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">حالة الفاتورة</label>
                                <select name="invoice_status" id="invoice_status" class="form-select" onchange="toggleCustomStatus()">
                                    <option value="">اختر الحالة...</option>
                                    <option value="رواتب">رواتب</option>
                                    <option value="عمولات">عمولات</option>
                                    <option value="عمل اضافي">عمل اضافي</option>
                                    <option value="رواتب-احتضان قانوني">رواتب-احتضان قانوني</option>
                                    <option value="يوزرات">يوزرات</option>
                                    <option value="مصاريف قانونية- احتضان قانوني">مصاريف قانونية- احتضان قانوني</option>
                                    <option value="mlghia">ملغية</option>
                                    <option value="other">أخرى...</option>
                                </select>
                            </div>
                            <div id="custom_status_container" class="hidden animate-fade-in-down">
                                <input type="text" name="custom_status" class="form-input" placeholder="أدخل الحالة الخاصة...">
                            </div>
                        </div>
                    </div>

                    <!-- Financial Calculations -->
                    <div class="card bg-gradient-to-br from-emerald-50 to-white dark:from-emerald-900/10 dark:to-slate-800 border-emerald-100 dark:border-emerald-800/30">
                        <div class="card-header bg-transparent border-b border-emerald-100 dark:border-emerald-800/30 p-6">
                            <h3 class="flex items-center text-lg font-bold text-emerald-800 dark:text-emerald-400">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 ml-3">
                                    <i class="fas fa-calculator text-xl"></i>
                                </div>
                                التكاليف المالية
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Hidden inputs for legacy/fallback if needed, but we rely on breakdown sum -->
                            <input type="hidden" name="work_days" id="total_work_days_hidden" value="0">

                            <div>
                                <label class="form-label">الأجر اليومي (لكل فرد)</label>
                                <div class="relative">
                                    <input type="number" name="daily_rate" id="daily_rate" class="form-input pl-10 text-lg font-bold text-emerald-700" min="0" step="0.01" value="0" required>
                                    <div class="absolute left-3 top-3.5 text-emerald-500 font-bold">﷼</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label text-xs">نسبة الضريبة (%)</label>
                                    <input type="number" name="tax_rate" id="tax_rate" class="form-input text-center" min="0" max="100" step="0.1" value="15" required>
                                </div>
                                <div>
                                    <label class="form-label text-xs">فرق مالي (+/-)</label>
                                    <input type="number" name="amount_difference" id="amount_difference" class="form-input text-center" step="0.01" value="0">
                                </div>
                            </div>

                            <div class="border-t border-emerald-100 dark:border-emerald-800/30 pt-4 space-y-3">
                                <div class="flex justify-between items-center text-sm text-slate-600 dark:text-slate-400">
                                    <span>المجموع قبل الضريبة</span>
                                    <span class="font-mono font-bold" id="subtotal_display">0.00</span>
                                </div>
                                <div class="flex justify-between items-center text-sm text-orange-600">
                                    <span>قيمة الضريبة</span>
                                    <span class="font-mono font-bold" id="tax_amount_display">0.00</span>
                                </div>
                                <div class="flex justify-between items-center text-xl font-black text-emerald-700 dark:text-emerald-400 pt-2 border-t border-dashed border-emerald-200">
                                    <span>الإجمالي النهائي</span>
                                    <span class="font-mono" id="total_amount_display">0.00 ﷼</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                         <div class="p-4">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-input" rows="3" placeholder="ملاحظات إضافية..."></textarea>
                         </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Add Client Modal -->
    <div class="modal fade" id="addClientModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
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
                            <label class="form-label">الرقم الضريبي <span class="text-danger">*</span></label>
                            <input type="text" name="tax_number" class="form-input" required maxlength="15" pattern="[0-9]{15}" placeholder="أدخل 15 رقم">
                            <small class="text-muted">يجب أن يكون 15 رقم بالضبط</small>
                        </div>
                        <div>
                            <label class="form-label">العنوان الوطني</label>
                            <textarea name="address" class="form-input" rows="2" placeholder="العنوان الوطني"></textarea>
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
        <div class="modal-dialog modal-dialog-centered">
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

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
             // Workforce Logic
             const types = ['workers', 'supervisors', 'managers', 'users'];
             
             const calculateRow = (type) => {
                 const countInput = document.querySelector(`input[name="total_${type}"]`);
                 const daysInput = document.querySelector(`input[name="${type}_days"]`);
                 const display = document.getElementById(`total_${type}_mandays`);
                 
                 const count = parseFloat(countInput.value) || 0;
                 const days = parseFloat(daysInput.value) || 0;
                 const mandays = count * days;
                 
                 display.textContent = mandays;
                 return { count, mandays };
             };
             
             const calculateAll = () => {
                 let grandTotalCount = 0;
                 let grandTotalManDays = 0;
                 
                 types.forEach(type => {
                     const result = calculateRow(type);
                     grandTotalCount += result.count;
                     grandTotalManDays += result.mandays;
                 });
                 
                 document.getElementById('grand_total_count').textContent = grandTotalCount;
                 document.getElementById('grand_total_mandays').textContent = grandTotalManDays;
                 // Set hidden work_days to grandTotalManDays (or average? user said "total for all days")
                 // If I set work_days to grandTotalManDays, and daily_rate is rate per day per person?
                 // No, standard logic is (Workforce * Days * Rate).
                 // If I use GrandTotalManDays, then the effective logic becomes (ManDays * Rate).
                 // The backend uses if(manDays > 0) base = ManDays * Rate.
                 // So I need to set work_days to something reasonable to avoid validation errors if required.
                 document.getElementById('total_work_days_hidden').value = 1; // Just a dummy value, real logic is in backend breakdown
                 
                 return grandTotalManDays;
             };
             
             // Financial Logic
             const calculateFinancials = () => {
                 const totalManDays = calculateAll();
                 const dailyRate = parseFloat(document.getElementById('daily_rate').value) || 0;
                 const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
                 const diff = parseFloat(document.getElementById('amount_difference').value) || 0;
                 
                 const subtotal = totalManDays * dailyRate;
                 const tax = subtotal * (taxRate / 100);
                 const total = subtotal + tax + diff;
                 
                 document.getElementById('subtotal_display').textContent = subtotal.toFixed(2);
                 document.getElementById('tax_amount_display').textContent = tax.toFixed(2);
                 document.getElementById('total_amount_display').textContent = total.toFixed(2) + ' ﷼';
             };
             
             // Attach listeners
             document.querySelectorAll('.workforce-count, .workforce-days').forEach(input => {
                 input.addEventListener('input', calculateFinancials);
             });
             
             ['daily_rate', 'tax_rate', 'amount_difference'].forEach(id => {
                 document.getElementById(id).addEventListener('input', calculateFinancials);
             });
             
             // Initial Calc
             calculateFinancials();
             
             // Client Info Display Logic
             const clientSelect = document.getElementById('clientSelect');
             const clientQuickInfo = document.getElementById('clientQuickInfo');
             
             clientSelect.addEventListener('change', function() {
                 const opt = this.options[this.selectedIndex];
                 if(opt.value) {
                     document.getElementById('clientPhoneDisplay').textContent = opt.dataset.phone || 'غير متوفر';
                     document.getElementById('clientAddressDisplay').textContent = opt.dataset.address || 'غير متوفر';
                     clientQuickInfo.classList.remove('hidden');
                 } else {
                     clientQuickInfo.classList.add('hidden');
                 }
             });
             
             // Modals Logic (Inline Create)
             const setupAjaxForm = (formId, route) => {
                 const form = document.getElementById(formId);
                 form.addEventListener('submit', async (e) => {
                     e.preventDefault();
                     const formData = new FormData(form);
                     try {
                         const resp = await fetch(route, {
                             method: 'POST',
                             headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                             body: formData
                         });
                         const data = await resp.json();
                         if(data.success) {
                             if(formId === 'addClientForm') {
                                 const sel = document.getElementById('clientSelect');
                                 const opt = new Option(data.client.name, data.client.id);
                                 opt.dataset.email = data.client.email || '';
                                 opt.dataset.phone = data.client.phone || '';
                                 opt.dataset.address = data.client.address || '';
                                 sel.add(opt);
                                 sel.value = data.client.id;
                                 sel.dispatchEvent(new Event('change'));
                             } else {
                                  // Service
                                 const sel = document.getElementById('serviceSelect');
                                 const opt = new Option(data.service.name, data.service.id);
                                 sel.add(opt);
                                 sel.value = data.service.id;
                             }
                             form.reset();
                             bootstrap.Modal.getInstance(document.getElementById(formId.replace('Form', 'Modal'))).hide();
                             if(window.toastr) toastr.success(data.message);
                         } else {
                             if(window.toastr) toastr.error(data.message);
                         }
                     } catch(err) { console.error(err); if(window.toastr) toastr.error('حدث خطأ'); }
                 });
             };
             
             setupAjaxForm('addClientForm', "{{ route('invoices.add-client') }}");
             setupAjaxForm('addServiceForm', "{{ route('invoices.add-service') }}");
        });

        function toggleCustomStatus() {
            const val = document.getElementById('invoice_status').value;
            const container = document.getElementById('custom_status_container');
            if(val === 'other') container.classList.remove('hidden');
            else container.classList.add('hidden');
        }
    </script>
@endpush

@section('styles')
<style>
    /* Custom Scrollbar for tables */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    
    .card { @apply bg-white dark:bg-gray-800 rounded-2xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 transition-all duration-300 hover:shadow-xl hover:shadow-slate-300/50 dark:hover:shadow-none; }
    .card-header { @apply rounded-t-2xl; }
    .btn-primary { @apply bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white px-6 py-3 rounded-xl font-bold transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg shadow-emerald-500/30; }
    .btn-secondary { @apply bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-800 px-6 py-3 rounded-xl font-bold transition-all shadow-sm hover:shadow-md; }
    .btn-icon { @apply p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors border border-emerald-100; }
    .form-input, .form-select { @apply w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all duration-200 outline-none shadow-sm; }
    .form-label { @apply block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5; }
    .modal-header { @apply bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-t-2xl px-6 py-4; }
    .modal-content { @apply rounded-2xl border-0 shadow-2xl; }
    .animate-fade-in-down { animation: fadeInDown 0.3s ease-out; }
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
