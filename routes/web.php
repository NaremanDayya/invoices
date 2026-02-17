<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CreditNoteController;
use App\Livewire\Index;
use App\Livewire\Chat;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/update-photo', [DashboardController::class, 'updatePhoto'])->name('admin.updatePhoto');
Route::get('/dashboard/reports/issued-invoices', [DashboardController::class, 'issuedInvoicesReport'])->name('dashboard.reports.issued-invoices');
Route::get('/dashboard/reports/cancelled-invoices', [DashboardController::class, 'cancelledInvoicesReport'])->name('dashboard.reports.cancelled-invoices');
Route::get('/dashboard/reports/late-invoices', [DashboardController::class, 'lateInvoicesReport'])->name('dashboard.reports.late-invoices');
Route::get('/dashboard/reports/users', [DashboardController::class, 'usersReport'])->name('dashboard.reports.users');
Route::get('/dashboard/reports/workers', [DashboardController::class, 'workersReport'])->name('dashboard.reports.workers');
Route::get('/dashboard/reports/supervisors', [DashboardController::class, 'supervisorsReport'])->name('dashboard.reports.supervisors');
Route::get('/dashboard/reports/managers', [DashboardController::class, 'managersReport'])->name('dashboard.reports.managers');
Route::get('/dashboard/reports/financial-for-us', [DashboardController::class, 'financialForUsReport'])->name('dashboard.reports.financial-for-us');
Route::get('/dashboard/reports/financial-against-us', [DashboardController::class, 'financialAgainstUsReport'])->name('dashboard.reports.financial-against-us');
Route::get('/dashboard/reports/work-days', [DashboardController::class, 'workDaysReport'])->name('dashboard.reports.work-days');
Route::middleware(['auth'])->group(function () {
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::middleware(['permission:add_invoices'])->group(function () {
        Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('payments', [PaymentsController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentsController::class, 'show'])->name('payments.show');
    Route::middleware(['permission:add_invoice_payment'])->group(function () {
        Route::get('payments/create', [PaymentsController::class, 'create'])->name('payments.create');
        Route::post('payments', [PaymentsController::class, 'store'])->name('payments.store');
    });
});

Route::middleware(['auth', 'permission:add_clients'])->group(function () {
    Route::post('invoices/add-client', [InvoiceController::class, 'addClient'])->name('invoices.add-client');
});

Route::post('invoices/add-service', [InvoiceController::class, 'addService'])->name('invoices.add-service');

Route::middleware(['auth', 'permission:add_invoice_payment'])->group(function () {
    Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'addPayment'])->name('invoices.add-payment');
});

// Credit Notes Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/invoices/{invoice}/credit-notes', [CreditNoteController::class, 'index'])->name('invoices.credit-notes.index');
    Route::get('/invoices/{invoice}/credit-notes/{creditNote}', [CreditNoteController::class, 'show'])->name('invoices.credit-notes.show');
    Route::get('/invoices/{invoice}/credit-notes-data', [CreditNoteController::class, 'getInvoiceCreditNotes'])->name('credit-notes.invoice');
    Route::get('/credit-notes/invoice/{invoice}/count', [CreditNoteController::class, 'getCreditNoteCount'])->name('credit-notes.count');
    
    Route::middleware(['permission:add_credit_note'])->group(function () {
        Route::post('/invoices/{invoice}/credit-notes', [CreditNoteController::class, 'store'])->name('credit-notes.store');
        Route::delete('/credit-notes/{creditNote}', [CreditNoteController::class, 'destroy'])->name('credit-notes.destroy');
    });
});

// Salary Invoice Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/salary-invoices/{invoice}/employees', [\App\Http\Controllers\SalaryInvoiceController::class, 'showEmployees'])->name('salary-invoices.employees.index');
    Route::get('/salary-invoices/{invoice}/employees-data', [\App\Http\Controllers\SalaryInvoiceController::class, 'getEmployees'])->name('salary-invoices.employees.data');
    Route::get('/salary-invoices/wps-settings', [\App\Http\Controllers\SalaryInvoiceController::class, 'getWpsSettings'])->name('salary-invoices.wps-settings');
    Route::put('/salary-invoices/wps-settings', [\App\Http\Controllers\SalaryInvoiceController::class, 'updateWpsSettings'])->name('salary-invoices.update-wps-settings');
    Route::get('/salary-invoices/download-template', [\App\Http\Controllers\SalaryInvoiceController::class, 'downloadTemplate'])->name('salary-invoices.download-template');
    
    Route::middleware(['permission:import_invoice_employees'])->group(function () {
        Route::post('/salary-invoices/import', [\App\Http\Controllers\SalaryInvoiceController::class, 'importEmployees'])->name('salary-invoices.import');
        Route::put('/salary-invoices/employees/{employee}/payment-method', [\App\Http\Controllers\SalaryInvoiceController::class, 'updatePaymentMethod'])->name('salary-invoices.update-payment-method');
        Route::delete('/salary-invoices/employees/{employee}', [\App\Http\Controllers\SalaryInvoiceController::class, 'deleteEmployee'])->name('salary-invoices.delete-employee');
        Route::delete('/salary-invoices/{invoice}/clear-employees', [\App\Http\Controllers\SalaryInvoiceController::class, 'clearAllEmployees'])->name('salary-invoices.clear-employees');
    });
});

// Salary Invoice Approval Routes (Admin only)
Route::middleware(['auth', 'permission:approve_invoice_employees'])->group(function () {
    Route::post('/salary-invoices/{invoice}/approve', [\App\Http\Controllers\SalaryInvoiceController::class, 'approve'])->name('salary-invoices.approve');
    Route::post('/salary-invoices/{invoice}/reject', [\App\Http\Controllers\SalaryInvoiceController::class, 'reject'])->name('salary-invoices.reject');
});

// Salary Invoice Revision Routes (Preview permission)
Route::middleware(['auth', 'permission:preview_invoice_employees'])->group(function () {
    Route::post('/salary-invoices/{invoice}/request-revision', [\App\Http\Controllers\SalaryInvoiceController::class, 'requestRevision'])->name('salary-invoices.request-revision');
    Route::post('/salary-invoices/{invoice}/complete-revision', [\App\Http\Controllers\SalaryInvoiceController::class, 'completeRevision'])->name('salary-invoices.complete-revision');
});

// Salary Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/salary-invoices/{invoice}/payment-summary', [\App\Http\Controllers\SalaryPaymentController::class, 'getPaymentSummary'])->name('salary-payments.summary');
    Route::get('/salary-invoices/employees/{employee}/payment-details', [\App\Http\Controllers\SalaryPaymentController::class, 'getEmployeePaymentDetails'])->name('salary-payments.employee-details');
    Route::get('/salary-invoices/employees/{employee}/payment-history', [\App\Http\Controllers\SalaryPaymentController::class, 'getEmployeePaymentHistory'])->name('salary-payments.history');
    Route::get('/salary-invoices/{invoice}/employees/{employee}/payments', [\App\Http\Controllers\SalaryPaymentController::class, 'showEmployeePayments'])->name('salary-invoices.employees.payments');
    
    Route::middleware(['permission:add_invoice_employee_payment'])->group(function () {
        Route::post('/salary-invoices/{invoice}/process-payments', [\App\Http\Controllers\SalaryPaymentController::class, 'processPayments'])->name('salary-payments.process');
        Route::post('/salary-invoices/employees/{employee}/calculate-breakdown', [\App\Http\Controllers\SalaryPaymentController::class, 'calculatePaymentBreakdown'])->name('salary-payments.calculate-breakdown');
    });
});

// Settings Routes
Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
Route::put('/settings/wps-percentage', [\App\Http\Controllers\SettingsController::class, 'updateWpsPercentage'])->name('settings.update-wps');
Route::get('/settings/wps-percentage', [\App\Http\Controllers\SettingsController::class, 'getWpsPercentage'])->name('settings.get-wps');

// Payment Additional Routes
Route::post('/payments/{payment}/confirm', [PaymentsController::class, 'confirm'])->name('payments.confirm');
Route::get('/payments/{payment}/print', [PaymentsController::class, 'print'])->name('payments.print');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/employees/clients/list', [EmployeeController::class, 'getClients'])->name('employees.clients.list');
    Route::get('/employees/invoices/list', [EmployeeController::class, 'getInvoices'])->name('employees.invoices.list');
});
Route::get('/services/{service}/details', function ($serviceId) {
    $service = \App\Models\Service::find($serviceId);

    if (!$service) {
        return response()->json([]);
    }

    return response()->json($service->serviceDetails);
})->name('services.details');
Route::middleware('auth')->group(function () {
    Route::get('/chat-clients', [InvoiceController::class, 'chatClients']);
    Route::get('/clientChat', Index::class)->name('chat.index');
    Route::get('client/{client}/invoice-chat/{invoice}', \App\Livewire\InvoiceChat::class)
        ->name('client.chat.invoice');
    Route::get('client/{client}/Chat/{conversation}', Chat::class)->name('client.chat');
    Route::get('client/{client}/message', [ChatController::class, 'message'])->name('client.message');
    Route::get('/chat/unread-count', [ChatController::class, 'unreadConversationsCount']);
    Route::post('/chat/send-image', [ChatController::class, 'sendImage'])->name('chat.send-image');
});
Route::middleware(['auth'])->group(function () {
    Route::get('clients', [\App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');
    Route::get('clients/{client}', [\App\Http\Controllers\ClientController::class, 'show'])->name('clients.show');
    Route::get('clients/{client}/monthly-report', [\App\Http\Controllers\ClientController::class, 'monthlyReport'])->name('clients.monthly-report');
    Route::get('clients/{client}/monthly-report/export', [\App\Http\Controllers\ClientController::class, 'exportMonthlyReport'])->name('clients.monthly-report.export');
    
    Route::middleware(['permission:add_clients'])->group(function () {
        Route::get('clients/create', [\App\Http\Controllers\ClientController::class, 'create'])->name('clients.create');
        Route::post('clients', [\App\Http\Controllers\ClientController::class, 'store'])->name('clients.store');
        Route::get('clients/{client}/edit', [\App\Http\Controllers\ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}', [\App\Http\Controllers\ClientController::class, 'update'])->name('clients.update');
        Route::delete('clients/{client}', [\App\Http\Controllers\ClientController::class, 'destroy'])->name('clients.destroy');
    });
});
Route::resource('services', \App\Http\Controllers\ServiceController::class);
Route::resource('invoice-statuses', \App\Http\Controllers\InvoiceStatusController::class);
Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'getNotifications']);

Route::middleware(['auth', 'permission:give_permissions_to_roles'])->group(function () {
    Route::get('/roles', [\App\Http\Controllers\RolePermissionController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}/edit', [\App\Http\Controllers\RolePermissionController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}/permissions', [\App\Http\Controllers\RolePermissionController::class, 'updatePermissions'])->name('roles.update-permissions');
    Route::get('/permissions', [\App\Http\Controllers\RolePermissionController::class, 'permissions'])->name('roles.permissions');
});

// Projects Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
});

require __DIR__.'/auth.php';
