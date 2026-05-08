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

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('questionarios.update', $questionario) }}" method="POST">
                @csrf @method('PUT')

                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 mb-5">
                    <h3 class="font-semibold text-gray-800 mb-4">Informações do Modelo</h3>
                    <div class="space-y-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Modelo</label>
                                <input type="text" name="nome" value="{{ old('nome', $questionario->nome) }}" required
                                       class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ID do Modelo</label>
                                <input type="text" value="{{ $questionario->modelo_id }}" disabled
                                       class="block w-full text-sm rounded-lg border-gray-200 bg-gray-50 text-gray-400 font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                            <input type="text" name="titulo" value="{{ old('titulo', $questionario->titulo) }}"
                                   placeholder="Ex: Check-up de Consistência da Margem"
                                   class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                            <textarea name="subtitulo" rows="2"
                                      placeholder="Ex: Este check-up avalia a consistência das condições..."
                                      class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">{{ old('subtitulo', $questionario->subtitulo) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                            <textarea name="descricao" rows="2"
                                      placeholder="Ex: O resultado representa um retrato do momento atual..."
                                      class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">{{ old('descricao', $questionario->descricao) }}</textarea>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $questionario->is_active ? 'checked' : '' }}
                               class="rounded border-gray-300 text-[#D0AE6D] focus:ring-[#D0AE6D]">
                        Ativo
                    </label>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 mb-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">Questões</h3>
                        <button type="button" id="btn-add-questao"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white rounded-lg"
                                style="background-color: #D0AE6D;">
                            <ion-icon name="add-outline"></ion-icon> Adicionar
                        </button>
                    </div>
                    <div id="questoes-container" class="space-y-4"></div>
                    <div id="questoes-empty" class="text-center py-8 text-gray-400 text-sm hidden">Nenhuma questão.</div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('questionarios.show', $questionario) }}" class="px-5 py-2 text-sm font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</a>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold text-white rounded-lg" style="background-color: #D0AE6D;">Salvar</button>
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
