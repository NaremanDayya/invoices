@section('title', 'إدارة أوامر السداد')
@push('styles')
    <style>
        .table th {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            padding: 1rem 0.75rem;
            white-space: nowrap;
        }

        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-color: #f3f4f6;
            white-space: nowrap;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .btn-sm {
            padding: 0.375rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
        }

        /* Modal styles matching invoices component */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-container {
            background: white;
            border-radius: 10px;
            width: 100%;
            max-width: 1200px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            background: linear-gradient(to right, #059669, #10b981);
            color: white;
            padding: 20px 24px;
            border-radius: 10px 10px 0 0;
        }

        .modal-body {
            padding: 24px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 20px 24px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            border-radius: 0 0 10px 10px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
    </style>
@endpush

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--primary);">
            <i class="fas fa-credit-card me-2"></i>
            إدارة المدفوعات
        </h2>
        <button class="btn" style="background: var(--primary); color: white;" wire:click="showCreateModal">
            <i class="fas fa-plus me-2"></i>
            دفعة جديدة
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--primary);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">إجمالي المدفوعات</h6>
                            <h3 class="mb-0" style="color: var(--primary);">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-money-bill-wave" style="color: var(--primary);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10b981;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">مكتملة</h6>
                            <h3 class="mb-0" style="color: #10b981;">{{ $stats['completed'] }}</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-check-circle" style="color: #10b981;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">قيد المراجعة</h6>
                            <h3 class="mb-0" style="color: #f59e0b;">{{ $stats['pending_review'] }}</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-clock" style="color: #f59e0b;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ef4444;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">ملغاة</h6>
                            <h3 class="mb-0" style="color: #ef4444;">{{ $stats['cancelled'] }}</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="بحث في المدفوعات..." wire:model.live="search">
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="clientFilter">
                        <option value="">كل العملاء</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="approvalStatusFilter">
                        <option value="">حالة الموافقة</option>
                        <option value="approved">موافق عليه</option>
                        <option value="pending">قيد المراجعة</option>
                        <option value="rejected">مرفوض</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="paymentStatusFilter">
                        <option value="">حالة الدفع</option>
                        <option value="paid">مدفوع</option>
                        <option value="pending">قيد الانتظار</option>
                        <option value="failed">فاشل</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" wire:model.live="fromDate" placeholder="من تاريخ">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-secondary w-100" wire:click="resetFilters">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Payments Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-hover mb-0">
                    <thead style="background: var(--light);">
                    <tr>
                        <th class="border-0">رقم المرجع</th>
                        <th class="border-0">العميل</th>
                        <th class="border-0">رقم الفاتورة</th>
                        <th class="border-0">تاريخ الإنشاء</th>
                        <th class="border-0">رقم ملف الموظفين</th>
                        <th class="border-0">نوع الدفع</th>
                        <th class="border-0">المبلغ الإجمالي</th>
                        <th class="border-0">إجمالي الخصم</th>
                        <th class="border-0">المبلغ المدفوع</th>
                        <th class="border-0">المبلغ المتبقي</th>
                        <th class="border-0">عدد الموظفين</th>
                        <th class="border-0">عدد الأيام</th>
                        <th class="border-0">حالة الموافقة</th>
                        <th class="border-0">تاريخ الموافقة الإدارية</th>
                        <th class="border-0">حالة الدفع</th>
                        <th class="border-0">تاريخ الدفع</th>
                        <th class="border-0">أيام التأخير</th>
                        <th class="border-0 text-center">الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>
                                <div class="fw-bold">#{{ $payment->reference_number ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $payment->client->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $payment->client->contact_person ?? '' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $payment->invoice ? '#'.$payment->invoice->invoice_number : 'N/A' }}</div>
                            </td>
                            <td>{{ $payment->generation_date->format('Y-m-d') }}</td>
                            <td>
                                <div class="fw-bold">{{ $payment->employees_file ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $payment->payment_type }}</span>
                            </td>
                            <td>
                                <div class="fw-bold" style="color: var(--primary);">{{ number_format($payment->total_price, 2) }} ﷼</div>
                            </td>
                            <td>
                                <div class="fw-bold text-danger">{{ number_format($payment->deductions_total, 2) }} ﷼</div>
                            </td>
                            <td>
                                <div class="fw-bold text-success">{{ number_format($payment->paid_amount, 2) }} ﷼</div>
                            </td>
                            <td>
                                <div class="fw-bold" style="color: {{ $payment->remaining_amount > 0 ? '#ef4444' : '#10b981' }};">
                                    {{ number_format($payment->remaining_amount, 2) }} ﷼
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-bold">{{ $payment->employees_number }}</div>
                                    <small class="text-muted">موظف</small>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-bold">{{ $payment->days_number }}</div>
                                    <small class="text-muted">يوم</small>
                                </div>
                            </td>
                            <td>
                                @if($payment->approvement_status === 'approved')
                                    <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                                        <i class="fas fa-check me-1"></i>
                                        موافق
                                    </span>
                                @elseif($payment->approvement_status === 'pending')
                                    <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                                        <i class="fas fa-clock me-1"></i>
                                        قيد المراجعة
                                    </span>
                                @else
                                    <span class="badge rounded-pill" style="background: #fee2e2; color: #991b1b;">
                                        <i class="fas fa-times me-1"></i>
                                        مرفوض
                                    </span>
                                @endif
                            </td>
                            <td>{{ $payment->management_acceptance_date?->format('Y-m-d') ?? '---' }}</td>
                            <td>
                                @if($payment->payment_status === 'paid')
                                    <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                                        <i class="fas fa-check-circle me-1"></i>
                                        مدفوع
                                    </span>
                                @elseif($payment->payment_status === 'pending')
                                    <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                                        <i class="fas fa-clock me-1"></i>
                                        قيد الانتظار
                                    </span>
                                @else
                                    <span class="badge rounded-pill" style="background: #fee2e2; color: #991b1b;">
                                        <i class="fas fa-times-circle me-1"></i>
                                        فاشل
                                    </span>
                                @endif
                            </td>
                            <td>{{ $payment->payment_date?->format('Y-m-d') ?? '---' }}</td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-bold {{ $payment->late_days > 0 ? 'text-danger' : 'text-success' }}">{{ $payment->late_days }}</div>
                                    <small class="text-muted">يوم</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm btn-outline-primary" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" title="تعديل" wire:click="showEditModal({{ $payment->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($payment->payment_status === 'pending' && $payment->approvement_status === 'approved')
                                        <button class="btn btn-sm btn-outline-success" title="تأكيد الدفع" wire:click="confirmPayment({{ $payment->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    @if($payment->approvement_status === 'pending')
                                        <button class="btn btn-sm btn-outline-info" title="اعتماد" wire:click="approvePayment({{ $payment->id }})">
                                            <i class="fas fa-thumbs-up"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-outline-danger" title="حذف" wire:click="deletePayment({{ $payment->id }})" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            عرض {{ $payments->firstItem() }} إلى {{ $payments->lastItem() }} من {{ $payments->total() }} دفعة
        </div>
        {{ $payments->links() }}
    </div>

    <!-- Add/Edit Payment Modal - Following the same pattern as invoices -->
    @if($showModal)
        <div class="modal-overlay">
            <div class="modal-container">
                <!-- Modal Header -->
                <div class="modal-header">
                    <div style="display: flex; justify-content: between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas {{ $editingPayment ? 'fa-edit' : 'fa-plus-circle' }}" style="font-size: 1.25rem;"></i>
                            <h5 style="margin: 0; font-size: 1.25rem; font-weight: 600;">
                                {{ $editingPayment ? 'تعديل الدفعة' : 'إضافة دفعة جديدة' }}
                            </h5>
                        </div>
                        <button type="button" style="background: none; border: none; color: rgba(255,255,255,0.8); font-size: 1.25rem; cursor: pointer;"
                                wire:click="closeModal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <form wire:submit.prevent="savePayment">
                        <!-- Basic Information -->
                        <div style="margin-bottom: 24px;">
                            <h6 style="margin-bottom: 16px; color: #374151; font-size: 1.125rem; font-weight: 600;">
                                <i class="fas fa-info-circle me-2" style="color: #059669;"></i>
                                معلومات أساسية
                            </h6>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">رقم المرجع *</label>
                                    <input type="text"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="reference_number" required>
                                    @error('reference_number') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">تاريخ الإنشاء *</label>
                                    <input type="date"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="generation_date" required>
                                    @error('generation_date') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">العميل *</label>
                                    <select style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                            wire:model="client_id" required>
                                        <option value="">اختر العميل</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('client_id') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">رقم الفاتورة</label>
                                    <select style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                            wire:model="invoice_id">
                                        <option value="">اختر الفاتورة</option>
                                        @foreach($invoices as $invoice)
                                            <option value="{{ $invoice->id }}">#{{ $invoice->invoice_number }}</option>
                                        @endforeach
                                    </select>
                                    @error('invoice_id') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div style="margin-bottom: 24px;">
                            <h6 style="margin-bottom: 16px; color: #374151; font-size: 1.125rem; font-weight: 600;">
                                <i class="fas fa-money-bill-wave me-2" style="color: #059669;"></i>
                                معلومات الدفع
                            </h6>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">نوع الدفع *</label>
                                    <select style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                            wire:model="payment_type" required>
                                        <option value="">اختر نوع الدفع</option>
                                        <option value="تحويل بنكي">تحويل بنكي</option>
                                        <option value="محفظة إلكترونية">محفظة إلكترونية</option>
                                        <option value="بطاقة ائتمان">بطاقة ائتمان</option>
                                        <option value="نقدي">نقدي</option>
                                    </select>
                                    @error('payment_type') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">طريقة الدفع *</label>
                                    <input type="text"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="payment_method" placeholder="طريقة الدفع" required>
                                    @error('payment_method') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">رقم ملف الموظفين</label>
                                    <input type="text"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="employees_file" placeholder="#EMP-2024-001">
                                    @error('employees_file') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">أيام التأخير</label>
                                    <input type="number"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="late_days" min="0">
                                    @error('late_days') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Employee Details -->
                        <div style="margin-bottom: 24px;">
                            <h6 style="margin-bottom: 16px; color: #374151; font-size: 1.125rem; font-weight: 600;">
                                <i class="fas fa-users me-2" style="color: #059669;"></i>
                                تفاصيل العمالة
                            </h6>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">عدد الموظفين *</label>
                                    <input type="number"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="employees_number" min="1" required>
                                    @error('employees_number') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">عدد الأيام *</label>
                                    <input type="number"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="days_number" min="1" required>
                                    @error('days_number') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">المبلغ الإجمالي (﷼) *</label>
                                    <input type="number"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="total_price" min="0" step="0.01" required>
                                    @error('total_price') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">إجمالي الخصم (﷼)</label>
                                    <input type="number"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="deductions_total" min="0" step="0.01">
                                    @error('deductions_total') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Financial Details -->
                        <div style="margin-bottom: 24px;">
                            <h6 style="margin-bottom: 16px; color: #374151; font-size: 1.125rem; font-weight: 600;">
                                <i class="fas fa-calculator me-2" style="color: #059669;"></i>
                                التفاصيل المالية
                            </h6>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">المبلغ المدفوع (﷼)</label>
                                    <input type="number"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="paid_amount" min="0" step="0.01">
                                    @error('paid_amount') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">المبلغ المتبقي (﷼)</label>
                                    <input type="text"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #e5e7eb; color: #374151; font-weight: 600;"
                                           value="{{ number_format($remaining_amount, 2) }} ﷼" readonly>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">صافي المبلغ (﷼)</label>
                                    <input type="text"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #e5e7eb; color: #374151; font-weight: 600;"
                                           value="{{ number_format(($total_price ?? 0) - ($deductions_total ?? 0), 2) }} ﷼" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Status and Dates -->
                        <div style="margin-bottom: 24px;">
                            <h6 style="margin-bottom: 16px; color: #374151; font-size: 1.125rem; font-weight: 600;">
                                <i class="fas fa-clipboard-check me-2" style="color: #059669;"></i>
                                الحالات والتواريخ
                            </h6>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">حالة الموافقة *</label>
                                    <select style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                            wire:model="approvement_status" required>
                                        <option value="pending">قيد المراجعة</option>
                                        <option value="approved">موافق عليه</option>
                                        <option value="rejected">مرفوض</option>
                                    </select>
                                    @error('approvement_status') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">تاريخ الموافقة الإدارية</label>
                                    <input type="date"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="management_acceptance_date">
                                    @error('management_acceptance_date') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">حالة الدفع *</label>
                                    <select style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                            wire:model="payment_status" required>
                                        <option value="pending">قيد الانتظار</option>
                                        <option value="paid">مدفوع</option>
                                        <option value="failed">فاشل</option>
                                    </select>
                                    @error('payment_status') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">تاريخ الدفع</label>
                                    <input type="date"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model="payment_date">
                                    @error('payment_date') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button"
                            style="padding: 12px 24px; border: 1px solid #d1d5db; background: white; color: #374151; border-radius: 8px; cursor: pointer; font-weight: 500;"
                            wire:click="closeModal">
                        إلغاء
                    </button>
                    <button type="button"
                            style="padding: 12px 24px; background: #059669; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 8px;"
                            wire:click="savePayment">
                        <i class="fas fa-save"></i>
                        {{ $editingPayment ? 'تحديث' : 'حفظ' }} الدفعة
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
