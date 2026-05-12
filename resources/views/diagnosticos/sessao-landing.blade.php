<x-diagnosticos.layout>
    <div class="bg-white sm:rounded-lg shadow-sm border border-gray-100 p-9 text-center">

        <!-- Logo / marca da sessão -->
        <div class="mb-6">
            <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider px-3 py-1.5 rounded-full bg-gold-light text-gold border border-gold/30">
                <ion-icon name="people-outline"></ion-icon>
                Diagnóstico Coletivo
            </span>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $sessao->titulo }}</h1>

        @if($sessao->descricao)
            <p class="text-sm text-gray-500 leading-relaxed mb-8 max-w-md mx-auto">{{ $sessao->descricao }}</p>
        @else
            <p class="text-sm text-gray-500 leading-relaxed mb-8 max-w-md mx-auto">
                Sua percepção é valiosa. Responda de forma independente e honesta — as respostas são anônimas.
            </p>
        @endif

        <!-- Instruções rápidas -->
        <div class="grid grid-cols-3 gap-4 mb-8 text-left">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-gold-light flex items-center justify-center mb-2">
                    <ion-icon name="shield-checkmark-outline" class="text-gold text-lg"></ion-icon>
                </div>
                <p class="text-xs font-semibold text-gray-700">Anônimo</p>
                <p class="text-xs text-gray-400 mt-0.5">Suas respostas não são identificadas individualmente.</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-gold-light flex items-center justify-center mb-2">
                    <ion-icon name="time-outline" class="text-gold text-lg"></ion-icon>
                </div>
                <p class="text-xs font-semibold text-gray-700">~10 minutos</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $sessao->questionario ? $sessao->questionario->questoes->count() : 18 }} questões no total.</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-gold-light flex items-center justify-center mb-2">
                    <ion-icon name="bar-chart-outline" class="text-gold text-lg"></ion-icon>
                </div>
                <p class="text-xs font-semibold text-gray-700">Resultado</p>
                <p class="text-xs text-gray-400 mt-0.5">Você verá seu IPM individual ao finalizar.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('sessao.iniciar', $sessao->token) }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 px-8 py-3.5 text-white font-semibold rounded-xl transition-colors bg-gold hover:bg-gold-dark text-sm">
                Participar agora
                <ion-icon name="arrow-forward-outline"></ion-icon>
            </button>
        </form>

        <p class="text-xs text-gray-400 mt-4">Ao participar, você confirma que responderá com base na realidade atual do empreendimento.</p>
    </div>
</x-diagnosticos.layout>
