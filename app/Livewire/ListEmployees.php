<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Client;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ListEmployees extends Component
{
    use WithPagination;

    public $search = '';
    public $client = '';
    public $bank = '';
    public $salary_type = '';
    public $salary_from = '';

    public $totalEmployees;
    public $totalSalaries;
    public $averageSalary;
    public $totalAbsences;

    protected $queryString = [
        'search' => ['except' => ''],
        'client' => ['except' => ''],
        'bank' => ['except' => ''],
        'salary_type' => ['except' => ''],
        'salary_from' => ['except' => ''],
    ];

    public function mount()
    {
        $this->calculateStatistics();
    }

    public function updating($property, $value)
    {
        if (in_array($property, ['search', 'client', 'bank', 'salary_type', 'salary_from'])) {
            $this->resetPage();
        }
    }

    public function getEmployees()
    {
        $query = Employee::with(['client', 'invoice', 'fileTypes'])
            ->select('employees.*')
            ->leftJoin('clients', 'employees.client_id', '=', 'clients.id')
            ->leftJoin('invoices', 'employees.invoice_id', '=', 'invoices.id');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('employees.name', 'like', "%{$this->search}%")
                    ->orWhere('employees.phone', 'like', "%{$this->search}%")
                    ->orWhere('clients.name', 'like', "%{$this->search}%");
            });
        }

        // Apply client filter
        if (!empty($this->client)) {
            $query->where('employees.client_id', $this->client);
        }

        // Apply bank filter
        if (!empty($this->bank)) {
            $query->where('employees.bank_name', $this->bank);
        }

        // Apply salary type filter
        if (!empty($this->salary_type)) {
            if ($this->salary_type === 'with_safety') {
                $query->where('employees.salary_with_safety', '>', 0);
            } else {
                $query->where('employees.salary_with_safety', 0);
            }
        }

        // Apply salary range filter
        if (!empty($this->salary_from)) {
            $query->where('employees.month_salary', '>=', $this->salary_from);
        }

        return $query->orderBy('employees.created_at', 'desc')
            ->paginate(10);
    }

    /**
     * Calculate statistics for the dashboard
     */
    private function calculateStatistics()
    {
        // Total employees
        $this->totalEmployees = Employee::count();

        // Total salaries for current month
        $this->totalSalaries = Employee::sum('final_salary');

        // Average salary
        $this->averageSalary = Employee::avg('month_salary');

        // Total absence days
        $this->totalAbsences = Employee::sum('absence_days');
    }

    /**
     * Get payment status badge
     */
    public function getPaymentStatusBadge($employee)
    {
        $status = $employee->payment_status;

        switch ($status) {
            case 'paid':
                return [
                    'class' => 'bg-success',
                    'text' => 'مدفوع',
                    'icon' => 'check-circle'
                ];
            case 'partially_paid':
                return [
                    'class' => 'bg-warning',
                    'text' => 'مدفوع جزئياً',
                    'icon' => 'clock'
                ];
            default:
                return [
                    'class' => 'bg-danger',
                    'text' => 'غير مدفوع',
                    'icon' => 'exclamation-triangle'
                ];
        }
    }

    /**
     * Get file type badge
     */
    public function getFileTypeBadge($employee)
    {
        $fileType = $employee->fileTypes->first();

        if ($fileType) {
            return [
                'name' => $fileType->name,
                'class' => $fileType->name === 'رواتب' ? 'bg-primary' : 'bg-success'
            ];
        }

        return [
            'name' => 'غير محدد',
            'class' => 'bg-secondary'
        ];
    }

    /**
     * Format currency
     */
    public function formatCurrency($amount)
    {
        return number_format($amount, 2) . ' ﷼';
    }
    #[Layout('layouts.master')]
    public function render()
    {
        $employees = $this->getEmployees();
        $clients = Client::all();

        return view('livewire.list-employees', [
            'employees' => $employees,
            'clients' => $clients,
        ]);
    }
}
