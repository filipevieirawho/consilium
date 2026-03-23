<x-diagnosticos.layout>
    <div class="text-center py-16 px-4">
        <!-- Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-6" style="background-color: #fdf8ed;">
            <ion-icon name="bar-chart-outline" style="color: #D0AE6D; font-size: 2.5rem;"></ion-icon>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4 leading-tight">
            Check-up de Consistência<br>da Margem
        </h1>

        <!-- Separator -->
        <div class="w-16 h-1 rounded-full mx-auto mb-6" style="background-color: #D0AE6D;"></div>

        <!-- Description -->
        <p class="text-gray-600 text-lg max-w-lg mx-auto mb-2 leading-relaxed">
            Este check-up avalia a consistência das condições que sustentam a previsibilidade da margem de um empreendimento.
        </p>
        <p class="text-gray-500 text-sm max-w-lg mx-auto mb-10">
            O resultado representa um retrato do momento atual, com base nas informações fornecidas.
        </p>

        <!-- Stats row -->
        <div class="flex justify-center gap-8 mb-12">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-800">6</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Dimensões</div>
            </div>
            <div class="w-px bg-gray-200"></div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-800">18</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Perguntas</div>
            </div>
            <div class="w-px bg-gray-200"></div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-800">~5 min</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Duração</div>
            </div>
        </div>

        <!-- CTA -->
        <a href="{{ route('diagnostico.form', $token) }}"
            class="inline-flex items-center gap-2 px-8 py-4 text-white font-semibold rounded-xl shadow-lg transition-all hover:shadow-xl hover:scale-105"
            style="background-color: #D0AE6D;">
            Iniciar Check-up
            <ion-icon name="arrow-forward-outline" class="text-xl"></ion-icon>
        </a>

        <p class="text-xs text-gray-400 mt-6">
            Responda com base na sua percepção atual do empreendimento, mesmo que nem todas as informações estejam estruturadas.
        </p>
    </div>
</x-diagnosticos.layout>
