<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;

class ListInvoices extends Component
{
    use WithPagination;

    public $totalSupervisors = 0;
    public $totalManagers = 0;
    public $basePrice = 0;
    public $totalWorkforce = 0;
    public $totalUsers = 0;

    // Form properties
    public $clientName = '';
    public $clientSearch = '';
    public $showNewClientInput = false;
    public $newClientName = '';
    public $filteredClients = [];

    public $serviceType = '';
    public $serviceSearch = '';
    public $filteredServices = [];
    public $newService = '';
    public $showNewServiceInput = false;

    public $clientEmail;
    public $clientPhone;
    public $clientAddress;
    public $invoiceNumber;
    public $invoiceDate;
    public $dueDate;
    public $totalWorkers;
    public $workDays;
    public $dailyRate;
    public $subtotal = 0;
    public $taxRate = 15;
    public $taxAmount = 0;
    public $totalAmount = 0;
    public $amountDifference = 0;
    public $paymentStatus = 'pending';
    public $paymentDate;
    public $invoiceStatus = 'pending';
    public $invoiceNotes;

    // Filter properties
    public $search = '';
    public $statusFilter = '';
    public $clientFilter = '';
    public $startDate = '';
    public $endDate = '';

    // Modal state
    public $showModal = false;

    protected $rules = [
        'clientName' => 'required|exists:clients,id',
        'clientEmail' => 'nullable|email',
        'clientPhone' => 'nullable|string',
        'clientAddress' => 'nullable|string',
        'invoiceNumber' => 'required|string|unique:invoices,number',
        'invoiceDate' => 'required|date',
        'dueDate' => 'required|date',
        'serviceType' => 'required|exists:services,id',
        'totalWorkers' => 'required|integer|min:1',
        'workDays' => 'required|integer|min:1',
        'dailyRate' => 'required|numeric|min:0',
        'taxRate' => 'required|numeric|min:0|max:100',
        'amountDifference' => 'nullable|numeric',
        'paymentStatus' => 'required|string',
        'invoiceStatus' => 'required|string',
        'invoiceNotes' => 'nullable|string',
    ];

    protected $messages = [
        'clientName.required' => 'يرجى اختيار أو إضافة عميل',
        'serviceType.required' => 'يرجى اختيار أو إضافة خدمة',
    ];

    public function mount()
    {
        $this->invoiceDate = now()->format('Y-m-d');
        $this->filteredServices = Service::pluck('name', 'id')->toArray();
        $this->filteredClients = Client::pluck('name', 'id')->toArray();
        $this->dueDate = now()->addDays(30)->format('Y-m-d');
        $this->invoiceNumber = '#INV-' . now()->format('Y-m-') . str_pad(Invoice::count() + 1, 3, '0', STR_PAD_LEFT);
    }

    // Client methods
    public function toggleNewClient()
    {
        $this->showNewClientInput = !$this->showNewClientInput;
        $this->newClientName = '';

        if (!$this->showNewClientInput) {
            $this->filterClients();
        }
    }

    public function addNewClient()
    {
        $this->validate([
            'newClientName' => 'required|string|min:2|unique:clients,name'
        ], [
            'newClientName.required' => 'اسم العميل مطلوب',
            'newClientName.unique' => 'هذا العميل موجود مسبقاً'
        ]);

        try {
            // Create new client with all details
            $client = Client::create([
                'name' => trim($this->newClientName),
                'email' => $this->clientEmail ?? null,
                'phone' => $this->clientPhone ?? null,
                'address' => $this->clientAddress ?? null
            ]);

            // Refresh clients list and select the new client
            $this->filteredClients = Client::pluck('name', 'id')->toArray();
            $this->clientName = $client->id;

            // Preserve contact info
            $this->clientEmail = $client->email;
            $this->clientPhone = $client->phone;
            $this->clientAddress = $client->address;

            $this->newClientName = '';
            $this->showNewClientInput = false;
            $this->clientSearch = $client->name;

            session()->flash('client_message', 'تم إضافة العميل بنجاح: ' . $client->name);

        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء إضافة العميل: ' . $e->getMessage());
        }
    }

    public function createClientFromSearch($name)
    {
        $this->newClientName = $name;
        $this->addNewClient();
    }

    public function filterClients()
    {
        if (empty($this->clientSearch)) {
            $this->filteredClients = Client::pluck('name', 'id')->toArray();
        } else {
            $this->filteredClients = Client::where('name', 'like', '%' . $this->clientSearch . '%')
                ->pluck('name', 'id')
                ->toArray();
        }
    }

    public function selectClient($clientId)
    {
        $client = Client::find($clientId);
        if ($client) {
            $this->clientName = $clientId;
            $this->clientEmail = $client->email;
            $this->clientPhone = $client->phone;
            $this->clientAddress = $client->address;
            $this->clientSearch = $client->name;
        }
    }

    // Service methods
    // Service methods
    public function toggleNewService()
    {
        $this->showNewServiceInput = !$this->showNewServiceInput;
        $this->newService = '';

        if (!$this->showNewServiceInput) {
            $this->filterServices();
        }
    }

    public function addNewService()
    {
        $this->validate([
            'newService' => 'required|string|min:2|unique:services,name'
        ], [
            'newService.required' => 'اسم الخدمة مطلوب',
            'newService.unique' => 'هذه الخدمة موجودة مسبقاً'
        ]);

        try {
            // Create new service
            $service = Service::create([
                'name' => trim($this->newService),
                'description' => ''
            ]);

            // Refresh services list and select the new service
            $this->filteredServices = Service::pluck('name', 'id')->toArray();
            $this->serviceType = $service->id;
            $this->newService = '';
            $this->showNewServiceInput = false;
            $this->serviceSearch = $service->name;

            session()->flash('service_message', 'تم إضافة الخدمة بنجاح: ' . $service->name);

        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء إضافة الخدمة: ' . $e->getMessage());
        }
    }

    public function createServiceFromSearch($name)
    {
        $this->newService = $name;
        $this->addNewService();
    }

    public function filterServices()
    {
        if (empty($this->serviceSearch)) {
            $this->filteredServices = Service::pluck('name', 'id')->toArray();
        } else {
            $this->filteredServices = Service::where('name', 'like', '%' . $this->serviceSearch . '%')
                ->pluck('name', 'id')
                ->toArray();
        }
    }

    public function selectService($serviceId)
    {
        $this->serviceType = $serviceId;
        $service = Service::find($serviceId);
        if ($service) {
            $this->serviceSearch = $service->name;
        }
    }
    public function calculateSubtotal()
    {
        $workers = intval($this->totalWorkers) ?: 0;
        $days = intval($this->workDays) ?: 0;
        $rate = floatval($this->dailyRate) ?: 0;

        $this->subtotal = $workers * $days * $rate;
        $this->calculateTaxAndTotal();
    }
    public function getTotalWorkforceProperty()
    {
        return $this->totalWorkers + $this->totalSupervisors + $this->totalManagers + $this->totalUsers;
    }

    public function getBasePriceProperty()
    {
        // هنا يمكنك إضافة منطق حساب السعر بناءً على أنواع العمالة وأيام العمل
        // مثال: (عدد العمال × سعر العامل) + (عدد المشرفين × سعر المشرف) ... إلخ
        $workerRate = 100; // سعر العامل اليومي
        $supervisorRate = 150; // سعر المشرف اليومي
        $managerRate = 200; // سعر المدير اليومي
        $userRate = 120; // سعر المستخدم اليومي

        return (
                ($this->totalWorkers * $workerRate) +
                ($this->totalSupervisors * $supervisorRate) +
                ($this->totalManagers * $managerRate) +
                ($this->totalUsers * $userRate)
            ) * $this->workDays;
    }

    public function getTotalAmountProperty()
    {
        return $this->basePrice + $this->taxAmount + $this->amountDifference;
    }
    public function calculateTaxAndTotal()
    {
        $subtotal = floatval($this->subtotal) ?: 0;
        $taxRate = floatval($this->taxRate) ?: 0;

        $this->taxAmount = ($subtotal * $taxRate) / 100;
        $this->totalAmount = $subtotal + $this->taxAmount;
    }

    public function updated($propertyName)
    {
        // Calculate financials
        if (in_array($propertyName, ['totalWorkers', 'workDays', 'dailyRate'])) {
            $this->calculateSubtotal();
        }

        if (in_array($propertyName, ['taxRate', 'subtotal'])) {
            $this->calculateTaxAndTotal();
        }

        // Auto-update filters
        if (in_array($propertyName, ['search', 'statusFilter', 'clientFilter', 'startDate', 'endDate'])) {
            $this->resetPage();
        }

        // Filter clients when search changes
        if ($propertyName === 'clientSearch' && !$this->showNewClientInput) {
            $this->filterClients();
        }

        // Filter services when search changes
        if ($propertyName === 'serviceSearch' && !$this->showNewServiceInput) {
            $this->filterServices();
        }

        // When client is selected from dropdown, update contact info
        if ($propertyName === 'clientName' && !empty($this->clientName)) {
            $this->loadClientInfo($this->clientName);
        }
    }

    protected function loadClientInfo($clientId)
    {
        $client = Client::find($clientId);
        if ($client) {
            $this->clientEmail = $client->email;
            $this->clientPhone = $client->phone;
            $this->clientAddress = $client->address;
            $this->clientSearch = $client->name;
        }
    }

    public function createInvoice()
    {
        $this->validate();

        try {
            // Create invoice
            $invoice = Invoice::create([
                'client_id' => $this->clientName,
                'client_email' => $this->clientEmail,
                'client_phone' => $this->clientPhone,
                'client_address' => $this->clientAddress,
                'number' => $this->invoiceNumber,
                'generation_date' => $this->invoiceDate,
                'last_generation_date' => $this->dueDate,
                'service_id' => $this->serviceType,
                'total_workers' => $this->totalWorkers,
                'work_days' => $this->workDays,
                'daily_rate' => $this->dailyRate,
                'subtotal' => $this->subtotal,
                'tax_rate' => $this->taxRate,
                'tax_amount' => $this->taxAmount,
                'total_amount' => $this->totalAmount,
                'amount_difference' => $this->amountDifference,
                'payment_status' => $this->paymentStatus,
                'payment_date' => $this->paymentDate,
                'invoice_status' => $this->invoiceStatus,
                'notes' => $this->invoiceNotes,
            ]);

            // Reset form and close modal
            $this->resetForm();
            $this->showModal = false;

            session()->flash('message', 'تم إنشاء الفاتورة بنجاح!');

        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset([
            'clientName', 'clientSearch', 'newClientName', 'showNewClientInput',
            'clientEmail', 'clientPhone', 'clientAddress',
            'invoiceNumber', 'invoiceDate', 'dueDate', 'serviceType', 'serviceSearch',
            'totalWorkers', 'workDays', 'dailyRate', 'subtotal',
            'taxRate', 'taxAmount', 'totalAmount', 'amountDifference',
            'paymentStatus', 'paymentDate', 'invoiceStatus', 'invoiceNotes'
        ]);

        $this->filteredClients = Client::pluck('name', 'id')->toArray();
        $this->filteredServices = Service::pluck('name', 'id')->toArray();

        $this->invoiceDate = now()->format('Y-m-d');
        $this->dueDate = now()->addDays(30)->format('Y-m-d');
        $this->invoiceNumber = '#INV-' . now()->format('Y-m-') . str_pad(Invoice::count() + 1, 3, '0', STR_PAD_LEFT);
        $this->taxRate = 15;
        $this->subtotal = 0;
        $this->taxAmount = 0;
        $this->totalAmount = 0;
        $this->amountDifference = 0;
        $this->paymentStatus = 'pending';
        $this->invoiceStatus = 'pending';
    }

    public function resetFilters()
    {
        $this->reset(['search', 'statusFilter', 'clientFilter', 'startDate', 'endDate']);
        $this->resetPage();
    }

    public function getInvoicesQuery()
    {
        return Invoice::with(['client', 'service'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('client', function ($clientQuery) {
                            $clientQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('payment_status', $this->statusFilter);
            })
            ->when($this->clientFilter, function ($query) {
                $query->whereHas('client', function ($clientQuery) {
                    $clientQuery->where('name', $this->clientFilter);
                });
            })
            ->when($this->startDate, function ($query) {
                $query->where('generation_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->where('generation_date', '<=', $this->endDate);
            })
            ->orderBy('created_at', 'desc');
    }

    #[Layout('layouts.master')]
    public function render()
    {
        $invoices = $this->getInvoicesQuery()->paginate(10);

        $stats = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('payment_status', 'paid')->count(),
            'pending' => Invoice::where('payment_status', 'pending')->count(),
            'overdue' => Invoice::where('payment_status', 'overdue')->count(),
            'late' => Invoice::where('payment_status', 'late')->count(),
        ];

        $clients = Client::distinct()->pluck('name');
        $services = Service::distinct()->pluck('name','id');

        return view('livewire.list-invoices', compact('invoices', 'stats', 'clients','services'));
    }
}
