<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('empresas.show', $empresa) }}" class="text-gray-400 hover:text-gray-700">
                    <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                </a>
                <h2 class="font-semibold text-xl text-gray-800">Editar: {{ $empresa->nome_fantasia }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <form action="{{ route('empresas.update', $empresa) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome Fantasia *</label>
                                <input type="text" name="nome_fantasia" value="{{ old('nome_fantasia', $empresa->nome_fantasia) }}" required
                                       class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
                                <input type="text" name="razao_social" value="{{ old('razao_social', $empresa->razao_social) }}"
                                       class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                                <input type="text" name="cnpj" maxlength="18" placeholder="00.000.000/0001-00"
                                       value="{{ old('cnpj', $empresa->cnpj ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', str_pad($empresa->cnpj, 14, '0', STR_PAD_LEFT)) : '') }}"
                                       class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                @error('cnpj') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Segmento</label>
                                <select name="segmento" class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                    <option value="">Selecione...</option>
                                    @foreach(['Imobiliária / Incorporação', 'Construção Civil', 'Engenharia & Projetos', 'Investimento / Fundo', 'Outros'] as $seg)
                                        <option value="{{ $seg }}" {{ old('segmento', $empresa->segmento) === $seg ? 'selected' : '' }}>{{ $seg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Porte</label>
                                <select name="porte" class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                    <option value="">Selecione...</option>
                                    @foreach(['Microempresa (ME)', 'Empresa de Pequeno Porte (EPP)', 'Médio Porte', 'Grande Porte'] as $p)
                                        <option value="{{ $p }}" {{ old('porte', $empresa->porte) === $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Unidade</label>
                            <select name="tipo_unidade" class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold max-w-xs">
                                <option value="">Selecione...</option>
                                <option value="Matriz" {{ old('tipo_unidade', $empresa->tipo_unidade) === 'Matriz' ? 'selected' : '' }}>Matriz</option>
                                <option value="Filial" {{ old('tipo_unidade', $empresa->tipo_unidade) === 'Filial' ? 'selected' : '' }}>Filial</option>
                            </select>
                        </div>

                        <div class="border-t pt-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Endereço</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach(['cep' => 'CEP', 'rua' => 'Rua / Logradouro', 'numero' => 'Número', 'complemento' => 'Complemento', 'bairro' => 'Bairro', 'cidade' => 'Cidade', 'estado' => 'Estado (UF)', 'pais' => 'País'] as $field => $label)
                                <div {{ in_array($field, ['rua']) ? 'class="md:col-span-2"' : '' }}>
                                    <label class="block text-xs text-gray-500 mb-1">{{ $label }}</label>
                                    <input type="text" name="{{ $field }}" value="{{ old($field, $empresa->$field) }}"
                                           {{ $field === 'estado' ? 'maxlength=2' : '' }}
                                           class="block w-full text-sm rounded-lg border-gray-300 focus:border-gold focus:ring-gold">
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-2 border-t">
                            <a href="{{ route('empresas.show', $empresa) }}" class="px-5 py-2 text-sm font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</a>
                            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white rounded-lg" class="bg-gold">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
