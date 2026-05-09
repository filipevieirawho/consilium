<?php

namespace App\Http\Controllers;

use App\Mail\DiagnosticoResultadoMail;
use App\Models\Contact;
use App\Models\Diagnostico;
use App\Models\DiagnosticoResposta;
use App\Models\Questionario;
use App\Models\Empresa;
use App\Services\IpmCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DiagnosticoController extends Controller
{
    // ─── Public flow ─────────────────────────────────────────────────────

    /**
     * Start a fresh generic diagnostic (Campaign Link)
     * GET /diagnostico/novo
     *
     * No DB record is created here — we only persist once the respondent
     * submits the first form step ("Sobre você"). The questionario_id is
     * carried through the URL so no session or cache dependency is needed.
     */
    public function startNovo(Request $request)
    {
        $request->validate([
            'q' => 'nullable|exists:questionarios,id',
        ]);

        $token = Str::random(32);
        $qId   = $request->query('q');

        $url = route('diagnostico.landing', $token);
        if ($qId) {
            $url .= '?q=' . $qId;
        }

        return redirect($url);
    }

    /**
     * BLOCK 0 — Landing screen.
     * GET /diagnostico/{token}
     */
    public function landing(string $token)
    {
        $diagnostico = Diagnostico::with('questionario')->where('token', $token)->first();

        if (!$diagnostico) {
            $diagnostico = $this->pendingDiagnostico($token, request()->query('q'));
        } elseif ($diagnostico->status === 'concluido') {
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
        $diagnostico = Diagnostico::where('token', $token)->first();

        if (!$diagnostico) {
            $diagnostico = $this->pendingDiagnostico($token, request()->query('q'));
        } elseif ($diagnostico->status === 'concluido') {
            return redirect()->route('diagnostico.result', $token);
        }

        $questionario_id = $diagnostico->questionario_id;

        return view('diagnosticos.form', compact('diagnostico', 'token', 'questionario_id'));
    }

    /**
     * POST /diagnostico/{token}/dados
     */
    public function saveForm(Request $request, string $token)
    {
        $diagnostico = Diagnostico::where('token', $token)->first();

        // First form submission for a campaign link — create the record now.
        // questionario_id is carried as a hidden field (no session dependency).
        if (!$diagnostico) {
            $qId = $request->input('questionario_id') ?: null;

            // Validate questionario_id if provided
            if ($qId && !Questionario::where('id', $qId)->exists()) {
                $qId = null;
            }

            $diagnostico = Diagnostico::create([
                'token'           => $token,
                'status'          => 'em_andamento',
                'questionario_id' => $qId,
            ]);
        }

        $validated = $request->validate([
            'nome'     => 'required|string|max:255',
            'cargo'    => 'nullable|string|max:255',
            'empresa'  => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'telefone' => 'nullable|string|max:30',
        ]);

        // Auto-link or create Empresa
        $empresa = null;
        if ($diagnostico->empresa_id) {
            $empresa = \App\Models\Empresa::find($diagnostico->empresa_id);
        }
        
        if (!$empresa && !empty($validated['empresa'])) {
            $empresa = \App\Models\Empresa::whereRaw('LOWER(nome_fantasia) = ?', [strtolower($validated['empresa'])])
                ->orWhereRaw('LOWER(razao_social) = ?', [strtolower($validated['empresa'])])
                ->first();
            
            if (!$empresa) {
                $empresa = \App\Models\Empresa::create(['nome_fantasia' => $validated['empresa']]);
            }
        }

        // Auto-link or create Contact (Lead)
        $contact = null;
        if ($diagnostico->contact_id) {
            $contact = \App\Models\Contact::find($diagnostico->contact_id);
        }

        if (!$contact && !empty($validated['email'])) {
            $contact = \App\Models\Contact::where('email', $validated['email'])->first();
            
            if (!$contact) {
                $contact = \App\Models\Contact::create([
                    'name' => $validated['nome'],
                    'email' => $validated['email'],
                    'phone' => $validated['telefone'],
                    'company' => $validated['empresa'],
                    'empresa_id' => $empresa ? $empresa->id : null,
                    'status' => 'Cliente Potencial',
                    'message' => 'Lead gerado automaticamente pelo formulário de Diagnóstico.',
                ]);
                
                $contact->activities()->create([
                    'user_id' => null,
                    'type' => 'lead_created',
                ]);
                $contact->activities()->create([
                    'user_id' => null,
                    'type' => 'status_change',
                    'old_value' => null,
                    'new_value' => 'Cliente Potencial',
                ]);
            }
        }

        // If contact exists but has no empresa_id, link it now
        if ($contact && !$contact->empresa_id && $empresa) {
            $contact->update(['empresa_id' => $empresa->id]);
        }

        $diagnostico->update(array_merge($validated, [
            'empresa_id' => $empresa ? $empresa->id : null,
            'contact_id' => $contact ? $contact->id : null,
        ]));

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

        // --- Dynamic mode: Questionario linked ---
        if ($diagnostico->questionario_id) {
            $diagnostico->loadMissing('questionario.questoes');
            $questoes = $diagnostico->questionario->questoes->values();
            $total = $questoes->count();

            if ($num < 1 || $num > $total) abort(404);

            $questaoObj   = $questoes[$num - 1];   // 0-indexed collection
            $perguntaAtual = [
                'texto'   => $questaoObj->texto,
                'dimensao' => $questaoObj->dimensao_nome,
                'questao_id' => $questaoObj->id,
            ];
            $dimensaoNome  = $questaoObj->dimensao_nome;
            $dimensaoAtual = $questaoObj->dimensao_nome;

            $respostaAtual = $diagnostico->respostas()->where('questao_id', $questaoObj->id)->first();
            $respondidas   = $diagnostico->respostas()->count();

            return view('diagnosticos.pergunta', compact(
                'diagnostico', 'token', 'num', 'total',
                'perguntaAtual', 'dimensaoAtual', 'dimensaoNome',
                'respostaAtual', 'respondidas'
            ));
        }

        // --- Legacy static mode ---
        if ($num < 1 || $num > 18) abort(404);

        $total         = 18;
        $perguntas     = $this->getPerguntas();
        $perguntaAtual = $perguntas[$num];
        $dimensaoAtual = $perguntaAtual['dimensao'];
        $dimensaoNome  = IpmCalculator::NOMES_DIMENSAO[$dimensaoAtual];
        $respostaAtual = $diagnostico->respostas()->where('pergunta', $num)->first();
        $respondidas   = $diagnostico->respostas()->count();

        return view('diagnosticos.pergunta', compact(
            'diagnostico', 'token', 'num', 'total',
            'perguntaAtual', 'dimensaoAtual', 'dimensaoNome',
            'respostaAtual', 'respondidas'
        ));
    }

    /**
     * AJAX — Save/update a single answer.
     * POST /diagnostico/{token}/resposta
     */
    public function saveAnswer(Request $request, string $token)
    {
        $diagnostico = Diagnostico::where('token', $token)->firstOrFail();

        // --- Dynamic mode ---
        if ($diagnostico->questionario_id) {
            $validated = $request->validate([
                'questao_id' => 'required|integer|exists:questionario_questoes,id',
                'resposta'   => 'required|integer|min:0|max:3',
                'num'        => 'required|integer|min:1',
            ]);

            DiagnosticoResposta::updateOrCreate(
                ['diagnostico_id' => $diagnostico->id, 'questao_id' => $validated['questao_id']],
                [
                    'resposta' => $validated['resposta'], 
                    'dimensao' => 0,
                    'pergunta' => $validated['num']
                ]
            );

            $diagnostico->loadMissing('questionario.questoes');
            $total       = $diagnostico->questionario->questoes->count();
            $respondidas = $diagnostico->respostas()->count();
            $next        = $validated['num'] < $total ? $validated['num'] + 1 : null;

            return response()->json([
                'ok'          => true,
                'respondidas' => $respondidas,
                'total'       => $total,
                'next_url'    => $next
                    ? route('diagnostico.pergunta', [$token, $next])
                    : route('diagnostico.finalizar', $token),
            ]);
        }

        // --- Legacy static mode ---
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

        // Dynamic mode: check all questoes answered
        if ($diagnostico->questionario_id) {
            $diagnostico->loadMissing('questionario.questoes');
            $total       = $diagnostico->questionario->questoes->count();
            $respondidas = $diagnostico->respostas->count();

            if ($respondidas < $total) {
                $answeredQuestaoIds = $diagnostico->respostas->pluck('questao_id')->toArray();
                foreach ($diagnostico->questionario->questoes as $i => $q) {
                    if (!in_array($q->id, $answeredQuestaoIds)) {
                        return redirect()->route('diagnostico.pergunta', [$token, $i + 1]);
                    }
                }
            }
        } else {
            // Legacy: 18 questions
            if ($diagnostico->respostas->count() < 18) {
                $respondidas = $diagnostico->respostas->pluck('pergunta')->toArray();
                for ($i = 1; $i <= 18; $i++) {
                    if (!in_array($i, $respondidas)) {
                        return redirect()->route('diagnostico.pergunta', [$token, $i]);
                    }
                }
            }
        }

        $resultado = IpmCalculator::calcular($diagnostico->respostas, $diagnostico);

        $diagnostico->update(['ipm' => $resultado['ipm'], 'status' => 'concluido']);

        if ($diagnostico->email) {
            Mail::to($diagnostico->email)->send(new DiagnosticoResultadoMail($diagnostico, $resultado));
        }

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

        $resultado = IpmCalculator::calcular($diagnostico->respostas, $diagnostico);

        return view('diagnosticos.resultado', compact('diagnostico', 'token', 'resultado'));
    }

    // ─── Admin area ───────────────────────────────────────────────────────

    /**
     * Admin listing.
     * GET /diagnosticos
     */
    public function index(Request $request)
    {
        $query = Diagnostico::query()->with('questionario')->latest();

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
        $contacts     = Contact::orderBy('name')->get();
        $questionarios = Questionario::withCount('questoes')->where('is_active', true)->orderBy('titulo')->get();

        return view('diagnosticos.index', compact('diagnosticos', 'contacts', 'questionarios'));
    }

    /**
     * Admin detail.
     * GET /diagnosticos/{id}
     */
    public function show(Diagnostico $diagnostico)
    {
        $diagnostico->load(['respostas', 'contact', 'questionario.questoes']);
        $resultado = null;

        if ($diagnostico->status === 'concluido') {
            $resultado = IpmCalculator::calcular($diagnostico->respostas()->get(), $diagnostico);
        }

        $contacts     = Contact::orderBy('name')->get();
        $questionarios = Questionario::withCount('questoes')->where('is_active', true)->orderBy('titulo')->get();
        $perguntas    = $this->getPerguntas();

        return view('diagnosticos.show', compact('diagnostico', 'resultado', 'contacts', 'questionarios', 'perguntas'));
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

        $diagnostico->update($validated);
        $diagnostico->load('contact');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vínculo de lead atualizado com sucesso!',
                'contact' => $diagnostico->contact
            ]);
        }

        return redirect()->route('diagnosticos.show', $diagnostico)
            ->with('success', 'Vínculo de lead atualizado com sucesso!');
    }

    /**
     * DELETE /diagnosticos/{diagnostico}
     * Admin deleting a diagnostic
     */
    public function destroy(Diagnostico $diagnostico)
    {
        $diagnostico->respostas()->delete();
        $diagnostico->delete();

        return redirect()->route('diagnosticos.index')
            ->with('success', 'Diagnóstico excluído com sucesso!');
    }

    /**
     * Generate a new diagnostic link (admin).
     * POST /diagnosticos/gerar-link
     */
    public function generateLink(Request $request)
    {
        $request->validate([
            'contact_id'      => 'nullable|exists:contacts,id',
            'empresa_id'      => 'nullable|exists:empresas,id',
            'questionario_id' => 'nullable|exists:questionarios,id',
        ]);

        $contact   = null;
        $empresaId = $request->empresa_id;
        $defaults  = [];
        $qFields   = [];

        if ($request->filled('contact_id')) {
            $contact = Contact::find($request->contact_id);
            if ($contact) {
                $empresaId = $empresaId ?? $contact->empresa_id;
                $defaults = [
                    'nome'    => $contact->name ?? null,
                    'empresa' => $contact->company ?? null,
                    'email'   => $contact->email ?? null,
                    'telefone' => $contact->phone ?? null,
                ];
            }
        }

        // If an Empresa was explicitly selected or came from the Contact,
        // ensure its name is also in the 'empresa' string field for the form.
        if ($empresaId) {
            $emp = Empresa::find($empresaId);
            if ($emp && empty($defaults['empresa'])) {
                $defaults['empresa'] = $emp->nome_fantasia;
            }
        }

        $qFields = [];
        if ($request->filled('questionario_id')) {
            $q = Questionario::find($request->questionario_id);
            if ($q) {
                $qFields = [
                    'titulo'    => $q->titulo,
                    'subtitulo' => $q->subtitulo,
                    'descricao' => $q->descricao,
                ];
            }
        }

        $diagnostico = Diagnostico::create(array_merge([
            'token'           => Str::random(40),
            'contact_id'      => $request->contact_id ?? null,
            'empresa_id'      => $empresaId,
            'questionario_id' => $request->questionario_id ?? null,
            'status'          => 'em_andamento',
        ], $defaults, $qFields));

        $url = route('diagnostico.landing', $diagnostico->token);

        if ($request->expectsJson()) {
            return response()->json(['url' => $url, 'token' => $diagnostico->token]);
        }

        return redirect()->away($url);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    /**
     * Build a lightweight anonymous object for views when the campaign token
     * hasn't been persisted to the DB yet. The questionario_id is passed
     * directly from the URL query param — no session dependency.
     */
    protected function pendingDiagnostico(string $token, ?string $questionarioId = null): object
    {
        $questionario = $questionarioId
            ? Questionario::with('questoes')->find($questionarioId)
            : null;

        return (object) [
            'titulo'              => null,
            'subtitulo'           => null,
            'descricao'           => null,
            'nome'                => null,
            'cargo'               => null,
            'empresa'             => null,
            'email'               => null,
            'telefone'            => null,
            'nome_empreendimento' => null,
            'status'              => 'em_andamento',
            'aceite'              => false,
            'questionario_id'     => $questionarioId,
            'questionario'        => $questionario,
        ];
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
