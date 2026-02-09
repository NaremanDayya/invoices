<div class="modal fade" id="creditNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3">
            <div class="modal-header bg-gradient-primary text-white p-4">
                <h5 class="modal-title fw-bold d-flex align-items-center">
                    <i class="bi bi-file-earmark-text-fill ms-2"></i>
                    إنشاء إشعار دائن جديد
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="creditNoteForm" method="POST">
                @csrf
                <input type="hidden" name="invoice_id" id="cn_invoice_id">
                
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 mb-4">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle-fill fs-4 ms-2"></i>
                            <div>
                                <strong>معلومات الفاتورة:</strong>
                                <div class="mt-2">
                                    <span class="badge bg-primary me-2">رقم الفاتورة: <span id="cn_invoice_number"></span></span>
                                    <span class="badge bg-success">الإجمالي الحالي: <span id="cn_current_total"></span> ر.س</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">نوع الإشعار <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="">اختر النوع</option>
                                <option value="internal">داخلي (لنا)</option>
                                <option value="client">للعميل</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">المبلغ <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">السبب <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="2" required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-header bg-transparent border-0">
                                    <h6 class="mb-0 fw-bold">تعديل القيم (اختياري)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">المبلغ الأساسي الجديد</label>
                                            <input type="number" name="new_base_price" class="form-control" step="0.01" min="0" id="cn_new_base_price">
                                            <small class="text-muted">الحالي: <span id="cn_old_base_price"></span> ر.س</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">نسبة الضريبة الجديدة (%)</label>
                                            <input type="number" name="new_tax_rate" class="form-control" step="0.01" min="0" max="100" id="cn_new_tax_rate">
                                            <small class="text-muted">الحالي: <span id="cn_old_tax_rate"></span>%</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">عدد الموظفين الجديد</label>
                                            <input type="number" name="new_employees_count" class="form-control" min="0" id="cn_new_employees">
                                            <small class="text-muted">الحالي: <span id="cn_old_employees"></span></small>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">أيام العمل الجديدة</label>
                                            <input type="number" name="new_work_days" class="form-control" min="0" id="cn_new_work_days">
                                            <small class="text-muted">الحالي: <span id="cn_old_work_days"></span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-warning border-0 mb-0">
                                <i class="bi bi-exclamation-triangle-fill ms-2"></i>
                                <strong>تنبيه:</strong> سيتم تحديث الفاتورة بالقيم الجديدة وحفظ القيم السابقة في سجل الإشعارات الدائنة.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle ms-1"></i>
                        إنشاء الإشعار
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCreditNoteModal(invoiceId, invoiceNumber, currentTotal, basePrice, taxRate, employees, workDays) {
    document.getElementById('cn_invoice_id').value = invoiceId;
    document.getElementById('cn_invoice_number').textContent = invoiceNumber;
    document.getElementById('cn_current_total').textContent = new Intl.NumberFormat('ar-SA').format(currentTotal);
    document.getElementById('cn_old_base_price').textContent = new Intl.NumberFormat('ar-SA').format(basePrice);
    document.getElementById('cn_old_tax_rate').textContent = taxRate;
    document.getElementById('cn_old_employees').textContent = employees || 0;
    document.getElementById('cn_old_work_days').textContent = workDays || 0;
    
    const form = document.getElementById('creditNoteForm');
    form.action = `/invoices/${invoiceId}/credit-notes`;
    form.reset();
    
    const modal = new bootstrap.Modal(document.getElementById('creditNoteModal'));
    modal.show();
}

document.getElementById('creditNoteForm')?.addEventListener('submit', function(e) {
    const newBasePrice = parseFloat(document.getElementById('cn_new_base_price').value) || null;
    const newTaxRate = parseFloat(document.getElementById('cn_new_tax_rate').value) || null;
    
    if (newBasePrice !== null && newTaxRate !== null) {
        const newTotal = newBasePrice + (newBasePrice * newTaxRate / 100);
        if (newTotal < 0) {
            e.preventDefault();
            alert('المبلغ الإجمالي الجديد لا يمكن أن يكون سالباً');
            return false;
        }
    }
});
</script>
