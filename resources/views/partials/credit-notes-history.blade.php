<div class="card border-0 shadow-sm" id="credit-notes">
    <div class="card-header bg-gradient-primary text-white p-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold d-flex align-items-center">
                <i class="bi bi-file-earmark-text-fill ms-2"></i>
                سجل الإشعارات الدائنة
                @if($invoice->creditNotes->count() > 0)
                    <span class="badge bg-white text-primary ms-2">{{ $invoice->creditNotes->count() }}</span>
                @endif
            </h5>
            <button type="button" class="btn btn-light btn-sm" 
                    onclick="openCreditNoteModal(
                        {{ $invoice->id }}, 
                        '{{ $invoice->number }}', 
                        {{ $invoice->total_price }},
                        {{ $invoice->base_price }},
                        {{ $invoice->tax_rate }},
                        {{ $invoice->employees_count ?? 0 }},
                        {{ $invoice->work_days_count ?? 0 }}
                    )">
                <i class="bi bi-plus-lg ms-1"></i>
                إضافة إشعار دائن
            </button>
        </div>
    </div>

    <div class="card-body p-4">
        @if($invoice->creditNotes->count() > 0)
            <div class="timeline">
                @foreach($invoice->creditNotes->sortByDesc('created_at') as $index => $creditNote)
                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-{{ $creditNote->type === 'internal' ? 'primary' : 'success' }}">
                            <i class="bi bi-{{ $creditNote->type === 'internal' ? 'building' : 'person-fill' }} text-white"></i>
                        </div>
                        
                        <div class="timeline-content">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light border-0 p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 fw-bold">
                                                {{ $creditNote->credit_note_number }}
                                                <span class="badge bg-{{ $creditNote->type === 'internal' ? 'primary' : 'success' }} ms-2">
                                                    {{ $creditNote->type === 'internal' ? 'داخلي' : 'للعميل' }}
                                                </span>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 ms-1"></i>
                                                {{ $creditNote->created_at->locale('ar')->translatedFormat('d F Y - h:i A') }}
                                                @if($creditNote->creator)
                                                    <span class="mx-2">•</span>
                                                    <i class="bi bi-person-fill ms-1"></i>
                                                    {{ $creditNote->creator->name }}
                                                @endif
                                            </small>
                                        </div>
                                        <form action="{{ route('credit-notes.destroy', $creditNote) }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار الدائن؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="alert alert-info border-0 mb-0">
                                                <strong>السبب:</strong> {{ $creditNote->reason }}
                                                @if($creditNote->notes)
                                                    <br><strong>ملاحظات:</strong> {{ $creditNote->notes }}
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body p-3">
                                                    <h6 class="fw-bold text-muted mb-3">
                                                        <i class="bi bi-arrow-right-circle ms-1"></i>
                                                        القيم السابقة
                                                    </h6>
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">المبلغ الأساسي</small>
                                                            <strong>{{ number_format($creditNote->previous_values['base_price'] ?? 0, 0) }} ر.س</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">الضريبة</small>
                                                            <strong>{{ number_format($creditNote->previous_values['tax_amount'] ?? 0, 0) }} ر.س</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">الإجمالي</small>
                                                            <strong class="text-danger">{{ number_format($creditNote->previous_total, 0) }} ر.س</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">نسبة الضريبة</small>
                                                            <strong>{{ $creditNote->previous_values['tax_rate'] ?? 0 }}%</strong>
                                                        </div>
                                                        @if(isset($creditNote->previous_values['employees_count']))
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">عدد الموظفين</small>
                                                                <strong>{{ $creditNote->previous_values['employees_count'] }}</strong>
                                                            </div>
                                                        @endif
                                                        @if(isset($creditNote->previous_values['work_days_count']))
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">أيام العمل</small>
                                                                <strong>{{ $creditNote->previous_values['work_days_count'] }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card bg-success bg-opacity-10 border-success border-opacity-25 h-100">
                                                <div class="card-body p-3">
                                                    <h6 class="fw-bold text-success mb-3">
                                                        <i class="bi bi-arrow-left-circle ms-1"></i>
                                                        القيم الجديدة
                                                    </h6>
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">المبلغ الأساسي</small>
                                                            <strong>{{ number_format($creditNote->new_values['base_price'] ?? 0, 0) }} ر.س</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">الضريبة</small>
                                                            <strong>{{ number_format($creditNote->new_values['tax_amount'] ?? 0, 0) }} ر.س</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">الإجمالي</small>
                                                            <strong class="text-success">{{ number_format($creditNote->new_total, 0) }} ر.س</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">نسبة الضريبة</small>
                                                            <strong>{{ $creditNote->new_values['tax_rate'] ?? 0 }}%</strong>
                                                        </div>
                                                        @if(isset($creditNote->new_values['employees_count']))
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">عدد الموظفين</small>
                                                                <strong>{{ $creditNote->new_values['employees_count'] }}</strong>
                                                            </div>
                                                        @endif
                                                        @if(isset($creditNote->new_values['work_days_count']))
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">أيام العمل</small>
                                                                <strong>{{ $creditNote->new_values['work_days_count'] }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="alert alert-warning border-0 mb-0 d-flex align-items-center">
                                                <i class="bi bi-arrow-down-up fs-4 ms-2"></i>
                                                <div>
                                                    <strong>فرق المبلغ:</strong>
                                                    <span class="badge bg-warning text-dark ms-2">
                                                        {{ number_format($creditNote->amount_difference, 0) }} ر.س
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-text display-1 text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد إشعارات دائنة</h5>
                <p class="text-muted mb-4">لم يتم إنشاء أي إشعارات دائنة لهذه الفاتورة بعد</p>
                <button type="button" class="btn btn-primary" 
                        onclick="openCreditNoteModal(
                            {{ $invoice->id }}, 
                            '{{ $invoice->number }}', 
                            {{ $invoice->total_price }},
                            {{ $invoice->base_price }},
                            {{ $invoice->tax_rate }},
                            {{ $invoice->employees_count ?? 0 }},
                            {{ $invoice->work_days_count ?? 0 }}
                        )">
                    <i class="bi bi-plus-lg ms-1"></i>
                    إضافة إشعار دائن
                </button>
            </div>
        @endif
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-right: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    right: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #0d6efd, #198754);
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: absolute;
    right: -15px;
    top: 0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 1;
}

.timeline-content {
    margin-right: 30px;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
}
</style>
