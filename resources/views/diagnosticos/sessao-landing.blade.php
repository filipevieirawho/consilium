<x-diagnosticos.layout>
    <div class="text-center py-16 px-4">

        <!-- Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-6 bg-gold-light">
            <ion-icon name="people-outline" class="text-gold" style="font-size: 2.5rem;"></ion-icon>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4 leading-tight">
            {!! nl2br(e($sessao->titulo)) !!}
        </h1>

        <!-- Separator -->
        <div class="w-16 h-1 rounded-full mx-auto mb-6 bg-gold"></div>

        <!-- Description -->
        @if($sessao->descricao)
            <p class="text-gray-600 text-lg max-w-lg mx-auto mb-2 leading-relaxed">
                {{ $sessao->descricao }}
            </p>
        @else
            <p class="text-gray-600 text-lg max-w-lg mx-auto mb-2 leading-relaxed">
                Responda com base na sua percepção atual do empreendimento.
            </p>
        @endif
        <p class="text-gray-500 text-sm max-w-lg mx-auto mb-10">
            Suas respostas são anônimas e não serão identificadas individualmente. Ao final você verá seu resultado pessoal.
        </p>

        <!-- Stats row -->
        @php
            $totalQ = $sessao->questionario ? $sessao->questionario->questoes->count() : 18;
            $totalD = $sessao->questionario ? $sessao->questionario->questoes->pluck('dimensao_nome')->unique()->count() : 6;
        @endphp
        <div class="flex justify-center gap-8 mb-12">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-800">{{ $totalD }}</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Dimensões</div>
            </div>
            <div class="w-px bg-gray-200"></div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-800">{{ $totalQ }}</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Perguntas</div>
            </div>
            <div class="w-px bg-gray-200"></div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-800">~{{ ceil($totalQ * 0.3) }} min</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Duração</div>
            </div>
        </div>

        <!-- CTA -->
        <form method="POST" action="{{ route('sessao.iniciar', $sessao->token) }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 px-8 py-4 text-white font-semibold rounded-lg transition-colors bg-gold hover:bg-gold-dark">
                Iniciar participação
                <ion-icon name="arrow-forward-outline" class="text-xl"></ion-icon>
            </button>
        </form>

        <p class="text-xs text-gray-400 mt-6">
            Ao participar, você confirma que responderá com base na realidade atual do empreendimento.
        </p>
    </div>
</x-diagnosticos.layout>
