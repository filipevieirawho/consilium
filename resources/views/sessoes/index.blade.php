<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between h-10">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <ion-icon name="speedometer-outline" class="text-2xl text-gold"></ion-icon>
                Sessões de Diagnóstico Coletivo
            </h2>
            <a href="{{ route('sessoes.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg bg-gold hover:bg-gold-dark transition-colors">
                <ion-icon name="add-outline" class="text-base"></ion-icon>
                Nova Sessão
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                {{ session('success') }}
            </div>
            @endif

            @if($sessoes->isEmpty())
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-16 text-center">
                <ion-icon name="people-outline" class="text-5xl text-gray-200 block mx-auto mb-4"></ion-icon>
                <h3 class="text-base font-semibold text-gray-600 mb-1">Nenhuma sessão criada ainda</h3>
                <p class="text-sm text-gray-400 mb-6">Crie uma sessão para coletar percepções de múltiplos respondentes sobre o mesmo diagnóstico.</p>
                <a href="{{ route('sessoes.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-lg bg-gold hover:bg-gold-dark transition-colors">
                    <ion-icon name="add-outline"></ion-icon>
                    Criar primeira sessão
                </a>
            </div>
            @else
            <div class="space-y-3">
                @foreach($sessoes as $s)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-5 flex items-center gap-4">
                        <!-- Status dot -->
                        <div class="flex-shrink-0">
                            <span class="w-2.5 h-2.5 rounded-full block {{ $s->is_active ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('sessoes.show', $s) }}"
                                   class="text-sm font-bold text-gray-900 hover:text-gold truncate">
                                    {{ $s->titulo }}
                                </a>
                                @if($s->is_active)
                                    <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded bg-green-100 text-green-700">Ativa</span>
                                @else
                                    <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded bg-gray-100 text-gray-500">Encerrada</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 mt-1 flex-wrap">
                                @if($s->questionario)
                                    <span class="text-xs text-gray-400">{{ $s->questionario->nome }}</span>
                                @endif
                                @if($s->empresa)
                                    <span class="text-xs text-gray-400">· {{ $s->empresa->nome_fantasia }}</span>
                                @endif
                                <span class="text-xs text-gray-400">· {{ $s->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <!-- Counters -->
                        <div class="flex items-center gap-6 flex-shrink-0 text-center">
                            <div>
                                <div class="text-lg font-bold text-gray-900">{{ $s->respostas_count }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-wide">Concluídos</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-400">{{ $s->diagnosticos_count }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-wide">Iniciados</div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <!-- Copy link -->
                            <button type="button"
                                onclick="navigator.clipboard.writeText('{{ route('sessao.landing', $s->token) }}').then(() => this.innerHTML = '<ion-icon name=\'checkmark-outline\'></ion-icon>')"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gold hover:bg-gold-light transition-colors"
                                title="Copiar link">
                                <ion-icon name="link-outline"></ion-icon>
                            </button>

                            <!-- Toggle active -->
                            <form method="POST" action="{{ route('sessoes.toggle', $s) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg transition-colors
                                           {{ $s->is_active ? 'text-green-500 hover:bg-red-50 hover:text-red-500' : 'text-gray-400 hover:bg-green-50 hover:text-green-500' }}"
                                    title="{{ $s->is_active ? 'Encerrar sessão' : 'Reativar sessão' }}">
                                    <ion-icon name="{{ $s->is_active ? 'pause-circle-outline' : 'play-circle-outline' }}"></ion-icon>
                                </button>
                            </form>

                            <!-- View -->
                            <a href="{{ route('sessoes.show', $s) }}"
                               class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gold hover:bg-gold-light transition-colors"
                               title="Ver resultado consolidado">
                                <ion-icon name="analytics-outline"></ion-icon>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
