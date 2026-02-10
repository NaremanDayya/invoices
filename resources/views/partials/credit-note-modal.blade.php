<div class="modal fade" id="creditNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
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
                                    <span class="badge bg-success">المبلغ قبل الضريبة: <span id="cn_invoice_base_price"></span> ر.س</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">رقم الإشعار الدائن <span class="text-danger">*</span></label>
                            <input type="text" name="credit_note_number" id="cn_number" class="form-control" readonly>
                            <small class="text-muted">سيتم إنشاؤه تلقائياً</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">نوع الإشعار <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="">اختر النوع</option>
                                <option value="internal">داخلي (لنا)</option>
                                <option value="client">للعميل</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">مبلغ الخصم قبل الضريبة <span class="text-danger">*</span></label>
                            <input type="number" name="credit_amount_before_tax" id="cn_credit_before_tax" class="form-control" step="0.01" min="0.01" required>
                        </div>

                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-muted">المبلغ قبل الضريبة (الفاتورة)</label>
                                            <input type="text" id="cn_display_base_price" class="form-control bg-white" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-muted">مبلغ الخصم قبل الضريبة</label>
                                            <input type="text" id="cn_display_credit_before" class="form-control bg-white" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-success">مبلغ الخصم بعد الضريبة</label>
                                            <input type="text" id="cn_display_credit_after" class="form-control bg-white fw-bold text-success" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-primary">الإجمالي الجديد</label>
                                            <input type="text" id="cn_display_new_total" class="form-control bg-white fw-bold text-primary" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
let currentInvoiceData = {
    basePrice: 0,
    taxRate: 0,
    totalPrice: 0,
    invoiceNumber: '',
    creditNoteCount: 0
};

function openCreditNoteModal(invoiceId, invoiceNumber, currentTotal, basePrice, taxRate, employees, workDays) {
    currentInvoiceData = {
        basePrice: parseFloat(basePrice),
        taxRate: parseFloat(taxRate),
        totalPrice: parseFloat(currentTotal),
        invoiceNumber: invoiceNumber,
        creditNoteCount: 0
    };
    
    document.getElementById('cn_invoice_id').value = invoiceId;
    document.getElementById('cn_invoice_number').textContent = invoiceNumber;
    document.getElementById('cn_invoice_base_price').textContent = new Intl.NumberFormat('ar-SA').format(basePrice);
    document.getElementById('cn_old_base_price').textContent = new Intl.NumberFormat('ar-SA').format(basePrice);
    document.getElementById('cn_old_tax_rate').textContent = taxRate;
    document.getElementById('cn_old_employees').textContent = employees || 0;
    document.getElementById('cn_old_work_days').textContent = workDays || 0;
    
    // Display initial values
    document.getElementById('cn_display_base_price').value = new Intl.NumberFormat('ar-SA').format(basePrice) + ' ر.س';
    document.getElementById('cn_display_credit_before').value = '0.00 ر.س';
    document.getElementById('cn_display_credit_after').value = '0.00 ر.س';
    document.getElementById('cn_display_new_total').value = new Intl.NumberFormat('ar-SA').format(currentTotal) + ' ر.س';
    
    // Fetch credit note count and generate number
    fetch(`/credit-notes/invoice/${invoiceId}/count`)
        .then(response => response.json())
        .then(data => {
            currentInvoiceData.creditNoteCount = data.count || 0;
            generateCreditNoteNumber();
        })
        .catch(() => {
            currentInvoiceData.creditNoteCount = 0;
            generateCreditNoteNumber();
        });
    
    const form = document.getElementById('creditNoteForm');
    form.action = `/invoices/${invoiceId}/credit-notes`;
    form.reset();
    
    const modal = new bootstrap.Modal(document.getElementById('creditNoteModal'));
    modal.show();
}

function generateCreditNoteNumber() {
    const invoiceNum = currentInvoiceData.invoiceNumber.replace('INV-', '').replace('#', '');
    const creditNum = String(currentInvoiceData.creditNoteCount + 1).padStart(3, '0');
    const creditNoteNumber = `CN-${invoiceNum}-${creditNum}`;
    document.getElementById('cn_number').value = creditNoteNumber;
}

function calculateCreditAmounts() {
    const creditBeforeTax = parseFloat(document.getElementById('cn_credit_before_tax').value) || 0;
    const taxRate = currentInvoiceData.taxRate;
    const basePrice = currentInvoiceData.basePrice;
    const currentTotal = currentInvoiceData.totalPrice;
    
    // Calculate credit amount after tax
    const creditAfterTax = creditBeforeTax * (1 + (taxRate / 100));
    
    // Calculate new base price and total
    const newBasePrice = basePrice - creditBeforeTax;
    const newTaxAmount = newBasePrice * (taxRate / 100);
    const newTotal = newBasePrice + newTaxAmount;
    
    // Update display fields
    document.getElementById('cn_display_base_price').value = new Intl.NumberFormat('ar-SA', {minimumFractionDigits: 2}).format(basePrice) + ' ر.س';
    document.getElementById('cn_display_credit_before').value = new Intl.NumberFormat('ar-SA', {minimumFractionDigits: 2}).format(creditBeforeTax) + ' ر.س';
    document.getElementById('cn_display_credit_after').value = new Intl.NumberFormat('ar-SA', {minimumFractionDigits: 2}).format(creditAfterTax) + ' ر.س';
    document.getElementById('cn_display_new_total').value = new Intl.NumberFormat('ar-SA', {minimumFractionDigits: 2}).format(newTotal) + ' ر.س';
    
    // Auto-fill new base price if credit is entered
    if (creditBeforeTax > 0) {
        document.getElementById('cn_new_base_price').value = newBasePrice.toFixed(2);
    }
}

// Add event listener for credit amount input
document.getElementById('cn_credit_before_tax')?.addEventListener('input', calculateCreditAmounts);

document.getElementById('creditNoteForm')?.addEventListener('submit', function(e) {
    const creditBeforeTax = parseFloat(document.getElementById('cn_credit_before_tax').value) || 0;
    const newBasePrice = parseFloat(document.getElementById('cn_new_base_price').value) || null;
    const newTaxRate = parseFloat(document.getElementById('cn_new_tax_rate').value) || null;
    
    // Validate credit amount
    if (creditBeforeTax > currentInvoiceData.basePrice) {
        e.preventDefault();
        alert('مبلغ الخصم لا يمكن أن يكون أكبر من المبلغ الأساسي للفاتورة');
        return false;
    }
    
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
