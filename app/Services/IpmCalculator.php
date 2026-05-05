<?php

namespace App\Services;

use App\Models\Diagnostico;
use App\Models\QuestionarioQuestao;

class IpmCalculator
{
    /**
     * Legacy hardcoded weights (used when no Questionario is linked).
     */
    const PESOS = [
        1 => 0.20,
        2 => 0.15,
        3 => 0.20,
        4 => 0.20,
        5 => 0.15,
        6 => 0.10,
    ];

    const NOMES_DIMENSAO = [
        1 => 'Viabilidade e Premissas',
        2 => 'Projetos',
        3 => 'Orçamento',
        4 => 'Planejamento',
        5 => 'Sustentação Financeira',
        6 => 'Confiabilidade da Informação',
    ];

    const PERGUNTA_DIMENSAO = [
        1  => 1, 2  => 1, 3  => 1,
        4  => 2, 5  => 2, 6  => 2,
        7  => 3, 8  => 3, 9  => 3,
        10 => 4, 11 => 4, 12 => 4,
        13 => 5, 14 => 5, 15 => 5,
        16 => 6, 17 => 6, 18 => 6,
    ];

    const PERGUNTAS_CRITICAS = [1, 2, 7, 8];

    /**
     * Calculate IPM from a Diagnostico with a dynamic Questionario,
     * OR fall back to the legacy static schema.
     *
     * @param  \Illuminate\Support\Collection  $respostas
     * @param  Diagnostico|null                $diagnostico  pass to enable dynamic mode
     * @return array [ipm, faixa, texto, dimensoes, dimensoes_fracas]
     */
    public static function calcular($respostas, ?Diagnostico $diagnostico = null): array
    {
        // --- Try dynamic mode (questao_id on respostas) ---
        $temQuestaoId = $respostas->first() && $respostas->first()->questao_id;
        $questoes     = null;

        if ($temQuestaoId || ($diagnostico && $diagnostico->questionario_id)) {
            $questoes = self::buildQuestoesMap($respostas, $diagnostico);
        }

        if ($questoes) {
            return self::calcularDinamico($respostas, $questoes);
        }

        // --- Legacy static mode ---
        return self::calcularLegacy($respostas);
    }

    /**
     * Build a map of questao_id/pergunta → [dimensao_nome, dimensao_peso]
     * from either the respostas' questao_id or from the diagnostico's questionario.
     */
    protected static function buildQuestoesMap($respostas, ?Diagnostico $diagnostico): ?array
    {
        // Try to get questao ids from respostas
        $questaoIds = $respostas->pluck('questao_id')->filter()->unique()->values();

        if ($questaoIds->isNotEmpty()) {
            $questoes = QuestionarioQuestao::whereIn('id', $questaoIds)->get();
            if ($questoes->isNotEmpty()) {
                return $questoes->keyBy('id')->map(fn($q) => [
                    'dimensao_nome' => $q->dimensao_nome,
                    'dimensao_peso' => (float) $q->dimensao_peso,
                ])->toArray();
            }
        }

        // Fallback: load from questionario linked to diagnostico
        if ($diagnostico && $diagnostico->questionario_id) {
            $diagnostico->loadMissing('questionario.questoes');
            if ($diagnostico->questionario) {
                return $diagnostico->questionario->questoes->keyBy('id')->map(fn($q) => [
                    'dimensao_nome' => $q->dimensao_nome,
                    'dimensao_peso' => (float) $q->dimensao_peso,
                ])->toArray();
            }
        }

        return null;
    }

    /**
     * Dynamic calculation using questao_id-based respostas.
     */
    protected static function calcularDinamico($respostas, array $questoesMap): array
    {
        // Group by dimensao_nome, collect scores
        $dimensoesData = []; // [nome => ['peso' => x, 'scores' => []]]

        foreach ($respostas as $r) {
            $qid = $r->questao_id;
            if (!$qid || !isset($questoesMap[$qid])) continue;

            $nome = $questoesMap[$qid]['dimensao_nome'];
            $peso = $questoesMap[$qid]['dimensao_peso'];

            if (!isset($dimensoesData[$nome])) {
                $dimensoesData[$nome] = ['peso' => $peso, 'scores' => []];
            }
            $dimensoesData[$nome]['scores'][] = (int) $r->resposta;
        }

        // Re-normalize weights in case they don't sum to 1
        $pesoTotal = array_sum(array_column($dimensoesData, 'peso'));

        $somaTotal = 0;
        $dimensoes = [];
        $pontuacoes = [];

        foreach ($dimensoesData as $nome => $data) {
            $media = count($data['scores']) > 0 ? array_sum($data['scores']) / count($data['scores']) : 0;
            $peso  = $pesoTotal > 0 ? $data['peso'] / $pesoTotal : 0;
            $score = round(($media / 3) * 100);

            $somaTotal += $media * $peso;
            $dimensoes[$nome] = [
                'nome'  => $nome,
                'peso'  => $peso,
                'media' => round($media, 2),
                'score' => $score,
                'fraca' => false, // will update below
            ];
            $pontuacoes[$nome] = $media;
        }

        $ipm = round(($somaTotal / max(1, $pesoTotal > 0 ? 1 : 0)) * 100, 1);
        // Correct formula: somaTotal is already the weighted average (0–3 scale), divide by 3 to get 0–1
        $ipm = round(($somaTotal / 3.0) * 100, 1);

        // Mark 2 weakest dimensions
        asort($pontuacoes);
        $fracas = array_keys(array_slice($pontuacoes, 0, 2, true));
        foreach ($fracas as $nome) {
            if (isset($dimensoes[$nome])) $dimensoes[$nome]['fraca'] = true;
        }

        [$faixa, $texto] = self::faixaTexto($ipm);

        return [
            'ipm'              => $ipm,
            'faixa'            => $faixa,
            'texto'            => $texto,
            'dimensoes'        => array_values($dimensoes),
            'dimensoes_fracas' => $fracas,
        ];
    }

    /**
     * Legacy static calculation (18 fixed questions).
     */
    protected static function calcularLegacy($respostas): array
    {
        $map = [];
        foreach ($respostas as $r) {
            $map[$r->pergunta] = (int) $r->resposta;
        }

        $somasPorDimensao = [];
        $countPorDimensao = [];

        foreach (self::PERGUNTA_DIMENSAO as $pergunta => $dimensao) {
            if (!isset($somasPorDimensao[$dimensao])) {
                $somasPorDimensao[$dimensao] = 0;
                $countPorDimensao[$dimensao] = 0;
            }
            if (isset($map[$pergunta])) {
                $somasPorDimensao[$dimensao] += $map[$pergunta];
                $countPorDimensao[$dimensao]++;
            }
        }

        $somaTotal = 0;
        $mediasPorDimensao = [];

        foreach (self::PESOS as $dimensao => $peso) {
            $count = $countPorDimensao[$dimensao] ?? 0;
            $media = $count > 0 ? $somasPorDimensao[$dimensao] / $count : 0;
            $mediasPorDimensao[$dimensao] = round($media, 2);
            $somaTotal += $media * $peso;
        }

        $ipm = round(($somaTotal / 3.0) * 100, 1);

        // Critical question rule
        foreach (self::PERGUNTAS_CRITICAS as $pergunta) {
            if (isset($map[$pergunta]) && $map[$pergunta] === 0) {
                $ipm = min($ipm, 55);
                break;
            }
        }

        [$faixa, $texto] = self::faixaTexto($ipm);

        $dimensoesOrdenadas = [];
        foreach ($mediasPorDimensao as $dimensao => $media) {
            if (($countPorDimensao[$dimensao] ?? 0) > 0) {
                $dimensoesOrdenadas[$dimensao] = $media;
            }
        }
        asort($dimensoesOrdenadas);
        $dimensoesFracas = array_keys(array_slice($dimensoesOrdenadas, 0, 2, true));

        $dimensoes = [];
        foreach (self::PESOS as $d => $peso) {
            $dimensoes[$d] = [
                'nome'  => self::NOMES_DIMENSAO[$d],
                'peso'  => $peso,
                'media' => $mediasPorDimensao[$d],
                'score' => round(($mediasPorDimensao[$d] / 3) * 100),
                'fraca' => in_array($d, $dimensoesFracas),
            ];
        }

        return [
            'ipm'              => $ipm,
            'faixa'            => $faixa,
            'texto'            => $texto,
            'dimensoes'        => $dimensoes,
            'dimensoes_fracas' => array_map(fn($d) => self::NOMES_DIMENSAO[$d], $dimensoesFracas),
        ];
    }

    protected static function faixaTexto(float $ipm): array
    {
        if ($ipm <= 40) {
            return ['red', 'O resultado indica inconsistências relevantes nas condições que sustentam a margem do empreendimento. Existe alta probabilidade de impacto em prazo e resultado econômico já em curso ou em formação.'];
        } elseif ($ipm <= 70) {
            return ['yellow', 'O empreendimento apresenta estrutura de gestão, porém com fragilidades importantes. O sistema aparenta controle, mas existem riscos relevantes de perda de margem.'];
        } else {
            return ['green', 'O empreendimento apresenta boa consistência entre planejamento e execução. Ainda assim, a manutenção dessa condição depende de monitoramento contínuo.'];
        }
    }
}
