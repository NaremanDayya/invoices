<div class="bg-white rounded-lg shadow-md p-6 mt-6" id="salaryEmployeesSection">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold text-gray-900">موظفي الرواتب</h3>
        <div class="flex gap-2" id="filterButtons">
            <button onclick="filterEmployees('all')" class="filter-btn active px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                الكل (<span id="countAll">0</span>)
            </button>
            <button onclick="filterEmployees('wps')" class="filter-btn px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">
                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                WPS (<span id="countWps">0</span>)
            </button>
            <button onclick="filterEmployees('monthly')" class="filter-btn px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                راتب شهري (<span id="countMonthly">0</span>)
            </button>
        </div>
    </div>

    <div class="flex justify-end items-center mb-4">
        <div class="flex gap-2">
            <button onclick="loadSalaryEmployees({{ $invoice->id }})"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                تحديث
            </button>
            <button onclick="showPaymentModal()" id="processPaymentBtn"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                معالجة الدفعات
            </button>
        </div>
    </div>

    <div id="employeesSummary" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 hidden">
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm text-blue-600 font-medium">إجمالي الموظفين</p>
            <p class="text-2xl font-bold text-blue-900" id="totalEmployees">0</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-green-600 font-medium">الموظفين المدفوعين</p>
            <p class="text-2xl font-bold text-green-900" id="paidEmployees">0</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm text-yellow-600 font-medium">صافي الرواتب</p>
            <p class="text-2xl font-bold text-yellow-900" id="totalNetSalaries">0</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg">
            <p class="text-sm text-red-600 font-medium">المتبقي</p>
            <p class="text-2xl font-bold text-red-900" id="remainingUnpaid">0</p>
        </div>
    </div>

    <div id="employeesTableContainer" class="overflow-x-auto">
        <div class="text-center py-8 text-gray-500" id="noEmployeesMessage">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="text-lg font-medium">لا يوجد موظفين مستوردين</p>
            <p class="text-sm mt-2">قم باستيراد موظفي الرواتب من ملف Excel</p>
        </div>

        <table id="employeesTable" class="min-w-full divide-y divide-gray-200 hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                    </th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">اسم الموظف</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">المشروع</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجمالي الراتب</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">المدفوع</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">المتبقي</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">نوع الراتب</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">حالة الدفع</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">آخر دفعة</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody id="employeesTableBody" class="bg-white divide-y divide-gray-200">
            </tbody>
        </table>
    </div>
</div>

<div id="paymentMethodModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-900">تحديث طريقة الدفع</h3>
            <button onclick="closePaymentMethodModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="paymentMethodForm" class="mt-4">
            <input type="hidden" id="employee_id" name="employee_id">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
                <select id="payment_method" name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    <option value="monthly">راتب شهري عادي</option>
                    <option value="wps">نظام حماية الأجور (WPS)</option>
                </select>
            </div>

            <div id="wpsAmountDiv" class="mb-4 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ WPS (ريال)</label>
                <input type="number" id="wps_amount" name="wps_amount"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md"
                       min="0" step="0.01" oninput="calculateMonthlyAmount()">
                <div class="mt-2 p-3 bg-blue-50 rounded-md">
                    <p class="text-xs text-blue-700 mb-1"><strong>صافي الراتب:</strong> <span id="displayNetSalary">0.00</span> ريال</p>
                    <p class="text-xs text-purple-700 mb-1"><strong>الحد الأقصى لـ WPS:</strong> <span id="maxWpsAmount">0.00</span> ريال (<span id="maxWpsPercentage">70</span>%)</p>
                    <p class="text-xs text-green-700"><strong>المبلغ الشهري المتبقي:</strong> <span id="calculatedMonthlyAmount">0.00</span> ريال</p>
                </div>
                <p class="text-xs text-red-600 mt-1 hidden" id="wpsAmountError"></p>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closePaymentMethodModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    إلغاء
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let selectedEmployees = new Set();
let maxWpsPercentage = 70;
let allEmployeesData = [];
let currentFilter = 'all';
let currentEmployeeNetSalary = 0;

async function loadSalaryEmployees(invoiceId) {
    try {
        const response = await fetch(`/salary-invoices/${invoiceId}/employees`);
        const data = await response.json();

        if (data.success) {
            displayEmployees(data.employees, data.summary);
        }
    } catch (error) {
        console.error('Error loading employees:', error);
    }
}

function displayEmployees(employees, summary) {
    const tableBody = document.getElementById('employeesTableBody');
    const noEmployeesMessage = document.getElementById('noEmployeesMessage');
    const employeesTable = document.getElementById('employeesTable');
    const employeesSummary = document.getElementById('employeesSummary');

    if (employees.length === 0) {
        noEmployeesMessage.classList.remove('hidden');
        employeesTable.classList.add('hidden');
        employeesSummary.classList.add('hidden');
        return;
    }

    noEmployeesMessage.classList.add('hidden');
    employeesTable.classList.remove('hidden');
    employeesSummary.classList.remove('hidden');

    document.getElementById('totalEmployees').textContent = summary.total_employees;
    document.getElementById('paidEmployees').textContent = summary.paid_employees;
    document.getElementById('totalNetSalaries').textContent = parseFloat(summary.total_net_salaries).toFixed(2) + ' ريال';
    document.getElementById('remainingUnpaid').textContent = parseFloat(summary.remaining_unpaid).toFixed(2) + ' ريال';

    allEmployeesData = employees;

    tableBody.innerHTML = employees.map(emp => `
        <tr class="hover:bg-gray-50 employee-row" data-payment-method="${emp.salary_type || emp.payment_method}" data-wps-amount="${emp.wps_amount || 0}">
            <td class="px-3 py-4 whitespace-nowrap">
                <input type="checkbox" class="employee-checkbox rounded border-gray-300"
                       value="${emp.id}"
                       ${emp.payment_status === 'paid' ? 'disabled' : ''}
                       onchange="updateSelectedEmployees()">
            </td>
            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">${emp.id}</td>
            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-employee-name>${emp.employee_name || '-'}</td>
            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">${emp.project || '-'}</td>
            <td class="px-3 py-4 whitespace-nowrap text-sm font-semibold text-blue-600" data-total-salary>${parseFloat(emp.total_salary || emp.net_salary).toFixed(2)}</td>
            <td class="px-3 py-4 whitespace-nowrap text-sm font-semibold text-green-600" data-total-paid>${parseFloat(emp.total_paid || 0).toFixed(2)}</td>
            <td class="px-3 py-4 whitespace-nowrap text-sm font-semibold text-red-600" data-remaining>${parseFloat(emp.remaining_amount || emp.net_salary).toFixed(2)}</td>
            <td class="px-3 py-4 whitespace-nowrap" data-salary-type>
                ${getSalaryTypeBadge(emp.salary_type || emp.payment_method)}
            </td>
            <td class="px-3 py-4 whitespace-nowrap">
                ${getPaymentStatusBadge(emp.payment_status)}
            </td>
            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                ${emp.last_payment_date ? new Date(emp.last_payment_date).toLocaleDateString('ar-SA') : '-'}
            </td>
            <td class="px-3 py-4 whitespace-nowrap text-sm">
                <button onclick="viewPaymentHistory(${emp.id})"
                        class="text-blue-600 hover:text-blue-900 ml-2"
                        title="عرض سجل الدفعات">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </button>
            </td>
        </tr>
    `).join('');

    updateFilterCounts();
}

function getSalaryTypeBadge(salaryType) {
    if (salaryType === 'wps') {
        return `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
            WPS
        </span>`;
    }
    return `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
        شهري
    </span>`;
}

function getPaymentStatusBadge(status) {
    const badges = {
        'paid': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">مدفوع</span>',
        'partially_paid': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">مدفوع جزئياً</span>',
        'unpaid': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">غير مدفوع</span>'
    };
    return badges[status] || badges['unpaid'];
}

function updateSelectedEmployees() {
    selectedEmployees.clear();
    document.querySelectorAll('.employee-checkbox:checked').forEach(cb => {
        selectedEmployees.add(parseInt(cb.value));
    });
    document.getElementById('processPaymentBtn').disabled = selectedEmployees.size === 0;
}

async function viewPaymentHistory(employeeId) {
    try {
        const response = await fetch(`/salary-invoices/employees/${employeeId}/payment-history`);
        const data = await response.json();
        
        if (data.success) {
            let historyHtml = `
                <div class="mb-4">
                    <h6 class="font-bold">الموظف: ${data.employee.name}</h6>
                    <div class="grid grid-cols-3 gap-2 mt-2 text-sm">
                        <div><strong>إجمالي الراتب:</strong> ${parseFloat(data.employee.total_salary).toFixed(2)} ريال</div>
                        <div><strong>المدفوع:</strong> ${parseFloat(data.employee.total_paid).toFixed(2)} ريال</div>
                        <div><strong>المتبقي:</strong> ${parseFloat(data.employee.remaining_amount).toFixed(2)} ريال</div>
                    </div>
                </div>
                <hr class="my-3">
                <h6 class="font-bold mb-3">سجل الدفعات:</h6>
            `;
            
            if (data.payments.length === 0) {
                historyHtml += '<p class="text-gray-500 text-center py-4">لا توجد دفعات مسجلة</p>';
            } else {
                historyHtml += '<div class="space-y-2">';
                data.payments.forEach(payment => {
                    historyHtml += `
                        <div class="bg-gray-50 p-3 rounded">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-semibold text-green-600">${parseFloat(payment.payment_amount).toFixed(2)} ريال</span>
                                    <span class="text-xs text-gray-500 mr-2">(${payment.payment_type === 'full' ? 'دفع كامل' : 'دفع جزئي'})</span>
                                    <span class="text-xs px-2 py-1 rounded ${payment.payment_mode === 'wps' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'} mr-2">
                                        ${payment.payment_mode === 'wps' ? 'WPS' : 'شهري'}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    ${new Date(payment.payment_date).toLocaleDateString('ar-SA')}
                                </div>
                            </div>
                            ${payment.notes ? `<div class="text-xs text-gray-600 mt-1">ملاحظات: ${payment.notes}</div>` : ''}
                            ${payment.created_by ? `<div class="text-xs text-gray-500 mt-1">بواسطة: ${payment.created_by.name || 'غير معروف'}</div>` : ''}
                        </div>
                    `;
                });
                historyHtml += '</div>';
            }
            
            Swal.fire({
                title: 'سجل دفعات الموظف',
                html: historyHtml,
                width: '600px',
                confirmButtonText: 'إغلاق'
            });
        }
    } catch (error) {
        console.error('Error loading payment history:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'حدث خطأ أثناء تحميل سجل الدفعات'
        });
    }
}

document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.employee-checkbox:not(:disabled)').forEach(cb => {
        cb.checked = this.checked;
    });
    updateSelectedEmployees();
});

async function paySelectedEmployees() {
    if (selectedEmployees.size === 0) return;

    if (!confirm(`هل أنت متأكد من دفع رواتب ${selectedEmployees.size} موظف؟`)) {
        return;
    }

    try {
        const response = await fetch('{{ route("salary-invoices.pay-employees") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                employee_ids: Array.from(selectedEmployees)
            })
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);
            loadSalaryEmployees({{ $invoice->id }});
            selectedEmployees.clear();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء الدفع');
    }
}

function openPaymentMethodModal(employeeId, netSalary, wpsAmount, paymentMethod) {
    document.getElementById('employee_id').value = employeeId;
    currentEmployeeNetSalary = parseFloat(netSalary);

    document.getElementById('paymentMethodModal').classList.remove('hidden');
    
    setTimeout(() => {
        const paymentMethodSelect = document.getElementById('payment_method');
        paymentMethodSelect.value = paymentMethod || 'monthly';

        document.getElementById('wps_amount').value = wpsAmount > 0 ? parseFloat(wpsAmount).toFixed(2) : '';
        document.getElementById('displayNetSalary').textContent = currentEmployeeNetSalary.toFixed(2);

        const wpsDiv = document.getElementById('wpsAmountDiv');
        const wpsAmountInput = document.getElementById('wps_amount');
        
        if (paymentMethod === 'wps') {
            console.log('Opening modal with WPS method - showing WPS div');
            wpsDiv.classList.remove('hidden');
            wpsAmountInput.required = true;
        } else {
            console.log('Opening modal with monthly method - hiding WPS div');
            wpsDiv.classList.add('hidden');
            wpsAmountInput.required = false;
        }

        loadWpsSettings();
        calculateMonthlyAmount();
    }, 50);
}
function closePaymentMethodModal() {
    document.getElementById('paymentMethodModal').classList.add('hidden');
    document.getElementById('paymentMethodForm').reset();
    document.getElementById('wpsAmountError').classList.add('hidden');
    currentEmployeeNetSalary = 0;
}

async function loadWpsSettings() {
    try {
        const response = await fetch('{{ route("salary-invoices.wps-settings") }}');
        const data = await response.json();
        if (data.success) {
            maxWpsPercentage = data.wps_max_percentage;
            document.getElementById('maxWpsPercentage').textContent = maxWpsPercentage;
            updateMaxWpsAmount();
        }
    } catch (error) {
        console.error('Error loading WPS settings:', error);
    }
}

function updateMaxWpsAmount() {
    const maxAmount = (currentEmployeeNetSalary * maxWpsPercentage) / 100;
    document.getElementById('maxWpsAmount').textContent = maxAmount.toFixed(2);
    document.getElementById('wps_amount').max = maxAmount;
}

function calculateMonthlyAmount() {
    const wpsAmount = parseFloat(document.getElementById('wps_amount').value) || 0;
    const monthlyAmount = currentEmployeeNetSalary - wpsAmount;
    const maxAmount = (currentEmployeeNetSalary * maxWpsPercentage) / 100;
    const errorDiv = document.getElementById('wpsAmountError');

    document.getElementById('calculatedMonthlyAmount').textContent = monthlyAmount.toFixed(2);

    if (wpsAmount > maxAmount) {
        errorDiv.textContent = `مبلغ WPS يتجاوز الحد الأقصى المسموح به (${maxAmount.toFixed(2)} ريال)`;
        errorDiv.classList.remove('hidden');
    } else if (wpsAmount > currentEmployeeNetSalary) {
        errorDiv.textContent = 'مبلغ WPS لا يمكن أن يتجاوز صافي الراتب';
        errorDiv.classList.remove('hidden');
    } else if (monthlyAmount < 0) {
        errorDiv.textContent = 'المبلغ الشهري لا يمكن أن يكون سالباً';
        errorDiv.classList.remove('hidden');
    } else {
        errorDiv.classList.add('hidden');
    }
}

document.getElementById('payment_method')?.addEventListener('change', function() {
    console.log('Payment method changed to:', this.value);
    const wpsDiv = document.getElementById('wpsAmountDiv');
    const wpsAmountInput = document.getElementById('wps_amount');

    if (this.value === 'wps') {
        console.log('Showing WPS div for WPS payment method');
        wpsDiv.classList.remove('hidden');
        wpsAmountInput.required = true;
        
        if (currentEmployeeNetSalary > 0) {
            updateMaxWpsAmount();
            calculateMonthlyAmount();
        }
    } else {
        console.log('Hiding WPS div for monthly payment method');
        wpsDiv.classList.add('hidden');
        wpsAmountInput.required = false;
        wpsAmountInput.value = '';
        document.getElementById('wpsAmountError').classList.add('hidden');
    }
});

document.getElementById('paymentMethodForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const employeeId = document.getElementById('employee_id').value;
    const paymentMethod = document.getElementById('payment_method').value;
    const wpsAmount = document.getElementById('wps_amount').value;

    console.log('Payment Method Update: Starting request', {
        employeeId,
        paymentMethod,
        wpsAmount,
        url: `/salary-invoices/employees/${employeeId}/payment-method`
    });

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('خطأ: لم يتم العثور على رمز الأمان (CSRF)');
            return;
        }

        // Validate WPS amount if payment method is wps
        if (paymentMethod === 'wps') {
            if (!wpsAmount || parseFloat(wpsAmount) <= 0) {
                alert('الرجاء إدخال مبلغ WPS');
                return;
            }

            const maxAmount = parseFloat(document.getElementById('maxWpsAmount').textContent);
            if (parseFloat(wpsAmount) > maxAmount) {
                alert(`مبلغ WPS يتجاوز الحد الأقصى المسموح به (${maxAmount.toFixed(2)} ريال)`);
                return;
            }
        }

        const requestBody = {
            payment_method: paymentMethod,
            wps_amount: paymentMethod === 'wps' ? parseFloat(wpsAmount) : null
        };

        console.log('Payment Method Update: Request details', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: requestBody
        });

        const response = await fetch(`/salary-invoices/employees/${employeeId}/payment-method`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestBody)
        });

        console.log('Payment Method Update: Response received', {
            status: response.status,
            statusText: response.statusText,
            ok: response.ok
        });

        // Handle non-JSON responses
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Payment Method Update: Non-JSON response', {
                status: response.status,
                contentType,
                body: text.substring(0, 500)
            });
            alert(`خطأ في الخادم (${response.status}): الاستجابة غير صالحة`);
            return;
        }

        const data = await response.json();
        console.log('Payment Method Update: Response data', data);

        if (response.ok && data.success) {
            console.log('Payment Method Update: Success');
            alert(data.message || 'تم التحديث بنجاح');
            closePaymentMethodModal();
            loadSalaryEmployees({{ $invoice->id }});
        } else {
            console.warn('Payment Method Update: Failed', data);

            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join('\n');
                alert(`خطأ في البيانات:\n${errorMessages}`);
            } else {
                alert(data.message || 'فشل التحديث');
            }
        }
    } catch (error) {
        console.error('Payment Method Update: Exception caught', {
            error: error.message,
            stack: error.stack,
            name: error.name
        });

        if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
            alert('خطأ في الاتصال: تعذر الوصول إلى الخادم. تحقق من اتصال الإنترنت.');
        } else {
            alert(`حدث خطأ أثناء التحديث: ${error.message}`);
        }
    }
});
function filterEmployees(filterType) {
    currentFilter = filterType;
    const rows = document.querySelectorAll('.employee-row');

    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    event.target.closest('.filter-btn').classList.add('active');

    rows.forEach(row => {
        const paymentMethod = row.dataset.paymentMethod;
        const wpsAmount = parseFloat(row.dataset.wpsAmount);

        if (filterType === 'all') {
            row.style.display = '';
        } else if (filterType === 'wps') {
            row.style.display = (paymentMethod === 'wps' && wpsAmount > 0) ? '' : 'none';
        } else if (filterType === 'monthly') {
            row.style.display = (paymentMethod === 'monthly' || wpsAmount === 0) ? '' : 'none';
        }
    });
}

function updateFilterCounts() {
    const allCount = allEmployeesData.length;
    const wpsCount = allEmployeesData.filter(emp => emp.payment_method === 'wps' && emp.wps_amount > 0).length;
    const monthlyCount = allEmployeesData.filter(emp => emp.payment_method === 'monthly' || !emp.wps_amount || emp.wps_amount === 0).length;

    document.getElementById('countAll').textContent = allCount;
    document.getElementById('countWps').textContent = wpsCount;
    document.getElementById('countMonthly').textContent = monthlyCount;
}

document.addEventListener('DOMContentLoaded', function() {
    @if($invoice->isSalaryInvoice())
        loadSalaryEmployees({{ $invoice->id }});
    @endif

    const style = document.createElement('style');
    style.textContent = `
        .filter-btn.active {
            ring: 2px;
            ring-offset: 2px;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
    `;
    document.head.appendChild(style);
});
</script>
