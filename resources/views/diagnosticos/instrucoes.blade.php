<x-diagnosticos.layout :progressPct="10" progressLabel="Etapa 2 de 4 — Instruções">
    <div class="bg-white sm:rounded-lg shadow-sm border border-gray-100 p-9">
        <!-- Header -->
        <div class="mb-8">
            <span class="text-xs font-semibold uppercase tracking-wider px-2 py-1 rounded-md bg-gold-light text-gold">Instruções</span>
            <h2 class="text-xl font-bold text-gray-900 mt-3">Como responder</h2>
            <p class="text-sm text-gray-500 mt-1">Para cada pergunta, selecione a opção que melhor representa a <strong>realidade atual</strong> do empreendimento.</p>
        </div>

        <!-- Scale cards -->
        <div class="space-y-3 mb-8">
            <div class="flex items-start gap-4 p-4 rounded-lg border-2 border-gray-100 bg-gray-50">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center font-bold text-red-600 text-lg">0</div>
                <div>
                    <div class="font-semibold text-gray-800 text-sm">Inexistente ou desconhecido</div>
                    <div class="text-xs text-gray-500 mt-0.5">Não existe prática estruturada ou não há informação disponível.</div>
                </div>
            </div>
            <div class="flex items-start gap-4 p-4 rounded-lg border-2 border-gray-100 bg-gray-50">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center font-bold text-orange-600 text-lg">1</div>
                <div>
                    <div class="font-semibold text-gray-800 text-sm">Existe de forma informal</div>
                    <div class="text-xs text-gray-500 mt-0.5">A prática ocorre, mas não está formalizada, depende de pessoas ou não é consistente.</div>
                </div>
            </div>
            <div class="flex items-start gap-4 p-4 rounded-lg border-2 border-gray-100 bg-gray-50">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center font-bold text-yellow-700 text-lg">2</div>
                <div>
                    <div class="font-semibold text-gray-800 text-sm">Formalizado, pouco utilizado para decisão</div>
                    <div class="text-xs text-gray-500 mt-0.5">Existe processo definido, porém não é utilizado de forma consistente ou não direciona decisões relevantes.</div>
                </div>
            </div>
            <div class="flex items-start gap-4 p-4 rounded-lg border-2 border-gray-100 bg-gray-50">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center font-bold text-green-700 text-lg">3</div>
                <div>
                    <div class="font-semibold text-gray-800 text-sm">Formalizado e utilizado para decisão</div>
                    <div class="text-xs text-gray-500 mt-0.5">A prática é estruturada, atualizada, confiável e utilizada para tomada de decisão em prazo, custo e margem.</div>
                </div>
            </div>
        </div>

        <!-- Tip -->
        <p class="text-xs text-gray-400 mb-8 -mt-2">Na dúvida, selecione a opção mais conservadora.</p>

        <!-- Navigation -->
        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
            @if($diagnostico->sessao_id)
                <a href="{{ route('sessao.landing', $diagnostico->sessao->token) }}" class="text-sm text-gray-500 hover:text-gray-700">
                    ← Voltar
                </a>
            @else
                <a href="{{ route('diagnostico.form2', $token) }}" class="text-sm text-gray-500 hover:text-gray-700">
                    ← Voltar
                </a>
            @endif
            <a href="{{ route('diagnostico.pergunta', [$token, 1]) }}"
                class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold rounded-lg transition-colors bg-gold hover:bg-gold-dark">
                Entendi, vamos começar →
            </a>
        </div>
    </div>
</x-diagnosticos.layout>
