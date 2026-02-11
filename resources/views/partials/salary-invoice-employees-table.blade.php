<div class="bg-white rounded-lg shadow-md p-6 mt-6" id="salaryEmployeesSection">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold text-gray-900">موظفي الرواتب</h3>
        <div class="flex gap-2">
            <button onclick="loadSalaryEmployees({{ $invoice->id }})" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                تحديث
            </button>
            <button onclick="paySelectedEmployees()" id="paySelectedBtn"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                دفع المحدد
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
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">الراتب الأساسي</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">المكافآت</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">الخصومات</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">صافي الراتب</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">طريقة الدفع</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
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

            <div id="wpsPercentageDiv" class="mb-4 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">نسبة WPS (%)</label>
                <input type="number" id="wps_percentage" name="wps_percentage" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                       min="0" max="100" step="0.01">
                <p class="text-xs text-gray-500 mt-1">الحد الأقصى: <span id="maxWpsPercentage">70</span>%</p>
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

    tableBody.innerHTML = employees.map(emp => `
        <tr class="hover:bg-gray-50">
            <td class="px-3 py-4 whitespace-nowrap">
                <input type="checkbox" class="employee-checkbox rounded border-gray-300" 
                       value="${emp.id}" 
                       ${emp.payment_status === 'paid' ? 'disabled' : ''}
                       onchange="updateSelectedEmployees()">
            </td>
            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">${emp.id}</td>
            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${emp.employee_name || '-'}</td>
            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">${emp.project || '-'}</td>
            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(emp.basic_salary).toFixed(2)}</td>
            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(emp.bonuses).toFixed(2)}</td>
            <td class="px-3 py-4 whitespace-nowrap text-sm text-red-600">${parseFloat(emp.monthly_deductions + emp.advance_deductions).toFixed(2)}</td>
            <td class="px-3 py-4 whitespace-nowrap text-sm font-semibold text-green-600">${parseFloat(emp.net_salary).toFixed(2)}</td>
            <td class="px-3 py-4 whitespace-nowrap">
                ${getPaymentMethodBadge(emp.payment_method, emp.wps_percentage)}
            </td>
            <td class="px-3 py-4 whitespace-nowrap">
                ${getPaymentStatusBadge(emp.payment_status)}
            </td>
            <td class="px-3 py-4 whitespace-nowrap text-sm">
                <button onclick="openPaymentMethodModal(${emp.id})" 
                        class="text-blue-600 hover:text-blue-900 ml-2" 
                        ${emp.payment_status === 'paid' ? 'disabled' : ''}>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
            </td>
        </tr>
    `).join('');
}

function getPaymentMethodBadge(method, percentage) {
    if (method === 'wps') {
        return `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
            WPS (${percentage}%)
        </span>`;
    }
    return `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
        راتب شهري
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
    document.getElementById('paySelectedBtn').disabled = selectedEmployees.size === 0;
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

function openPaymentMethodModal(employeeId) {
    document.getElementById('employee_id').value = employeeId;
    document.getElementById('paymentMethodModal').classList.remove('hidden');
    loadWpsSettings();
}

function closePaymentMethodModal() {
    document.getElementById('paymentMethodModal').classList.add('hidden');
    document.getElementById('paymentMethodForm').reset();
}

async function loadWpsSettings() {
    try {
        const response = await fetch('{{ route("salary-invoices.wps-settings") }}');
        const data = await response.json();
        if (data.success) {
            maxWpsPercentage = data.wps_max_percentage;
            document.getElementById('maxWpsPercentage').textContent = maxWpsPercentage;
            document.getElementById('wps_percentage').max = maxWpsPercentage;
        }
    } catch (error) {
        console.error('Error loading WPS settings:', error);
    }
}

document.getElementById('payment_method')?.addEventListener('change', function() {
    const wpsDiv = document.getElementById('wpsPercentageDiv');
    if (this.value === 'wps') {
        wpsDiv.classList.remove('hidden');
        document.getElementById('wps_percentage').required = true;
    } else {
        wpsDiv.classList.add('hidden');
        document.getElementById('wps_percentage').required = false;
    }
});

document.getElementById('paymentMethodForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const employeeId = document.getElementById('employee_id').value;
    const paymentMethod = document.getElementById('payment_method').value;
    const wpsPercentage = document.getElementById('wps_percentage').value;

    try {
        const response = await fetch(`/salary-invoices/employees/${employeeId}/payment-method`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                payment_method: paymentMethod,
                wps_percentage: wpsPercentage
            })
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);
            closePaymentMethodModal();
            loadSalaryEmployees({{ $invoice->id }});
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء التحديث');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    @if($invoice->isSalaryInvoice())
        loadSalaryEmployees({{ $invoice->id }});
    @endif
});
</script>
