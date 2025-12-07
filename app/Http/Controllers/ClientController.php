<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
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
