{{-- Single Employee Payment Modal --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e4a46,#2d6a65);">
                <h5 class="modal-title text-white" id="paymentModalLabel">
                    <i class="bi bi-cash-coin me-2"></i>معالجة دفعة الراتب
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="employeeId">

                <div class="alert alert-warning d-none" id="wpsWarning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>تحذير WPS:</strong> الحد الأقصى المسموح به هو <span id="wpsMaxPercentageDisplay">70</span>% من إجمالي الراتب
                </div>

                <div id="singleEmployeeCard"></div>

                <div id="singleAmountError" class="text-danger small mt-1"></div>

                <form id="paymentForm" class="mt-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">ملاحظات (اختياري)</label>
                            <textarea class="form-control" id="paymentNotes" rows="2"
                                      placeholder="أضف ملاحظات على هذه الدفعة..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>إلغاء
                </button>
                <button type="submit" form="paymentForm" class="btn btn-success" id="submitPaymentBtn">
                    <i class="bi bi-check-circle me-1"></i>تأكيد الدفع
                </button>
            </div>
        </div>
    </div>
</div>
