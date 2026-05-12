<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between h-10" 
             x-data="{ activeTab: (new URLSearchParams(window.location.search)).get('tab') || 'info' }"
             x-on:tab-changed.window="activeTab = $event.detail">
            <div class="flex items-center gap-3">
                <a href="{{ route('questionarios.index') }}" class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-700">
                    <ion-icon name="arrow-back-outline" class="text-2xl"></ion-icon>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $questionario->nome }}</h2>
            </div>
            <a :href="'{{ route('questionarios.edit', $questionario) }}?tab=' + activeTab"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-colors bg-gold hover:bg-gold-dark">
                <ion-icon name="create-outline"></ion-icon> Editar
            </a>
        </div>
    </x-slot>

    <div class="py-8" 
         x-data="{ activeTab: (new URLSearchParams(window.location.search)).get('tab') || 'info' }"
         x-init="$watch('activeTab', value => window.dispatchEvent(new CustomEvent('tab-changed', { detail: value })))">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex flex-col gap-6">
                <!-- Tab Navigation (Horizontal) -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-1.5 flex gap-1">
                    <button type="button" @click="activeTab = 'info'"
                            :class="activeTab === 'info' ? 'bg-gold-light text-gold font-bold' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 flex items-center justify-center gap-2 px-6 py-2.5 rounded-md text-sm transition-colors group">
                        <ion-icon name="information-circle-outline" class="text-xl"></ion-icon>
                        Informações
                    </button>
                    <button type="button" @click="activeTab = 'questions'"
                            :class="activeTab === 'questions' ? 'bg-gold-light text-gold font-bold' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 flex items-center justify-center gap-2 px-6 py-2.5 rounded-md text-sm transition-colors group">
                        <ion-icon name="list-outline" class="text-xl"></ion-icon>
                        Questões
                    </button>
                    <button type="button" @click="activeTab = 'result'"
                            :class="activeTab === 'result' ? 'bg-gold-light text-gold font-bold' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 flex items-center justify-center gap-2 px-6 py-2.5 rounded-md text-sm transition-colors group">
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
                                        <h3 class="text-lg font-bold text-gray-900">Informações do Modelo</h3>
                                        <p class="text-sm text-gray-500 mt-1">Visão geral das informações de apresentação.</p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Criado em {{ $questionario->created_at->format('d/m/Y H:i') }}</span>
                                        @if($questionario->is_active)
                                            <span class="text-xs font-bold px-3 py-1 bg-green-100 text-green-700 rounded-md uppercase tracking-wide">Ativo</span>
                                        @else
                                            <span class="text-xs font-bold px-3 py-1 bg-gray-100 text-gray-500 rounded-md uppercase tracking-wide">Inativo</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <!-- Line 1: Name and ID -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nome do Modelo</h4>
                                            <p class="text-sm font-semibold text-gray-800 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100">{{ $questionario->nome }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">ID do Modelo</h4>
                                            <p class="text-sm font-mono text-gray-500 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100">{{ $questionario->modelo_id }}</p>
                                        </div>
                                    </div>

                                    <!-- Separator-ish Space -->
                                    <div class="pt-2 space-y-6">
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Título da Página</h4>
                                            <p class="text-sm font-semibold text-gray-800">{{ $questionario->titulo ?: '—' }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Subtítulo / Chamada</h4>
                                            <p class="text-sm text-gray-600 leading-relaxed">{{ $questionario->subtitulo ?: '—' }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Descrição do Resultado</h4>
                                            <p class="text-sm text-gray-500 leading-relaxed">{{ $questionario->descricao ?: '—' }}</p>
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
                                            <span class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-sm bg-gold">
                                                {{ $loop->iteration }}
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-800 font-medium mb-2">{{ $q->texto }}</p>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 bg-gold-light text-gold rounded-md border border-gold text-[10px] font-semibold uppercase tracking-wide">{{ $q->dimensao_nome }}</span>
                                                    <span class="px-2.5 py-0.5 bg-gray-100 text-gray-600 rounded-md border border-gray-200 text-[10px] font-medium uppercase tracking-wide">Peso: {{ number_format($q->dimensao_peso, 2) }}</span>
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

                        <!-- Tab: Resultado -->
                        <div x-show="activeTab === 'result'" class="space-y-4 animate-fade-in" x-cloak>
                            @php
                                $faixas = [
                                    ['key' => 'red',    'range' => '0 – 40',   'label' => 'Previsibilidade Comprometida', 'bg' => 'bg-red-50',    'border' => 'border-red-200',   'dot' => 'bg-red-500',    'text' => 'text-red-700'],
                                    ['key' => 'yellow', 'range' => '41 – 70',  'label' => 'Previsibilidade Instável',     'bg' => 'bg-yellow-50', 'border' => 'border-yellow-200','dot' => 'bg-yellow-500', 'text' => 'text-yellow-700'],
                                    ['key' => 'green',  'range' => '71 – 100', 'label' => 'Previsibilidade Consistente',  'bg' => 'bg-green-50',  'border' => 'border-green-200', 'dot' => 'bg-green-500',  'text' => 'text-green-700'],
                                ];
                                $defaults = [
                                    'red'    => 'O resultado indica inconsistências relevantes nas condições que sustentam a margem do empreendimento. Existe alta probabilidade de impacto em prazo e resultado econômico já em curso ou em formação.',
                                    'yellow' => 'O empreendimento apresenta estrutura de gestão, porém com fragilidades importantes. O sistema aparenta controle, mas existem riscos relevantes de perda de margem.',
                                    'green'  => 'O empreendimento apresenta boa consistência entre planejamento e execução. Ainda assim, a manutenção dessa condição depende de monitoramento contínuo.',
                                ];
                            @endphp

                            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-50">
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Textos de Análise do Resultado</h3>
                                        <p class="text-xs text-gray-500 mt-0.5">Mensagens exibidas ao respondente conforme a faixa de IPM obtida.</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    @foreach($faixas as $f)
                                    @php $customText = $questionario->{'texto_resultado_' . $f['key']}; @endphp
                                    <div class="rounded-xl border {{ $f['border'] }} {{ $f['bg'] }} p-5">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="w-2.5 h-2.5 rounded-full {{ $f['dot'] }}"></span>
                                            <span class="text-xs font-bold uppercase tracking-wider {{ $f['text'] }}">IPM {{ $f['range'] }} — {{ $f['label'] }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $customText ?: $defaults[$f['key']] }}</p>
                                        @if(!$customText)
                                        <p class="text-[10px] text-gray-400 mt-2 italic">Texto padrão — personalize na edição.</p>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Disclaimer -->
                                <div class="mt-6 pt-6 border-t border-gray-100">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Texto de Disclaimer</h4>
                                    <p class="text-sm text-gray-600 italic leading-relaxed">
                                        "{{ $questionario->texto_disclaimer ?: 'Este resultado representa um retrato do momento atual do empreendimento. Assim como um exame, sua validade está associada ao momento em que foi realizado. Recomenda-se sua reaplicação periódica ou em marcos relevantes da obra.' }}"
                                    </p>
                                    @if(!$questionario->texto_disclaimer)
                                    <p class="text-[10px] text-gray-400 mt-1 italic">Texto padrão — personalize na edição.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
