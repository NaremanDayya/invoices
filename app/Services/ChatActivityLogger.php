<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatActivityLogger
{
    /**
     * Log invoice creation
     */
    public function logInvoiceCreated(Invoice $invoice): ?Message
    {
        $conversation = $this->getOrCreateInvoiceConversation($invoice);
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $message = "📄 **تم إنشاء فاتورة جديدة**\n\n" .
                   "رقم الفاتورة: {$invoice->number}\n" .
                   "العميل: {$invoice->client->name}\n" .
                   "الخدمة: {$invoice->service->name}\n" .
                   "المبلغ الإجمالي: " . number_format($invoice->total_price, 2) . " ريال\n" .
                   "تاريخ الإصدار: {$invoice->generation_date->format('Y-m-d')}\n" .
                   "تاريخ الاستحقاق: {$invoice->due_date->format('Y-m-d')}\n" .
                   "الحالة: {$this->getPaymentStatusArabic($invoice->payment_status)}\n\n" .
                   "👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, $invoice, $message, 'invoice_created', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'client_id' => $invoice->client_id,
            'total_price' => $invoice->total_price,
            'action' => 'created'
        ]);
    }

    /**
     * Log invoice update
     */
    public function logInvoiceUpdated(Invoice $invoice, array $changes = []): ?Message
    {
        $conversation = $this->getOrCreateInvoiceConversation($invoice);
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $changesText = '';
        if (!empty($changes)) {
            $changesText = "\n\n**التغييرات:**\n";
            foreach ($changes as $field => $change) {
                $changesText .= "• {$field}: {$change['old']} ← {$change['new']}\n";
            }
        }
        
        $message = "✏️ **تم تحديث الفاتورة**\n\n" .
                   "رقم الفاتورة: {$invoice->number}\n" .
                   "العميل: {$invoice->client->name}\n" .
                   $changesText .
                   "\n👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, $invoice, $message, 'invoice_updated', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'changes' => $changes,
            'action' => 'updated'
        ]);
    }

    /**
     * Log invoice payment
     */
    public function logInvoicePayment(Invoice $invoice, float $amount, string $paymentMethod = null): ?Message
    {
        $conversation = $this->getOrCreateInvoiceConversation($invoice);
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $remainingAmount = $invoice->total_price - $invoice->paid_amount;
        $paymentMethodText = $paymentMethod ? "\nطريقة الدفع: {$this->getPaymentMethodArabic($paymentMethod)}" : '';
        
        $message = "💰 **تم استلام دفعة**\n\n" .
                   "رقم الفاتورة: {$invoice->number}\n" .
                   "مبلغ الدفعة: " . number_format($amount, 2) . " ريال\n" .
                   "المبلغ المدفوع: " . number_format($invoice->paid_amount, 2) . " ريال\n" .
                   "المبلغ المتبقي: " . number_format($remainingAmount, 2) . " ريال\n" .
                   "الحالة: {$this->getPaymentStatusArabic($invoice->payment_status)}" .
                   $paymentMethodText .
                   "\n\n👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, $invoice, $message, 'invoice_payment', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'payment_amount' => $amount,
            'total_paid' => $invoice->paid_amount,
            'remaining_amount' => $remainingAmount,
            'payment_method' => $paymentMethod,
            'action' => 'payment_received'
        ]);
    }

    /**
     * Log invoice approval
     */
    public function logInvoiceApproved(Invoice $invoice, string $notes = null): ?Message
    {
        $conversation = $this->getOrCreateInvoiceConversation($invoice);
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $notesText = $notes ? "\n\n**ملاحظات:** {$notes}" : '';
        
        $message = "✅ **تم اعتماد الفاتورة**\n\n" .
                   "رقم الفاتورة: {$invoice->number}\n" .
                   "العميل: {$invoice->client->name}\n" .
                   "تاريخ الاعتماد: " . now()->format('Y-m-d H:i') .
                   $notesText .
                   "\n\n👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, $invoice, $message, 'invoice_approved', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'approved_by' => $invoice->approved_by,
            'notes' => $notes,
            'action' => 'approved'
        ]);
    }

    /**
     * Log invoice rejection
     */
    public function logInvoiceRejected(Invoice $invoice, string $notes = null): ?Message
    {
        $conversation = $this->getOrCreateInvoiceConversation($invoice);
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $notesText = $notes ? "\n\n**سبب الرفض:** {$notes}" : '';
        
        $message = "❌ **تم رفض الفاتورة**\n\n" .
                   "رقم الفاتورة: {$invoice->number}\n" .
                   "العميل: {$invoice->client->name}\n" .
                   "تاريخ الرفض: " . now()->format('Y-m-d H:i') .
                   $notesText .
                   "\n\n👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, $invoice, $message, 'invoice_rejected', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'rejected_by' => $invoice->approved_by,
            'notes' => $notes,
            'action' => 'rejected'
        ]);
    }

    /**
     * Log invoice cancellation
     */
    public function logInvoiceCancelled(Invoice $invoice, string $reason = null): ?Message
    {
        $conversation = $this->getOrCreateInvoiceConversation($invoice);
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $reasonText = $reason ? "\n\n**السبب:** {$reason}" : '';
        
        $message = "🚫 **تم إلغاء الفاتورة**\n\n" .
                   "رقم الفاتورة: {$invoice->number}\n" .
                   "العميل: {$invoice->client->name}\n" .
                   "تاريخ الإلغاء: " . now()->format('Y-m-d H:i') .
                   $reasonText .
                   "\n\n👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, $invoice, $message, 'invoice_cancelled', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'cancellation_reason' => $reason,
            'action' => 'cancelled'
        ]);
    }

    /**
     * Log employee creation
     */
    public function logEmployeeCreated(Employee $employee, Invoice $invoice = null): ?Message
    {
        // If employee is linked to an invoice, log to invoice conversation
        if ($invoice) {
            $conversation = $this->getOrCreateInvoiceConversation($invoice);
        } else {
            // Log to client conversation if available
            $conversation = $this->getOrCreateClientConversation($employee->client);
        }
        
        if (!$conversation) {
            return null;
        }
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $invoiceText = $invoice ? "\nالفاتورة: {$invoice->number}" : '';
        
        $message = "👤 **تم إضافة موظف جديد**\n\n" .
                   "الاسم: {$employee->name}\n" .
                   "العميل: {$employee->client->name}\n" .
                   "الراتب الشهري: " . number_format($employee->monthly_salary ?? 0, 2) . " ريال\n" .
                   "راتب حماية الأجور: " . number_format($employee->wage_salary ?? 0, 2) . " ريال\n" .
                   "نوع الملف: {$employee->file_type}" .
                   $invoiceText .
                   "\n\n👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, $invoice, $message, 'employee_created', [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'client_id' => $employee->client_id,
            'invoice_id' => $invoice?->id,
            'action' => 'employee_added'
        ]);
    }

    /**
     * Log employees import
     */
    public function logEmployeesImported(Invoice $invoice, int $count, array $summary = []): ?Message
    {
        $conversation = $this->getOrCreateInvoiceConversation($invoice);
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $summaryText = '';
        if (!empty($summary)) {
            $summaryText = "\n\n**الملخص:**\n";
            if (isset($summary['total_salaries'])) {
                $summaryText .= "• إجمالي الرواتب: " . number_format($summary['total_salaries'], 2) . " ريال\n";
            }
            if (isset($summary['wps_count'])) {
                $summaryText .= "• موظفي حماية الأجور: {$summary['wps_count']}\n";
            }
            if (isset($summary['monthly_count'])) {
                $summaryText .= "• موظفي الرواتب الشهرية: {$summary['monthly_count']}\n";
            }
        }
        
        $message = "📊 **تم استيراد موظفين**\n\n" .
                   "رقم الفاتورة: {$invoice->number}\n" .
                   "العميل: {$invoice->client->name}\n" .
                   "عدد الموظفين: {$count}" .
                   $summaryText .
                   "\n\n👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, $invoice, $message, 'employees_imported', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'employees_count' => $count,
            'summary' => $summary,
            'action' => 'employees_imported'
        ]);
    }

    /**
     * Log employee payment
     */
    public function logEmployeePayment($employeeId, string $employeeName, Invoice $invoice, float $amount, string $paymentType = 'full'): ?Message
    {
        $conversation = $this->getOrCreateInvoiceConversation($invoice);
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $paymentTypeText = $paymentType === 'full' ? 'كامل' : 'جزئي';
        
        $message = "💵 **تم دفع راتب موظف**\n\n" .
                   "الموظف: {$employeeName}\n" .
                   "الفاتورة: {$invoice->number}\n" .
                   "المبلغ المدفوع: " . number_format($amount, 2) . " ريال\n" .
                   "نوع الدفع: {$paymentTypeText}\n\n" .
                   "👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, $invoice, $message, 'employee_payment', [
            'employee_id' => $employeeId,
            'employee_name' => $employeeName,
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'payment_amount' => $amount,
            'payment_type' => $paymentType,
            'action' => 'employee_paid'
        ]);
    }

    /**
     * Log bulk employee payments
     */
    public function logBulkEmployeePayments(Invoice $invoice, int $count, float $totalAmount): ?Message
    {
        $conversation = $this->getOrCreateInvoiceConversation($invoice);
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $message = "💰 **تم دفع رواتب متعددة**\n\n" .
                   "الفاتورة: {$invoice->number}\n" .
                   "عدد الموظفين: {$count}\n" .
                   "إجمالي المبلغ: " . number_format($totalAmount, 2) . " ريال\n\n" .
                   "👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, $invoice, $message, 'bulk_employee_payment', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'employees_count' => $count,
            'total_amount' => $totalAmount,
            'action' => 'bulk_payment'
        ]);
    }

    /**
     * Log client creation
     */
    public function logClientCreated(Client $client): ?Message
    {
        $conversation = $this->getOrCreateClientConversation($client);
        
        if (!$conversation) {
            return null;
        }
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $message = "🏢 **تم إضافة عميل جديد**\n\n" .
                   "الاسم: {$client->name}\n" .
                   "الرقم الضريبي: {$client->tax_number}\n" .
                   "الهاتف: " . ($client->phone ?? 'غير محدد') . "\n" .
                   "البريد الإلكتروني: " . ($client->email ?? 'غير محدد') . "\n\n" .
                   "👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, null, $message, 'client_created', [
            'client_id' => $client->id,
            'client_name' => $client->name,
            'action' => 'client_created'
        ]);
    }

    /**
     * Log client update
     */
    public function logClientUpdated(Client $client, array $changes = []): ?Message
    {
        $conversation = $this->getOrCreateClientConversation($client);
        
        if (!$conversation) {
            return null;
        }
        
        $user = Auth::user();
        $userName = $user ? $user->name : 'النظام';
        
        $changesText = '';
        if (!empty($changes)) {
            $changesText = "\n\n**التغييرات:**\n";
            foreach ($changes as $field => $change) {
                $changesText .= "• {$field}: {$change['old']} ← {$change['new']}\n";
            }
        }
        
        $message = "✏️ **تم تحديث بيانات العميل**\n\n" .
                   "العميل: {$client->name}\n" .
                   $changesText .
                   "\n👤 بواسطة: {$userName}";
        
        return $this->createSystemMessage($conversation, null, $message, 'client_updated', [
            'client_id' => $client->id,
            'client_name' => $client->name,
            'changes' => $changes,
            'action' => 'client_updated'
        ]);
    }

    /**
     * Get or create conversation for invoice
     */
    protected function getOrCreateInvoiceConversation(Invoice $invoice): Conversation
    {
        $conversation = Conversation::where('invoice_id', $invoice->id)
            ->where('client_id', $invoice->client_id)
            ->first();

        if ($conversation) {
            return $conversation;
        }

        $conversation = Conversation::create([
            'id' => (string) Str::uuid(),
            'sender_id' => Auth::id() ?? 1,
            'receiver_id' => $invoice->client->sales_rep_id ?? Auth::id() ?? 1,
            'client_id' => $invoice->client_id,
            'invoice_id' => $invoice->id,
            'type' => 'invoice',
        ]);

        $allUserIds = User::pluck('id')->toArray();
        $conversation->users()->sync(array_unique($allUserIds));

        return $conversation;
    }

    /**
     * Get or create conversation for client
     */
    protected function getOrCreateClientConversation(Client $client): ?Conversation
    {
        $conversation = Conversation::where('client_id', $client->id)
            ->whereNull('invoice_id')
            ->where('type', 'client')
            ->first();

        if ($conversation) {
            return $conversation;
        }

        $conversation = Conversation::create([
            'id' => (string) Str::uuid(),
            'sender_id' => Auth::id() ?? 1,
            'receiver_id' => $client->sales_rep_id ?? Auth::id() ?? 1,
            'client_id' => $client->id,
            'invoice_id' => null,
            'type' => 'client',
        ]);

        $allUserIds = User::pluck('id')->toArray();
        $conversation->users()->sync(array_unique($allUserIds));

        return $conversation;
    }

    /**
     * Create system message
     */
    protected function createSystemMessage(
        Conversation $conversation,
        ?Invoice $invoice,
        string $message,
        string $messageType,
        array $metadata = []
    ): Message {
        return Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id() ?? 1,
            'receiver_id' => null,
            'invoice_id' => $invoice?->id,
            'message' => $message,
            'message_type' => 'system',
            'metadata' => array_merge($metadata, [
                'system_message_type' => $messageType,
                'timestamp' => now()->toIso8601String(),
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->name ?? 'النظام'
            ]),
        ]);
    }

    /**
     * Get payment status in Arabic
     */
    protected function getPaymentStatusArabic(string $status): string
    {
        return match($status) {
            'paid' => '✅ مدفوعة',
            'pending' => '⏳ قيد الانتظار',
            'overdue' => '⚠️ متأخرة',
            'late' => '🔔 متأخرة جزئياً',
            'partially_paid' => '💵 مدفوعة جزئياً',
            'cancelled' => '❌ ملغاة',
            default => $status,
        };
    }

    /**
     * Get payment method in Arabic
     */
    protected function getPaymentMethodArabic(string $method): string
    {
        return match($method) {
            'bank_transfer' => 'تحويل بنكي',
            'cash' => 'نقداً',
            'check' => 'شيك',
            'wps' => 'حماية الأجور',
            'monthly' => 'شهري',
            default => $method,
        };
    }
}
