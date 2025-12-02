<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Employee;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $statistics = $this->getDashboardStatistics();

        return view('dashboard', compact('statistics'));
    }

    private function getDashboardStatistics()
    {
        $today = Carbon::today();

        // Invoices Statistics
        $totalInvoices = Invoice::count();
        $issuedInvoices = Invoice::where('invoice_status', '!=', 'cancelled')->count();
        $cancelledInvoices = Invoice::where('invoice_status', 'cancelled')->count();

        // Late Invoices (both payment and generation)
        $latePaymentInvoices = Invoice::where('payment_status', 'late')
            ->orWhere(function($query) use ($today) {
                $query->where('due_date', '<', $today)
                    ->where('payment_status', 'pending');
            })->count();

        $lateGenerationInvoices = Invoice::where('generation_date', '<', $today)
            ->where('invoice_status', 'pending')
            ->count();

        $totalLateInvoices = $latePaymentInvoices + $lateGenerationInvoices;

        // Employees Statistics by type
        $usersCount = Employee::where('file_type', '!=', 'حماية أجور')->count();
        $workersCount = Employee::where('file_type', 'حماية أجور')->count();
        $supervisorsCount = Invoice::sum('total_supervisors');
        $managersCount = Invoice::sum('total_managers');
        // Financial Differences
        $financialForUs = Invoice::sum('price_difference');
        $financialAgainstUs = Invoice::where('price_difference', '<', 0)->sum('price_difference');
        $totalWorkDays = Employee::sum('work_days');

        return [
            // Invoices
            'total_invoices' => $totalInvoices,
            'issued_invoices' => $issuedInvoices,
            'cancelled_invoices' => $cancelledInvoices,
            'late_invoices' => $totalLateInvoices,
            'late_payment_invoices' => $latePaymentInvoices,
            'late_generation_invoices' => $lateGenerationInvoices,

            // Employees
            'users_count' => $usersCount,
            'workers_count' => $workersCount,
            'supervisors_count' => $supervisorsCount,
            'managers_count' => $managersCount,
            'total_employees' => $usersCount + $workersCount,

            // Financial
            'financial_for_us' => $financialForUs,
            'financial_against_us' => abs($financialAgainstUs),
            'total_work_days' => $totalWorkDays,

            // Additional useful stats
            'active_employees' => Employee::all()->count(),
            'pending_invoices' => Invoice::where('payment_status', 'pending')->count(),
            'paid_invoices' => Invoice::where('payment_status', 'paid')->count(),
        ];
    }

    // Report methods that will be called when cards are clicked
    public function issuedInvoicesReport()
    {
        $invoices = Invoice::where('invoice_status', '!=', 'cancelled')
            ->with('client')
            ->latest()
            ->get();

        return view('dashboard.reports.issued-invoices', compact('invoices'));
    }

    public function cancelledInvoicesReport()
    {
        $invoices = Invoice::where('invoice_status', 'cancelled')
            ->with('client')
            ->latest()
            ->get();

        return view('dashboard.reports.cancelled-invoices', compact('invoices'));
    }

    public function lateInvoicesReport()
    {
        $today = Carbon::today();

        $invoices = Invoice::where(function($query) use ($today) {
            $query->where('payment_status', 'late')
                ->orWhere(function($q) use ($today) {
                    $q->where('due_date', '<', $today)
                        ->where('payment_status', 'pending');
                })
                ->orWhere(function($q) use ($today) {
                    $q->where('generation_date', '<', $today)
                        ->where('invoice_status', 'pending');
                });
        })
            ->with('client')
            ->latest()
            ->get();

        return view('dashboard.reports.late-invoices', compact('invoices'));
    }

    public function usersReport()
    {
        $employees = Employee::where('file_type', '!=', 'حماية أجور')
            ->with('client')
            ->latest()
            ->get();

        return view('dashboard.reports.users', compact('employees'));
    }

    public function workersReport()
    {
        $employees = Employee::where('file_type', 'حماية أجور')
            ->with('client')
            ->latest()
            ->get();

        return view('dashboard.reports.workers', compact('employees'));
    }

    public function supervisorsReport()
    {
        // Get sum of total_supervisors from invoices grouped by client
        $supervisorsData = Invoice::with('client')
            ->select('client_id', DB::raw('SUM(total_supervisors) as total_supervisors'))
            ->groupBy('client_id')
            ->having('total_supervisors', '>', 0)
            ->latest()
            ->get();

        // Calculate overall total
        $totalSupervisors = Invoice::sum('total_supervisors');

        return view('dashboard.reports.supervisors', compact('supervisorsData', 'totalSupervisors'));
    }

    public function managersReport()
    {
        // Get sum of total_managers from invoices grouped by client
        $managersData = Invoice::with('client')
            ->select('client_id', DB::raw('SUM(total_managers) as total_managers'))
            ->groupBy('client_id')
            ->having('total_managers', '>', 0)
            ->latest()
            ->get();

        // Calculate overall total
        $totalManagers = Invoice::sum('total_managers');

        return view('dashboard.reports.managers', compact('managersData', 'totalManagers'));
    }

    public function financialForUsReport()
    {
        $invoices = Invoice::where('price_difference', '>', 0)
            ->with('client')
            ->orderBy('price_difference', 'desc')
            ->get();

        return view('dashboard.reports.financial-for-us', compact('invoices'));
    }

    public function financialAgainstUsReport()
    {
        $invoices = Invoice::where('price_difference', '<', 0)
            ->with('client')
            ->orderBy('price_difference', 'asc')
            ->get();

        return view('dashboard.reports.financial-against-us', compact('invoices'));
    }

    public function workDaysReport()
    {
        $employees = Employee::where('work_days', '>', 0)
            ->with('client')
            ->orderBy('work_days', 'desc')
            ->get();

        return view('dashboard.reports.work-days', compact('employees'));
    }
}
