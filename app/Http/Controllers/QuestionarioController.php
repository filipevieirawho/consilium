<?php

namespace App\Http\Controllers;

use App\Models\Questionario;
use App\Models\QuestionarioQuestao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Questionario::withCount('questoes')->latest();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('nome', 'like', "%{$s}%")
                  ->orWhere('modelo_id', 'like', "%{$s}%")
                  ->orWhere('titulo', 'like', "%{$s}%");
            });
        }

        $questionarios = $query->paginate(20)->withQueryString();
        return view('questionarios.index', compact('questionarios'));
    }

    public function create()
    {
        $modeloId = 'CON-' . date('Y') . '-' . strtoupper(Str::random(4));
        return view('questionarios.create', compact('modeloId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'      => 'required|string|max:255',
            'titulo'    => 'nullable|string|max:255',
            'subtitulo' => 'nullable|string',
            'descricao' => 'nullable|string',
            'modelo_id' => 'required|string|max:50|unique:questionarios,modelo_id',
            'questoes'  => 'required|array|min:1',
            'questoes.*.dimensao_nome' => 'required|string|max:100',
            'questoes.*.dimensao_peso' => 'required|numeric|min:0.01|max:1',
            'questoes.*.texto'         => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $questionario = Questionario::create([
                'nome'      => $request->nome,
                'titulo'    => $request->titulo,
                'subtitulo' => $request->subtitulo,
                'descricao' => $request->descricao,
                'modelo_id' => $request->modelo_id,
                'is_active' => $request->boolean('is_active', true),
            ]);

            $questoes = collect($request->questoes)->map(fn($q, $index) => [
                'dimensao_nome' => $q['dimensao_nome'],
                'dimensao_peso' => $q['dimensao_peso'],
                'texto'         => $q['texto'],
                'ordem'         => $index,
            ])->toArray();

            $questionario->questoes()->createMany($questoes);
        });

        return redirect()->route('questionarios.index')
            ->with('success', 'Questionário criado com sucesso!');
    }

    public function show(Questionario $questionario)
    {
        $questionario->load('questoes');
        return view('questionarios.show', compact('questionario'));
    }

    public function edit(Questionario $questionario)
    {
        $questionario->load('questoes');
        return view('questionarios.edit', compact('questionario'));
    }

    public function update(Request $request, Questionario $questionario)
    {
        $request->validate([
            'nome'      => 'required|string|max:255',
            'titulo'    => 'nullable|string|max:255',
            'subtitulo' => 'nullable|string',
            'descricao' => 'nullable|string',
            'questoes'  => 'required|array|min:1',
            'questoes.*.dimensao_nome' => 'required|string|max:100',
            'questoes.*.dimensao_peso' => 'required|numeric|min:0.01|max:1',
            'questoes.*.texto'         => 'required|string',
        ]);

        DB::transaction(function () use ($request, $questionario) {
            $questionario->update([
                'nome'      => $request->nome,
                'titulo'    => $request->titulo,
                'subtitulo' => $request->subtitulo,
                'descricao' => $request->descricao,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Replace all questions
            $questionario->questoes()->delete();
            
            $questoes = collect($request->questoes)->map(fn($q, $index) => [
                'dimensao_nome' => $q['dimensao_nome'],
                'dimensao_peso' => $q['dimensao_peso'],
                'texto'         => $q['texto'],
                'ordem'         => $index,
            ])->toArray();

            $questionario->questoes()->createMany($questoes);
        });

        return redirect()->route('questionarios.show', $questionario)
            ->with('success', 'Questionário atualizado!');
    }

    public function destroy(Questionario $questionario)
    {
        $questionario->questoes()->delete();
        $questionario->delete();
        return redirect()->route('questionarios.index')->with('success', 'Questionário excluído.');
    }
}
