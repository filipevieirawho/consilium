<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Empresa;
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
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhereHas('contactNotes', function ($noteQuery) use ($search) {
                        $noteQuery->where('note', 'like', "%{$search}%");
                    });
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

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
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
            'company' => 'nullable|string|max:255',
        ]);

        $contact = Contact::create($validated);

        // Auto-link to Empresa by email domain or company name
        $this->vinculateEmpresa($contact);

        $contact->activities()->create([
            'user_id' => auth()->check() ? auth()->id() : null,
            'type' => 'lead_created',
        ]);

        $contact->activities()->create([
            'user_id' => auth()->check() ? auth()->id() : null,
            'type' => 'status_change',
            'old_value' => null,
            'new_value' => 'Cliente Potencial',
        ]);

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
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
        ]);

        // Merge default values for manual creation
        $data = array_merge($validated, [
            'opt_in' => $request->has('opt_in'),
            'status' => 'Cliente Potencial'
        ]);

        $contact = Contact::create($data);

        // Auto-link to Empresa by company name or email domain
        $this->vinculateEmpresa($contact);

        $contact->activities()->create([
            'user_id' => auth()->id(),
            'type' => 'lead_created',
        ]);

        $contact->activities()->create([
            'user_id' => auth()->id(),
            'type' => 'status_change',
            'old_value' => null,
            'new_value' => 'Cliente Potencial',
        ]);

        return redirect()->route('dashboard')->with('success', 'Lead adicionado com sucesso!');
    }

    /**
     * Try to link contact to an existing Empresa by company name or corporate email domain.
     * If no match, create a new Empresa automatically if company name is provided.
     */
    protected function vinculateEmpresa(Contact $contact, $force = false): void
    {
        if ($contact->empresa_id && !$force) return; // already linked

        $empresa = null;

        // 1. Match by company name (exact, case-insensitive)
        if ($contact->company) {
            $empresa = Empresa::whereRaw('LOWER(nome_fantasia) = ?', [strtolower($contact->company)])
                ->orWhereRaw('LOWER(razao_social) = ?', [strtolower($contact->company)])
                ->first();
        }

        // 2. Match by corporate email domain
        if (!$empresa && $contact->email) {
            $domain = Contact::corporateDomain($contact->email);
            if ($domain) {
                // Find any contact with same domain that has an empresa
                $sameEmail = Contact::where('email', 'like', "%@{$domain}")
                    ->whereNotNull('empresa_id')
                    ->first();
                if ($sameEmail) {
                    $empresa = $sameEmail->empresa;
                }
            }
        }

        // 3. Auto-create Empresa if we have a company name but no match
        if (!$empresa && $contact->company) {
            $empresa = Empresa::create(['nome_fantasia' => $contact->company]);
        }

        if ($empresa) {
            $contact->update(['empresa_id' => $empresa->id]);
        }
    }

    public function updateData(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
            'empresa_id' => 'nullable|exists:empresas,id',
        ]);

        $data = array_merge($validated, [
            'opt_in' => $request->has('opt_in')
        ]);

        $contact->update($data);

        // Se o usuário não setou uma empresa manualmente, tenta vincular pelo nome
        if (!$request->filled('empresa_id') && $contact->company) {
            $this->vinculateEmpresa($contact, true);
        }

        return redirect()->route('contacts.show', $contact)->with('success', 'Dados do lead atualizados com sucesso!');
    }

    public function updateStatus(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'status' => 'required|in:Cliente Potencial,Contactado,Proposta Enviada,Negociação,Stand By',
        ]);

        $oldStatus = $contact->status;
        $newStatus = $validated['status'];

        $contact->update($validated);

        if ($oldStatus !== $newStatus) {
            $contact->activities()->create([
                'user_id' => auth()->id(),
                'type' => 'status_change',
                'old_value' => $oldStatus,
                'new_value' => $newStatus,
            ]);
        }

        return response()->json(['message' => 'Status atualizado com sucesso!']);
    }

    public function show(Contact $contact)
    {
        $contact->load([
            'contactNotes' => function ($query) {
                // Pin logic can be handled in memory or just sort it all
                $query->orderBy('is_pinned', 'desc')->latest();
            },
            'contactNotes.user',
            'activities.user'
        ]);

        $notes = $contact->contactNotes;
        $activities = $contact->activities;

        $timeline = $notes->concat($activities)->sortByDesc(function ($item) {
            // Keep pinned notes at top? That might break the timeline flow. 
            // If they want a pure timeline, pinned notes might just have a visual indicator but stay in chronological order, 
            // or we pin them artificially at the top of the timeline.
            // Let's sort by is_pinned first (if it exists), then by created_at.
            $isPinned = isset($item->is_pinned) ? $item->is_pinned : false;
            return sprintf('%d%s', $isPinned ? 1 : 0, $item->created_at->timestamp);
        });

        $users = \App\Models\User::all();
        $questionarios = \App\Models\Questionario::where('is_active', true)->orderBy('titulo')->get();
        return view('contacts.show', compact('contact', 'users', 'timeline', 'questionarios'));
    }

    public function updateDetails(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'status' => 'required|in:Cliente Potencial,Contactado,Proposta Enviada,Negociação,Stand By',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $oldStatus = $contact->status;
        $newStatus = $validated['status'];

        $oldOwner = $contact->user_id;
        $newOwner = $validated['user_id'] ?? null;

        $contact->update($validated);

        if ($oldStatus !== $newStatus) {
            $contact->activities()->create([
                'user_id' => auth()->id(),
                'type' => 'status_change',
                'old_value' => $oldStatus,
                'new_value' => $newStatus,
            ]);
        }

        if ($oldOwner != $newOwner) {
            $contact->activities()->create([
                'user_id' => auth()->id(),
                'type' => 'owner_change',
                'old_value' => $oldOwner,
                'new_value' => $newOwner,
            ]);
        }

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

        if ($note->user_id !== auth()->id())
            abort(403, 'Unauthorized action.');

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

        if ($note->user_id !== auth()->id())
            abort(403, 'Unauthorized action.');

        $note->update(['is_pinned' => !$note->is_pinned]);

        return redirect()->route('contacts.show', $contact)->with('success', 'Status de fixação alterado.');
    }

    public function destroyNote(Contact $contact, ContactNote $note)
    {
        if ($note->contact_id !== $contact->id)
            abort(404);

        if ($note->user_id !== auth()->id())
            abort(403, 'Unauthorized action.');

        $note->delete();

        return redirect()->route('contacts.show', $contact)->with('success', 'Anotação excluída.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('dashboard')->with('success', 'Lead excluído com sucesso.');
    }
}
