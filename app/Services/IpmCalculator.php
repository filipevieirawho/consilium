<?php

namespace App\Services;

class IpmCalculator
{
    /**
     * Dimension weights (must sum to 1.0)
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

    /**
     * Mapping of question number → dimension number.
     */
    const PERGUNTA_DIMENSAO = [
        1  => 1, 2  => 1, 3  => 1,
        4  => 2, 5  => 2, 6  => 2,
        7  => 3, 8  => 3, 9  => 3,
        10 => 4, 11 => 4, 12 => 4,
        13 => 5, 14 => 5, 15 => 5,
        16 => 6, 17 => 6, 18 => 6,
    ];

    /**
     * Critical questions: if any has nota 0, cap IPM at 55.
     * Questions 1, 2 (D1) and 7, 8 (D3) are the most critical.
     */
    const PERGUNTAS_CRITICAS = [1, 2, 7, 8];

    /**
     * Calculate IPM given a collection/array of respostas.
     * Each item must have: ->pergunta (int) and ->resposta (int 0-3)
     *
     * @param  \Illuminate\Support\Collection|array  $respostas
     * @return array  [ipm, faixa, texto, dimensoes, dimensoes_fracas, medias_dimensao]
     */
    public static function calcular($respostas): array
    {
        // Build a map: pergunta_num => resposta
        $map = [];
        foreach ($respostas as $r) {
            $map[$r->pergunta] = (int) $r->resposta;
        }

        // Group by dimension and compute average per dimension
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

        // Weighted sum
        $somaTotal = 0;
        $mediasPorDimensao = [];

        foreach (self::PESOS as $dimensao => $peso) {
            $count = $countPorDimensao[$dimensao] ?? 0;
            $media = $count > 0 ? $somasPorDimensao[$dimensao] / $count : 0;
            $mediasPorDimensao[$dimensao] = round($media, 2);
            $somaTotal += $media * $peso;
        }

        // Convert to 0–100 scale: max raw is 3.0
        $ipm = round(($somaTotal / 3.0) * 100, 1);

        // Apply critical rule (hidden from user)
        foreach (self::PERGUNTAS_CRITICAS as $pergunta) {
            if (isset($map[$pergunta]) && $map[$pergunta] === 0) {
                $ipm = min($ipm, 55);
                break;
            }
        }

        // Determine faixa
        if ($ipm <= 40) {
            $faixa = 'red';
            $texto = 'O resultado indica inconsistências relevantes nas condições que sustentam a margem do empreendimento. Existe alta probabilidade de impacto em prazo e resultado econômico já em curso ou em formação.';
        } elseif ($ipm <= 70) {
            $faixa = 'yellow';
            $texto = 'O empreendimento apresenta estrutura de gestão, porém com fragilidades importantes. O sistema aparenta controle, mas existem riscos relevantes de perda de margem.';
        } else {
            $faixa = 'green';
            $texto = 'O empreendimento apresenta boa consistência entre planejamento e execução. Ainda assim, a manutenção dessa condição depende de monitoramento contínuo.';
        }

        // Identify the 2 weakest dimensions (with at least 1 answer)
        $dimensoesOrdenadas = [];
        foreach ($mediasPorDimensao as $dimensao => $media) {
            if (($countPorDimensao[$dimensao] ?? 0) > 0) {
                $dimensoesOrdenadas[$dimensao] = $media;
            }
        }
        asort($dimensoesOrdenadas);
        $dimensoesFracas = array_keys(array_slice($dimensoesOrdenadas, 0, 2, true));

        // Build full dimension result
        $dimensoes = [];
        foreach (self::PESOS as $d => $peso) {
            $dimensoes[$d] = [
                'nome'     => self::NOMES_DIMENSAO[$d],
                'peso'     => $peso,
                'media'    => $mediasPorDimensao[$d],
                'score'    => round(($mediasPorDimensao[$d] / 3) * 100),
                'fraca'    => in_array($d, $dimensoesFracas),
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
}
