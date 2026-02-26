{{-- Salary Pay Status Modal --}}
<div class="modal fade" id="salaryPayStatusModal" tabindex="-1" aria-labelledby="salaryPayStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #1e4a46, #2d6a65);">
                <h5 class="modal-title text-white fw-bold" id="salaryPayStatusModalLabel">
                    <i class="bi bi-tag-fill me-2"></i>تحديث حالة صرف الراتب
                    <span class="badge bg-warning text-dark ms-2" id="statusSelectedCount">0</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4">
                    <i class="bi bi-info-circle me-1"></i>
                    اختر حالة صرف الراتب للموظفين المحددين. سيتم إرسال إشعار تلقائي في سجل المحادثة.
                </p>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="salary-status-card w-100 cursor-pointer" for="statusFullPaid">
                            <input type="radio" class="d-none" name="salary_pay_status" id="statusFullPaid" value="full_paid">
                            <div class="status-card-inner text-center p-4 rounded-3 border-2 border" style="border-color: #e2e8f0; transition: all .2s;">
                                <div class="status-icon mb-2" style="font-size: 2.2rem;">✅</div>
                                <div class="fw-bold text-success">مدفوع بالكامل</div>
                                <small class="text-muted d-block mt-1">تم صرف الراتب كاملاً</small>
                            </div>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="salary-status-card w-100 cursor-pointer" for="statusPartialPaid">
                            <input type="radio" class="d-none" name="salary_pay_status" id="statusPartialPaid" value="partial_paid">
                            <div class="status-card-inner text-center p-4 rounded-3 border-2 border" style="border-color: #e2e8f0; transition: all .2s;">
                                <div class="status-icon mb-2" style="font-size: 2.2rem;">🔶</div>
                                <div class="fw-bold text-warning">مدفوع جزئياً</div>
                                <small class="text-muted d-block mt-1">تم صرف جزء من الراتب</small>
                            </div>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="salary-status-card w-100 cursor-pointer" for="statusPended">
                            <input type="radio" class="d-none" name="salary_pay_status" id="statusPended" value="pended">
                            <div class="status-card-inner text-center p-4 rounded-3 border-2 border" style="border-color: #e2e8f0; transition: all .2s;">
                                <div class="status-icon mb-2" style="font-size: 2.2rem;">⏸️</div>
                                <div class="fw-bold text-secondary">معلق (On Hold)</div>
                                <small class="text-muted d-block mt-1">الصرف في الانتظار</small>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="alert alert-info mt-4 d-none" id="statusPreviewAlert">
                    <i class="bi bi-bell-fill me-2"></i>
                    سيتم إرسال رسالة تلقائية في سجل المحادثة تُعلم المحاسب بالتحديث مع رابط للعرض.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>إلغاء
                </button>
                <button type="button" class="btn btn-primary px-4" id="confirmSalaryStatusBtn" disabled>
                    <i class="bi bi-check-circle me-1"></i>
                    تأكيد التحديث
                    <span class="ms-1">(<span id="statusConfirmCount">0</span> موظف)</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .salary-status-card input[type="radio"]:checked + .status-card-inner {
        border-color: #1e4a46 !important;
        background: #f0fdf4;
        box-shadow: 0 0 0 3px rgba(30, 74, 70, 0.15);
    }
    .salary-status-card .status-card-inner:hover {
        border-color: #2d6a65 !important;
        background: #f8fffe;
        cursor: pointer;
    }
    .cursor-pointer { cursor: pointer; }
</style>
