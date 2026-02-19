{{-- Batch Payment Modal --}}
<div class="modal fade" id="batchPaymentModal" tabindex="-1" aria-labelledby="batchPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e4a46,#2d6a65);">
                <h5 class="modal-title text-white" id="batchPaymentModalLabel">
                    <i class="bi bi-cash-stack me-2"></i>معالجة دفعات متعددة
                    <span class="badge bg-warning text-dark ms-2" id="batchSelectedCount">0</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning d-none" id="batchWpsWarning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>تحذير WPS:</strong> الحد الأقصى المسموح به هو <span id="batchWpsMaxPercentage">70</span>% من إجمالي الراتب لكل موظف
                </div>

                <div id="batchEmployeesList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>إلغاء
                </button>
                <button type="button" class="btn btn-success" id="confirmBatchPaymentBtn">
                    <i class="bi bi-check-circle me-1"></i>تأكيد معالجة الدفعات (<span id="batchConfirmCount">0</span>)
                </button>
            </div>
        </div>
    </div>
</div>
