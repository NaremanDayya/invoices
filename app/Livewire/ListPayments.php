<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class ListPayments extends Component
{
    use WithPagination;

    public $search = '';
    public $clientFilter = '';
    public $approvalStatusFilter = '';
    public $paymentStatusFilter = '';
    public $fromDate = '';
    public $toDate = '';

    // Modal fields - following the same pattern as ListInvoices
    public $showModal = false;
    public $editingPayment = null;

    // Form fields
    public $client_id = '';
    public $payment_type = '';
    public $total_price = 0;
    public $employees_number = 0;
    public $approvement_status = 'pending';
    public $payment_status = 'pending';
    public $late_days = 0;
    public $generation_date;
    public $employees_file = '';
    public $days_number = 0;
    public $management_acceptance_date;
    public $payment_date;
    public $deductions_total = 0;
    public $invoice_id = '';
    public $paid_amount = 0;
    public $remaining_amount = 0;
    public $payment_method = '';
    public $reference_number = '';

    protected $rules = [
        'client_id' => 'required|exists:clients,id',
        'payment_type' => 'required|string',
        'total_price' => 'required|numeric|min:0',
        'employees_number' => 'required|integer|min:1',
        'approvement_status' => 'required|in:pending,approved,rejected',
        'payment_status' => 'required|in:pending,paid,failed',
        'late_days' => 'integer|min:0',
        'generation_date' => 'required|date',
        'employees_file' => 'nullable|string',
        'days_number' => 'required|integer|min:1',
        'management_acceptance_date' => 'nullable|date',
        'payment_date' => 'nullable|date',
        'deductions_total' => 'numeric|min:0',
        'invoice_id' => 'nullable|exists:invoices,id',
        'paid_amount' => 'numeric|min:0',
        'remaining_amount' => 'numeric|min:0',
        'payment_method' => 'required|string',
        'reference_number' => 'nullable|string'
    ];

    public function mount()
    {
        $this->generation_date = now()->format('Y-m-d');
        // Generate reference number like invoices component
        $this->reference_number = '#PAY-' . now()->format('Y-m-') . str_pad(Payment::count() + 1, 3, '0', STR_PAD_LEFT);
    }

    #[Layout('layouts.master')]
    public function render()
    {
        $query = Payment::with(['client', 'invoice'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('reference_number', 'like', '%' . $this->search . '%')
                        ->orWhere('employees_file', 'like', '%' . $this->search . '%')
                        ->orWhereHas('client', function ($clientQuery) {
                            $clientQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->clientFilter, function ($query) {
                $query->where('client_id', $this->clientFilter);
            })
            ->when($this->approvalStatusFilter, function ($query) {
                $query->where('approvement_status', $this->approvalStatusFilter);
            })
            ->when($this->paymentStatusFilter, function ($query) {
                $query->where('payment_status', $this->paymentStatusFilter);
            })
            ->when($this->fromDate, function ($query) {
                $query->where('generation_date', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($query) {
                $query->where('generation_date', '<=', $this->toDate);
            });

        $payments = $query->latest()->paginate(10);

        $clients = Client::all();
        $invoices = Invoice::all();

        // Statistics
        $stats = [
            'total' => Payment::count(),
            'completed' => Payment::where('payment_status', 'paid')->where('approvement_status', 'approved')->count(),
            'pending_review' => Payment::where('approvement_status', 'pending')->count(),
            'cancelled' => Payment::where('approvement_status', 'rejected')->orWhere('payment_status', 'failed')->count(),
        ];

        return view('livewire.list-payments', compact('payments', 'clients', 'invoices', 'stats'));
    }

    public function resetFilters()
    {
        $this->reset(['search', 'clientFilter', 'approvalStatusFilter', 'paymentStatusFilter', 'fromDate', 'toDate']);
        $this->resetPage();
    }

    // Updated modal methods to follow invoices pattern
    public function showCreateModal()
    {
        $this->resetModal();
        $this->showModal = true;
    }

    public function showEditModal($paymentId)
    {
        $this->resetModal();
        $this->editingPayment = Payment::findOrFail($paymentId);

        // Fill the form with existing data
        $this->fill($this->editingPayment->toArray());
        $this->showModal = true;
    }

    public function savePayment()
    {
        $this->validate();

        // Calculate remaining amount
        $this->remaining_amount = $this->total_price - $this->paid_amount - $this->deductions_total;

        DB::transaction(function () {
            if ($this->editingPayment) {
                $this->editingPayment->update($this->getPaymentData());
                session()->flash('message', 'تم تحديث الدفع بنجاح.');
            } else {
                Payment::create($this->getPaymentData());
                session()->flash('message', 'تم إنشاء الدفع بنجاح.');
            }
        });

        $this->resetModal();
        $this->showModal = false;
    }

    public function closeModal()
    {
        $this->resetModal();
        $this->showModal = false;
    }

    public function deletePayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->delete();

        session()->flash('message', 'تم حذف الدفع بنجاح.');
    }

    public function confirmPayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->update([
            'payment_status' => 'paid',
            'payment_date' => now(),
            'paid_amount' => $payment->total_price - $payment->deductions_total,
            'remaining_amount' => 0
        ]);

        session()->flash('message', 'تم تأكيد الدفع بنجاح.');
    }

    public function approvePayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->update([
            'approvement_status' => 'approved',
            'management_acceptance_date' => now()
        ]);

        session()->flash('message', 'تم اعتماد الدفع بنجاح.');
    }

    private function getPaymentData()
    {
        return [
            'client_id' => $this->client_id,
            'payment_type' => $this->payment_type,
            'total_price' => $this->total_price,
            'employees_number' => $this->employees_number,
            'approvement_status' => $this->approvement_status,
            'payment_status' => $this->payment_status,
            'late_days' => $this->late_days,
            'generation_date' => $this->generation_date,
            'employees_file' => $this->employees_file,
            'days_number' => $this->days_number,
            'management_acceptance_date' => $this->management_acceptance_date,
            'payment_date' => $this->payment_date,
            'deductions_total' => $this->deductions_total,
            'invoice_id' => $this->invoice_id,
            'paid_amount' => $this->paid_amount,
            'remaining_amount' => $this->remaining_amount,
            'payment_method' => $this->payment_method,
            'reference_number' => $this->reference_number,
        ];
    }

    private function resetModal()
    {
        $this->reset([
            'editingPayment', 'client_id', 'payment_type', 'total_price', 'employees_number',
            'approvement_status', 'payment_status', 'late_days', 'generation_date',
            'employees_file', 'days_number', 'management_acceptance_date', 'payment_date',
            'deductions_total', 'invoice_id', 'paid_amount', 'remaining_amount',
            'payment_method', 'reference_number'
        ]);
        $this->generation_date = now()->format('Y-m-d');
        $this->reference_number = '#PAY-' . now()->format('Y-m-') . str_pad(Payment::count() + 1, 3, '0', STR_PAD_LEFT);
        $this->resetErrorBag();
    }

    // Real-time calculations
    public function updatedTotalPrice()
    {
        $this->calculateRemainingAmount();
    }

    public function updatedPaidAmount()
    {
        $this->calculateRemainingAmount();
    }

    public function updatedDeductionsTotal()
    {
        $this->calculateRemainingAmount();
    }

    public function updated($propertyName)
    {
        // Auto-update filters
        if (in_array($propertyName, ['search', 'clientFilter', 'approvalStatusFilter', 'paymentStatusFilter', 'fromDate', 'toDate'])) {
            $this->resetPage();
        }

        // Auto-calculate remaining amount
        if (in_array($propertyName, ['total_price', 'paid_amount', 'deductions_total'])) {
            $this->calculateRemainingAmount();
        }
    }

    private function calculateRemainingAmount()
    {
        $total = floatval($this->total_price ?? 0);
        $paid = floatval($this->paid_amount ?? 0);
        $deductions = floatval($this->deductions_total ?? 0);

        $this->remaining_amount = $total - $paid - $deductions;
    }
}
