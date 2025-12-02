@extends('layouts.master')

@section('title', 'إضافة دفعة جديدة')

@section('content')
    <div class="container-fluid px-4">
        <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
            @csrf

            <!-- Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                <div class="mb-4 lg:mb-0">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-plus-circle text-primary mr-2"></i>
                        إضافة دفعة جديدة
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">املأ المعلومات أدناه لتسجيل دفعة جديدة</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('payments.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-right ml-2"></i>
                        رجوع للقائمة
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save ml-2"></i>
                        حفظ الدفعة
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Invoice Information -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-invoice text-primary ml-2"></i>
                        معلومات الفاتورة
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">اختر الفاتورة <span class="text-red-500">*</span></label>
                            <select name="invoice_id" class="form-select" required id="invoiceSelect">
                                <option value="">-- اختر الفاتورة --</option>
                                @foreach($invoices as $invoice)
                                    <option value="{{ $invoice->id }}"
                                            data-amount="{{ $invoice->total_price }}"
                                            data-client-name="{{ $invoice->client->name }}"
                                            data-client-email="{{ $invoice->client->email }}"
                                            data-client-phone="{{ $invoice->client->phone }}"
                                            data-client-address="{{ $invoice->client->address }}">
                                        {{ $invoice->invoice_number }} - {{ $invoice->client->name }} - {{ number_format($invoice->total_price, 2) }} ﷼
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Client Info Display -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mt-4">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">معلومات العميل</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label text-sm">اسم العميل</label>
                                    <input type="text" class="form-input bg-white dark:bg-gray-600 text-sm" id="clientName" readonly>
                                </div>
                                <div>
                                    <label class="form-label text-sm">البريد الإلكتروني</label>
                                    <input type="email" class="form-input bg-white dark:bg-gray-600 text-sm" id="clientEmail" readonly>
                                </div>
                                <div>
                                    <label class="form-label text-sm">الهاتف</label>
                                    <input type="text" class="form-input bg-white dark:bg-gray-600 text-sm" id="clientPhone" readonly>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="form-label text-sm">العنوان</label>
                                    <textarea class="form-input bg-white dark:bg-gray-600 text-sm" id="clientAddress" rows="2" readonly></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-credit-card text-primary ml-2"></i>
                        معلومات الدفع
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">تاريخ الدفع <span class="text-red-500">*</span></label>
                                <input type="date" name="payment_date" class="form-input" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="form-label">المبلغ <span class="text-red-500">*</span></label>
                                <input type="number" name="amount" id="amount" class="form-input" min="0" step="0.01" value="0" required>
                            </div>
                            <div>
                                <label class="form-label">طريقة الدفع <span class="text-red-500">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="cash">نقدي</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="check">شيك</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">حالة الدفع <span class="text-red-500">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="pending">قيد الانتظار</option>
                                    <option value="completed">مكتمل</option>
                                    <option value="cancelled">ملغى</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-receipt text-primary ml-2"></i>
                    تفاصيل إضافية
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">رقم المرجع</label>
                            <input type="text" name="reference_number" class="form-input" placeholder="رقم المرجع البنكي أو المعرف">
                        </div>
                        <div>
                            <label class="form-label">مبلغ الفاتورة</label>
                            <input type="text" id="invoice_amount_display" class="form-input bg-gray-50" value="0.00 ﷼" readonly>
                        </div>
                    </div>


                    <!-- Amount Comparison -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <label class="form-label text-blue-700 dark:text-blue-300">مبلغ الفاتورة (﷼)</label>
                            <input type="text" id="invoice_amount" class="form-input bg-white dark:bg-gray-800 text-blue-700 dark:text-blue-300 font-semibold text-center text-lg" value="0.00" readonly>
                        </div>
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4">
                            <label class="form-label text-emerald-700 dark:text-emerald-300">المبلغ المدفوع (﷼)</label>
                            <input type="text" id="paid_amount" class="form-input bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 font-semibold text-center text-lg" value="0.00" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auto-generated Info -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle text-primary ml-2"></i>
                    معلومات النظام
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">رقم الدفعة</label>
                            <input type="text" class="form-input bg-gray-50" value="{{ 'PAY-' . date('Ymd') . '-' . str_pad(($payments->count() ?? 0) + 1, 3, '0', STR_PAD_LEFT) }}" readonly>
                            <small class="text-gray-500">يتم إنشاء رقم الدفعة تلقائياً</small>
                        </div>
                        <div>
                            <label class="form-label">تاريخ الإنشاء</label>
                            <input type="text" class="form-input bg-gray-50" value="{{ now()->format('Y-m-d H:i') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Update client info and amount when invoice is selected
            document.getElementById('invoiceSelect').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const invoiceAmount = parseFloat(selectedOption.getAttribute('data-amount')) || 0;

                // Update payment amount
                document.getElementById('amount').value = invoiceAmount;
                document.getElementById('invoice_amount').value = invoiceAmount.toFixed(2);
                document.getElementById('invoice_amount_display').value = invoiceAmount.toFixed(2) + ' ﷼';
                document.getElementById('paid_amount').value = invoiceAmount.toFixed(2);

                // Update client information
                document.getElementById('clientName').value = selectedOption.getAttribute('data-client-name') || '';
                document.getElementById('clientEmail').value = selectedOption.getAttribute('data-client-email') || '';
                document.getElementById('clientPhone').value = selectedOption.getAttribute('data-client-phone') || '';
                document.getElementById('clientAddress').value = selectedOption.getAttribute('data-client-address') || '';
            });

            // Update paid amount when amount changes
            document.getElementById('amount').addEventListener('input', function() {
                const paidAmount = parseFloat(this.value) || 0;
                document.getElementById('paid_amount').value = paidAmount.toFixed(2);
            });

            // Form validation
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                const invoiceId = document.getElementById('invoiceSelect').value;
                const amount = parseFloat(document.getElementById('amount').value) || 0;

                if (!invoiceId) {
                    e.preventDefault();
                    alert('يرجى اختيار الفاتورة');
                    return false;
                }

                if (amount <= 0) {
                    e.preventDefault();
                    alert('يرجى إدخال مبلغ صحيح أكبر من الصفر');
                    return false;
                }
            });

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                // Trigger initial invoice update if invoice is pre-selected
                const invoiceSelect = document.getElementById('invoiceSelect');
                if (invoiceSelect.value) {
                    invoiceSelect.dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush

    <style>
        .card {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700;
        }

        .card-header {
            @apply bg-gradient-to-r from-primary to-blue-600 text-white px-6 py-4 rounded-t-xl border-b border-blue-400;
        }

        .card-body {
            @apply p-6;
        }

        .form-label {
            @apply block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2;
        }

        .form-input {
            @apply w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white transition-all duration-200 shadow-sm;
        }

        .form-select {
            @apply w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white transition-all duration-200 shadow-sm;
        }

        .btn-primary {
            @apply bg-gradient-to-r from-primary to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 shadow-lg;
        }

        .btn-secondary {
            @apply bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-lg;
        }
    </style>
@endsection
