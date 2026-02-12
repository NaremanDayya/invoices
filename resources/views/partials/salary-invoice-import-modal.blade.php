<div class="modal fade" id="salaryImportModal" tabindex="-1" aria-labelledby="salaryImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="salaryImportModalLabel">
                    <i class="bi bi-upload me-2"></i>استيراد موظفي الرواتب
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info mb-4">
                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>تعليمات الاستيراد:</h6>
                    <ul class="mb-2">
                        <li>يجب أن يحتوي الملف على العناوين التالية بالضبط</li>
                        <li>صيغة الملف: Excel (.xlsx أو .xls)</li>
                        <li>الحد الأقصى لحجم الملف: 10 ميجابايت</li>
                        <li>تأكد من صحة أرقام الآيبان (تبدأ بـ SA وتحتوي على 24 حرف)</li>
                    </ul>
                    <a href="{{ route('salary-invoices.download-template') }}" 
                       class="btn btn-sm btn-primary">
                        <i class="bi bi-download me-2"></i>تحميل قالب Excel
                    </a>
                </div>

                <form id="salaryImportForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="invoice_id" id="import_invoice_id">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">اختر ملف Excel</label>
                        <div class="border border-2 border-dashed rounded p-5 text-center" style="border-color: #dee2e6;">
                            <i class="bi bi-file-earmark-excel text-success" style="font-size: 3rem;"></i>
                            <div class="mt-3">
                                <label for="excel_file" class="btn btn-outline-primary">
                                    <i class="bi bi-folder2-open me-2"></i>اختر ملف
                                    <input id="excel_file" name="excel_file" type="file" class="d-none" accept=".xlsx,.xls" required>
                                </label>
                            </div>
                            <p class="text-muted small mt-2">XLSX, XLS حتى 10MB</p>
                        </div>
                        <div id="fileName" class="mt-2 text-muted small"></div>
                    </div>

                    <div id="importProgress" class="d-none mb-4">
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: 0%" 
                                 id="progressBar">0%</div>
                        </div>
                        <p class="text-center text-muted mt-2" id="progressText">جاري الاستيراد...</p>
                    </div>

                    <div id="importResult" class="d-none mb-4"></div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>إلغاء
                </button>
                <button type="button" id="importBtn" class="btn btn-primary" onclick="submitImportForm()">
                    <i class="bi bi-upload me-2"></i>استيراد الموظفين
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openSalaryImportModal(invoiceId) {
    document.getElementById('import_invoice_id').value = invoiceId;
    const modal = new bootstrap.Modal(document.getElementById('salaryImportModal'));
    modal.show();
    document.getElementById('salaryImportForm').reset();
    document.getElementById('fileName').textContent = '';
    document.getElementById('importResult').classList.add('d-none');
    document.getElementById('importProgress').classList.add('d-none');
}

document.getElementById('excel_file').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
        document.getElementById('fileName').textContent = 'الملف المحدد: ' + fileName;
    }
});

function submitImportForm() {
    const form = document.getElementById('salaryImportForm');
    const formData = new FormData(form);
    const importBtn = document.getElementById('importBtn');
    const progressDiv = document.getElementById('importProgress');
    const progressBar = document.getElementById('progressBar');
    const resultDiv = document.getElementById('importResult');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    importBtn.disabled = true;
    importBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري الاستيراد...';
    progressDiv.classList.remove('d-none');
    resultDiv.classList.add('d-none');
    progressBar.style.width = '30%';
    progressBar.textContent = '30%';
    
    fetch('{{ route("salary-invoices.import") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        progressBar.style.width = '70%';
        progressBar.textContent = '70%';
        return response.json();
    })
    .then(data => {
        progressBar.style.width = '100%';
        progressBar.textContent = '100%';
        
        setTimeout(() => {
            progressDiv.classList.add('d-none');
            
            if (data.success) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                            <div>
                                <h6 class="fw-bold mb-2">${data.message}</h6>
                                <div class="small">
                                    <p class="mb-1">عدد الموظفين: ${data.data.employees_count}</p>
                                    <p class="mb-1">إجمالي الرواتب: ${parseFloat(data.data.total_salaries).toFixed(2)} ريال</p>
                                    <p class="mb-1">إجمالي الخصومات: ${parseFloat(data.data.total_deductions).toFixed(2)} ريال</p>
                                    <p class="mb-0">صافي الرواتب: ${parseFloat(data.data.total_net_salaries).toFixed(2)} ريال</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                resultDiv.classList.remove('d-none');
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-circle-fill fs-4 me-2"></i>
                            <p class="mb-0 fw-bold">${data.message}</p>
                        </div>
                    </div>
                `;
                resultDiv.classList.remove('d-none');
                importBtn.disabled = false;
                importBtn.innerHTML = '<i class="bi bi-upload me-2"></i>استيراد الموظفين';
            }
        }, 500);
    })
    .catch(error => {
        progressDiv.classList.add('d-none');
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                حدث خطأ أثناء الاستيراد. يرجى المحاولة مرة أخرى.
            </div>
        `;
        resultDiv.classList.remove('d-none');
        importBtn.disabled = false;
        importBtn.innerHTML = '<i class="bi bi-upload me-2"></i>استيراد الموظفين';
    });
}
</script>
