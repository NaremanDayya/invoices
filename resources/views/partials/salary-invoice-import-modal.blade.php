<div id="salaryImportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-900">استيراد موظفي الرواتب</h3>
            <button onclick="closeSalaryImportModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="mt-4">
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="font-semibold text-blue-900 mb-2">تعليمات الاستيراد:</h4>
                <ul class="list-disc list-inside text-sm text-blue-800 space-y-1">
                    <li>يجب أن يحتوي الملف على العناوين التالية بالضبط</li>
                    <li>صيغة الملف: Excel (.xlsx أو .xls)</li>
                    <li>الحد الأقصى لحجم الملف: 10 ميجابايت</li>
                    <li>تأكد من صحة أرقام الآيبان (تبدأ بـ SA وتحتوي على 24 حرف)</li>
                </ul>
                <div class="mt-3">
                    <a href="{{ route('salary-invoices.download-template') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        تحميل قالب Excel
                    </a>
                </div>
            </div>

            <form id="salaryImportForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="invoice_id" id="import_invoice_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        اختر ملف Excel
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="excel_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                    <span>اختر ملف</span>
                                    <input id="excel_file" name="excel_file" type="file" class="sr-only" accept=".xlsx,.xls" required>
                                </label>
                                <p class="pr-1">أو اسحب الملف هنا</p>
                            </div>
                            <p class="text-xs text-gray-500">XLSX, XLS حتى 10MB</p>
                        </div>
                    </div>
                    <div id="fileName" class="mt-2 text-sm text-gray-600"></div>
                </div>

                <div id="importProgress" class="hidden mb-4">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%" id="progressBar"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2 text-center" id="progressText">جاري الاستيراد...</p>
                </div>

                <div id="importResult" class="hidden mb-4"></div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeSalaryImportModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        إلغاء
                    </button>
                    <button type="submit" id="importBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        استيراد الموظفين
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSalaryImportModal(invoiceId) {
    document.getElementById('import_invoice_id').value = invoiceId;
    document.getElementById('salaryImportModal').classList.remove('hidden');
    document.getElementById('salaryImportForm').reset();
    document.getElementById('fileName').textContent = '';
    document.getElementById('importResult').classList.add('hidden');
    document.getElementById('importProgress').classList.add('hidden');
}

function closeSalaryImportModal() {
    document.getElementById('salaryImportModal').classList.add('hidden');
}

document.getElementById('excel_file').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
        document.getElementById('fileName').textContent = 'الملف المحدد: ' + fileName;
    }
});

document.getElementById('salaryImportForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const importBtn = document.getElementById('importBtn');
    const progressDiv = document.getElementById('importProgress');
    const progressBar = document.getElementById('progressBar');
    const resultDiv = document.getElementById('importResult');
    
    importBtn.disabled = true;
    progressDiv.classList.remove('hidden');
    resultDiv.classList.add('hidden');
    progressBar.style.width = '30%';
    
    try {
        const response = await fetch('{{ route("salary-invoices.import") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        progressBar.style.width = '70%';
        const data = await response.json();
        progressBar.style.width = '100%';
        
        setTimeout(() => {
            progressDiv.classList.add('hidden');
            
            if (data.success) {
                resultDiv.innerHTML = `
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-green-900">${data.message}</p>
                                <div class="mt-2 text-sm text-green-800">
                                    <p>عدد الموظفين: ${data.data.employees_count}</p>
                                    <p>إجمالي الرواتب: ${parseFloat(data.data.total_salaries).toFixed(2)} ريال</p>
                                    <p>إجمالي الخصومات: ${parseFloat(data.data.total_deductions).toFixed(2)} ريال</p>
                                    <p>صافي الرواتب: ${parseFloat(data.data.total_net_salaries).toFixed(2)} ريال</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                resultDiv.classList.remove('hidden');
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                resultDiv.innerHTML = `
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="font-semibold text-red-900">${data.message}</p>
                        </div>
                    </div>
                `;
                resultDiv.classList.remove('hidden');
            }
            
            importBtn.disabled = false;
        }, 500);
        
    } catch (error) {
        progressDiv.classList.add('hidden');
        resultDiv.innerHTML = `
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-900">حدث خطأ أثناء الاستيراد. يرجى المحاولة مرة أخرى.</p>
            </div>
        `;
        resultDiv.classList.remove('hidden');
        importBtn.disabled = false;
    }
});
</script>
