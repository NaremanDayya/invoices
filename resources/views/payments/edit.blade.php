@extends('layouts.master')

@section('title', 'تعديل الدفعة')

@section('content')
    <div class="container-fluid px-4 py-6">
        <form action="{{ route('payments.update', $payment->id) }}" method="POST" id="paymentForm" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Header -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                <div class="mb-4 lg:mb-0">
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-500">
                            <i class="fas fa-edit mr-2 text-blue-600"></i>
                            تعديل الدفعة #{{ $payment->number }}
                        </span>
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-2 text-lg">تحديث بيانات الدفعة</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('payments.index') }}" class="btn-secondary flex items-center justify-center">
                        <i class="fas fa-arrow-right ml-2 group-hover:-translate-x-1 transition-transform"></i>
                        رجوع للقائمة
                    </a>
                    <button type="submit" class="btn-primary flex items-center justify-center shadow-blue-500/20">
                        <i class="fas fa-save ml-2"></i>
                        حفظ التعديلات
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <!-- Invoice Info -->
                <div class="card overflow-hidden">
                    <div class="card-header bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 border-b border-slate-200 dark:border-slate-700 p-6">
                        <h3 class="flex items-center text-lg font-bold text-slate-800 dark:text-white">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 ml-3">
                                <i class="fas fa-file-invoice text-xl"></i>
                            </div>
                            بيانات الفاتورة والعميل
                        </h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="form-label">الفاتورة المرتبطة <span class="text-red-500">*</span></label>
                            <select name="invoice_id" class="form-select" required id="invoiceSelect">
                                <option value="">-- اختر الفاتورة --</option>
                                @foreach($invoices as $invoice)
                                    <option value="{{ $invoice->id }}"
                                            {{ $payment->invoice_id == $invoice->id ? 'selected' : '' }}
                                            data-total="{{ $invoice->total_price }}"
                                            data-paid="{{ $invoice->paid_amount }}"
                                            data-remaining="{{ $invoice->remaining_amount }}"
                                            data-status="{{ $invoice->payment_status }}"
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
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 border border-slate-100 dark:border-slate-700">
                             <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 border-b border-slate-200 pb-2">تفاصيل العميل</h4>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                 <div>
                                     <label class="text-xs text-slate-400 font-bold mb-1 block">اسم العميل</label>
                                     <input type="text" class="w-full bg-transparent border-none p-0 text-slate-800 dark:text-white font-semibold" id="clientName" readonly placeholder="-" value="{{ $payment->invoice->client->name ?? '' }}">
                                 </div>
                                 <div>
                                     <label class="text-xs text-slate-400 font-bold mb-1 block">البريد الإلكتروني</label>
                                     <input type="text" class="w-full bg-transparent border-none p-0 text-slate-800 dark:text-white" id="clientEmail" readonly placeholder="-" value="{{ $payment->invoice->client->email ?? '' }}">
                                 </div>
                                 <div class="md:col-span-2">
                                     <label class="text-xs text-slate-400 font-bold mb-1 block">الهاتف والعنوان</label>
                                     <div class="flex gap-4">
                                         <span class="flex items-center text-sm"><i class="fas fa-phone text-blue-400 ml-2"></i> <span id="clientPhone">{{ $payment->invoice->client->phone ?? '-' }}</span></span>
                                         <span class="flex items-center text-sm"><i class="fas fa-map-marker-alt text-blue-400 ml-2"></i> <span id="clientAddress">{{ $payment->invoice->client->address ?? '-' }}</span></span>
                                     </div>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="card overflow-hidden">
                    <div class="card-header bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 border-b border-slate-200 dark:border-slate-700 p-6">
                        <h3 class="flex items-center text-lg font-bold text-slate-800 dark:text-white">
                            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 ml-3">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                            </div>
                            تفاصيل الدفع
                        </h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label">تاريخ الدفع <span class="text-red-500">*</span></label>
                                <input type="date" name="payment_date" id="payment_date" class="form-input" value="{{ $payment->payment_date->format('Y-m-d') }}" required>
                                @if($payment->late_days > 0)
                                    <small class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>متأخر {{ $payment->late_days }} يوم</small>
                                @endif
                            </div>
                            <div>
                                <label class="form-label">المبلغ المدفوع (ر.س) <span class="text-red-500">*</span></label>
                                <input type="number" name="amount" id="amount" class="form-input text-lg font-bold text-green-600" min="0" step="0.01" value="{{ $payment->amount }}" required>
                            </div>
                            <div>
                                <label class="form-label">عدد الموظفين</label>
                                <input type="number" name="employees_count" id="employees_count" class="form-input" min="0" value="{{ $payment->employees_count }}" placeholder="عدد الموظفين المدفوع لهم">
                                <small class="text-muted">الحد الأقصى: {{ $payment->invoice->total_workforce ?? 0 }} موظف</small>
                            </div>
                            <div>
                                <label class="form-label">أيام العمل</label>
                                <input type="number" name="work_days" id="work_days" class="form-input" min="0" value="{{ $payment->work_days }}" placeholder="أيام العمل المتعلقة بالدفع">
                                <small class="text-muted">الحد الأقصى: {{ $payment->invoice->work_days ?? 0 }} يوم</small>
                            </div>
                            <div>
                                <label class="form-label">طريقة الدفع <span class="text-red-500">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="direct_bank_transfer" {{ $payment->payment_method == 'direct_bank_transfer' ? 'selected' : '' }}>🏦 تحويل بنكي مباشر</option>
                                    <option value="bank_wage_protection_transfer" {{ $payment->payment_method == 'bank_wage_protection_transfer' ? 'selected' : '' }}>💼 تحويل بنكي حماية الأجور</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">حالة العملية <span class="text-red-500">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>🕘 قيد الانتظار</option>
                                    <option value="completed" {{ $payment->status == 'completed' ? 'selected' : '' }}>✅ مكتمل</option>
                                    <option value="cancelled" {{ $payment->status == 'cancelled' ? 'selected' : '' }}>🚫 ملغى</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label">رقم المرجع</label>
                                <input type="text" name="reference_number" class="form-input" placeholder="مثال: REF-123456" value="{{ $payment->reference_number }}">
                            </div>
                            <div>
                                <label class="form-label">اسم البنك</label>
                                <input type="text" name="bank_name" class="form-input" placeholder="مثال: الراجحي" value="{{ $payment->bank_name }}">
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-input" rows="3" placeholder="أي ملاحظات إضافية...">{{ $payment->notes }}</textarea>
                        </div>
                        
                        <!-- Summary Widget -->
                        <div class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/20 dark:to-slate-800 rounded-xl p-6 border border-blue-100 dark:border-blue-800/30 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-slate-500">إجمالي الفاتورة</span>
                                <span class="font-mono font-bold text-lg text-slate-800 dark:text-white" id="invoice_total_display">{{ number_format($payment->invoice->total_price ?? 0, 2) }} ر.س</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-blue-600">المبلغ المدفوع سابقاً</span>
                                <span class="font-mono font-bold text-lg text-blue-600" id="invoice_paid_display">{{ number_format($payment->invoice->paid_amount ?? 0, 2) }} ر.س</span>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-blue-200">
                                <span class="text-sm font-bold text-orange-600">المبلغ المتبقي</span>
                                <span class="font-mono font-bold text-xl text-orange-600" id="invoice_remaining_display">{{ number_format($payment->invoice->remaining_amount ?? 0, 2) }} ر.س</span>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t-2 border-green-200">
                                <span class="text-sm font-bold text-green-600">المبلغ الحالي للدفع</span>
                                <span class="font-mono font-bold text-2xl text-green-600" id="paid_amount_display">{{ number_format($payment->amount, 2) }} ر.س</span>
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
            
            // Store original payment amount for validation
            const originalAmount = parseFloat(amountInput.value);
            const originalInvoiceId = parseInt(invoiceSelect.value);
            
            invoiceSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (!opt.value) {
                    resetDisplays();
                    return;
                }
                
                const total = parseFloat(opt.dataset.total) || 0;
                const paid = parseFloat(opt.dataset.paid) || 0;
                let remaining = parseFloat(opt.dataset.remaining) || 0;
                
                // If editing same invoice, add back the original payment amount to remaining
                if (parseInt(opt.value) === originalInvoiceId) {
                    remaining += originalAmount;
                }
                
                // Auto-fill amount with remaining or keep original
                amountInput.max = remaining;
                
                // Update displays
                invTotalDisplay.textContent = total.toFixed(2) + ' ر.س';
                invPaidDisplay.textContent = paid.toFixed(2) + ' ر.س';
                invRemainingDisplay.textContent = remaining.toFixed(2) + ' ر.س';
                
                // Fill Client Info
                clientName.value = opt.dataset.clientName || '';
                clientEmail.value = opt.dataset.clientEmail || '';
                clientPhone.textContent = opt.dataset.clientPhone || '-';
                clientAddress.textContent = opt.dataset.clientAddress || '-';
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
            
            // Trigger initial display update
            if (invoiceSelect.value) {
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
