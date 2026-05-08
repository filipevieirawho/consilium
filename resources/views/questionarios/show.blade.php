<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between h-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('questionarios.index') }}" class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-700">
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

    <div class="py-8" x-data="{ activeTab: 'info' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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
                        
                        <!-- Tab: Informações -->
                        <div x-show="activeTab === 'info'" class="space-y-5 animate-fade-in">
                            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-8">
                                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-50">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">Dados do Modelo</h3>
                                        <p class="text-sm text-gray-500 mt-1">Visão geral das informações de apresentação.</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if($questionario->is_active)
                                            <span class="text-xs font-bold px-3 py-1 bg-green-100 text-green-700 rounded-full uppercase tracking-wide">Ativo</span>
                                        @else
                                            <span class="text-xs font-bold px-3 py-1 bg-gray-100 text-gray-500 rounded-full uppercase tracking-wide">Inativo</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="space-y-6">
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nome do Modelo</h4>
                                            <p class="text-sm font-semibold text-gray-800 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100">{{ $questionario->nome }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">ID do Modelo</h4>
                                            <p class="text-sm font-mono text-gray-500 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100">{{ $questionario->modelo_id }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Página (Título)</h4>
                                            <p class="text-sm font-semibold text-gray-800">{{ $questionario->titulo ?: '—' }}</p>
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Subtítulo / Chamada</h4>
                                            <p class="text-sm text-gray-600 leading-relaxed">{{ $questionario->subtitulo ?: '—' }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Descrição do Resultado</h4>
                                            <p class="text-sm text-gray-500 leading-relaxed">{{ $questionario->descricao ?: '—' }}</p>
                                        </div>
                                        <div class="pt-4 border-t border-gray-50">
                                            <p class="text-[11px] text-gray-400">Criado em {{ $questionario->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Questões -->
                        <div x-show="activeTab === 'questions'" class="space-y-4 animate-fade-in" x-cloak>
                            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-50">
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Lista de Questões</h3>
                                        <p class="text-xs text-gray-500 mt-0.5">Este modelo possui {{ $questionario->questoes->count() }} questões cadastradas.</p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    @forelse($questionario->questoes as $q)
                                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 transition-all hover:shadow-sm">
                                        <div class="flex items-start gap-3">
                                            <span class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-sm" style="background-color: #D0AE6D;">
                                                {{ $loop->iteration }}
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-800 font-medium mb-2">{{ $q->texto }}</p>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <div class="flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-50 text-amber-700 rounded-full border border-amber-100">
                                                        <ion-icon name="pricetag-outline" class="text-[10px]"></ion-icon>
                                                        <span class="text-[10px] font-bold uppercase tracking-tight">{{ $q->dimensao_nome }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 px-2.5 py-0.5 bg-gray-100 text-gray-600 rounded-full border border-gray-200">
                                                        <ion-icon name="bar-chart-outline" class="text-[10px]"></ion-icon>
                                                        <span class="text-[10px] font-medium">Peso: {{ number_format($q->dimensao_peso, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-10 text-gray-400 border-2 border-dashed border-gray-100 rounded-2xl">
                                        <ion-icon name="help-circle-outline" class="text-4xl block mx-auto mb-2 opacity-20"></ion-icon>
                                        <p class="text-sm font-medium">Nenhuma questão cadastrada neste modelo.</p>
                                    </div>
                                    @endforelse
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
                                    Esta área está sendo preparada. Em breve você poderá visualizar as configurações de faixas de IPM e textos de feedback.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
