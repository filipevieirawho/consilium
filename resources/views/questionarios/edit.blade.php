<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 h-10">
            <a href="{{ route('questionarios.show', $questionario) }}" class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-700">
                <ion-icon name="arrow-back-outline" class="text-2xl"></ion-icon>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar: {{ $questionario->titulo }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ activeTab: 'info' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('questionarios.update', $questionario) }}" method="POST" id="formQuestionario">
                @csrf @method('PUT')

                <div class="flex flex-col gap-6">
                    <!-- Tab Navigation (Horizontal) -->
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-2 flex gap-1">
                        <button type="button" @click="activeTab = 'info'" 
                                :class="activeTab === 'info' ? 'bg-[#fdf8ed] text-[#D0AE6D] font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50'"
                                class="flex-1 flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm transition-all group">
                            <ion-icon name="information-circle-outline" class="text-xl"></ion-icon>
                            Informações
                        </button>
                        <button type="button" @click="activeTab = 'questions'" 
                                :class="activeTab === 'questions' ? 'bg-[#fdf8ed] text-[#D0AE6D] font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50'"
                                class="flex-1 flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm transition-all group">
                            <ion-icon name="list-outline" class="text-xl"></ion-icon>
                            Questões
                        </button>
                        <button type="button" @click="activeTab = 'result'" 
                                :class="activeTab === 'result' ? 'bg-[#fdf8ed] text-[#D0AE6D] font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50'"
                                class="flex-1 flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm transition-all group">
                            <ion-icon name="analytics-outline" class="text-xl"></ion-icon>
                            Resultado
                        </button>
                    </div>

                    <div class="flex flex-col lg:flex-row gap-6">
                        <!-- Main Content Area -->
                        <div class="flex-1">
                            
                            <!-- Tab: Informações do Modelo -->
                            <div x-show="activeTab === 'info'" class="space-y-5 animate-fade-in">
                                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-8">
                                    <div class="mb-6 pb-4 border-b border-gray-50">
                                        <h3 class="text-lg font-bold text-gray-900">Informações do Modelo</h3>
                                        <p class="text-sm text-gray-500 mt-1">Configure os dados básicos de identificação e apresentação do questionário.</p>
                                    </div>

                                    <div class="space-y-5">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome do Modelo</label>
                                                <input type="text" name="nome" value="{{ old('nome', $questionario->nome) }}" required
                                                       placeholder="Ex: Check-up IPM 2026"
                                                       class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D] shadow-sm transition-all px-4 py-2.5">
                                                @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1.5">ID do Modelo</label>
                                                <input type="text" value="{{ $questionario->modelo_id }}" disabled
                                                       class="block w-full text-sm rounded-lg border-gray-200 bg-gray-50 text-gray-500 focus:outline-none px-4 py-2.5 font-mono">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Título da Página</label>
                                            <input type="text" name="titulo" value="{{ old('titulo', $questionario->titulo) }}"
                                                   placeholder="Ex: Check-up de Consistência da Margem"
                                                   class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D] shadow-sm transition-all px-4 py-2.5">
                                            @error('titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Subtítulo (Call to action)</label>
                                            <textarea name="subtitulo" rows="3"
                                                      placeholder="Ex: Este check-up avalia a consistência das condições..."
                                                      class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D] shadow-sm transition-all px-4 py-2.5">{{ old('subtitulo', $questionario->subtitulo) }}</textarea>
                                            @error('subtitulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição do Resultado</label>
                                            <textarea name="descricao" rows="3"
                                                      placeholder="Ex: O resultado representa um retrato do momento atual..."
                                                      class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D] shadow-sm transition-all px-4 py-2.5">{{ old('descricao', $questionario->descricao) }}</textarea>
                                            @error('descricao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="pt-4 mt-4 border-t border-gray-50">
                                            <label class="flex items-center gap-3 text-sm text-gray-700 cursor-pointer group">
                                                <input type="checkbox" name="is_active" value="1" {{ $questionario->is_active ? 'checked' : '' }}
                                                       class="w-5 h-5 rounded border-gray-300 text-[#D0AE6D] focus:ring-[#D0AE6D] transition-all cursor-pointer">
                                                <span class="group-hover:text-gray-900 transition-colors">Ativo (disponível para uso em novos diagnósticos)</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Questões -->
                            <div x-show="activeTab === 'questions'" class="space-y-5 animate-fade-in" x-cloak>
                                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-8">
                                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-50">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">Questões do Modelo</h3>
                                            <p class="text-sm text-gray-500 mt-1">Gerencie as perguntas que compõem este diagnóstico.</p>
                                        </div>
                                        <button type="button" id="btn-add-questao"
                                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl transition-all hover:shadow-md"
                                                style="background-color: #D0AE6D;">
                                            <ion-icon name="add-outline" class="text-lg"></ion-icon> Adicionar Questão
                                        </button>
                                    </div>

                                    <div class="bg-[#fdf8ed] border border-[#D0AE6D] p-4 rounded-xl flex items-center gap-3 text-[#D0AE6D] mb-6">
                                        <ion-icon name="bulb-outline" class="text-xl flex-shrink-0"></ion-icon>
                                        <p class="text-sm font-medium leading-relaxed">
                                            Lembre-se que o somatório dos pesos das dimensões deve preferencialmente totalizar 1.00 para uma escala padrão.
                                        </p>
                                    </div>

                                    @if($errors->has('questoes'))
                                    <div class="bg-red-50 text-red-600 text-sm p-4 rounded-lg mb-6 flex items-center gap-2">
                                        <ion-icon name="alert-circle-outline" class="text-lg"></ion-icon>
                                        {{ $errors->first('questoes') }}
                                    </div>
                                    @endif

                                    <div id="questoes-container" class="space-y-6">
                                        {{-- JS will insert rows here --}}
                                    </div>

                                    <div id="questoes-empty" class="text-center py-20 text-gray-400 rounded-2xl mt-4 hidden">
                                        <ion-icon name="help-circle-outline" class="text-5xl block mx-auto mb-4 opacity-20"></ion-icon>
                                        <p class="font-medium">Nenhuma questão adicionada.</p>
                                        <p class="text-xs mt-1">Comece clicando no botão de adicionar acima.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Resultado (Placeholder) -->
                            <div x-show="activeTab === 'result'" class="space-y-5 animate-fade-in" x-cloak>
                                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-8 min-h-[400px] flex flex-col items-center justify-center text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <ion-icon name="construct-outline" class="text-3xl text-gray-300"></ion-icon>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900">Configurações de Resultado</h3>
                                    <p class="text-sm text-gray-500 mt-2 max-w-sm">
                                        Esta área está sendo preparada. Em breve você poderá configurar as faixas de IPM e textos automáticos por desempenho.
                                    </p>
                                </div>
                            </div>

                            <!-- Global Actions -->
                            <div class="mt-6 flex flex-col gap-4">
                                
                                <div class="flex justify-end items-center gap-4 py-4">
                                     <a href="{{ route('questionarios.show', $questionario) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                                        Descartar alterações
                                     </a>
                                     <button type="submit" class="px-8 py-3 text-sm font-bold text-white rounded-xl shadow-lg shadow-gold-100 transition-all hover:scale-[1.02] active:scale-[0.98]" 
                                             style="background-color: #D0AE6D;">
                                        Salvar Alterações
                                     </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <style>
        /* Hide datalist arrow */
        input::-webkit-calendar-picker-indicator {
            display: none !important;
        }
    </style>
    <script>
    const DIMENSOES_DEFAULT = ['Viabilidade e Premissas', 'Projetos', 'Orçamento', 'Planejamento', 'Sustentação Financeira', 'Confiabilidade da Informação'];
    let questaoIndex = 0;

    function criarQuestaoRow(idx, data = {}) {
        const div = document.createElement('div');
        div.className = 'questao-row border border-gray-200 rounded-xl p-4 bg-gray-50';
        
        let datalistHtml = `<datalist id="dimensoes-list">`;
        DIMENSOES_DEFAULT.forEach(d => {
            datalistHtml += `<option value="${d}">`;
        });
        datalistHtml += `</datalist>`;

        // Ensure datalist exists in the body
        if (!document.getElementById('dimensoes-list')) {
            const dl = document.createElement('div');
            dl.innerHTML = datalistHtml;
            document.body.appendChild(dl.firstElementChild);
        }

        div.innerHTML = `
            <div class="flex items-start justify-between gap-3 mb-3">
                <span class="numero flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background-color:#D0AE6D;">${idx+1}</span>
                <button type="button" onclick="this.closest('.questao-row').remove(); atualizarNumeracao();" class="text-gray-400 hover:text-red-500 ml-auto">
                    <ion-icon name="trash-outline" class="text-base"></ion-icon>
                </button>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Texto da Questão *</label>
                    <textarea name="questoes[${idx}][texto]" rows="2" required
                              class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">${data.texto||''}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Dimensão</label>
                        <input type="text" name="questoes[${idx}][dimensao_nome]" list="dimensoes-list" required
                               value="${data.dimensao_nome||''}" placeholder="Ex: Projetos"
                               class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Peso</label>
                        <input type="number" step="0.0001" name="questoes[${idx}][dimensao_peso]" required
                               value="${data.dimensao_peso||''}" placeholder="Ex: 0.20"
                               class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                    </div>
                </div>
            </div>
        `;
        return div;
    }

    function atualizarNumeracao() {
        document.querySelectorAll('.questao-row').forEach((row, i) => {
            row.querySelector('.numero').textContent = i + 1;
        });
    }

    // Pre-load existing questions
    @foreach($questionario->questoes as $i => $q)
    (function() {
        const row = criarQuestaoRow({{ $i }}, {
            texto: @json($q->texto),
            dimensao_nome: @json($q->dimensao_nome),
            dimensao_peso: {{ $q->dimensao_peso }},
        });
        document.getElementById('questoes-container').appendChild(row);
        questaoIndex = {{ $i + 1 }};
    })();
    @endforeach

    atualizarNumeracao();

    document.getElementById('btn-add-questao').addEventListener('click', function() {
        const container = document.getElementById('questoes-container');
        const row = criarQuestaoRow(questaoIndex++);
        container.appendChild(row);
        atualizarNumeracao();
    });
    </script>
    @endpush
</x-app-layout>
