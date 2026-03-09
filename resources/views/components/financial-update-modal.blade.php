@props(['invoiceId' => null, 'paymentId' => null, 'clientId' => null])

<div class="modal fade" id="financialUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #1e4a46 0%, #2d6a65 100%); border-radius: 20px 20px 0 0; padding: 24px;">
                <div class="text-white">
                    <h5 class="modal-title fw-bold mb-1">
                        <i class="bi bi-cash-coin me-2"></i>
                        إضافة تحديث مالي
                    </h5>
                    <p class="mb-0 small opacity-75">تسجيل التحديثات والملاحظات المالية</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="financialUpdateForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">
                                <i class="bi bi-tag me-1"></i>نوع التحديث <span class="text-danger">*</span>
                            </label>
                            <select name="update_type" class="form-select rounded-xl" required>
                                <option value="">اختر النوع</option>
                                <option value="payment_received">دفعة مستلمة</option>
                                <option value="payment_delayed">تأخير في الدفع</option>
                                <option value="invoice_adjustment">تعديل فاتورة</option>
                                <option value="credit_note">إشعار دائن</option>
                                <option value="discount_applied">خصم مطبق</option>
                                <option value="penalty_applied">غرامة مطبقة</option>
                                <option value="general">عام</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">
                                <i class="bi bi-calendar-event me-1"></i>تاريخ التحديث <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="update_date" class="form-control rounded-xl" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">
                                <i class="bi bi-pencil me-1"></i>عنوان التحديث <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" class="form-control rounded-xl" placeholder="مثال: دفعة جزئية من العميل" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">
                                <i class="bi bi-text-paragraph me-1"></i>الوصف
                            </label>
                            <textarea name="description" class="form-control rounded-xl" rows="3" placeholder="أدخل تفاصيل التحديث المالي..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">
                                <i class="bi bi-currency-dollar me-1"></i>المبلغ (ريال)
                            </label>
                            <input type="number" name="amount" class="form-control rounded-xl" step="0.01" min="0" placeholder="0.00">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">
                                <i class="bi bi-check-circle me-1"></i>الحالة
                            </label>
                            <select name="status" class="form-select rounded-xl">
                                <option value="active">نشط</option>
                                <option value="archived">مؤرشف</option>
                            </select>
                        </div>

                        <input type="hidden" name="invoice_id" value="{{ $invoiceId }}">
                        <input type="hidden" name="payment_id" value="{{ $paymentId }}">
                        <input type="hidden" name="client_id" value="{{ $clientId }}">
                    </div>

                    <div id="updateAlert" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-xl px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary rounded-xl px-4">
                        <i class="bi bi-check-lg me-2"></i>حفظ التحديث
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="financialUpdatesDisplay" class="mt-4 d-none">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-0 py-3">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-clock-history me-2"></i>التحديثات المالية
            </h6>
        </div>
        <div class="card-body p-0">
            <div id="updatesTimeline" class="p-3">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('financialUpdateForm');
    const modal = document.getElementById('financialUpdateModal');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            fetch('/api/financial-updates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showAlert('success', result.message);
                    form.reset();
                    
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(modal).hide();
                        loadFinancialUpdates();
                    }, 1500);
                } else {
                    showAlert('danger', result.message || 'حدث خطأ أثناء الحفظ');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'حدث خطأ في الاتصال بالخادم');
            });
        });
    }
});

function showAlert(type, message) {
    const alertDiv = document.getElementById('updateAlert');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.classList.remove('d-none');
    
    setTimeout(() => {
        alertDiv.classList.add('d-none');
    }, 3000);
}

function loadFinancialUpdates() {
    const invoiceId = document.querySelector('[name="invoice_id"]')?.value;
    const paymentId = document.querySelector('[name="payment_id"]')?.value;
    
    let url = '';
    if (invoiceId) {
        url = `/api/financial-updates/invoice/${invoiceId}`;
    } else if (paymentId) {
        url = `/api/financial-updates/payment/${paymentId}`;
    }
    
    if (!url) return;
    
    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success && result.updates.length > 0) {
            displayFinancialUpdates(result.updates);
        }
    })
    .catch(error => console.error('Error loading updates:', error));
}

function displayFinancialUpdates(updates) {
    const container = document.getElementById('updatesTimeline');
    const display = document.getElementById('financialUpdatesDisplay');
    
    if (!container || !updates.length) return;
    
    container.innerHTML = updates.map(update => `
        <div class="update-item mb-3 pb-3 border-bottom">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="mb-1 fw-bold">${update.title}</h6>
                    <small class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>${update.update_date}
                        <span class="mx-2">•</span>
                        <i class="bi bi-person me-1"></i>${update.creator?.name || 'غير معروف'}
                    </small>
                </div>
                ${update.amount ? `<span class="badge bg-success">${parseFloat(update.amount).toLocaleString()} ر.س</span>` : ''}
            </div>
            ${update.description ? `<p class="mb-0 small text-muted">${update.description}</p>` : ''}
        </div>
    `).join('');
    
    display.classList.remove('d-none');
}
</script>
@endpush
