# Salary Invoice Employees Module - Implementation Guide

## Overview
This module adds comprehensive salary invoice management with Excel import, WPS (Wage Protection System) support, and bulk payment functionality to your invoices system.

## Database Schema

### 1. Invoice Employees Table
**Table:** `invoice_employees`

New fields added:
- `employee_name` - اسم الموظف
- `project` - المشروع
- `basic_salary` - الراتب الأساسي
- `bonuses` - المكافآت
- `monthly_deductions` - خصومات الشهر
- `advance_deductions` - خصومات السلف
- `work_days_count` - أيام العمل
- `absence_days_count` - أيام الغياب
- `net_salary` - صافي الراتب
- `iban` - رقم الآيبان
- `account_holder_name` - اسم صاحب الحساب
- `bank_name` - البنك
- `payment_method` - طريقة الدفع (wps | monthly)
- `wps_percentage` - نسبة WPS
- `wps_amount` - مبلغ WPS
- `payment_status` - حالة الدفع (unpaid | partially_paid | paid)
- `payment_date` - تاريخ الدفع
- `paid_amount` - المبلغ المدفوع

### 2. Invoices Table
New field:
- `type` - نوع الفاتورة (regular | salary_invoice)

### 3. Settings Table
New table for system configuration:
- `key` - مفتاح الإعداد
- `value` - القيمة
- `type` - نوع البيانات
- `description` - الوصف

Default setting: `wps_max_percentage` = 70%

## Models

### InvoiceEmployee Model
**Location:** `app/Models/InvoiceEmployee.php`

**Key Methods:**
- `calculateNetSalary()` - حساب صافي الراتب
- `calculateWpsAmount()` - حساب مبلغ WPS
- `validateWpsPercentage()` - التحقق من نسبة WPS
- `markAsPaid($amount)` - تحديد الموظف كمدفوع
- `getRemainingAmountAttribute()` - المبلغ المتبقي

**Relationships:**
- `belongsTo(Invoice::class)`
- `belongsTo(Employee::class)` (optional)

### Invoice Model Updates
**Location:** `app/Models/Invoice.php`

**New Methods:**
- `isSalaryInvoice()` - التحقق من نوع الفاتورة
- `getSalaryEmployeesCountAttribute()` - عدد الموظفين
- `getTotalPaidSalariesAttribute()` - إجمالي المدفوع
- `getRemainingUnpaidSalariesAttribute()` - المتبقي

**New Scopes:**
- `scopeSalaryInvoice($query)` - فلترة فواتير الرواتب
- `scopeRegularInvoice($query)` - فلترة الفواتير العادية

### Setting Model
**Location:** `app/Models/Setting.php`

**Key Methods:**
- `Setting::get($key, $default)` - جلب إعداد
- `Setting::set($key, $value, $type, $description)` - حفظ إعداد

## Services

### SalaryInvoiceImportService
**Location:** `app/Services/SalaryInvoiceImportService.php`

**Key Methods:**

1. **import($filePath, $invoiceId)**
   - استيراد موظفين من ملف Excel
   - التحقق من صحة البيانات
   - حساب الإجماليات تلقائياً
   - تحديث الفاتورة

2. **updateEmployeePaymentMethod($employeeId, $paymentMethod, $wpsPercentage)**
   - تحديث طريقة دفع الموظف
   - التحقق من نسبة WPS

3. **paySelectedEmployees($employeeIds, $paymentData)**
   - دفع رواتب موظفين محددين
   - تحديث حالة الدفع
   - تحديث إجماليات الفاتورة

## Controller

### SalaryInvoiceController
**Location:** `app/Http/Controllers/SalaryInvoiceController.php`

**Endpoints:**

1. **POST** `/salary-invoices/import`
   - استيراد موظفين من Excel
   - Parameters: `invoice_id`, `excel_file`

2. **GET** `/salary-invoices/{invoice}/employees`
   - جلب موظفي فاتورة معينة
   - Returns: employees list + summary

3. **PUT** `/salary-invoices/employees/{employee}/payment-method`
   - تحديث طريقة الدفع
   - Parameters: `payment_method`, `wps_percentage`

4. **POST** `/salary-invoices/pay-employees`
   - دفع رواتب موظفين محددين
   - Parameters: `employee_ids[]`

5. **DELETE** `/salary-invoices/employees/{employee}`
   - حذف موظف

6. **DELETE** `/salary-invoices/{invoice}/employees`
   - حذف جميع الموظفين

7. **GET** `/salary-invoices/wps-settings`
   - جلب إعدادات WPS

8. **PUT** `/salary-invoices/wps-settings`
   - تحديث إعدادات WPS

9. **GET** `/salary-invoices/download-template`
   - تحميل قالب Excel

## UI Components

### 1. Import Modal
**Location:** `resources/views/partials/salary-invoice-import-modal.blade.php`

**Features:**
- رفع ملف Excel
- عرض التقدم
- عرض النتائج
- تحميل قالب Excel

**Usage:**
```blade
@include('partials.salary-invoice-import-modal')

<button onclick="openSalaryImportModal({{ $invoice->id }})">
    استيراد موظفي الرواتب
</button>
```

### 2. Employees Table
**Location:** `resources/views/partials/salary-invoice-employees-table.blade.php`

**Features:**
- عرض قائمة الموظفين
- إحصائيات ملخصة
- تحديد متعدد للدفع
- تحديث طريقة الدفع
- حالة الدفع لكل موظف

**Usage:**
```blade
@if($invoice->isSalaryInvoice())
    @include('partials.salary-invoice-employees-table', ['invoice' => $invoice])
@endif
```

## Integration Steps

### Step 1: Run Migrations
```bash
php artisan migrate
```

This will create:
- Settings table
- Add salary invoice fields to invoice_employees
- Add type field to invoices

### Step 2: Update Invoice Index View
**File:** `resources/views/invoices/index.blade.php`

Add import button to invoice actions:
```blade
@if($invoice->type === 'regular' && !$invoice->invoiceEmployees()->exists())
    <button onclick="openSalaryImportModal({{ $invoice->id }})" 
            class="text-blue-600 hover:text-blue-900">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
        </svg>
        استيراد رواتب
    </button>
@endif

@include('partials.salary-invoice-import-modal')
```

### Step 3: Update Invoice Show View
**File:** `resources/views/invoices/show.blade.php`

Add after invoice details section:
```blade
@if($invoice->isSalaryInvoice())
    @include('partials.salary-invoice-employees-table', ['invoice' => $invoice])
@else
    <div class="mt-6">
        <button onclick="openSalaryImportModal({{ $invoice->id }})" 
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            استيراد موظفي الرواتب
        </button>
    </div>
    @include('partials.salary-invoice-import-modal')
@endif
```

### Step 4: Install PhpSpreadsheet (if not installed)
```bash
composer require phpoffice/phpspreadsheet
```

## Excel File Format

### Required Headers (Arabic):
1. ID
2. اسم الموظف
3. المشروع
4. الراتب الأساسي
5. المكافآت
6. خصومات الشهر
7. خصومات السلف
8. أيام العمل
9. أيام الغياب
10. صافي الراتب
11. رقم الآيبان
12. اسم صاحب الحساب
13. البنك

### Sample Data:
| ID | اسم الموظف | المشروع | الراتب الأساسي | المكافآت | خصومات الشهر | خصومات السلف | أيام العمل | أيام الغياب | صافي الراتب | رقم الآيبان | اسم صاحب الحساب | البنك |
|----|------------|---------|----------------|----------|--------------|--------------|-----------|------------|-------------|-------------|-----------------|------|
| 1  | أحمد محمد  | مشروع أ | 5000           | 500      | 100          | 200          | 30        | 0          | 5200        | SA0380000000608010167519 | أحمد محمد | البنك الأهلي |

### IBAN Validation:
- Format: SA + 22 digits
- Example: SA0380000000608010167519

## Payment Methods

### 1. Monthly Salary (راتب شهري)
- Full salary payment
- No WPS constraints
- Direct payment to employee

### 2. Wage Protection System (WPS)
- Percentage-based payment
- Admin-defined maximum percentage (default: 70%)
- Calculated amount: `net_salary × wps_percentage / 100`
- Validation prevents exceeding max percentage

## Payment Flow

1. **Import Employees**
   - Upload Excel file
   - System validates and imports
   - Invoice type changes to 'salary_invoice'
   - Totals calculated automatically

2. **Configure Payment Methods** (Optional)
   - Select employee
   - Choose payment method
   - Set WPS percentage if applicable

3. **Pay Salaries**
   - Select employees to pay
   - Click "Pay Selected"
   - System updates payment status
   - Invoice totals updated

4. **Track Status**
   - View summary statistics
   - Monitor paid/unpaid employees
   - Check remaining amounts

## Error Handling

### Import Errors:
- Missing headers
- Invalid data types
- Empty rows
- Duplicate imports
- Invalid IBAN format

### Payment Errors:
- Already paid employees
- WPS percentage overflow
- Invalid payment amounts
- Database transaction failures

## Security & Validation

1. **File Upload:**
   - Max size: 10MB
   - Allowed types: .xlsx, .xls
   - Server-side validation

2. **Data Validation:**
   - Required fields check
   - Numeric validation
   - IBAN format validation
   - WPS percentage limits

3. **Payment Validation:**
   - Prevent duplicate payments
   - Check payment status
   - Validate amounts

## API Response Format

### Success Response:
```json
{
    "success": true,
    "message": "تم استيراد 10 موظف بنجاح",
    "data": {
        "employees_count": 10,
        "total_salaries": 50000.00,
        "total_deductions": 5000.00,
        "total_net_salaries": 45000.00
    }
}
```

### Error Response:
```json
{
    "success": false,
    "message": "فشل الاستيراد: الملف يفتقد العناوين التالية: اسم الموظف"
}
```

## Testing Checklist

- [ ] Import Excel file with valid data
- [ ] Import Excel file with missing headers
- [ ] Import Excel file with invalid IBAN
- [ ] Update payment method to WPS
- [ ] Update payment method to Monthly
- [ ] Set WPS percentage above maximum
- [ ] Pay single employee
- [ ] Pay multiple employees
- [ ] Try to pay already paid employee
- [ ] Delete unpaid employee
- [ ] Try to delete paid employee
- [ ] Delete all employees
- [ ] Download Excel template
- [ ] View employee statistics
- [ ] Check invoice totals update

## Troubleshooting

### Issue: Import fails with "Headers missing"
**Solution:** Ensure Excel file has exact Arabic headers as specified

### Issue: WPS percentage validation fails
**Solution:** Check settings table for `wps_max_percentage` value

### Issue: Payment status not updating
**Solution:** Check database transactions and invoice relationship

### Issue: UI not loading employees
**Solution:** Check browser console for JavaScript errors, verify routes are registered

## Future Enhancements

1. Export paid employees to Excel
2. Generate salary slips PDF
3. Bank file generation for WPS
4. Email notifications to employees
5. Salary history tracking
6. Multi-currency support
7. Approval workflow
8. Audit trail

## Support

For issues or questions, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Browser console for JavaScript errors
3. Network tab for API responses
4. Database for data integrity

---

**Implementation Date:** February 11, 2026
**Version:** 1.0.0
**Status:** Production Ready
