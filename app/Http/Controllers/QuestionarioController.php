<?php

namespace App\Http\Controllers;

use App\Models\Questionario;
use App\Models\QuestionarioQuestao;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionarioController extends Controller
{
    public function index()
    {
        $questionarios = Questionario::withCount('questoes')->latest()->get();
        return view('questionarios.index', compact('questionarios'));
    }

    public function create()
    {
        $modeloId = 'MOD-' . date('Y') . '-' . strtoupper(Str::random(4));
        return view('questionarios.create', compact('modeloId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'    => 'required|string|max:255',
            'modelo_id' => 'required|string|max:50|unique:questionarios,modelo_id',
            'questoes'  => 'required|array|min:1',
            'questoes.*.dimensao_nome' => 'required|string|max:100',
            'questoes.*.dimensao_peso' => 'required|numeric|min:0.01|max:1',
            'questoes.*.texto'         => 'required|string',
        ]);

        $questionario = Questionario::create([
            'titulo'    => $request->titulo,
            'modelo_id' => $request->modelo_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        foreach ($request->questoes as $ordem => $q) {
            $questionario->questoes()->create([
                'dimensao_nome' => $q['dimensao_nome'],
                'dimensao_peso' => $q['dimensao_peso'],
                'texto'         => $q['texto'],
                'ordem'         => $ordem,
            ]);
        }

        return redirect()->route('questionarios.show', $questionario)
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
            'titulo'    => 'required|string|max:255',
            'questoes'  => 'required|array|min:1',
            'questoes.*.dimensao_nome' => 'required|string|max:100',
            'questoes.*.dimensao_peso' => 'required|numeric|min:0.01|max:1',
            'questoes.*.texto'         => 'required|string',
        ]);

        $questionario->update([
            'titulo'    => $request->titulo,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Replace all questions
        $questionario->questoes()->delete();
        foreach ($request->questoes as $ordem => $q) {
            $questionario->questoes()->create([
                'dimensao_nome' => $q['dimensao_nome'],
                'dimensao_peso' => $q['dimensao_peso'],
                'texto'         => $q['texto'],
                'ordem'         => $ordem,
            ]);
        }

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
