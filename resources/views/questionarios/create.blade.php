<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('questionarios.index') }}" class="text-gray-400 hover:text-gray-700">
                <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
            </a>
            <h2 class="font-semibold text-xl text-gray-800">Novo Questionário</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('questionarios.store') }}" method="POST" id="formQuestionario">
                @csrf

                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 mb-5">
                    <h3 class="font-semibold text-gray-800 mb-4">Informações do Modelo</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Título do modelo *</label>
                            <input type="text" name="titulo" value="{{ old('titulo') }}" required
                                   placeholder="Ex: Check-up IPM 2026"
                                   class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                            @error('titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ID do Modelo *</label>
                            <input type="text" name="modelo_id" value="{{ old('modelo_id', $modeloId) }}" required
                                   class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D] font-mono">
                            @error('modelo_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-[#D0AE6D] focus:ring-[#D0AE6D]">
                        Ativo (disponível para uso em novos diagnósticos)
                    </label>
                </div>

                {{-- Questão Builder --}}
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 mb-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">Questões</h3>
                        <button type="button" id="btn-add-questao"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white rounded-lg"
                                style="background-color: #D0AE6D;">
                            <ion-icon name="add-outline"></ion-icon> Adicionar Questão
                        </button>
                    </div>

                    @if($errors->has('questoes'))
                    <p class="text-red-500 text-xs mb-3">{{ $errors->first('questoes') }}</p>
                    @endif

                    <div id="questoes-container" class="space-y-4">
                        {{-- JS will insert rows here --}}
                    </div>

                    <div id="questoes-empty" class="text-center py-10 text-gray-400 text-sm">
                        <ion-icon name="help-circle-outline" class="text-3xl block mx-auto mb-2"></ion-icon>
                        Nenhuma questão adicionada. Clique em "Adicionar Questão" para começar.
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('questionarios.index') }}" class="px-5 py-2 text-sm font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</a>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold text-white rounded-lg" style="background-color: #D0AE6D;">
                        Salvar Questionário
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    const DIMENSOES_DEFAULT = ['Viabilidade e Premissas', 'Projetos', 'Orçamento', 'Planejamento', 'Sustentação Financeira', 'Confiabilidade da Informação'];
    let questaoIndex = 0;

    function criarQuestaoRow(idx, data = {}) {
        const div = document.createElement('div');
        div.className = 'questao-row border border-gray-200 rounded-xl p-4 bg-gray-50';
        div.dataset.idx = idx;
        div.innerHTML = `
            <div class="flex items-start justify-between gap-3 mb-3">
                <span class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background-color: #D0AE6D;">${idx + 1}</span>
                <button type="button" onclick="removerQuestao(this)" class="text-gray-400 hover:text-red-500 ml-auto">
                    <ion-icon name="trash-outline" class="text-base"></ion-icon>
                </button>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Texto da Questão *</label>
                    <textarea name="questoes[${idx}][texto]" rows="2" required
                              class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">${data.texto || ''}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Dimensão</label>
                        <input type="text" name="questoes[${idx}][dimensao_nome]" required list="dimensoes-list"
                               value="${data.dimensao_nome || ''}"
                               placeholder="Ex: Planejamento"
                               class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Peso da Dimensão (0–1)</label>
                        <input type="number" step="0.01" min="0.01" max="1" name="questoes[${idx}][dimensao_peso]" required
                               value="${data.dimensao_peso || ''}"
                               placeholder="0.20"
                               class="block w-full text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                    </div>
                </div>
            </div>
        `;
        return div;
    }

    function atualizarNumeracao() {
        document.querySelectorAll('.questao-row').forEach((row, i) => {
            row.querySelector('span').textContent = i + 1;
        });
        document.getElementById('questoes-empty').style.display =
            document.querySelectorAll('.questao-row').length ? 'none' : 'block';
    }

    function removerQuestao(btn) {
        btn.closest('.questao-row').remove();
        atualizarNumeracao();
    }

    document.getElementById('btn-add-questao').addEventListener('click', function() {
        const container = document.getElementById('questoes-container');
        const row = criarQuestaoRow(questaoIndex++);
        container.appendChild(row);
        atualizarNumeracao();
        row.querySelector('textarea').focus();
    });

    // Pre-fill old values on validation errors
    @if(old('questoes'))
    @foreach(old('questoes', []) as $i => $q)
    (function() {
        const row = criarQuestaoRow({{ $i }}, {
            texto: @json($q['texto'] ?? ''),
            dimensao_nome: @json($q['dimensao_nome'] ?? ''),
            dimensao_peso: @json($q['dimensao_peso'] ?? ''),
        });
        document.getElementById('questoes-container').appendChild(row);
        questaoIndex = {{ $i + 1 }};
    })();
    @endforeach
    atualizarNumeracao();
    @endif
    </script>
    <datalist id="dimensoes-list">
        @foreach(['Viabilidade e Premissas', 'Projetos', 'Orçamento', 'Planejamento', 'Sustentação Financeira', 'Confiabilidade da Informação'] as $d)
        <option value="{{ $d }}">
        @endforeach
    </datalist>
    @endpush
</x-app-layout>
