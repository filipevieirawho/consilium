<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewContactAlert;
use App\Models\ContactNote;

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

        // Send email alert
        try {
            Mail::to('filipe@consilium.eng.br')->send(new NewContactAlert($contact));
        } catch (\Exception $e) {
            \Log::error('Failed to send contact alert: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Contato enviado com sucesso!'], 201);
    }

    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
        ]);

        // Merge default values for manual creation
        $data = array_merge($validated, [
            'opt_in' => false,
            'status' => 'novo'
        ]);

        Contact::create($data);

        return redirect()->route('dashboard')->with('success', 'Lead adicionado com sucesso!');
    }

    public function updateStatus(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'status' => 'required|in:novo,contactado,perdido,ganho',
        ]);

        $contact->update($validated);

        return response()->json(['message' => 'Status atualizado com sucesso!']);
    }

    public function show(Contact $contact)
    {
        $contact->load([
            'contactNotes' => function ($query) {
                $query->orderBy('is_pinned', 'desc')->latest();
            },
            'contactNotes.user'
        ]);

        $users = \App\Models\User::all();
        return view('contacts.show', compact('contact', 'users'));
    }

    public function updateDetails(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'status' => 'required|in:novo,contactado,perdido,ganho',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $contact->update($validated);

        return redirect()->route('contacts.show', $contact)->with('success', 'Detalhes atualizados com sucesso!');
    }

    public function storeNote(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'note' => 'required|string',
        ]);

        $contact->contactNotes()->create([
            'user_id' => auth()->id(),
            'note' => $validated['note'],
        ]);

        return redirect()->route('contacts.show', $contact)->with('success', 'Anotação adicionada com sucesso!');
    }
    public function updateNote(Request $request, Contact $contact, ContactNote $note)
    {
        if ($note->contact_id !== $contact->id)
            abort(404);

        $validated = $request->validate([
            'note' => 'required|string',
        ]);

        $note->update(['note' => $validated['note']]);

        return redirect()->route('contacts.show', $contact)->with('success', 'Anotação atualizada.');
    }

    public function togglePinNote(Contact $contact, ContactNote $note)
    {
        if ($note->contact_id !== $contact->id)
            abort(404);

        $note->update(['is_pinned' => !$note->is_pinned]);

        return redirect()->route('contacts.show', $contact)->with('success', 'Status de fixação alterado.');
    }

    public function destroyNote(Contact $contact, ContactNote $note)
    {
        if ($note->contact_id !== $contact->id)
            abort(404);

        $note->delete();

        return redirect()->route('contacts.show', $contact)->with('success', 'Anotação excluída.');
    }
}
