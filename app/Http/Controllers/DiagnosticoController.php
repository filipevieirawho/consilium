<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Diagnostico;
use App\Models\DiagnosticoResposta;
use App\Services\IpmCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiagnosticoController extends Controller
{
    // ─── Public flow ─────────────────────────────────────────────────────

    /**
     * BLOCK 0 — Landing screen.
     * GET /diagnostico/{token}
     */
    public function landing(string $token)
    {
        $diagnostico = Diagnostico::where('token', $token)->firstOrFail();

        if ($diagnostico->status === 'concluido') {
            return redirect()->route('diagnostico.result', $token);
        }

        return view('diagnosticos.landing', compact('diagnostico', 'token'));
    }

    /**
     * BLOCK 1 — Respondent & enterprise data form.
     * GET /diagnostico/{token}/dados
     */
    public function showForm(string $token)
    {
        $diagnostico = Diagnostico::where('token', $token)->firstOrFail();

        if ($diagnostico->status === 'concluido') {
            return redirect()->route('diagnostico.result', $token);
        }

        return view('diagnosticos.form', compact('diagnostico', 'token'));
    }

    /**
     * POST /diagnostico/{token}/dados
     */
    public function saveForm(Request $request, string $token)
    {
        $diagnostico = Diagnostico::where('token', $token)->firstOrFail();

        $validated = $request->validate([
            'nome'     => 'required|string|max:255',
            'cargo'    => 'nullable|string|max:255',
            'empresa'  => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'telefone' => 'nullable|string|max:30',
        ]);

        $diagnostico->update($validated);

        return redirect()->route('diagnostico.form2', $token);
    }

    /**
     * BLOCK 1b — Show empreendimento form (step 2).
     * GET /diagnostico/{token}/dados/empreendimento
     */
    public function showForm2(string $token)
    {
        $diagnostico = Diagnostico::where('token', $token)->firstOrFail();

        if ($diagnostico->status === 'concluido') {
            return redirect()->route('diagnostico.result', $token);
        }

        // Must have completed step 1 first
        if (!$diagnostico->nome || !$diagnostico->email) {
            return redirect()->route('diagnostico.form', $token);
        }

        return view('diagnosticos.form2', compact('diagnostico', 'token'));
    }

    /**
     * BLOCK 1b — Save empreendimento data (step 2).
     * POST /diagnostico/{token}/dados/empreendimento
     */
    public function saveForm2(Request $request, string $token)
    {
        $diagnostico = Diagnostico::where('token', $token)->firstOrFail();

        $validated = $request->validate([
            'nome_empreendimento' => 'required|string|max:255',
            'cidade'              => 'nullable|string|max:255',
            'tipologia'           => 'nullable|string|max:100',
            'num_torres'          => 'nullable|integer|min:1',
            'estagio_obra'        => 'nullable|integer|min:0|max:100',
            'prazo_inicial'       => 'nullable|integer|min:1',
            'prazo_atual'         => 'nullable|integer|min:1',
            'aceite'              => 'required|accepted',
        ]);

        $validated['aceite'] = true;
        $diagnostico->update($validated);

        return redirect()->route('diagnostico.instrucoes', $token);
    }


    /**
     * BLOCK 2 — Scale instructions.
     * GET /diagnostico/{token}/instrucoes
     */
    public function showInstrucoes(string $token)
    {
        $diagnostico = Diagnostico::where('token', $token)->firstOrFail();

        if (!$diagnostico->aceite) {
            return redirect()->route('diagnostico.form', $token);
        }

        return view('diagnosticos.instrucoes', compact('diagnostico', 'token'));
    }

    /**
     * BLOCK 3 — Single question per screen.
     * GET /diagnostico/{token}/pergunta/{num}
     */
    public function showPergunta(string $token, int $num)
    {
        $diagnostico = Diagnostico::where('token', $token)->firstOrFail();

        if (!$diagnostico->aceite) {
            return redirect()->route('diagnostico.form', $token);
        }

        if ($diagnostico->status === 'concluido') {
            return redirect()->route('diagnostico.result', $token);
        }

        if ($num < 1 || $num > 18) {
            abort(404);
        }

        $perguntas = $this->getPerguntas();
        $perguntaAtual = $perguntas[$num];
        $dimensaoAtual = $perguntaAtual['dimensao'];
        $dimensaoNome  = IpmCalculator::NOMES_DIMENSAO[$dimensaoAtual];

        // Load previously saved answer for this question (if any)
        $respostaAtual = $diagnostico->respostas()->where('pergunta', $num)->first();

        $respondidas = $diagnostico->respostas()->count();

        return view('diagnosticos.pergunta', compact(
            'diagnostico',
            'token',
            'num',
            'perguntaAtual',
            'dimensaoAtual',
            'dimensaoNome',
            'respostaAtual',
            'respondidas'
        ));
    }

    /**
     * AJAX — Save/update a single answer.
     * POST /diagnostico/{token}/resposta
     */
    public function saveAnswer(Request $request, string $token)
    {
        $diagnostico = Diagnostico::where('token', $token)->firstOrFail();

        $validated = $request->validate([
            'pergunta' => 'required|integer|min:1|max:18',
            'resposta' => 'required|integer|min:0|max:3',
        ]);

        $dimensao = IpmCalculator::PERGUNTA_DIMENSAO[$validated['pergunta']];

        DiagnosticoResposta::updateOrCreate(
            [
                'diagnostico_id' => $diagnostico->id,
                'pergunta'       => $validated['pergunta'],
            ],
            [
                'dimensao' => $dimensao,
                'resposta' => $validated['resposta'],
            ]
        );

        $respondidas = $diagnostico->respostas()->count();
        $total = 18;
        $next = $validated['pergunta'] < 18 ? $validated['pergunta'] + 1 : null;

        return response()->json([
            'ok'          => true,
            'respondidas' => $respondidas,
            'total'       => $total,
            'next_url'    => $next
                ? route('diagnostico.pergunta', [$token, $next])
                : route('diagnostico.finalizar', $token),
        ]);
    }

    /**
     * Finalize the diagnostic and compute the IPM.
     * GET /diagnostico/{token}/finalizar
     */
    public function finalizar(string $token)
    {
        $diagnostico = Diagnostico::where('token', $token)->with('respostas')->firstOrFail();

        if ($diagnostico->respostas->count() < 18) {
            // Find first unanswered question
            $respondidas = $diagnostico->respostas->pluck('pergunta')->toArray();
            for ($i = 1; $i <= 18; $i++) {
                if (!in_array($i, $respondidas)) {
                    return redirect()->route('diagnostico.pergunta', [$token, $i]);
                }
            }
        }

        // Calculate IPM
        $resultado = IpmCalculator::calcular($diagnostico->respostas);

        $diagnostico->update([
            'ipm'    => $resultado['ipm'],
            'status' => 'concluido',
        ]);

        return redirect()->route('diagnostico.result', $token);
    }

    /**
     * Result page.
     * GET /diagnostico/{token}/resultado
     */
    public function result(string $token)
    {
        $diagnostico = Diagnostico::where('token', $token)->with('respostas')->firstOrFail();

        if ($diagnostico->status !== 'concluido') {
            return redirect()->route('diagnostico.landing', $token);
        }

        $resultado = IpmCalculator::calcular($diagnostico->respostas);

        return view('diagnosticos.resultado', compact('diagnostico', 'token', 'resultado'));
    }

    // ─── Admin area ───────────────────────────────────────────────────────

    /**
     * Admin listing.
     * GET /diagnosticos
     */
    public function index(Request $request)
    {
        $query = Diagnostico::query()->latest();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('nome', 'like', "%{$s}%")
                  ->orWhere('empresa', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('nome_empreendimento', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $diagnosticos = $query->paginate(15);
        $contacts = Contact::orderBy('name')->get();

        return view('diagnosticos.index', compact('diagnosticos', 'contacts'));
    }

    /**
     * Admin detail.
     * GET /diagnosticos/{id}
     */
    public function show(Diagnostico $diagnostico)
    {
        $diagnostico->load(['respostas', 'contact']);
        $resultado = null;

        if ($diagnostico->status === 'concluido') {
            $resultado = IpmCalculator::calcular($diagnostico->respostas()->get());
        }

        $contacts = Contact::orderBy('name')->get();
        $perguntas = $this->getPerguntas();

        return view('diagnosticos.show', compact('diagnostico', 'resultado', 'contacts', 'perguntas'));
    }

    /**
     * Vinculate diagnostic to a contact.
     * PATCH /diagnosticos/{id}/vincular
     */
    public function vincular(Request $request, Diagnostico $diagnostico)
    {
        $validated = $request->validate([
            'contact_id' => 'nullable|exists:contacts,id',
        ]);

        $diagnostico->update(['contact_id' => $validated['contact_id']]);

        return redirect()->route('diagnosticos.show', $diagnostico)
            ->with('success', 'Lead vinculado ao diagnóstico com sucesso!');
    }

    /**
     * Generate a new diagnostic link (admin).
     * POST /diagnosticos/gerar-link
     */
    public function generateLink(Request $request)
    {
        $request->validate([
            'contact_id' => 'nullable|exists:contacts,id',
        ]);

        $contact = null;
        $defaults = [];

        if ($request->filled('contact_id')) {
            $contact = Contact::find($request->contact_id);
            $defaults = [
                'nome'    => $contact->name ?? null,
                'empresa' => $contact->company ?? null,
                'email'   => $contact->email ?? null,
                'telefone'=> $contact->phone ?? null,
            ];
        }

        $diagnostico = Diagnostico::create(array_merge([
            'token'      => Str::random(40),
            'contact_id' => $request->contact_id ?? null,
            'status'     => 'em_andamento',
        ], $defaults));

        $url = route('diagnostico.landing', $diagnostico->token);

        return response()->json(['url' => $url, 'token' => $diagnostico->token]);
    }

    // ─── Data ─────────────────────────────────────────────────────────────

    public static function getPerguntas(): array
    {
        return [
            1  => ['dimensao' => 1, 'texto' => 'As premissas da viabilidade (custo de construção, prazo e preço de venda) estão formalizadas e acessíveis às áreas envolvidas?'],
            2  => ['dimensao' => 1, 'texto' => 'Existe rotina de revisão das premissas da viabilidade ao longo do empreendimento, especialmente após mudanças relevantes?'],
            3  => ['dimensao' => 1, 'texto' => 'Existe prática estruturada de avaliação do impacto na margem antes da decisão quando ocorrem mudanças relevantes no empreendimento?'],
            4  => ['dimensao' => 2, 'texto' => 'Existe prática sistemática de validação de custos quando os projetos executivos são desenvolvidos, comparando com a viabilidade?'],
            5  => ['dimensao' => 2, 'texto' => 'Existe processo estruturado de compatibilização entre projetos antes da execução?'],
            6  => ['dimensao' => 2, 'texto' => 'Existe prática estruturada de avaliar impacto em custo, prazo e margem antes de implementar alterações de projeto?'],
            7  => ['dimensao' => 3, 'texto' => 'O orçamento evolui de forma estruturada (viabilidade → preliminar → executivo), com validação contínua das premissas?'],
            8  => ['dimensao' => 3, 'texto' => 'Existe rotina estruturada e frequente de controle de custo, comparando previsto, contratado e realizado?'],
            9  => ['dimensao' => 3, 'texto' => 'As principais incertezas de custo (estimativas, indefinições de projeto, itens não contratados) estão formalmente identificadas e monitoradas?'],
            10 => ['dimensao' => 4, 'texto' => 'O cronograma é estruturado com EAP detalhada e validada, refletindo adequadamente torres e áreas comuns?'],
            11 => ['dimensao' => 4, 'texto' => 'O avanço físico é medido com base na produção real da obra e utilizado para revisão do planejamento?'],
            12 => ['dimensao' => 4, 'texto' => 'Existe rotina estruturada de planejamento de curto prazo (look ahead) com identificação e remoção de restrições?'],
            13 => ['dimensao' => 5, 'texto' => 'Existe fluxo de caixa estruturado e alinhado ao cronograma físico, com atualizações ao longo da obra?'],
            14 => ['dimensao' => 5, 'texto' => 'Existe prática de avaliar impacto de alterações de prazo no custo indireto e na margem do empreendimento?'],
            15 => ['dimensao' => 5, 'texto' => 'Existe monitoramento contínuo da relação entre prazo, custo indireto e resultado econômico?'],
            16 => ['dimensao' => 6, 'texto' => 'Existem indicadores estruturados e padronizados para acompanhamento de prazo e custo, utilizados de forma consistente?'],
            17 => ['dimensao' => 6, 'texto' => 'Os critérios de medição do avanço físico consideram retrabalho, perdas e problemas de terminalidade?'],
            18 => ['dimensao' => 6, 'texto' => 'Os indicadores são utilizados de forma estruturada para tomada de decisão e definição de ações?'],
        ];
    }
}
