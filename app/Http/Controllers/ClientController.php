<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::latest()->paginate(10);
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
            'default_payment_day' => 'nullable|integer|min:1|max:31',
            'grace_period_days' => 'nullable|integer|min:0',
        ]);

        Client::create($validated);

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
            'default_payment_day' => 'nullable|integer|min:1|max:31',
            'grace_period_days' => 'nullable|integer|min:0',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'تم تحديث بيانات العميل بنجاح');
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
}
