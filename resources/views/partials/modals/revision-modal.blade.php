{{-- Revision Modal --}}
<div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e4a46,#2d6a65);">
                <h5 class="modal-title text-white" id="revisionModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>إتمام مراجعة الفاتورة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="revisionForm">
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-3">قرار المراجعة</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check p-0">
                                    <label class="d-block cursor-pointer">
                                        <input class="form-check-input d-none" type="radio"
                                               name="revision_status" value="revision_approved" id="revisionApproved">
                                        <div class="card border-2 h-100 p-3 text-center revision-option"
                                             style="border-color:#e2e8f0;cursor:pointer;transition:all .2s;"
                                             id="revisionApprovedCard">
                                            <i class="bi bi-patch-check-fill fs-2 text-success mb-2"></i>
                                            <div class="fw-bold text-success">قبول المراجعة</div>
                                            <small class="text-muted">الموافقة على بيانات الموظفين المرفوعة</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check p-0">
                                    <label class="d-block cursor-pointer">
                                        <input class="form-check-input d-none" type="radio"
                                               name="revision_status" value="revision_rejected" id="revisionRejected">
                                        <div class="card border-2 h-100 p-3 text-center revision-option"
                                             style="border-color:#e2e8f0;cursor:pointer;transition:all .2s;"
                                             id="revisionRejectedCard">
                                            <i class="bi bi-patch-exclamation-fill fs-2 text-danger mb-2"></i>
                                            <div class="fw-bold text-danger">رفض المراجعة</div>
                                            <small class="text-muted">رفض البيانات وحذف الموظفين لإعادة الرفع</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="revisionNotes" class="form-label fw-bold">
                            ملاحظات المراجعة
                            <span class="text-danger" id="revisionNotesRequired" style="display:none;">*</span>
                        </label>
                        <textarea class="form-control" id="revisionNotes" name="revision_notes" rows="4"
                                  placeholder="أدخل ملاحظاتك على المراجعة..."></textarea>
                        <div class="form-text" id="revisionNotesHint">اختياري عند القبول، إلزامي عند الرفض.</div>
                    </div>
                    <div class="modal-footer border-0 px-0 pb-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>إلغاء
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitRevisionBtn">
                            <i class="bi bi-send me-1"></i>إرسال المراجعة
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="display:none;"></div>
        </div>
    </div>
</div>

<style>
#revisionApprovedCard.selected { border-color: #198754 !important; background: #f0fdf4; }
#revisionRejectedCard.selected { border-color: #dc3545 !important; background: #fef2f2; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="revision_status"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('revisionApprovedCard').classList.remove('selected');
            document.getElementById('revisionRejectedCard').classList.remove('selected');

            if (this.value === 'revision_approved') {
                document.getElementById('revisionApprovedCard').classList.add('selected');
                document.getElementById('revisionNotesRequired').style.display = 'none';
            } else {
                document.getElementById('revisionRejectedCard').classList.add('selected');
                document.getElementById('revisionNotesRequired').style.display = 'inline';
            }
        });
    });

    document.querySelectorAll('.revision-option').forEach(card => {
        card.addEventListener('click', function() {
            const radio = this.closest('label').querySelector('input[type="radio"]');
            if (radio) { radio.checked = true; radio.dispatchEvent(new Event('change')); }
        });
    });
});
</script>
