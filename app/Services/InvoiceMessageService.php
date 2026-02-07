<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvoiceMessageService
{
    /**
     * Create or get conversation for an invoice
     */
    public function getOrCreateConversation(Invoice $invoice): Conversation
    {
        // Try to find existing conversation for this invoice
        $conversation = Conversation::where('invoice_id', $invoice->id)
            ->where('client_id', $invoice->client_id)
            ->first();

        if ($conversation) {
            return $conversation;
        }

        // Create new conversation
        $conversation = Conversation::create([
            'id' => (string) Str::uuid(),
            'sender_id' => Auth::id(),
            'receiver_id' => $invoice->client->sales_rep_id ?? Auth::id(),
            'client_id' => $invoice->client_id,
            'invoice_id' => $invoice->id,
            'type' => 'invoice',
        ]);

        // Add all system users as participants
        $allUserIds = User::pluck('id')->toArray();
        $conversation->users()->sync(array_unique($allUserIds));

        return $conversation;
    }

    /**
     * Send invoice details as a message
     */
    public function sendInvoiceDetailsMessage(Invoice $invoice, ?string $additionalText = null): Message
    {
        $conversation = $this->getOrCreateConversation($invoice);

        $invoiceDetails = [
            'invoice_number' => $invoice->number,
            'service_name' => $invoice->service->name ?? 'N/A',
            'total_price' => $invoice->total_price,
            'due_date' => $invoice->due_date?->format('Y-m-d'),
            'payment_status' => $invoice->payment_status,
            'generation_date' => $invoice->generation_date?->format('Y-m-d'),
        ];

        $messageText = $additionalText ?? $this->formatInvoiceMessage($invoice);

        return Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $conversation->getReceiver()?->id,
            'invoice_id' => $invoice->id,
            'message' => $messageText,
            'message_type' => 'invoice_info',
            'metadata' => $invoiceDetails,
        ]);
    }

    /**
     * Send a custom message in invoice conversation
     */
    public function sendMessage(Invoice $invoice, string $message, string $type = 'text'): Message
    {
        $conversation = $this->getOrCreateConversation($invoice);

        return Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $conversation->getReceiver()?->id,
            'invoice_id' => $invoice->id,
            'message' => $message,
            'message_type' => $type,
        ]);
    }

    /**
     * Format invoice details as Arabic message
     */
    private function formatInvoiceMessage(Invoice $invoice): string
    {
        $serviceName = $invoice->service->name ?? 'غير محدد';
        $totalPrice = number_format($invoice->total_price, 2);
        $dueDate = $invoice->due_date?->format('Y-m-d') ?? 'غير محدد';

        return "📄 تم إنشاء فاتورة جديدة\n\n" .
               "رقم الفاتورة: {$invoice->number}\n" .
               "الخدمة: {$serviceName}\n" .
               "المبلغ الإجمالي: {$totalPrice} ريال\n" .
               "تاريخ الاستحقاق: {$dueDate}\n" .
               "الحالة: " . $this->getPaymentStatusArabic($invoice->payment_status);
    }

    /**
     * Get Arabic payment status
     */
    private function getPaymentStatusArabic(string $status): string
    {
        return match($status) {
            'paid' => '✅ مدفوعة',
            'pending' => '⏳ قيد الانتظار',
            'overdue' => '⚠️ متأخرة',
            'late' => '🔔 متأخرة جزئياً',
            'cancelled' => '❌ ملغاة',
            default => $status,
        };
    }

    /**
     * Send invoice update notification
     */
    public function sendInvoiceUpdateMessage(Invoice $invoice, string $updateType, array $changes = []): Message
    {
        $conversation = $this->getOrCreateConversation($invoice);

        $message = $this->formatUpdateMessage($invoice, $updateType, $changes);

        return Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $conversation->getReceiver()?->id,
            'invoice_id' => $invoice->id,
            'message' => $message,
            'message_type' => 'system',
            'metadata' => [
                'update_type' => $updateType,
                'changes' => $changes,
            ],
        ]);
    }

    /**
     * Format update message
     */
    private function formatUpdateMessage(Invoice $invoice, string $updateType, array $changes): string
    {
        $messages = [
            'status_changed' => "🔄 تم تحديث حالة الفاتورة {$invoice->number}",
            'payment_received' => "💰 تم استلام دفعة للفاتورة {$invoice->number}",
            'invoice_cancelled' => "❌ تم إلغاء الفاتورة {$invoice->number}",
            'due_date_changed' => "📅 تم تغيير تاريخ استحقاق الفاتورة {$invoice->number}",
        ];

        return $messages[$updateType] ?? "تم تحديث الفاتورة {$invoice->number}";
    }

    /**
     * Get unread messages count for user
     */
    public function getUnreadCount(?int $userId = null): int
    {
        $userId = $userId ?? Auth::id();

        return Message::whereHas('conversation', function($query) use ($userId) {
                $query->whereHas('users', function($q) use ($userId) {
                    $q->where('users.id', $userId);
                });
            })
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
