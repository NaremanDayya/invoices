<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $clientId = $request->get('client_id');

        // Get all projects with their employees
        $query = InvoiceEmployee::with(['invoice.client', 'employee'])
            ->whereNotNull('project')
            ->where('project', '!=', '');

        if (!empty($search)) {
            $query->where('project', 'like', '%' . $search . '%');
        }

        if ($clientId) {
            $query->whereHas('invoice', function($q) use ($clientId) {
                $q->where('client_id', $clientId);
            });
        }

        $employees = $query->get();

        // Group by project
        $projects = $employees->groupBy('project')->map(function($projectEmployees, $projectName) {
            $firstEmployee = $projectEmployees->first();
            $invoice = $firstEmployee->invoice;
            $client = $invoice->client ?? null;

            return [
                'name' => $projectName,
                'client_name' => $client->name ?? '-',
                'client_id' => $client->id ?? null,
                'invoice_number' => $invoice->number ?? '-',
                'invoice_id' => $invoice->id ?? null,
                'employees_count' => $projectEmployees->count(),
                'total_salaries' => $projectEmployees->sum('total_salary'),
                'total_paid' => $projectEmployees->sum('total_paid'),
                'total_remaining' => $projectEmployees->sum('remaining_amount'),
                'employees' => $projectEmployees->map(function($emp) {
                    return [
                        'id' => $emp->id,
                        'name' => $emp->employee_name,
                        'salary' => $emp->total_salary ?? $emp->net_salary,
                        'paid' => $emp->total_paid ?? 0,
                        'remaining' => $emp->remaining_amount ?? ($emp->total_salary ?? $emp->net_salary),
                        'payment_status' => $emp->payment_status,
                        'work_days' => $emp->work_days ?? $emp->work_days_count ?? '-',
                        'salary_type' => $emp->salary_type ?? 'monthly'
                    ];
                })
            ];
        });

        // Get all clients for filter
        $clients = \App\Models\Client::all();

        $stats = [
            'total_projects' => $projects->count(),
            'total_employees' => $employees->count(),
            'total_salaries' => $employees->sum('total_salary'),
            'total_paid' => $employees->sum('total_paid'),
            'total_remaining' => $employees->sum('remaining_amount')
        ];

        return view('projects.index', compact('projects', 'stats', 'clients', 'search', 'clientId'));
    }

    public function show($projectName)
    {
        $projectName = urldecode($projectName);

        $employees = InvoiceEmployee::with(['invoice.client', 'employee'])
            ->where('project', $projectName)
            ->get();

        if ($employees->isEmpty()) {
            abort(404, 'المشروع غير موجود');
        }

        $firstEmployee = $employees->first();
        $invoice = $firstEmployee->invoice;
        $client = $invoice->client ?? null;

        $projectData = [
            'name' => $projectName,
            'client_name' => $client->name ?? '-',
            'invoice_number' => $invoice->number ?? '-',
            'invoice_id' => $invoice->id ?? null,
            'employees_count' => $employees->count(),
            'total_salaries' => $employees->sum('total_salary'),
            'total_paid' => $employees->sum('total_paid'),
            'total_remaining' => $employees->sum('remaining_amount'),
        ];

        return view('projects.show', compact('projectData', 'employees'));
    }
}
