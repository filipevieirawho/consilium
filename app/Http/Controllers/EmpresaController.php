<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = Empresa::withCount(['contacts', 'diagnosticos'])
            ->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nome_fantasia', 'like', "%{$q}%")
                    ->orWhere('razao_social', 'like', "%{$q}%")
                    ->orWhere('cnpj', 'like', "%{$q}%");
            });
        }

        if ($request->filled('segmento')) {
            $query->where('segmento', $request->segmento);
        }

        $empresas = $query->paginate(20)->withQueryString();

        $segmentos = Empresa::whereNotNull('segmento')
            ->where('segmento', '!=', '')
            ->distinct()
            ->orderBy('segmento')
            ->pluck('segmento');

        return view('empresas.index', compact('empresas', 'segmentos'));
    }

    public function create()
    {
        return view('empresas.create');
    }

    public function storeQuick(Request $request)
    {
        $validated = $request->validate([
            'nome_fantasia' => 'required|string|max:255',
        ]);

        $existing = Empresa::whereRaw('LOWER(nome_fantasia) = ?', [strtolower($validated['nome_fantasia'])])
            ->first();

        if ($existing) {
            return response()->json($existing);
        }

        $empresa = Empresa::create([
            'nome_fantasia' => $validated['nome_fantasia'],
            'user_id'       => Auth::id(),
            'pais'          => 'Brasil',
        ]);

        return response()->json($empresa);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_fantasia' => 'required|string|max:255',
            'razao_social'  => 'nullable|string|max:255',
            'cnpj'          => 'nullable|string|max:20|unique:empresas,cnpj',
            'segmento'      => 'nullable|string|max:100',
            'porte'         => 'nullable|string|max:50',
            'tipo_unidade'  => 'nullable|string|max:50',
            'cep'           => 'nullable|string|max:10',
            'rua'           => 'nullable|string|max:255',
            'numero'        => 'nullable|string|max:20',
            'complemento'   => 'nullable|string|max:100',
            'bairro'        => 'nullable|string|max:100',
            'cidade'        => 'nullable|string|max:100',
            'estado'        => 'nullable|string|max:2',
            'pais'          => 'nullable|string|max:50',
        ]);

        $existing = Empresa::whereRaw('LOWER(nome_fantasia) = ?', [strtolower($validated['nome_fantasia'])])
            ->orWhereRaw('LOWER(razao_social) = ?', [strtolower($validated['nome_fantasia'])])
            ->first();

        if ($existing) {
            return back()->withInput()->withErrors(['nome_fantasia' => 'Já existe uma empresa cadastrada com este nome.']);
        }

        $empresa = Empresa::create(array_merge($validated, [
            'user_id' => Auth::id(),
            'cnpj'    => isset($validated['cnpj']) ? preg_replace('/\D/', '', $validated['cnpj']) : null,
            'pais'    => $validated['pais'] ?? 'Brasil',
        ]));

        return redirect()->route('empresas.show', $empresa)
            ->with('success', 'Empresa cadastrada com sucesso!');
    }

    public function show(Empresa $empresa)
    {
        $empresa->load(['contacts' => fn($q) => $q->latest()->limit(10), 'diagnosticos' => fn($q) => $q->latest()->limit(10)]);
        return view('empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'nome_fantasia' => 'required|string|max:255',
            'razao_social'  => 'nullable|string|max:255',
            'cnpj'          => 'nullable|string|max:20|unique:empresas,cnpj,' . $empresa->id,
            'segmento'      => 'nullable|string|max:100',
            'porte'         => 'nullable|string|max:50',
            'tipo_unidade'  => 'nullable|string|max:50',
            'cep'           => 'nullable|string|max:10',
            'rua'           => 'nullable|string|max:255',
            'numero'        => 'nullable|string|max:20',
            'complemento'   => 'nullable|string|max:100',
            'bairro'        => 'nullable|string|max:100',
            'cidade'        => 'nullable|string|max:100',
            'estado'        => 'nullable|string|max:2',
            'pais'          => 'nullable|string|max:50',
        ]);

        $existing = Empresa::where('id', '!=', $empresa->id)
            ->where(function ($query) use ($validated) {
                $query->whereRaw('LOWER(nome_fantasia) = ?', [strtolower($validated['nome_fantasia'])])
                      ->orWhereRaw('LOWER(razao_social) = ?', [strtolower($validated['nome_fantasia'])]);
            })
            ->first();

        if ($existing) {
            return back()->withInput()->withErrors(['nome_fantasia' => 'Já existe outra empresa cadastrada com este nome.']);
        }

        if (isset($validated['cnpj'])) {
            $validated['cnpj'] = preg_replace('/\D/', '', $validated['cnpj']);
        }

        $empresa->update($validated);

        return redirect()->route('empresas.show', $empresa)
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return redirect()->route('empresas.index')
            ->with('success', 'Empresa excluída.');
    }

    /**
     * AJAX: lookup CNPJ via BrasilAPI and return data.
     */
    public function cnpjLookup(Request $request)
    {
        $cnpj = preg_replace('/\D/', '', $request->cnpj);
        if (strlen($cnpj) !== 14) {
            return response()->json(['error' => 'CNPJ inválido'], 422);
        }

        try {
            $response = Http::timeout(8)->get("https://brasilapi.com.br/api/cnpj/v1/{$cnpj}");
            if ($response->successful()) {
                $data = $response->json();
                // 1 = MATRIZ, 2 = FILIAL according to typical Brazilian API standards
                $tipoUnidade = null;
                if (isset($data['identificador_matriz_filial'])) {
                    $tipoUnidade = $data['identificador_matriz_filial'] == 1 ? 'Matriz' : 'Filial';
                }

                return response()->json([
                    'nome_fantasia' => $data['nome_fantasia'] ?: $data['razao_social'] ?? '',
                    'razao_social'  => $data['razao_social'] ?? '',
                    'tipo_unidade'  => $tipoUnidade,
                    'cep'           => $data['cep'] ?? '',
                    'rua'           => $data['logradouro'] ?? '',
                    'numero'        => $data['numero'] ?? '',
                    'complemento'   => $data['complemento'] ?? '',
                    'bairro'        => $data['bairro'] ?? '',
                    'cidade'        => $data['municipio'] ?? '',
                    'estado'        => $data['uf'] ?? '',
                ]);
            }
        } catch (\Exception $e) {
            // silently fail
        }

        return response()->json(['error' => 'CNPJ não encontrado'], 404);
    }
}
