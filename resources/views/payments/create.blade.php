@extends('layouts.master')

@section('title', 'إضافة دفعة جديدة')

@section('content')
    <div class="container-fluid px-4 py-6">
        <form action="{{ route('payments.store') }}" method="POST" id="paymentForm" class="space-y-8">
            @csrf

            <!-- Header -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                <div class="mb-4 lg:mb-0">
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-500">
                            <i class="fas fa-plus-circle mr-2 text-emerald-600"></i>
                            إضافة دفعة جديدة
                        </span>
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-2 text-lg">سجل دفعة جديدة للفاتورة بدقة</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('payments.index') }}" class="btn-secondary flex items-center justify-center">
                        <i class="fas fa-arrow-right ml-2 group-hover:-translate-x-1 transition-transform"></i>
                        رجوع للقائمة
                    </a>
                    <button type="submit" class="btn bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl px-6 py-2 fw-bold shadow-lg shadow-emerald-500/30 border-0 hover:scale-105 transition-transform flex items-center justify-center">
                        <i class="fas fa-save ml-2"></i>
                        حفظ الدفعة
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <!-- Invoice Info -->
                <div class="card overflow-hidden border-0 shadow-2xl rounded-2xl">
                    <div class="card-header bg-gradient-to-r from-emerald-600 to-teal-600 text-white border-0 p-6">
                        <h3 class="flex items-center text-lg font-bold text-white">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white ml-3">
                                <i class="fas fa-file-invoice text-xl"></i>
                            </div>
                            بيانات الفاتورة والعميل
                        </h3>
                    </div>
                    <div class="p-8 space-y-6 bg-slate-50">
                        <div>
                            <label class="form-label small fw-bold text-slate-600">اختر الفاتورة <span class="text-danger">*</span></label>
                            <select name="invoice_id" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" required id="invoiceSelect">
                                <option value="">-- اختر الفاتورة --</option>
                                @foreach($invoices as $invoice)
                                    <option value="{{ $invoice->id }}"
                                            data-total="{{ $invoice->total_price }}"
                                            data-paid="{{ $invoice->paid_amount }}"
                                            data-remaining="{{ $invoice->remaining_amount }}"
                                            data-status="{{ $invoice->payment_status }}"
                                            data-generation-date="{{ $invoice->generation_date ? $invoice->generation_date->format('Y-m-d') : '' }}"
                                            data-total-workforce="{{ $invoice->total_workforce ?? 0 }}"
                                            data-work-days="{{ $invoice->work_days ?? 0 }}"
                                            data-client-name="{{ $invoice->client->name }}"
                                            data-client-email="{{ $invoice->client->email ?? '' }}"
                                            data-client-phone="{{ $invoice->client->phone ?? '' }}"
                                            data-client-address="{{ $invoice->client->address ?? '' }}">
                                        #{{ $invoice->number }} - {{ $invoice->client->name }} - متبقي: {{ number_format($invoice->remaining_amount, 2) }} ر.س
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Client Info Readonly -->
                        <div class="bg-white dark:bg-slate-800/50 rounded-xl p-6 border border-emerald-100 dark:border-slate-700 shadow-sm">
                             <h4 class="text-sm font-bold text-emerald-600 uppercase tracking-wider mb-4 border-b border-emerald-200 pb-2"><i class="fas fa-user-circle ml-2"></i>تفاصيل العميل</h4>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                 <div>
                                     <label class="text-xs text-slate-400 font-bold mb-1 block">اسم العميل</label>
                                     <input type="text" class="w-full bg-transparent border-none p-0 text-slate-800 dark:text-white font-semibold" id="clientName" readonly placeholder="-">
                                 </div>
                                 <div>
                                     <label class="text-xs text-slate-400 font-bold mb-1 block">البريد الإلكتروني</label>
                                     <input type="text" class="w-full bg-transparent border-none p-0 text-slate-800 dark:text-white" id="clientEmail" readonly placeholder="-">
                                 </div>
                                 <div class="md:col-span-2">
                                     <label class="text-xs text-slate-400 font-bold mb-1 block">الهاتف والعنوان</label>
                                     <div class="flex gap-4">
                                         <span class="flex items-center text-sm"><i class="fas fa-phone text-blue-400 ml-2"></i> <span id="clientPhone">-</span></span>
                                         <span class="flex items-center text-sm"><i class="fas fa-map-marker-alt text-blue-400 ml-2"></i> <span id="clientAddress">-</span></span>
                                     </div>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="card overflow-hidden border-0 shadow-2xl rounded-2xl">
                    <div class="card-header bg-gradient-to-r from-emerald-600 to-teal-600 text-white border-0 p-6">
                        <h3 class="flex items-center text-lg font-bold text-white">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white ml-3">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                            </div>
                            تفاصيل الدفع
                        </h3>
                    </div>
                    <div class="p-8 space-y-6 bg-slate-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label small fw-bold text-slate-600">تاريخ الدفع <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" id="payment_date" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                <small class="text-muted" id="late_indicator" style="display:none;"></small>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label small fw-bold text-slate-600">المبلغ المدفوع (ر.س) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amount" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm text-lg font-bold text-success @error('amount') is-invalid @enderror" min="0" step="0.01" value="{{ old('amount', 0) }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label small fw-bold text-slate-600">عدد الموظفين</label>
                                <input type="number" name="employees_count" id="employees_count" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm @error('employees_count') is-invalid @enderror" min="0" value="{{ old('employees_count') }}" placeholder="عدد الموظفين المدفوع لهم">
                                <small class="text-muted" id="employees_limit"></small>
                                @error('employees_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label small fw-bold text-slate-600">أيام العمل</label>
                                <input type="number" name="work_days" id="work_days" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm @error('work_days') is-invalid @enderror" min="0" value="{{ old('work_days') }}" placeholder="أيام العمل المتعلقة بالدفع">
                                <small class="text-muted" id="work_days_limit"></small>
                                @error('work_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label small fw-bold text-slate-600">طريقة الدفع <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" required>
                                    <option value="direct_bank_transfer" selected>🏦 تحويل بنكي مباشر</option>
                                    <option value="bank_wage_protection_transfer">💼 تحويل بنكي حماية الأجور</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label small fw-bold text-slate-600">حالة العملية <span class="text-danger">*</span></label>
                                <select name="status" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" required>
                                    <option value="pending">🕘 قيد الانتظار</option>
                                    <option value="completed" selected>✅ مكتمل</option>
                                    <option value="cancelled">🚫 ملغى</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label small fw-bold text-slate-600">رقم المرجع</label>
                                <input type="text" name="reference_number" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm @error('reference_number') is-invalid @enderror" value="{{ old('reference_number') }}" placeholder="مثال: REF-123456">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label small fw-bold text-slate-600">اسم البنك</label>
                                <input type="text" name="bank_name" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm @error('bank_name') is-invalid @enderror" value="{{ old('bank_name') }}" placeholder="مثال: الراجحي">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label small fw-bold text-slate-600">ملاحظات</label>
                            <textarea name="notes" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm @error('notes') is-invalid @enderror" rows="3" placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Summary Widget -->
                        <div class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/20 dark:to-slate-800 rounded-xl p-6 border border-blue-100 dark:border-blue-800/30 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-slate-500">إجمالي الفاتورة</span>
                                <span class="font-mono font-bold text-lg text-slate-800 dark:text-white" id="invoice_total_display">0.00 ر.س</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-blue-600">المبلغ المدفوع سابقاً</span>
                                <span class="font-mono font-bold text-lg text-blue-600" id="invoice_paid_display">0.00 ر.س</span>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-blue-200">
                                <span class="text-sm font-bold text-orange-600">المبلغ المتبقي</span>
                                <span class="font-mono font-bold text-xl text-orange-600" id="invoice_remaining_display">0.00 ر.س</span>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t-2 border-green-200">
                                <span class="text-sm font-bold text-green-600">المبلغ سيتم دفعه الآن</span>
                                <span class="font-mono font-bold text-2xl text-green-600" id="paid_amount_display">0.00 ر.س</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const invoiceSelect = document.getElementById('invoiceSelect');
            const amountInput = document.getElementById('amount');
            
            // Elements to update
            const clientName = document.getElementById('clientName');
            const clientEmail = document.getElementById('clientEmail');
            const clientPhone = document.getElementById('clientPhone');
            const clientAddress = document.getElementById('clientAddress');
            const invTotalDisplay = document.getElementById('invoice_total_display');
            const invPaidDisplay = document.getElementById('invoice_paid_display');
            const invRemainingDisplay = document.getElementById('invoice_remaining_display');
            const paidDisplay = document.getElementById('paid_amount_display');
            
            invoiceSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (!opt.value) {
                    resetDisplays();
                    return;
                }
                
                const total = parseFloat(opt.dataset.total) || 0;
                const paid = parseFloat(opt.dataset.paid) || 0;
                const remaining = parseFloat(opt.dataset.remaining) || 0;
                const invoiceNumber = opt.text.split('#')[1]?.split(' ')[0] || '';
                const generationDate = opt.dataset.generationDate || '';
                const totalWorkforce = parseInt(opt.dataset.totalWorkforce) || 0;
                const workDays = parseInt(opt.dataset.workDays) || 0;
                
                // Auto-fill amount with remaining
                amountInput.value = remaining.toFixed(2);
                amountInput.max = remaining;
                
                // Set payment date to last day of invoice month
                if (generationDate) {
                    const date = new Date(generationDate);
                    const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                    const paymentDateInput = document.getElementById('payment_date');
                    paymentDateInput.value = lastDay.toISOString().split('T')[0];
                    paymentDateInput.dataset.lastDay = lastDay.toISOString().split('T')[0];
                }
                
                // Update displays
                invTotalDisplay.textContent = total.toFixed(2) + ' ر.س';
                invPaidDisplay.textContent = paid.toFixed(2) + ' ر.س';
                invRemainingDisplay.textContent = remaining.toFixed(2) + ' ر.س';
                paidDisplay.textContent = remaining.toFixed(2) + ' ر.س';
                
                // Show invoice limits
                document.getElementById('employees_limit').textContent = 'الحد الأقصى: ' + totalWorkforce + ' موظف';
                document.getElementById('work_days_limit').textContent = 'الحد الأقصى: ' + workDays + ' يوم';
                
                // Fill Client Info
                clientName.value = opt.dataset.clientName || '';
                clientEmail.value = opt.dataset.clientEmail || '';
                clientPhone.textContent = opt.dataset.clientPhone || '-';
                clientAddress.textContent = opt.dataset.clientAddress || '-';
            });
            
            // Check for late payment
            const paymentDateInput = document.getElementById('payment_date');
            paymentDateInput.addEventListener('change', function() {
                const lastDay = this.dataset.lastDay;
                if (lastDay) {
                    const selectedDate = new Date(this.value);
                    const lastDayDate = new Date(lastDay);
                    const lateIndicator = document.getElementById('late_indicator');
                    
                    if (selectedDate > lastDayDate) {
                        const diffTime = Math.abs(selectedDate - lastDayDate);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        lateIndicator.style.display = 'block';
                        lateIndicator.className = 'text-danger small';
                        lateIndicator.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i>متأخر ' + diffDays + ' يوم';
                    } else {
                        lateIndicator.style.display = 'none';
                    }
                }
            });
            
            function resetDisplays() {
                invTotalDisplay.textContent = '0.00 ر.س';
                invPaidDisplay.textContent = '0.00 ر.س';
                invRemainingDisplay.textContent = '0.00 ر.س';
                paidDisplay.textContent = '0.00 ر.س';
                amountInput.value = '0';
                clientName.value = '';
                clientEmail.value = '';
                clientPhone.textContent = '-';
                clientAddress.textContent = '-';
            }
            
            amountInput.addEventListener('input', function() {
                const paid = parseFloat(this.value) || 0;
                const max = parseFloat(this.max) || Infinity;
                
                if (paid > max) {
                    this.value = max.toFixed(2);
                    paidDisplay.textContent = max.toFixed(2) + ' ر.س';
                    alert('المبلغ المدفوع لا يمكن أن يتجاوز المبلغ المتبقي');
                } else {
                    paidDisplay.textContent = paid.toFixed(2) + ' ر.س';
                }
            });
            
            // Pre-fill if invoice_id is in URL
            const urlParams = new URLSearchParams(window.location.search);
            const invoiceId = urlParams.get('invoice_id');
            if (invoiceId) {
                invoiceSelect.value = invoiceId;
                invoiceSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endpush

@section('styles')
<style>
    .card { @apply bg-white dark:bg-gray-800 rounded-2xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 transition-all duration-300; }
    .card-header { @apply rounded-t-2xl; }
    .btn-primary { @apply bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-700 hover:to-indigo-600 text-white px-6 py-3 rounded-xl font-bold transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg shadow-blue-500/30; }
    .btn-secondary { @apply bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-800 px-6 py-3 rounded-xl font-bold transition-all shadow-sm hover:shadow-md; }
    .form-input, .form-select { @apply w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all duration-200 outline-none shadow-sm; }
    .form-label { @apply block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5; }
</style>
@endsection
