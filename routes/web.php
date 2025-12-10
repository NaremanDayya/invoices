<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\ListInvoices;
use App\Livewire\ListPayments;
use App\Livewire\ListEmployees;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
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
Route::post('/invoices/add-credit-note', [InvoiceController::class, 'addCreditNote'])
    ->name('invoices.add-credit-note');
Route::middleware('auth')->group(function () {
    Route::get('/employees',ListEmployees::class)->name('payments.index');
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
Route::middleware('auth')->group(function () {
    Route::get('/chat-clients', [InvoiceController::class, 'chatClients']);
    Route::get('/clientChat', Index::class)->name('chat.index');
    Route::get('client/{client}/invoice-chat/{invoice:uuid}', \App\Livewire\Chat::class)
        ->name('client.chat.invoice');    Route::get('client/{client}/Chat/{conversation}', Chat::class)->name('client.chat');
    Route::get('client/{client}/message', [ChatController::class, 'message'])->name('client.message');
    Route::get('/chat/unread-count', [ChatController::class, 'unreadConversationsCount']);
});
Route::get('/clients', [\App\Http\Controllers\ClientController::class, 'chatClients']);
Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'getNotifications']);
require __DIR__.'/auth.php';
