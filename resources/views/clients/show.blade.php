@extends('layouts.master')

@section('title', 'تفاصيل العميل - ' . $client->name)
@section('page_title', 'ملف العميل')
@section('page_subtitle', 'عرض تقارير الأداء وإعدادات الدفع للعميل')

@section('page_actions')
    <div class="d-flex gap-2">
        <button class="btn btn-light rounded-xl px-4 py-2 fw-bold border" data-bs-toggle="modal" data-bs-target="#editClientModal{{ $client->id }}">
            <i class="bi bi-gear me-2"></i>
            <span>إعدادات العميل</span>
        </button>
        <a href="{{ route('clients.index') }}" class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-arrow-right"></i>
            <span>العودة للقائمة</span>
        </a>
    </div>
@endsection

@section('content')
    <!-- Client Header with Logo -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="d-flex align-items-center gap-3">
                    @if($client->logo)
                        <img src="{{ asset('storage/' . $client->logo) }}" alt="{{ $client->name }}" class="rounded" style="width: 80px; height: 80px; object-fit: contain; border: 2px solid #f0f0f0; padding: 8px;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-building fs-1 text-muted"></i>
                        </div>
                    @endif
                    <div>
                        <h3 class="fw-bold mb-1">{{ $client->name }}</h3>
                        <div class="d-flex gap-3 text-muted small">
                            @if($client->email)
                                <span><i class="bi bi-envelope me-1"></i>{{ $client->email }}</span>
                            @endif
                            @if($client->phone)
                                <span><i class="bi bi-telephone me-1"></i>{{ $client->phone }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Client Stats Overview -->
        <div class="col-12">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm h-100">
                        <p class="text-muted small mb-1">إجمالي الفواتير</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $stats['total_invoices'] }}</h3>
                        <div class="mt-2 small text-success">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            {{ $stats['paid_invoices'] }} مدفوعة
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm h-100">
                        <p class="text-muted small mb-1">المبالغ المستحقة</p>
                        <h3 class="fw-bold mb-0 text-danger">{{ number_format($stats['total_amount'] - $stats['paid_amount'], 0) }} ر.س</h3>
                        <div class="mt-2 small text-danger">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>
                            {{ $stats['pending_invoices'] }} فواتير معلقة
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm h-100">
                        <p class="text-muted small mb-1">يوم الدفع المعتاد</p>
                        <h3 class="fw-bold mb-0 text-primary-accent">{{ $client->default_payment_day ?? '1' }} للشهر</h3>
                        <div class="mt-2 small text-muted">
                            <i class="bi bi-calendar-event me-1"></i>
                            تاريخ السداد الشهري
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm h-100">
                        <p class="text-muted small mb-1">فترة السماح</p>
                        <h3 class="fw-bold mb-0 text-warning">{{ $client->grace_period_days ?? '0' }} يوم</h3>
                        <div class="mt-2 small text-muted">
                            <i class="bi bi-clock-history me-1"></i>
                            تأخير مسموح بالدفع
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-8">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-4">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0 fw-bold">آخر الفواتير</h5>
                    <a href="{{ route('invoices.index', ['client_id' => $client->id]) }}" class="small text-primary-accent text-decoration-none fw-bold">عرض الكل</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-0 small text-muted">رقم الفاتورة</th>
                                <th class="px-4 py-3 border-0 small text-muted text-center">التاريخ</th>
                                <th class="px-4 py-3 border-0 small text-muted text-center">المبلغ</th>
                                <th class="px-4 py-3 border-0 small text-muted text-center">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->invoices as $invoice)
                                <tr>
                                    <td class="px-4 py-3 fw-bold">{{ $invoice->number }}</td>
                                    <td class="px-4 py-3 text-center text-muted small">{{ $invoice->generation_date }}</td>
                                    <td class="px-4 py-3 text-center fw-bold">{{ number_format($invoice->total_price, 0) }} ر.س</td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $statusClass = [
                                                'paid' => 'success',
                                                'pending' => 'warning',
                                                'late' => 'danger',
                                                'overdue' => 'danger',
                                                'cancelled' => 'secondary'
                                            ][$invoice->payment_status] ?? 'warning';
                                            
                                            $statusLabel = [
                                                'paid' => 'مدفوعة',
                                                'pending' => 'معلقة',
                                                'late' => 'متأخرة',
                                                'overdue' => 'متجاوزة',
                                                'cancelled' => 'ملغاة'
                                            ][$invoice->payment_status] ?? 'معلقة';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}-soft text-{{ $statusClass }} rounded-pill px-3">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        لا توجد فواتير لهذا العميل حتى الآن
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Performance Report Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <h5 class="fw-bold mb-4">تقرير الالتزام المالي</h5>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted">نسبة الفواتير المدفوعة</span>
                                <span class="small fw-bold">{{ $stats['total_invoices'] > 0 ? round(($stats['paid_invoices'] / $stats['total_invoices']) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stats['total_invoices'] > 0 ? ($stats['paid_invoices'] / $stats['total_invoices']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted">نسبة المبالغ المحصلة</span>
                                <span class="small fw-bold">{{ $stats['total_amount'] > 0 ? round(($stats['paid_amount'] / $stats['total_amount']) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 10px;">
                                <div class="progress-bar bg-primary-accent" role="progressbar" style="width: {{ $stats['total_amount'] > 0 ? ($stats['paid_amount'] / $stats['total_amount']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-center border-start">
                        <div class="p-3">
                            <div class="text-muted small mb-1">التقييم الائتماني</div>
                            @php
                                $ratio = $stats['total_invoices'] > 0 ? ($stats['paid_invoices'] / $stats['total_invoices']) : 0;
                                if($ratio >= 0.9) { $rank = 'A+'; $c = 'success'; }
                                elseif($ratio >= 0.7) { $rank = 'B'; $c = 'primary'; }
                                elseif($ratio >= 0.5) { $rank = 'C'; $c = 'warning'; }
                                else { $rank = 'D'; $c = 'danger'; }
                            @endphp
                            <h1 class="display-3 fw-bold text-{{ $c }} mb-0">{{ $rank }}</h1>
                            <p class="text-muted small">بناءً على تاريخ السداد</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4">معلومات العميل</h5>
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-light rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-person fs-3 text-muted"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">{{ $client->name }}</h6>
                        <span class="badge bg-success-soft text-success">عميل نشط</span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="bi bi-envelope text-muted mt-1"></i>
                        <div>
                            <div class="small text-muted">البريد الإلكتروني</div>
                            <div class="fw-medium">{{ $client->email ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="bi bi-telephone text-muted mt-1"></i>
                        <div>
                            <div class="small text-muted">رقم الهاتف</div>
                            <div class="fw-medium" dir="ltr text-end">{{ $client->phone ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-geo-alt text-muted mt-1"></i>
                        <div>
                            <div class="small text-muted">العنوان الوطني</div>
                            <div class="fw-medium">{{ $client->address ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Rules Card -->
            <div class="bg-primary-soft rounded-xl p-4 border border-primary-light">
                <h5 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="bi bi-shield-check text-primary-accent me-2"></i>
                    قواعد الدفع
                </h5>
                <p class="small text-muted mb-4 text-justify">
                    هذا العميل يتبع القواعد التالية في إصدار الفواتير والتحصيل المالي السنوي والشهري:
                </p>
                <ul class="list-unstyled small space-y-3 mb-0">
                    <li class="d-flex justify-content-between mb-2">
                        <span class="text-muted">موعد الدفع الشهري:</span>
                        <span class="fw-bold">يوم {{ $client->default_payment_day ?? '1' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-2">
                        <span class="text-muted">فترة السماح القصوى:</span>
                        <span class="fw-bold text-danger">{{ $client->grace_period_days ?? '0' }} أيام</span>
                    </li>
                    <li class="d-flex justify-content-between mb-2">
                        <span class="text-muted">تاريخ الانضمام:</span>
                        <span class="fw-bold">{{ $client->created_at->format('Y-m-d') }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">إصدار آلي للفواتير:</span>
                        <span class="fw-bold text-success">مفعل</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Edit/Settings Modal -->
    <div class="modal fade" id="editClientModal{{ $client->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-2xl">
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="modal-title fw-bold">إعدادات العميل والدفع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('clients.update', $client) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">اسم العميل</label>
                                <input type="text" name="name" class="form-control rounded-xl py-2 px-3" value="{{ $client->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">يوم الدفع المفضل</label>
                                <input type="number" name="default_payment_day" class="form-control rounded-xl py-2 px-3" value="{{ $client->default_payment_day }}" min="1" max="31">
                                <div class="form-text small">يوم من كل شهر (1-31)</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">أيام السماح للتأخير</label>
                                <input type="number" name="grace_period_days" class="form-control rounded-xl py-2 px-3" value="{{ $client->grace_period_days }}" min="0">
                                <div class="form-text small">عدد أيام التأخير المسموحة</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control rounded-xl py-2 px-3" value="{{ $client->email }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">الهاتف</label>
                                <input type="text" name="phone" class="form-control rounded-xl py-2 px-3" value="{{ $client->phone }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-xl px-4" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold">حفظ الإعدادات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
