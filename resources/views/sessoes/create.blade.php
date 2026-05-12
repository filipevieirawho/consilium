<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 h-10">
            <a href="{{ route('sessoes.index') }}" class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-700">
                <ion-icon name="arrow-back-outline" class="text-2xl"></ion-icon>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nova Sessão</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('sessoes.store') }}" class="space-y-6">
                @csrf

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 space-y-6">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 mb-1">Configurar sessão</h3>
                        <p class="text-sm text-gray-500">Defina o título e o modelo de questionário que os participantes vão responder.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                            Título da Sessão <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="titulo" value="{{ old('titulo') }}" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                            placeholder="Ex: Diagnóstico Diretoria + Obra — Edifício Lorena">
                        @error('titulo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                            Descrição <span class="text-gray-300 font-normal">(opcional)</span>
                        </label>
                        <textarea name="descricao" rows="2"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                            placeholder="Instrução exibida na tela de boas-vindas para os participantes...">{{ old('descricao') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                            Questionário <span class="text-red-500">*</span>
                        </label>
                        <select name="questionario_id" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm">
                            <option value="">Selecione um questionário...</option>
                            @foreach($questionarios as $q)
                                <option value="{{ $q->id }}" {{ old('questionario_id') == $q->id ? 'selected' : '' }}>
                                    {{ $q->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('questionario_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                            Empresa <span class="text-gray-300 font-normal">(opcional)</span>
                        </label>
                        <select name="empresa_id"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm">
                            <option value="">Sem empresa vinculada</option>
                            @foreach($empresas as $e)
                                <option value="{{ $e->id }}" {{ old('empresa_id') == $e->id ? 'selected' : '' }}>
                                    {{ $e->nome_fantasia }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('sessoes.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Cancelar</a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white rounded-lg bg-gold hover:bg-gold-dark transition-colors">
                        Criar sessão e gerar link
                        <ion-icon name="arrow-forward-outline"></ion-icon>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
