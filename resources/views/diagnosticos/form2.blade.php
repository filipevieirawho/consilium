<x-diagnosticos.layout :progressPct="8" progressLabel="Etapa 1b — Sobre o empreendimento">
    <div class="bg-white sm:rounded-lg shadow-sm border border-gray-100 p-9">
        <!-- Header -->
        <div class="mb-8">
            <span class="text-xs font-semibold uppercase tracking-wider px-2 py-1 rounded-md bg-gold-light text-gold">Etapa 2 de 2</span>
            <h2 class="text-xl font-bold text-gray-900 mt-3">Sobre o empreendimento</h2>
            <p class="text-sm text-gray-500 mt-1">Informe os dados do empreendimento a ser avaliado.</p>
        </div>

        <form method="POST" action="{{ route('diagnostico.saveForm2', $token) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do empreendimento <span class="text-red-500">*</span></label>
                <input type="text" name="nome_empreendimento" value="{{ old('nome_empreendimento', $diagnostico->nome_empreendimento) }}" required
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                    placeholder="Ex: Residencial Aurora">
                @error('nome_empreendimento')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                    <input type="text" name="cidade" value="{{ old('cidade', $diagnostico->cidade) }}"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                        placeholder="Cidade / Estado">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipologia</label>
                    <select name="tipologia"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold pl-4 pr-8 py-2.5 text-sm">
                        <option value="">Selecionar...</option>
                        <option value="Residencial Vertical" {{ old('tipologia', $diagnostico->tipologia) == 'Residencial Vertical' ? 'selected' : '' }}>Residencial Vertical</option>
                        <option value="MCMV" {{ old('tipologia', $diagnostico->tipologia) == 'MCMV' ? 'selected' : '' }}>MCMV</option>
                        <option value="Médio/Alto Padrão" {{ old('tipologia', $diagnostico->tipologia) == 'Médio/Alto Padrão' ? 'selected' : '' }}>Médio/Alto Padrão</option>
                        <option value="Residencial Horizontal" {{ old('tipologia', $diagnostico->tipologia) == 'Residencial Horizontal' ? 'selected' : '' }}>Residencial Horizontal</option>
                        <option value="Comercial" {{ old('tipologia', $diagnostico->tipologia) == 'Comercial' ? 'selected' : '' }}>Comercial</option>
                        <option value="Outro" {{ old('tipologia', $diagnostico->tipologia) == 'Outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nº de torres</label>
                    <input type="number" name="num_torres" value="{{ old('num_torres', $diagnostico->num_torres) }}" min="1"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                        placeholder="Ex: 2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estágio da obra (%)</label>
                    <input type="number" name="estagio_obra" value="{{ old('estagio_obra', $diagnostico->estagio_obra) }}" min="0" max="100"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                        placeholder="Ex: 45">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prazo inicial da obra (meses)</label>
                    <input type="number" name="prazo_inicial" value="{{ old('prazo_inicial', $diagnostico->prazo_inicial) }}" min="1"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                        placeholder="Ex: 36">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prazo atual previsto (meses)</label>
                    <input type="number" name="prazo_atual" value="{{ old('prazo_atual', $diagnostico->prazo_atual) }}" min="1"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                        placeholder="Ex: 42">
                </div>
            </div>

            <!-- Aceite -->
            <div class="bg-amber-50 border border-amber-200 sm:rounded-lg p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="aceite" id="aceite" value="1"
                        class="mt-0.5 h-4 w-4 rounded border-gray-300 accent-gold focus:ring-gold"
                        required>
                    <span class="text-sm text-amber-800">
                        <strong>Declaração de responsabilidade:</strong>
                        Declaro que as informações fornecidas são aproximadas e refletem minha percepção atual do empreendimento.
                        Entendo que serão utilizadas exclusivamente para fins de diagnóstico e contato profissional pela Consilium.
                    </span>
                </label>
                @error('aceite')<p class="text-red-500 text-xs mt-2 ml-7">{{ $message }}</p>@enderror
            </div>

            <!-- Navigation -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <a href="{{ route('diagnostico.form', $token) }}" class="text-sm text-gray-500 hover:text-gray-700">
                    ← Voltar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold rounded-xl transition-all hover:shadow-md bg-gold hover:bg-gold-dark">
                    Continuar →
                </button>
            </div>
        </form>
    </div>
</x-diagnosticos.layout>
