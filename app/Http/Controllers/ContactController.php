<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewContactAlert;

class ContactController extends Controller
{

    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
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

        // Send email alert
        try {
            Mail::to('filipe@dmtpalestras.com.br')->send(new NewContactAlert($contact));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send contact alert: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Contato enviado com sucesso!'], 201);
    }
}
