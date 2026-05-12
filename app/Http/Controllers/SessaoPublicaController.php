<?php

namespace App\Http\Controllers;

use App\Models\DiagnosticoSessao;
use App\Models\Diagnostico;
use Illuminate\Support\Str;

class SessaoPublicaController extends Controller
{
    /**
     * Landing pública da sessão.
     * GET /sessao/{token}
     */
    public function landing(string $token)
    {
        $sessao = DiagnosticoSessao::with('questionario')->where('token', $token)->firstOrFail();

        if (!$sessao->is_active) {
            return view('diagnosticos.sessao-encerrada', compact('sessao'));
        }

        return view('diagnosticos.sessao-landing', compact('sessao'));
    }

    /**
     * Cria um Diagnostico anônimo vinculado à sessão e inicia o fluxo.
     * POST /sessao/{token}/iniciar
     */
    public function iniciar(string $token)
    {
        $sessao = DiagnosticoSessao::where('token', $token)->firstOrFail();

        if (!$sessao->is_active) {
            return view('diagnosticos.sessao-encerrada', compact('sessao'));
        }

        $diagnostico = Diagnostico::create([
            'token'           => Str::random(40),
            'sessao_id'       => $sessao->id,
            'questionario_id' => $sessao->questionario_id,
            'status'          => 'em_andamento',
            'aceite'          => true, // pula formulários pessoais
        ]);

        return redirect()->route('diagnostico.instrucoes', $diagnostico->token);
    }
}
