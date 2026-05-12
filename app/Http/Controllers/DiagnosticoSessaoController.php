<?php

namespace App\Http\Controllers;

use App\Models\DiagnosticoSessao;
use App\Models\Empresa;
use App\Models\Questionario;
use App\Services\IpmCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiagnosticoSessaoController extends Controller
{
    public function index()
    {
        $sessoes = DiagnosticoSessao::with(['questionario', 'empresa'])
            ->withCount(['diagnosticos', 'respostas'])
            ->latest()
            ->get();

        return view('sessoes.index', compact('sessoes'));
    }

    public function create()
    {
        $questionarios = Questionario::where('is_active', true)->orderBy('nome')->get();
        $empresas      = Empresa::orderBy('nome_fantasia')->get();

        return view('sessoes.create', compact('questionarios', 'empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'          => 'required|string|max:255',
            'descricao'       => 'nullable|string',
            'questionario_id' => 'required|exists:questionarios,id',
            'empresa_id'      => 'nullable|exists:empresas,id',
        ]);

        $sessao = DiagnosticoSessao::create(array_merge($validated, [
            'token'     => Str::random(48),
            'is_active' => true,
        ]));

        return redirect()->route('sessoes.show', $sessao)
            ->with('success', 'Sessão criada com sucesso!');
    }

    public function show(DiagnosticoSessao $sessao)
    {
        $sessao->load(['questionario.questoes', 'empresa']);

        // Todos os diagnósticos concluídos nesta sessão (anônimos)
        $concluidos = $sessao->diagnosticos()
            ->where('status', 'concluido')
            ->with('respostas')
            ->get();

        $total      = $sessao->diagnosticos()->count();
        $concluidos_count = $concluidos->count();

        // Sem respostas ainda → exibe painel vazio
        if ($concluidos_count === 0 || !$sessao->questionario) {
            return view('sessoes.show', compact(
                'sessao', 'concluidos', 'total', 'concluidos_count'
            ));
        }

        $questoes = $sessao->questionario->questoes;

        // ── Por questão: distribuição (0-3) e avg/range ──────────────────
        $questoesData = $questoes->map(function ($q) use ($concluidos) {
            $answers = $concluidos->flatMap->respostas
                ->filter(fn($r) => $r->questao_id === $q->id)
                ->pluck('resposta');

            $dist = [0 => 0, 1 => 0, 2 => 0, 3 => 0];
            foreach ($answers as $a) {
                $dist[(int)$a]++;
            }

            $avg   = $answers->count() > 0 ? round($answers->avg(), 2) : null;
            $range = $answers->count() > 0 ? ($answers->max() - $answers->min()) : 0;

            return [
                'id'        => $q->id,
                'texto'     => $q->texto,
                'dimensao'  => $q->dimensao_nome,
                'dist'      => $dist,
                'avg'       => $avg,
                'range'     => $range,
                'divergente' => $range >= 2,
            ];
        })->values();

        // ── Por dimensão: série média + série por respondente ────────────
        $dimensoes = $questoes->pluck('dimensao_nome')->unique()->values();

        // Score médio por dimensão (0-100) para cada respondente
        $seriesIndividuais = $concluidos->map(function ($diag) use ($questoes, $dimensoes) {
            return $dimensoes->map(function ($dim) use ($diag, $questoes) {
                $qs      = $questoes->where('dimensao_nome', $dim)->pluck('id');
                $answers = $diag->respostas->whereIn('questao_id', $qs)->pluck('resposta');
                return $answers->count() > 0
                    ? round($answers->avg() / 3 * 100, 1)
                    : 0;
            })->values()->toArray();
        })->values()->toArray();

        // Média geral por dimensão (0-100)
        $serieMedia = $dimensoes->map(function ($dim) use ($concluidos, $questoes) {
            $qs      = $questoes->where('dimensao_nome', $dim)->pluck('id');
            $answers = $concluidos->flatMap->respostas->whereIn('questao_id', $qs)->pluck('resposta');
            return $answers->count() > 0
                ? round($answers->avg() / 3 * 100, 1)
                : 0;
        })->values()->toArray();

        // IPMs individuais para distribuição de score geral
        $ipms = $concluidos->map(fn($d) => IpmCalculator::calcular($d)['ipm'])->sort()->values();

        // Score de alinhamento: 1 - desvio padrão normalizado médio por questão
        $desvioPorQuestao = $questoesData->filter(fn($q) => $q['avg'] !== null)->map(function ($q) use ($concluidos) {
            $answers = $concluidos->flatMap->respostas
                ->filter(fn($r) => $r->questao_id === $q['id'])
                ->pluck('resposta');
            if ($answers->count() < 2) return 0;
            $mean = $answers->avg();
            $variance = $answers->map(fn($v) => pow($v - $mean, 2))->avg();
            return sqrt($variance) / 3; // normalizado 0-1
        });

        $scoreAlinhamento = $desvioPorQuestao->count() > 0
            ? round((1 - $desvioPorQuestao->avg()) * 100)
            : null;

        return view('sessoes.show', compact(
            'sessao', 'concluidos', 'total', 'concluidos_count',
            'questoesData', 'dimensoes', 'seriesIndividuais', 'serieMedia',
            'ipms', 'scoreAlinhamento'
        ));
    }

    public function toggle(DiagnosticoSessao $sessao)
    {
        $sessao->update(['is_active' => !$sessao->is_active]);

        return back()->with('success',
            $sessao->is_active ? 'Sessão ativada.' : 'Sessão encerrada.'
        );
    }

    public function destroy(DiagnosticoSessao $sessao)
    {
        $sessao->delete();
        return redirect()->route('sessoes.index')->with('success', 'Sessão removida.');
    }
}
