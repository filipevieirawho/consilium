<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('empresas.index') }}" class="text-gray-400 hover:text-gray-700">
                    <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                </a>
                <h2 class="font-semibold text-xl text-gray-800">Nova Empresa</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">

                {{-- CNPJ Lookup --}}
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <label class="block text-sm font-semibold text-amber-800 mb-2">
                        <ion-icon name="search-outline" class="align-middle"></ion-icon>
                        Busca Rápida por CNPJ
                    </label>
                    <div class="flex gap-2">
                        <input type="text" id="cnpj-lookup-input" placeholder="00.000.000/0001-00"
                               class="flex-1 text-sm rounded-lg border-amber-300 focus:border-amber-500 focus:ring-amber-500"
                               maxlength="18">
                        <button type="button" id="btn-cnpj-buscar"
                                class="px-4 py-2 text-sm font-medium text-white rounded-lg"
                                class="bg-gold">
                            Buscar
                        </button>
                    </div>
                    <p id="cnpj-feedback" class="text-xs text-amber-600 mt-1 hidden"></p>
                </div>

                <form action="{{ route('empresas.store') }}" method="POST">
                    @csrf

                    <div class="space-y-5">
                        {{-- Row 1 --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome Fantasia *</label>
                                <input type="text" name="nome_fantasia" id="nome_fantasia"
                                       value="{{ old('nome_fantasia') }}" required
                                       class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                @error('nome_fantasia') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
                                <input type="text" name="razao_social" id="razao_social"
                                       value="{{ old('razao_social') }}"
                                       class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                            </div>
                        </div>

                        {{-- Row 2 --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                                <input type="text" name="cnpj" id="cnpj"
                                       value="{{ old('cnpj') }}" maxlength="18"
                                       placeholder="00.000.000/0001-00"
                                       class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                @error('cnpj') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Segmento</label>
                                <select name="segmento" class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                    <option value="">Selecione...</option>
                                    @foreach(['Imobiliária / Incorporação', 'Construção Civil', 'Engenharia & Projetos', 'Investimento / Fundo', 'Outros'] as $seg)
                                        <option value="{{ $seg }}" {{ old('segmento') === $seg ? 'selected' : '' }}>{{ $seg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Porte</label>
                                <select name="porte" class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                    <option value="">Selecione...</option>
                                    @foreach(['Microempresa (ME)', 'Empresa de Pequeno Porte (EPP)', 'Médio Porte', 'Grande Porte'] as $p)
                                        <option value="{{ $p }}" {{ old('porte') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Unidade</label>
                            <select name="tipo_unidade" class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold max-w-xs">
                                <option value="">Selecione...</option>
                                <option value="Matriz" {{ old('tipo_unidade') === 'Matriz' ? 'selected' : '' }}>Matriz</option>
                                <option value="Filial" {{ old('tipo_unidade') === 'Filial' ? 'selected' : '' }}>Filial</option>
                            </select>
                        </div>

                        {{-- Endereço --}}
                        <div class="border-t pt-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Endereço</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">CEP</label>
                                    <input type="text" name="cep" id="cep" value="{{ old('cep') }}" maxlength="9"
                                           class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1">Rua / Logradouro</label>
                                    <input type="text" name="rua" id="rua" value="{{ old('rua') }}"
                                           class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Número</label>
                                    <input type="text" name="numero" id="numero" value="{{ old('numero') }}"
                                           class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Complemento</label>
                                    <input type="text" name="complemento" id="complemento" value="{{ old('complemento') }}"
                                           class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Bairro</label>
                                    <input type="text" name="bairro" id="bairro" value="{{ old('bairro') }}"
                                           class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Cidade</label>
                                    <input type="text" name="cidade" id="cidade" value="{{ old('cidade') }}"
                                           class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Estado (UF)</label>
                                    <input type="text" name="estado" id="estado" value="{{ old('estado') }}" maxlength="2"
                                           class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">País</label>
                                    <input type="text" name="pais" value="{{ old('pais', 'Brasil') }}"
                                           class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-2 border-t">
                            <a href="{{ route('empresas.index') }}" class="px-5 py-2 text-sm font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</a>
                            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white rounded-lg" class="bg-gold">
                                Salvar Empresa
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function() {
        const fields = {
            nome_fantasia: document.getElementById('nome_fantasia'),
            razao_social:  document.getElementById('razao_social'),
            cnpj:          document.getElementById('cnpj'),
            cep:           document.getElementById('cep'),
            rua:           document.getElementById('rua'),
            numero:        document.getElementById('numero'),
            complemento:   document.getElementById('complemento'),
            bairro:        document.getElementById('bairro'),
            cidade:        document.getElementById('cidade'),
            estado:        document.getElementById('estado'),
            tipo_unidade:  document.querySelector('select[name="tipo_unidade"]'),
        };

        function applyMask(input, val) {
            val = val.replace(/\D/g, '').slice(0,14);
            val = val.replace(/^(\d{2})(\d)/, '$1.$2');
            val = val.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            val = val.replace(/\.(\d{3})(\d)/, '.$1/$2');
            val = val.replace(/(\d{4})(\d)/, '$1-$2');
            input.value = val;
        }

        [fields.cnpj, document.getElementById('cnpj-lookup-input')].forEach(el => {
            if (el) el.addEventListener('input', e => applyMask(e.target, e.target.value));
        });

        document.getElementById('btn-cnpj-buscar').addEventListener('click', function() {
            const lookupInput = document.getElementById('cnpj-lookup-input');
            const feedback = document.getElementById('cnpj-feedback');
            const cnpj = lookupInput.value.replace(/\D/g, '');

            if (cnpj.length !== 14) {
                feedback.textContent = 'Digite um CNPJ com 14 dígitos.';
                feedback.classList.remove('hidden', 'text-green-600');
                feedback.classList.add('text-red-600');
                return;
            }

            this.textContent = 'Buscando...';
            this.disabled = true;
            feedback.classList.add('hidden');

            fetch(@json(route('empresas.cnpjLookup')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ cnpj }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    feedback.textContent = data.error;
                    feedback.classList.remove('hidden', 'text-green-600');
                    feedback.classList.add('text-red-600');
                } else {
                    Object.keys(data).forEach(key => {
                        if (fields[key] && data[key]) fields[key].value = data[key];
                    });
                    if (data.cep) fields.cnpj.value = lookupInput.value;
                    feedback.textContent = '✓ Dados preenchidos automaticamente!';
                    feedback.classList.remove('hidden', 'text-red-600');
                    feedback.classList.add('text-green-600');
                }
            })
            .catch(() => {
                feedback.textContent = 'Erro ao consultar CNPJ. Tente novamente.';
                feedback.classList.remove('hidden', 'text-green-600');
                feedback.classList.add('text-red-600');
            })
            .finally(() => {
                document.getElementById('btn-cnpj-buscar').textContent = 'Buscar';
                document.getElementById('btn-cnpj-buscar').disabled = false;
            });
        });
    })();
    </script>
    @endpush
</x-app-layout>
