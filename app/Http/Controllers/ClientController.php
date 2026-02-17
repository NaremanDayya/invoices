<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ChatActivityLogger;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected $chatLogger;

    public function __construct(ChatActivityLogger $chatLogger)
    {
        $this->chatLogger = $chatLogger;
    }
    public function index()
    {
        $clients = Client::withCount('invoices')->latest()->paginate(10);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tax_number' => 'required|numeric|digits:15',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo_data' => 'nullable|string',
            'default_payment_day' => 'nullable|integer|min:1|max:31',
            'grace_period_days' => 'nullable|integer|min:0',
        ]);

        // Handle logo upload - either file or pasted data
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('clients/logos', 'public');
            $validated['logo'] = $logoPath;
        } elseif ($request->filled('logo_data')) {
            // Handle pasted image from clipboard
            $logoPath = $this->saveBase64Image($request->logo_data, 'clients/logos');
            if ($logoPath) {
                $validated['logo'] = $logoPath;
            }
        }

        unset($validated['logo_data']);
        $client = Client::create($validated);

        // Log client creation to chat
        $this->chatLogger->logClientCreated($client);

        return redirect()->route('clients.index')->with('success', 'تم إضافة العميل بنجاح');
    }

    public function show(Client $client)
    {
        $client->load(['invoices' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        $stats = [
            'total_invoices' => $client->invoices()->count(),
            'paid_invoices' => $client->invoices()->where('payment_status', 'paid')->count(),
            'pending_invoices' => $client->invoices()->whereIn('payment_status', ['pending', 'late', 'overdue'])->count(),
            'total_amount' => $client->invoices()->sum('total_price'),
            'paid_amount' => $client->invoices()->where('payment_status', 'paid')->sum('total_price'),
        ];
        
        return view('clients.show', compact('client', 'stats'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tax_number' => 'required|numeric|digits:15',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo_data' => 'nullable|string',
            'default_payment_day' => 'nullable|integer|min:1|max:31',
            'grace_period_days' => 'nullable|integer|min:0',
        ]);

        // Track changes for logging
        $changes = [];
        if ($client->name != $validated['name']) {
            $changes['الاسم'] = ['old' => $client->name, 'new' => $validated['name']];
        }
        if ($client->phone != $validated['phone']) {
            $changes['الهاتف'] = ['old' => $client->phone ?? '-', 'new' => $validated['phone'] ?? '-'];
        }
        if ($client->email != $validated['email']) {
            $changes['البريد الإلكتروني'] = ['old' => $client->email ?? '-', 'new' => $validated['email'] ?? '-'];
        }

        // Handle logo upload - either file or pasted data
        if ($request->hasFile('logo')) {
            if ($client->logo && \Storage::disk('public')->exists($client->logo)) {
                \Storage::disk('public')->delete($client->logo);
            }
            $logoPath = $request->file('logo')->store('clients/logos', 'public');
            $validated['logo'] = $logoPath;
        } elseif ($request->filled('logo_data')) {
            // Handle pasted image from clipboard
            if ($client->logo && \Storage::disk('public')->exists($client->logo)) {
                \Storage::disk('public')->delete($client->logo);
            }
            $logoPath = $this->saveBase64Image($request->logo_data, 'clients/logos');
            if ($logoPath) {
                $validated['logo'] = $logoPath;
            }
        }

        unset($validated['logo_data']);
        $client->update($validated);

        // Log client update to chat
        if (!empty($changes)) {
            $this->chatLogger->logClientUpdated($client, $changes);
        }

        return redirect()->route('clients.index')->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    protected function saveBase64Image($base64String, $directory)
    {
        try {
            // Extract base64 data
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
                $type = strtolower($type[1]);
                
                // Validate image type
                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
                    return null;
                }
                
                $base64String = base64_decode($base64String);
                
                if ($base64String === false) {
                    return null;
                }
                
                // Generate unique filename
                $filename = uniqid() . '.' . $type;
                $path = $directory . '/' . $filename;
                
                // Save to storage
                \Storage::disk('public')->put($path, $base64String);
                
                return $path;
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Failed to save base64 image: ' . $e->getMessage());
            return null;
        }
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'تم حذف العميل بنجاح');
    }

    public function chatClients()
    {
        return Client::with('invoices')
            ->select('id', 'name')
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'invoices_count' => optional($client->invoices->count()) ?? '0',
                ];
            });

    }

    public function monthlyReport(Request $request, Client $client)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $endDate = \Carbon\Carbon::parse($month . '-01')->endOfMonth();

        // Get invoices for the month
        $invoices = $client->invoices()
            ->whereBetween('generation_date', [$startDate, $endDate])
            ->with(['payments', 'creditNotes'])
            ->orderBy('generation_date', 'desc')
            ->get();

        // Calculate totals
        $totalInvoiced = $invoices->sum('total_price');
        $totalPaid = $invoices->sum('paid_amount');
        $totalRemaining = $totalInvoiced - $totalPaid;

        // Prepare invoice breakdown
        $invoiceBreakdown = $invoices->map(function ($invoice) {
            $totalAfterCredits = $invoice->total_price - ($invoice->total_credit_notes ?? 0);
            $remaining = $totalAfterCredits - $invoice->paid_amount;

            return [
                'number' => $invoice->number,
                'date' => $invoice->generation_date->format('Y-m-d'),
                'total_amount' => $invoice->total_price,
                'credit_notes' => $invoice->total_credit_notes ?? 0,
                'total_after_credits' => $totalAfterCredits,
                'paid_amount' => $invoice->paid_amount,
                'remaining_balance' => $remaining,
                'payment_status' => $invoice->payment_status,
                'payments' => $invoice->payments->map(function ($payment) {
                    return [
                        'number' => $payment->number,
                        'amount' => $payment->amount,
                        'date' => $payment->payment_date->format('Y-m-d'),
                        'method' => $payment->payment_method,
                    ];
                }),
            ];
        });

        $reportData = [
            'client' => $client,
            'month' => $month,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_invoices' => $invoices->count(),
                'total_invoiced' => $totalInvoiced,
                'total_paid' => $totalPaid,
                'total_remaining' => $totalRemaining,
            ],
            'invoices' => $invoiceBreakdown,
        ];

        return view('clients.monthly-report', $reportData);
    }

    public function exportMonthlyReport(Request $request, Client $client)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $format = $request->input('format', 'pdf'); // pdf or excel

        $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $endDate = \Carbon\Carbon::parse($month . '-01')->endOfMonth();

        // Get invoices for the month
        $invoices = $client->invoices()
            ->whereBetween('generation_date', [$startDate, $endDate])
            ->with(['payments', 'creditNotes'])
            ->orderBy('generation_date', 'desc')
            ->get();

        // Calculate totals
        $totalInvoiced = $invoices->sum('total_price');
        $totalPaid = $invoices->sum('paid_amount');
        $totalRemaining = $totalInvoiced - $totalPaid;

        // Prepare invoice breakdown
        $invoiceBreakdown = $invoices->map(function ($invoice) {
            $totalAfterCredits = $invoice->total_price - ($invoice->total_credit_notes ?? 0);
            $remaining = $totalAfterCredits - $invoice->paid_amount;

            return [
                'number' => $invoice->number,
                'date' => $invoice->generation_date->format('Y-m-d'),
                'total_amount' => $invoice->total_price,
                'credit_notes' => $invoice->total_credit_notes ?? 0,
                'total_after_credits' => $totalAfterCredits,
                'paid_amount' => $invoice->paid_amount,
                'remaining_balance' => $remaining,
                'payment_status' => $invoice->payment_status,
            ];
        });

        $reportData = [
            'client' => $client,
            'month' => $month,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_invoices' => $invoices->count(),
                'total_invoiced' => $totalInvoiced,
                'total_paid' => $totalPaid,
                'total_remaining' => $totalRemaining,
            ],
            'invoices' => $invoiceBreakdown,
        ];

        if ($format === 'excel') {
            return $this->exportToExcel($reportData);
        } else {
            return $this->exportToPdf($reportData);
        }
    }

    private function exportToPdf($reportData)
    {
        $pdf = \PDF::loadView('clients.reports.monthly-pdf', $reportData);
        $filename = 'monthly-report-' . $reportData['client']->name . '-' . $reportData['month'] . '.pdf';
        return $pdf->download($filename);
    }

    private function exportToExcel($reportData)
    {
        return \Excel::download(new \App\Exports\ClientMonthlyReportExport($reportData), 
            'monthly-report-' . $reportData['client']->name . '-' . $reportData['month'] . '.xlsx');
    }
}
