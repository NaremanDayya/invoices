<!-- Payment Processing Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="bi bi-cash-coin me-2"></i>معالجة دفعات الرواتب
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>تم تحديد <span id="selectedCount">0</span> موظف</strong>
                </div>

                <form id="paymentForm">
                    <div id="selectedEmployeesList" class="mb-4">
                        <!-- Selected employees will be dynamically added here -->
                    </div>

                    <div class="alert alert-warning" id="wpsWarning" style="display: none;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>تحذير WPS:</strong> الحد الأقصى المسموح به هو <span id="wpsMaxPercentage">70</span>% من إجمالي الراتب
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>إلغاء
                </button>
                <button type="button" class="btn btn-success" id="processPaymentBtn">
                    <i class="bi bi-check-circle me-2"></i>معالجة الدفعات
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.employee-payment-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
}

.employee-payment-card.wps-mode {
    border-color: #ffc107;
    background: #fff9e6;
}

.employee-payment-card h6 {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 12px;
}

.payment-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.payment-info .label {
    color: #718096;
    font-weight: 500;
}

.payment-info .value {
    color: #2d3748;
    font-weight: 600;
}

.wps-indicator {
    display: inline-block;
    background: #ffc107;
    color: #000;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-right: 8px;
}

.monthly-indicator {
    display: inline-block;
    background: #10a37f;
    color: #fff;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-right: 8px;
}
</style>

<script>
let selectedEmployees = [];
let wpsMaxPercentage = 70;

// Load WPS settings
async function loadWpsSettings() {
    try {
        const response = await fetch('/salary-invoices/wps-settings');
        const data = await response.json();
        if (data.success) {
            wpsMaxPercentage = data.wps_max_percentage;
            document.getElementById('wpsMaxPercentage').textContent = wpsMaxPercentage;
        }
    } catch (error) {
        console.error('Error loading WPS settings:', error);
    }
}

// Show payment modal
function showPaymentModal() {
    const checkboxes = document.querySelectorAll('.employee-checkbox:checked');

    if (checkboxes.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'تحذير',
            text: 'يرجى تحديد موظف واحد على الأقل',
            confirmButtonText: 'حسناً'
        });
        return;
    }

    selectedEmployees = [];
    checkboxes.forEach(checkbox => {
        const employeeData = {
            id: checkbox.value,
            name: checkbox.dataset.employeeName,
            total_salary: parseFloat(checkbox.dataset.totalSalary),
            total_paid: parseFloat(checkbox.dataset.totalPaid),
            remaining_amount: parseFloat(checkbox.dataset.remaining),
            salary_type: checkbox.dataset.salaryType,
            wps_paid: parseFloat(checkbox.dataset.wpsPaid || 0)
        };
        selectedEmployees.push(employeeData);
    });

    document.getElementById('selectedCount').textContent = selectedEmployees.length;
    renderSelectedEmployees();

    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

// Render selected employees in modal
// Render selected employees in modal
function renderSelectedEmployees() {
    const container = document.getElementById('selectedEmployeesList');
    container.innerHTML = '';

    selectedEmployees.forEach((employee, index) => {
        // Get current WPS paid amount from server (you might need to fetch this)
        // For now, we'll use the value from the checkbox data
        const currentWpsPaid = employee.wps_paid || 0;
        const maxWpsAmount = (employee.total_salary * wpsMaxPercentage) / 100;
        const remainingWpsAllowed = maxWpsAmount - currentWpsPaid;
        const maxWpsForRemaining = Math.min(remainingWpsAllowed, employee.remaining_amount);

        const card = document.createElement('div');
        card.className = 'employee-payment-card';
        card.innerHTML = `
            <h6>
                <i class="bi bi-person-circle me-2"></i>${employee.name}
                <span class="${employee.salary_type === 'wps' ? 'wps-indicator' : 'monthly-indicator'}">
                    ${employee.salary_type === 'wps' ? 'WPS' : 'شهري'}
                </span>
            </h6>

            <div class="payment-info">
                <span class="label">إجمالي الراتب:</span>
                <span class="value">${formatNumber(employee.total_salary)} ريال</span>
            </div>
            <div class="payment-info">
                <span class="label">المبلغ المتبقي:</span>
                <span class="value text-danger">${formatNumber(employee.remaining_amount)} ريال</span>
            </div>
            <div class="payment-info">
                <span class="label">المدفوع عبر WPS سابقاً:</span>
                <span class="value text-info">${formatNumber(currentWpsPaid)} ريال</span>
            </div>
            <div class="payment-info">
                <span class="label">الحد المتبقي لـ WPS:</span>
                <span class="value text-warning">${formatNumber(maxWpsAmount)} ريال (${wpsMaxPercentage}%)</span>
            </div>
            <div class="payment-info">
                <span class="label">المتبقي المسموح لـ WPS:</span>
                <span class="value text-primary">${formatNumber(remainingWpsAllowed)} ريال</span>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">نوع الدفع</label>
                    <select class="form-select payment-type" data-index="${index}" required>
                        <option value="full">دفع كامل</option>
                        <option value="partial">دفع جزئي</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">وضع الدفع</label>
                    <select class="form-select payment-mode" data-index="${index}" required>
                        <option value="monthly">شهري</option>
                        <option value="wps">WPS</option>
                    </select>
                </div>
            </div>

            <div class="row partial-amount-section" data-index="${index}" style="display: none;">
                <div class="col-12">
                    <label class="form-label fw-bold">المبلغ</label>
                    <input type="number"
                           class="form-control payment-amount"
                           data-index="${index}"
                           step="0.01"
                           min="0.01"
                           max="${employee.remaining_amount}"
                           placeholder="أدخل المبلغ">
                    <small class="text-muted">الحد الأقصى: ${formatNumber(employee.remaining_amount)} ريال</small>
                    <div class="wps-limit-info" data-index="${index}" style="display: none; margin-top: 8px;">
                        <small class="text-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            الحد الأقصى المتبقي لـ WPS: ${formatNumber(remainingWpsAllowed)} ريال (من أصل ${formatNumber(maxWpsAmount)})
                        </small>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <label class="form-label">ملاحظات (اختياري)</label>
                    <textarea class="form-control payment-notes"
                              data-index="${index}"
                              rows="2"
                              placeholder="أضف ملاحظات إن وجدت"></textarea>
                </div>
            </div>
        `;

        container.appendChild(card);
    });

    attachEventListeners();
}
// Attach event listeners to form elements
function attachEventListeners() {
    // Payment type change
    document.querySelectorAll('.payment-type').forEach(select => {
        select.addEventListener('change', function() {
            const index = this.dataset.index;
            const partialSection = document.querySelector(`.partial-amount-section[data-index="${index}"]`);

            if (this.value === 'partial') {
                partialSection.style.display = 'block';
            } else {
                partialSection.style.display = 'none';
            }
        });
    });

    // Payment mode change
    document.querySelectorAll('.payment-mode').forEach(select => {
        select.addEventListener('change', function() {
            const index = this.dataset.index;
            const wpsLimitInfo = document.querySelector(`.wps-limit-info[data-index="${index}"]`);
            const card = this.closest('.employee-payment-card');

            if (this.value === 'wps') {
                wpsLimitInfo.style.display = 'block';
                card.classList.add('wps-mode');
                document.getElementById('wpsWarning').style.display = 'block';
            } else {
                wpsLimitInfo.style.display = 'none';
                card.classList.remove('wps-mode');

                // Check if any other employee has WPS mode
                const hasWps = Array.from(document.querySelectorAll('.payment-mode'))
                    .some(s => s.value === 'wps');
                if (!hasWps) {
                    document.getElementById('wpsWarning').style.display = 'none';
                }
            }
        });
    });

// Amount validation
    document.querySelectorAll('.payment-amount').forEach(input => {
        input.addEventListener('input', function() {
            const index = this.dataset.index;
            const employee = selectedEmployees[index];
            const paymentMode = document.querySelector(`.payment-mode[data-index="${index}"]`).value;
            const amount = parseFloat(this.value);

            if (isNaN(amount) || amount <= 0) {
                this.setCustomValidity('المبلغ يجب أن يكون أكبر من صفر');
                return;
            }

            if (amount > employee.remaining_amount) {
                this.setCustomValidity(`المبلغ يتجاوز المبلغ المتبقي (${formatNumber(employee.remaining_amount)} ريال)`);
                return;
            }

            if (paymentMode === 'wps') {
                const currentWpsPaid = employee.wps_paid || 0;
                const maxWpsAmount = (employee.total_salary * wpsMaxPercentage) / 100;
                const remainingWpsAllowed = maxWpsAmount - currentWpsPaid;

                if (amount > remainingWpsAllowed) {
                    this.setCustomValidity(
                        `المبلغ يتجاوز الحد المتبقي المسموح به لـ WPS. ` +
                        `المدفوع سابقاً: ${formatNumber(currentWpsPaid)} ريال, ` +
                        `الحد الأقصى: ${formatNumber(maxWpsAmount)} ريال, ` +
                        `المتبقي: ${formatNumber(remainingWpsAllowed)} ريال`
                    );
                    return;
                }
            }

            this.setCustomValidity('');
        });
    });
}

// Process payments
document.getElementById('processPaymentBtn').addEventListener('click', async function() {
    const payments = [];
    let hasErrors = false;

    selectedEmployees.forEach((employee, index) => {
        const paymentType = document.querySelector(`.payment-type[data-index="${index}"]`).value;
        const paymentMode = document.querySelector(`.payment-mode[data-index="${index}"]`).value;
        const notes = document.querySelector(`.payment-notes[data-index="${index}"]`).value;

        let amount = employee.remaining_amount;

        if (paymentType === 'partial') {
            const amountInput = document.querySelector(`.payment-amount[data-index="${index}"]`);
            amount = parseFloat(amountInput.value);

            if (!amount || amount <= 0) {
                hasErrors = true;
                amountInput.classList.add('is-invalid');
                return;
            }

            if (amount > employee.remaining_amount) {
                hasErrors = true;
                amountInput.classList.add('is-invalid');
                return;
            }

            if (paymentMode === 'wps') {
                const maxWpsAmount = (employee.total_salary * wpsMaxPercentage) / 100;
                const remainingWpsAllowance = maxWpsAmount - employee.wps_paid;
                const maxWpsForRemaining = Math.min(remainingWpsAllowance, employee.remaining_amount);

                if (amount > maxWpsForRemaining) {
                    hasErrors = true;
                    amountInput.classList.add('is-invalid');
                    return;
                }
            }
        }

        payments.push({
            employee_id: employee.id,
            payment_type: paymentType,
            payment_mode: paymentMode,
            amount: amount,
            notes: notes
        });
    });

    if (hasErrors) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ في البيانات',
            text: 'يرجى التحقق من المبالغ المدخلة',
            confirmButtonText: 'حسناً'
        });
        return;
    }

    // Confirm before processing
    const result = await Swal.fire({
        title: 'تأكيد معالجة الدفعات',
        text: `هل أنت متأكد من معالجة ${payments.length} دفعة؟`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، معالجة',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#10a37f',
        cancelButtonColor: '#6c757d'
    });

    if (!result.isConfirmed) return;

    // Show loading
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري المعالجة...';

    try {
        const response = await fetch(`/salary-invoices/{{ $invoice->id }}/process-payments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ payments })
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'نجح!',
                text: data.message,
                confirmButtonText: 'حسناً'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'فشل',
                text: data.message,
                confirmButtonText: 'حسناً'
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'حدث خطأ أثناء معالجة الدفعات',
            confirmButtonText: 'حسناً'
        });
    } finally {
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-check-circle me-2"></i>معالجة الدفعات';
    }
});

// Utility function
function formatNumber(num) {
    return new Intl.NumberFormat('ar-SA', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(num);
}

// Load WPS settings on page load
document.addEventListener('DOMContentLoaded', loadWpsSettings);
</script>
