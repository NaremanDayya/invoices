@section('title', 'إدارة الفواتير')
@push('styles')
    <style>
        .flatpickr-calendar {
            font-family: 'Tajawal', sans-serif;
            direction: rtl;
        }

        .flatpickr-months .flatpickr-month {
            height: 34px;
        }

        .flatpickr-weekdays {
            height: 28px;
        }

        .flatpickr-day {
            border-radius: 4px;
        }

        .flatpickr-day.selected {
            background: var(--primary);
            border-color: var(--primary);
        }

        .flatpickr-day.today {
            border-color: var(--primary);
        }

        .flatpickr-day.today:hover {
            background: var(--primary);
            color: white;
        }
        .table th {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            padding: 1rem 0.75rem;
        }

        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-color: #f3f4f6;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }
        .modal.show {
            display: block !important;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-backdrop.show {
            display: block !important;
            opacity: 0.5;
        }
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
        }

    </style>
@endpush
<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--primary);">
            <i class="fas fa-file-invoice me-2"></i>
            إدارة الفواتير
        </h2>
        <button class="btn" style="background: var(--primary); color: white;" wire:click="$set('showModal', true)">
            <i class="fas fa-plus me-2"></i>
            فاتورة جديدة
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--primary);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">إجمالي الفواتير</h6>
                            <h3 class="mb-0" style="color: var(--primary);">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-receipt" style="color: var(--primary);"></i>
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
                            <h6 class="card-title text-muted mb-2">مدفوعة</h6>
                            <h3 class="mb-0" style="color: #10b981;">{{ $stats['paid'] ?? 0 }}</h3>
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
                            <h6 class="card-title text-muted mb-2">قيد الانتظار</h6>
                            <h3 class="mb-0" style="color: #f59e0b;">{{ $stats['pending'] ?? 0 }}</h3>
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
                            <h6 class="card-title text-muted mb-2">متأخرة</h6>
                            <h3 class="mb-0" style="color: #ef4444;">{{ $stats['late'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">بحث سريع</label>
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        class="block w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200"
                        placeholder="ابحث في الفواتير..."
                        wire:model.live="search"
                    >
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">حالة السداد</label>
                <select
                    class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 appearance-none"
                    wire:model.live="statusFilter"
                >
                    <option value="">كل الحالات</option>
                    <option value="paid">مدفوعة</option>
                    <option value="pending">قيد الانتظار</option>
                    <option value="overdue">متأخرة</option>
                </select>
            </div>

            <!-- Client Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">العميل</label>
                <select
                    class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 appearance-none"
                    wire:model.live="clientFilter"
                >
                    <option value="">كل العملاء</option>
                    @foreach($clients as $client)
                        <option value="{{ $client }}">{{ $client }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                <input
                    type="text"
                    class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200"
                    wire:model.live="startDate" id="start_date" placeholder="من تاريخ"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                <input
                    type="text"
                    class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200"
                    wire:model.live="endDate" id="end_date" placeholder="إلى تاريخ"
                >
            </div>

            <!-- Reset Button -->
            <div>
                <button
                    class="w-full px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 font-medium"
                    wire:click="resetFilters"
                >
                    <i class="fas fa-refresh text-gray-500"></i>
                    <span>إعادة تعيين</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: var(--light);">
                    <tr>
                        <th class="border-0">رقم الفاتورة</th>
                        <th class="border-0">العميل</th>
                        <th class="border-0">تاريخ الإصدار</th>
                        <th class="border-0">أيام تأخير الإصدار</th>
                        <th class="border-0">نوع الخدمة</th>
                        <th class="border-0">إجمالي العمالة</th>
                        <th class="border-0">أيام العمل</th>
                        <th class="border-0">المبلغ قبل الضريبة</th>
                        <th class="border-0">الضريبة</th>
                        <th class="border-0">المبلغ الإجمالي</th>
                        <th class="border-0">فرق المبلغ</th>
                        <th class="border-0">حالة السداد</th>
                        <th class="border-0">تاريخ السداد</th>
                        <th class="border-0">أيام تأخير السداد</th>
                        <th class="border-0">حالة الفاتورة</th>
                        <th class="border-0 text-center">الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            {{-- رقم الفاتورة --}}
                            <td>{{ $invoice->number }}</td>

                            {{-- العميل --}}
                            <td>
                                <div class="fw-bold">{{ $invoice->client->name ?? 'غير معروف' }}</div>
                            </td>

                            {{-- تاريخ الإصدار --}}
                            <td>{{ $invoice->generation_date ? \Carbon\Carbon::parse($invoice->generation_date)->format('Y-m-d') : '—' }}</td>

                            {{-- أيام تأخير الإصدار --}}
                            <td>
                                @php
                                    $delayDays = $invoice->last_generation_date
                                        ? \Carbon\Carbon::parse($invoice->generation_date)->diffInDays($invoice->last_generation_date)
                                        : 0;
                                @endphp
                                {{ $delayDays }}
                            </td>

                            {{-- نوع الخدمة --}}
                            <td>{{ $invoice->service->name ?? '—' }}</td>

                            {{-- إجمالي العمالة --}}
                            <td>{{ $invoice->total_workers + $invoice->total_supervisors + $invoice->total_managers + $invoice->total_users }}</td>

                            {{-- أيام العمل --}}
                            <td>{{ $invoice->work_days }}</td>

                            {{-- المبلغ قبل الضريبة --}}
                            <td>{{ number_format($invoice->base_price, 2) }} ﷼</td>

                            {{-- الضريبة --}}
                            <td>{{ number_format($invoice->tax, 2) }} ﷼</td>

                            {{-- المبلغ الإجمالي --}}
                            <td>{{ number_format($invoice->total_price, 2) }} ﷼</td>

                            {{-- فرق المبلغ --}}
                            <td>{{ number_format($invoice->price_difference ?? 0, 2) }} ﷼</td>

                            {{-- حالة السداد --}}
                            <td>
                                @php
                                    $statusColors = [
                                        'paid' => ['bg' => '#d1fae5', 'color' => '#065f46', 'icon' => 'check-circle'],
                                        'pending' => ['bg' => '#fef3c7', 'color' => '#92400e', 'icon' => 'clock'],
                                        'late' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'exclamation-triangle']
                                    ];
                                    $status = $statusColors[$invoice->payment_status] ?? $statusColors['pending'];
                                @endphp
                                <span class="badge rounded-pill" style="background: {{ $status['bg'] }}; color: {{ $status['color'] }};">
                <i class="fas fa-{{ $status['icon'] }} me-1"></i>
                {{ $invoice->payment_status === 'paid' ? 'مدفوعة' : ($invoice->payment_status === 'pending' ? 'قيد الانتظار' : 'متأخرة') }}
            </span>
                            </td>

                            {{-- تاريخ السداد --}}
                            <td>{{ $invoice->payment_date ? \Carbon\Carbon::parse($invoice->payment_date)->format('Y-m-d') : '—' }}</td>

                            {{-- أيام تأخير السداد --}}
                            <td>
                                @php
                                    $lateDays = ($invoice->payment_date && $invoice->last_paying_date)
                                        ? \Carbon\Carbon::parse($invoice->last_paying_date)->diffInDays($invoice->payment_date)
                                        : 0;
                                @endphp
                                {{ $lateDays }}
                            </td>

                            {{-- حالة الفاتورة (حسب نوعها) --}}
                            <td>{{ $invoice->invoice_type ?? '—' }}</td>

                            {{-- الإجراءات --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" title="حذف">
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
            عرض {{ $invoices->firstItem() ?? 0 }} إلى {{ $invoices->lastItem() ?? 0 }} من {{ $invoices->total() ?? 0 }} فاتورة
        </div>
        {{ $invoices->links() }}
    </div>


    @if($showModal)

        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div style="background: white; border-radius: 10px; width: 100%; max-width: 1200px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
                <!-- Modal Header -->
                <div style="background: linear-gradient(to right, #059669, #10b981); color: white; padding: 20px 24px; border-radius: 10px 10px 0 0;">
                    <div style="display: flex; justify-content: between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-plus-circle" style="font-size: 1.25rem;"></i>
                            <h5 style="margin: 0; font-size: 1.25rem; font-weight: 600;">إضافة فاتورة جديدة</h5>
                        </div>
                        <button type="button" style="background: none; border: none; color: rgba(255,255,255,0.8); font-size: 1.25rem; cursor: pointer;"
                                wire:click="$set('showModal', false)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div style="padding: 24px; max-height: 60vh; overflow-y: auto;">

                    <!-- Client Information -->
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                            اسم العميل *
                        </label>

                        <!-- Success/Error Messages -->
                        @if (session()->has('client_message'))
                            <div class="mb-2 p-2 bg-green-100 text-green-700 rounded text-sm">
                                {{ session('client_message') }}
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="mb-2 p-2 bg-red-100 text-red-700 rounded text-sm">
                                {{ session('error') }}
                            </div>
                        @endif

                        @error('clientName')
                        <div class="mb-2 p-2 bg-red-100 text-red-700 rounded text-sm">
                            {{ $message }}
                        </div>
                        @enderror

                        <!-- Switch between select and input -->
                        @if(!$showNewClientInput)
                            <div style="display: flex; gap: 8px; align-items: flex-start;">
                                <div style="position: relative; flex: 1;" class="client-search-container">
                                    <input type="text"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model.live="clientSearch"
                                           placeholder="ابحث عن عميل..."
                                           x-on:click.outside="closeDropdowns()"
                                           wire:key="client-search">

                                    <!-- Search Results Dropdown -->
                                    @if(!empty($clientSearch) && !$showNewClientInput)
                                        <div class="search-dropdown" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #d1d5db; border-radius: 8px; z-index: 1000; max-height: 200px; overflow-y: auto; margin-top: 2px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                            @forelse($filteredClients as $id => $name)
                                                <div style="padding: 10px 16px; cursor: pointer; border-bottom: 1px solid #f3f4f6;"
                                                     wire:click="selectClient({{ $id }})"
                                                     class="hover:bg-gray-50 transition-colors duration-150">
                                                    {{ $name }}
                                                </div>
                                            @empty
                                                <div style="padding: 10px 16px; color: #6b7280; text-align: center;">
                                                    لا توجد نتائج
                                                </div>
                                            @endforelse
                                        </div>
                                    @endif
                                </div>

                                <button type="button"
                                        style="background: #059669; color: white; border: none; border-radius: 8px; padding: 12px 16px; cursor: pointer; white-space: nowrap; min-width: 60px; height: 46px;"
                                        wire:click="toggleNewClient"
                                        wire:key="new-client-btn">
                                    <i class="fas fa-plus"></i> جديد
                                </button>
                            </div>

                            <!-- Selected client display -->
                            @if($clientName && isset($filteredClients[$clientName]))
                                <div style="background: #f0f9ff; padding: 8px 12px; border-radius: 6px; border-right: 3px solid #059669; margin-top: 8px;">
                                    <small style="color: #059669;">
                                        <i class="fas fa-check-circle me-1"></i>
                                        العميل المحدد: {{ $filteredClients[$clientName] }}
                                    </small>
                                </div>
                            @endif

                        @else
                            <!-- New Client Input -->
                            <div style="margin-bottom: 8px;">
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <div style="flex: 1;">
                                        <input type="text"
                                               style="width: 100%; padding: 12px 16px; border: 1px solid #059669; border-radius: 8px; background: white;"
                                               wire:model="newClientName"
                                               placeholder="أدخل اسم العميل الجديد"
                                               wire:key="new-client-input">
                                        @error('newClientName')
                                        <span style="color: #ef4444; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <button type="button"
                                            style="background: #059669; color: white; border: none; border-radius: 8px; padding: 12px 16px; cursor: pointer; min-width: 50px; height: 46px;"
                                            wire:click="addNewClient"
                                            wire:key="save-client-btn">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button"
                                            style="background: #6b7280; color: white; border: none; border-radius: 8px; padding: 12px 16px; cursor: pointer; min-width: 50px; height: 46px;"
                                            wire:click="toggleNewClient"
                                            wire:key="cancel-client-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Help text -->
                            <div style="background: #f0f9ff; padding: 8px 12px; border-radius: 6px; border-right: 3px solid #059669;">
                                <small style="color: #059669;">
                                    <i class="fas fa-info-circle me-1"></i>
                                    أدخل اسم العميل الجديد ثم انقر على ✓ لحفظه
                                </small>
                            </div>
                        @endif
                    </div>

                    <!-- Invoice Information -->
                    <div style="margin-bottom: 24px;">
                        <h6 style="margin-bottom: 16px; color: #374151; font-size: 1.125rem; font-weight: 600;">
                            <i class="fas fa-file-invoice me-2" style="color: #059669;"></i>
                            معلومات الفاتورة
                        </h6>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">رقم الفاتورة *</label>
                                <input type="text"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                       wire:model="invoiceNumber" required>
                                @error('invoiceNumber') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">تاريخ الإصدار *</label>
                                <input type="date"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                       wire:model="invoiceDate" required>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">تاريخ الاستحقاق *</label>
                                <input type="date"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                       wire:model="dueDate" required>
                            </div>
                        </div>
                    </div>

                    <!-- Service Details -->
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                            نوع الخدمة *
                        </label>

                        <!-- Success/Error Messages -->
                        @if (session()->has('service_message'))
                            <div class="mb-2 p-2 bg-green-100 text-green-700 rounded text-sm">
                                {{ session('service_message') }}
                            </div>
                        @endif

                        @error('serviceType')
                        <div class="mb-2 p-2 bg-red-100 text-red-700 rounded text-sm">
                            {{ $message }}
                        </div>
                        @enderror

                        <!-- Switch between select and input -->
                        @if(!$showNewServiceInput)
                            <div style="display: flex; gap: 8px; align-items: flex-start;">
                                <div style="position: relative; flex: 1;" class="service-search-container">
                                    <input type="text"
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                           wire:model.live="serviceSearch"
                                           placeholder="ابحث عن خدمة..."
                                           x-on:click.outside="closeDropdowns()"
                                           wire:key="service-search">

                                    <!-- Search Results Dropdown -->
                                    @if(!empty($serviceSearch) && !$showNewServiceInput)
                                        <div class="search-dropdown" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #d1d5db; border-radius: 8px; z-index: 1000; max-height: 200px; overflow-y: auto; margin-top: 2px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                            @forelse($filteredServices as $id => $name)
                                                <div style="padding: 10px 16px; cursor: pointer; border-bottom: 1px solid #f3f4f6;"
                                                     wire:click="selectService({{ $id }})"
                                                     class="hover:bg-gray-50 transition-colors duration-150">
                                                    {{ $name }}
                                                </div>
                                            @empty
                                                <div style="padding: 10px 16px; color: #6b7280; text-align: center;">
                                                    لا توجد نتائج
                                                </div>
                                            @endforelse
                                        </div>
                                    @endif
                                </div>

                                <button type="button"
                                        style="background: #059669; color: white; border: none; border-radius: 8px; padding: 12px 16px; cursor: pointer; white-space: nowrap; min-width: 60px; height: 46px;"
                                        wire:click="toggleNewService"
                                        wire:key="new-service-btn">
                                    <i class="fas fa-plus"></i> جديد
                                </button>
                            </div>

                            <!-- Selected service display -->
                            @if($serviceType && isset($filteredServices[$serviceType]))
                                <div style="background: #f0f9ff; padding: 8px 12px; border-radius: 6px; border-right: 3px solid #059669; margin-top: 8px;">
                                    <small style="color: #059669;">
                                        <i class="fas fa-check-circle me-1"></i>
                                        الخدمة المحددة: {{ $filteredServices[$serviceType] }}
                                    </small>
                                </div>
                            @endif

                        @else
                            <!-- New Service Input -->
                            <div style="margin-bottom: 8px;">
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <div style="flex: 1;">
                                        <input type="text"
                                               style="width: 100%; padding: 12px 16px; border: 1px solid #059669; border-radius: 8px; background: white;"
                                               wire:model="newService"
                                               placeholder="أدخل اسم الخدمة الجديدة"
                                               wire:key="new-service-input">
                                        @error('newService')
                                        <span style="color: #ef4444; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <button type="button"
                                            style="background: #059669; color: white; border: none; border-radius: 8px; padding: 12px 16px; cursor: pointer; min-width: 50px; height: 46px;"
                                            wire:click="addNewService"
                                            wire:key="save-service-btn">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button"
                                            style="background: #6b7280; color: white; border: none; border-radius: 8px; padding: 12px 16px; cursor: pointer; min-width: 50px; height: 46px;"
                                            wire:click="toggleNewService"
                                            wire:key="cancel-service-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Help text -->
                            <div style="background: #f0f9ff; padding: 8px 12px; border-radius: 6px; border-right: 3px solid #059669;">
                                <small style="color: #059669;">
                                    <i class="fas fa-info-circle me-1"></i>
                                    أدخل اسم الخدمة الجديدة ثم انقر على ✓ لحفظه
                                </small>
                            </div>
                        @endif
                    </div>

                    <!-- Workforce Details -->
                    <div style="margin-bottom: 24px;">
                        <h6 style="margin-bottom: 16px; color: #374151; font-size: 1.125rem; font-weight: 600;">
                            <i class="fas fa-users me-2" style="color: #059669;"></i>
                            تفاصيل العمالة وأيام العمل
                        </h6>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">عدد العمال</label>
                                <input type="number"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                       wire:model.live="totalWorkers" min="0" step="1" value="0">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">عدد المشرفين</label>
                                <input type="number"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                       wire:model.live="totalSupervisors" min="0" step="1" value="0">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">عدد المديرين</label>
                                <input type="number"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                       wire:model.live="totalManagers" min="0" step="1" value="0">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">عدد المستخدمين</label>
                                <input type="number"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                       wire:model.live="totalUsers" min="0" step="1" value="0">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">أيام العمل</label>
                                <input type="number"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                       wire:model.live="workDays" min="0" step="1" value="0">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">إجمالي العمالة</label>
                                <input type="text"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #e5e7eb; color: #374151; font-weight: 600;"
                                       value="{{ $totalWorkforce }}" readonly>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">المبلغ قبل الضريبة (﷼)</label>
                                <input type="text"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #e5e7eb; color: #374151; font-weight: 600;"
                                       value="{{ number_format($basePrice, 2) }} ﷼" readonly>
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
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">نسبة الضريبة (%)</label>
                                <input type="number"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                       wire:model.live="taxRate" min="0" max="100" step="0.1">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">قيمة الضريبة (﷼)</label>
                                <input type="text"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #e5e7eb; color: #374151; font-weight: 600;"
                                       value="{{ number_format($taxAmount, 2) }} ﷼" readonly>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">المبلغ الإجمالي (﷼)</label>
                                <input type="text"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #e5e7eb; color: #374151; font-weight: 600;"
                                       value="{{ number_format($totalAmount, 2) }} ﷼" readonly>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">فرق المبلغ (﷼)</label>
                                <input type="number"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                       wire:model.live="amountDifference" step="0.01" value="0">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div style="margin-bottom: 24px;">
                        <h6 style="margin-bottom: 16px; color: #374151; font-size: 1.125rem; font-weight: 600;">
                            <i class="fas fa-credit-card me-2" style="color: #059669;"></i>
                            حالة السداد
                        </h6>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">حالة السداد *</label>
                                <select style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                        wire:model="paymentStatus" required>
                                    <option value="pending">قيد الانتظار</option>
                                    <option value="paid">مدفوعة</option>
                                    <option value="overdue">متأخرة</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">تاريخ السداد</label>
                                <input type="date"
                                       style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                       wire:model="paymentDate">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">حالة الفاتورة *</label>
                                <select style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;"
                                        wire:model="invoiceStatus" required>
                                    <option value="pending">معلقة</option>
                                    <option value="completed">مكتملة</option>
                                    <option value="overdue">متأخرة</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div style="margin-bottom: 24px;">
                        <h6 style="margin-bottom: 16px; color: #374151; font-size: 1.125rem; font-weight: 600;">
                            <i class="fas fa-sticky-note me-2" style="color: #059669;"></i>
                            ملاحظات إضافية
                        </h6>
                        <textarea style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb; min-height: 100px;"
                                  wire:model="invoiceNotes" placeholder="أي ملاحظات إضافية حول الفاتورة..."></textarea>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div style="padding: 20px 24px; background: #f9fafb; border-top: 1px solid #e5e7eb; border-radius: 0 0 10px 10px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button"
                            style="padding: 12px 24px; border: 1px solid #d1d5db; background: white; color: #374151; border-radius: 8px; cursor: pointer; font-weight: 500;"
                            wire:click="$set('showModal', false)">
                        إلغاء
                    </button>
                    <button type="button"
                            style="padding: 12px 24px; background: #059669; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 8px;"
                            wire:click="createInvoice">
                        <i class="fas fa-save"></i>
                        حفظ الفاتورة
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM loaded - invoices component');

                const flatpickrOptions = {
                    locale: 'ar',
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    clickOpens: true,
                    position: 'auto right',
                };

                flatpickr('#start_date', flatpickrOptions);
                flatpickr('#end_date', flatpickrOptions);

                // Close dropdowns when clicking outside
                document.addEventListener('click', function(e) {
                    // Close client dropdown if clicked outside
                    if (!e.target.closest('.client-search-container')) {
                        const clientDropdown = document.querySelector('.client-search-container .search-dropdown');
                        if (clientDropdown) {
                            clientDropdown.style.display = 'none';
                        }
                    }

                    // Close service dropdown if clicked outside
                    if (!e.target.closest('.service-search-container')) {
                        const serviceDropdown = document.querySelector('.service-search-container .search-dropdown');
                        if (serviceDropdown) {
                            serviceDropdown.style.display = 'none';
                        }
                    }
                });

                // Hide dropdowns after Livewire updates
                document.addEventListener('livewire:updated', function() {
                    const clientDropdown = document.querySelector('.client-search-container .search-dropdown');
                    const serviceDropdown = document.querySelector('.service-search-container .search-dropdown');

                    if (clientDropdown) clientDropdown.style.display = 'none';
                    if (serviceDropdown) serviceDropdown.style.display = 'none';
                });
            });

            // Function to close dropdowns manually
            function closeDropdowns() {
                const clientDropdown = document.querySelector('.client-search-container .search-dropdown');
                const serviceDropdown = document.querySelector('.service-search-container .search-dropdown');

                if (clientDropdown) clientDropdown.style.display = 'none';
                if (serviceDropdown) serviceDropdown.style.display = 'none';
            }
        </script>
    @endpush

