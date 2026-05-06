<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between h-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('questionarios.index') }}" class="text-gray-400 hover:text-gray-700 flex items-center">
                    <ion-icon name="arrow-back-outline" class="text-2xl"></ion-icon>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $questionario->nome }}</h2>
            </div>
            <a href="{{ route('questionarios.edit', $questionario) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg"
               style="background-color: #D0AE6D;">
                <ion-icon name="create-outline"></ion-icon> Editar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 mb-5">
                <div class="flex items-center gap-3 mb-4">
                    @if($questionario->is_active)
                        <span class="text-xs font-bold px-2 py-0.5 bg-green-100 text-green-700 rounded-md uppercase tracking-wide">Ativo</span>
                    @else
                        <span class="text-xs font-bold px-2 py-0.5 bg-gray-100 text-gray-500 rounded-md uppercase tracking-wide">Inativo</span>
                    @endif
                    <span class="text-sm text-gray-400">{{ $questionario->questoes->count() }} questão(s)</span>
                    <span class="text-xs text-gray-400 ml-auto">Criado em {{ $questionario->created_at->format('d/m/Y') }}</span>
                </div>

                <div class="space-y-4 pt-4 border-t border-gray-50">
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Título</h4>
                        <p class="text-sm font-semibold text-gray-800">{{ $questionario->titulo ?: '—' }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Subtítulo</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $questionario->subtitulo ?: '—' }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Descrição</h4>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $questionario->descricao ?: '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($questionario->questoes as $q)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background-color: #D0AE6D;">{{ $loop->iteration }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800 mb-2">{{ $q->texto }}</p>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium px-2 py-0.5 bg-amber-50 text-amber-700 rounded-md border border-amber-200">
                                    {{ $q->dimensao_nome }}
                                </span>
                                <span class="text-xs text-gray-400">Peso: {{ number_format($q->dimensao_peso, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 text-gray-400 text-sm">Nenhuma questão cadastrada.</div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
