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
Route::resource('invoices', InvoiceController::class);
Route::resource('payments', PaymentsController::class);
Route::post('invoices/add-client', [InvoiceController::class, 'addClient'])->name('invoices.add-client');
Route::post('invoices/add-service', [InvoiceController::class, 'addService'])->name('invoices.add-service');
Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'addPayment'])->name('invoices.add-payment');

// Credit Notes Routes
Route::post('/invoices/{invoice}/credit-notes', [CreditNoteController::class, 'store'])->name('credit-notes.store');
Route::delete('/credit-notes/{creditNote}', [CreditNoteController::class, 'destroy'])->name('credit-notes.destroy');
Route::get('/invoices/{invoice}/credit-notes', [CreditNoteController::class, 'getInvoiceCreditNotes'])->name('credit-notes.invoice');
Route::get('/credit-notes/invoice/{invoice}/count', [CreditNoteController::class, 'getCreditNoteCount'])->name('credit-notes.count');

// Salary Invoice Routes
Route::post('/salary-invoices/import', [\App\Http\Controllers\SalaryInvoiceController::class, 'importEmployees'])->name('salary-invoices.import');
Route::get('/salary-invoices/{invoice}/employees', [\App\Http\Controllers\SalaryInvoiceController::class, 'getEmployees'])->name('salary-invoices.employees');
Route::put('/salary-invoices/employees/{employee}/payment-method', [\App\Http\Controllers\SalaryInvoiceController::class, 'updatePaymentMethod'])->name('salary-invoices.update-payment-method');
Route::post('/salary-invoices/pay-employees', [\App\Http\Controllers\SalaryInvoiceController::class, 'paySelectedEmployees'])->name('salary-invoices.pay-employees');
Route::delete('/salary-invoices/employees/{employee}', [\App\Http\Controllers\SalaryInvoiceController::class, 'deleteEmployee'])->name('salary-invoices.delete-employee');
Route::delete('/salary-invoices/{invoice}/clear-employees', [\App\Http\Controllers\SalaryInvoiceController::class, 'clearAllEmployees'])->name('salary-invoices.clear-employees');
Route::get('/salary-invoices/wps-settings', [\App\Http\Controllers\SalaryInvoiceController::class, 'getWpsSettings'])->name('salary-invoices.wps-settings');
Route::put('/salary-invoices/wps-settings', [\App\Http\Controllers\SalaryInvoiceController::class, 'updateWpsSettings'])->name('salary-invoices.update-wps-settings');
Route::get('/salary-invoices/download-template', [\App\Http\Controllers\SalaryInvoiceController::class, 'downloadTemplate'])->name('salary-invoices.download-template');

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
Route::resource('clients', \App\Http\Controllers\ClientController::class);
Route::get('clients/{client}/monthly-report', [\App\Http\Controllers\ClientController::class, 'monthlyReport'])->name('clients.monthly-report');
Route::get('clients/{client}/monthly-report/export', [\App\Http\Controllers\ClientController::class, 'exportMonthlyReport'])->name('clients.monthly-report.export');
Route::resource('services', \App\Http\Controllers\ServiceController::class);
Route::resource('invoice-statuses', \App\Http\Controllers\InvoiceStatusController::class);
Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'getNotifications']);
require __DIR__.'/auth.php';
