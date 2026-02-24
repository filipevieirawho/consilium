<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Services\TursoSync;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewContactAlert;

class ContactController extends Controller
{

    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Year filter
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->input('year'));
        }

        // Month filter
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->input('month'));
        }

        $contacts = $query->latest()->paginate(10);

        return view('dashboard', compact('contacts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
            'opt_in' => 'boolean',
        ]);

        $contact = Contact::create($validated);

        // Sync to Turso for persistence
        TursoSync::upsertContact($contact);

        // Send email alert
        try {
            Mail::to('filipe@consilium.eng.br')->send(new NewContactAlert($contact));
        } catch (\Exception $e) {
            \Log::error('Failed to send contact alert: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Contato enviado com sucesso!'], 201);
    }

    public function updateStatus(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'status' => 'required|in:novo,contactado,perdido,ganho',
        ]);

        $contact->update($validated);

        // Sync to Turso for persistence
        TursoSync::upsertContact($contact);

        return response()->json(['message' => 'Status atualizado com sucesso!']);
    }
}
