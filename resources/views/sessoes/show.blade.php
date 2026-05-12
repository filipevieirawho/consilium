<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between h-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('sessoes.index') }}" class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-700">
                    <ion-icon name="arrow-back-outline" class="text-2xl"></ion-icon>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $sessao->titulo }}</h2>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Link de participação -->
                <button type="button" id="btn-copiar-link"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    <ion-icon name="link-outline"></ion-icon>
                    Copiar link
                </button>

                <!-- Toggle ativo -->
                <form method="POST" action="{{ route('sessoes.toggle', $sessao) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg transition-colors
                               {{ $sessao->is_active
                                   ? 'text-red-600 bg-red-50 hover:bg-red-100'
                                   : 'text-green-700 bg-green-50 hover:bg-green-100' }}">
                        <ion-icon name="{{ $sessao->is_active ? 'pause-circle-outline' : 'play-circle-outline' }}"></ion-icon>
                        {{ $sessao->is_active ? 'Encerrar sessão' : 'Reativar sessão' }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                {{ session('success') }}
            </div>
            @endif

            <!-- Status bar -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-6 flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $sessao->is_active ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                    <span class="text-xs font-bold uppercase tracking-wide text-gray-500">
                        {{ $sessao->is_active ? 'Sessão ativa' : 'Sessão encerrada' }}
                    </span>
                </div>
                <div class="h-4 w-px bg-gray-200"></div>
                <div class="text-sm text-gray-500">
                    <span class="font-bold text-gray-900">{{ $concluidos_count }}</span> respondente(s) concluídos
                    <span class="text-gray-300 mx-1">/</span>
                    <span class="font-bold text-gray-400">{{ $total }}</span> iniciados
                </div>
                @if(isset($scoreAlinhamento))
                <div class="h-4 w-px bg-gray-200"></div>
                <div class="text-sm text-gray-500">
                    Score de alinhamento:
                    <span class="font-bold {{ $scoreAlinhamento >= 70 ? 'text-green-600' : ($scoreAlinhamento >= 50 ? 'text-yellow-600' : 'text-red-500') }}">
                        {{ $scoreAlinhamento }}%
                    </span>
                </div>
                @endif
                @if($sessao->questionario)
                <div class="h-4 w-px bg-gray-200"></div>
                <span class="text-xs text-gray-400">{{ $sessao->questionario->nome }}</span>
                @endif
                <div class="ml-auto">
                    <span class="text-xs text-gray-400">Criada em {{ $sessao->created_at->format('d/m/Y') }}</span>
                </div>
            </div>

            @if($concluidos_count === 0)
            <!-- Aguardando respostas -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-16 text-center">
                <ion-icon name="hourglass-outline" class="text-5xl text-gray-200 block mx-auto mb-4"></ion-icon>
                <h3 class="text-base font-semibold text-gray-600 mb-1">Aguardando respostas</h3>
                <p class="text-sm text-gray-400 mb-6 max-w-sm mx-auto">
                    Compartilhe o link com os participantes. O painel consolidado será exibido assim que houver ao menos uma resposta concluída.
                </p>
                <div class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5">
                    <ion-icon name="link-outline" class="text-gray-400"></ion-icon>
                    <span class="text-xs font-mono text-gray-600" id="link-sessao-vazia">{{ route('sessao.landing', $sessao->token) }}</span>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ route('sessao.landing', $sessao->token) }}')"
                        class="text-gold hover:text-gold-dark ml-1">
                        <ion-icon name="copy-outline" class="text-sm"></ion-icon>
                    </button>
                </div>
            </div>

            @else

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Radar de convergência -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-gray-900">Radar de Convergência</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Cada linha representa um respondente. A linha dourada é a média do grupo.
                        </p>
                    </div>
                    <div class="relative" style="height: 300px;">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>

                <!-- IPMs individuais -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-gray-900">IPM por Respondente</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Distribuição dos scores individuais.</p>
                    </div>

                    @php
                        $ipmMedia = $ipms->count() > 0 ? round($ipms->avg(), 1) : null;
                        $ipmMin   = $ipms->count() > 0 ? round($ipms->min(), 1) : null;
                        $ipmMax   = $ipms->count() > 0 ? round($ipms->max(), 1) : null;
                    @endphp

                    <!-- Métricas -->
                    <div class="grid grid-cols-3 gap-3 mb-5">
                        <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-100">
                            <div class="text-xl font-bold text-gray-900">{{ $ipmMedia }}</div>
                            <div class="text-[10px] text-gray-400 uppercase tracking-wide mt-0.5">Média</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-100">
                            <div class="text-xl font-bold text-gray-500">{{ $ipmMin }}</div>
                            <div class="text-[10px] text-gray-400 uppercase tracking-wide mt-0.5">Mínimo</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-100">
                            <div class="text-xl font-bold text-gray-500">{{ $ipmMax }}</div>
                            <div class="text-[10px] text-gray-400 uppercase tracking-wide mt-0.5">Máximo</div>
                        </div>
                    </div>

                    <!-- Barras de IPM individuais -->
                    <div class="space-y-2">
                        @foreach($ipms->sortDesc()->values() as $i => $ipm)
                        @php
                            $cor = $ipm <= 40 ? '#ef4444' : ($ipm <= 70 ? '#eab308' : '#22c55e');
                            $bg  = $ipm <= 40 ? 'bg-red-50' : ($ipm <= 70 ? 'bg-yellow-50' : 'bg-green-50');
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="text-xs text-gray-400 w-20 text-right flex-shrink-0">
                                Resp. {{ $i + 1 }}
                            </div>
                            <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full transition-all"
                                     style="width: {{ $ipm }}%; background-color: {{ $cor }}"></div>
                            </div>
                            <div class="text-xs font-bold text-gray-700 w-10 flex-shrink-0">{{ $ipm }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Mapa de divergência por questão -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="mb-5 flex items-start justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Mapa de Percepções por Questão</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Para cada questão, veja como as respostas se distribuem entre os respondentes.
                            <span class="text-red-500 font-medium">Vermelho</span> indica alta divergência de percepção.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 text-[10px] text-gray-400 flex-shrink-0 ml-4">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-sm bg-red-200 inline-block"></span>0 — Inexistente</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-sm bg-orange-200 inline-block"></span>1 — Informal</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-sm bg-yellow-200 inline-block"></span>2 — Formalizado</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-sm bg-green-200 inline-block"></span>3 — Consolidado</span>
                    </div>
                </div>

                @php $dimensaoAtual = null; @endphp
                <div class="space-y-1">
                    @foreach($questoesData as $i => $q)
                        @if($q['dimensao'] !== $dimensaoAtual)
                            @php $dimensaoAtual = $q['dimensao']; @endphp
                            <div class="{{ $i > 0 ? 'mt-4' : '' }} mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gold px-2 py-0.5 bg-gold-light rounded">
                                    {{ $q['dimensao'] }}
                                </span>
                            </div>
                        @endif

                        <div class="flex items-center gap-3 py-2 px-3 rounded-lg {{ $q['divergente'] ? 'bg-red-50 border border-red-100' : 'hover:bg-gray-50' }} group">
                            <!-- Texto da questão -->
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-700 leading-relaxed truncate group-hover:whitespace-normal">
                                    @if($q['divergente'])
                                        <ion-icon name="warning-outline" class="text-red-400 mr-0.5 align-middle"></ion-icon>
                                    @endif
                                    {{ $q['texto'] }}
                                </p>
                            </div>

                            <!-- Distribuição visual: 4 blocos (0-3) com tamanho proporcional -->
                            <div class="flex items-center gap-0.5 flex-shrink-0">
                                @foreach([0 => ['bg-red-300','bg-red-100'], 1 => ['bg-orange-300','bg-orange-100'], 2 => ['bg-yellow-300','bg-yellow-100'], 3 => ['bg-green-400','bg-green-100']] as $val => [$cor, $corLight])
                                    @php $count = $q['dist'][$val]; @endphp
                                    <div class="flex flex-col items-center gap-0.5">
                                        <div class="w-8 h-6 rounded flex items-center justify-center text-[10px] font-bold
                                                    {{ $count > 0 ? $cor . ' text-white' : $corLight . ' text-gray-300' }}">
                                            {{ $count > 0 ? $count : '' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Média -->
                            <div class="w-10 text-right flex-shrink-0">
                                @if($q['avg'] !== null)
                                    <span class="text-xs font-bold {{ $q['avg'] >= 2.5 ? 'text-green-600' : ($q['avg'] >= 1.5 ? 'text-yellow-600' : 'text-red-500') }}">
                                        {{ number_format($q['avg'], 1) }}
                                    </span>
                                    <span class="text-[9px] text-gray-300">/3</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

    @push('scripts')
    @if($concluidos_count > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    (function() {
        const dimensoes = @json($dimensoes->values());
        const seriesIndividuais = @json($seriesIndividuais);
        const serieMedia = @json($serieMedia);

        const COLORS_INDIVIDUAL = [
            'rgba(156,163,175,0.4)',
            'rgba(167,139,250,0.4)',
            'rgba(96,165,250,0.4)',
            'rgba(52,211,153,0.4)',
            'rgba(251,191,36,0.4)',
            'rgba(248,113,113,0.4)',
            'rgba(129,140,248,0.4)',
            'rgba(34,197,94,0.4)',
        ];

        const datasets = seriesIndividuais.map((data, i) => ({
            label: `Resp. ${i + 1}`,
            data,
            borderColor: COLORS_INDIVIDUAL[i % COLORS_INDIVIDUAL.length],
            backgroundColor: COLORS_INDIVIDUAL[i % COLORS_INDIVIDUAL.length],
            borderWidth: 1.5,
            pointRadius: 2,
            pointHoverRadius: 4,
        }));

        // Série média — dourado, mais grossa
        datasets.push({
            label: 'Média do grupo',
            data: serieMedia,
            borderColor: '#D0AE6D',
            backgroundColor: 'rgba(208,174,109,0.1)',
            borderWidth: 2.5,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: '#D0AE6D',
        });

        new Chart(document.getElementById('radarChart'), {
            type: 'radar',
            data: { labels: dimensoes, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    r: {
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 25,
                            font: { size: 9 },
                            color: '#9ca3af',
                            backdropColor: 'transparent',
                        },
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        pointLabels: {
                            font: { size: 10, weight: '600' },
                            color: '#374151',
                        },
                    },
                },
            },
        });
    })();
    </script>
    @endif

    <script>
    document.getElementById('btn-copiar-link')?.addEventListener('click', function() {
        navigator.clipboard.writeText('{{ route('sessao.landing', $sessao->token) }}')
            .then(() => {
                this.innerHTML = '<ion-icon name="checkmark-outline"></ion-icon> Copiado!';
                setTimeout(() => this.innerHTML = '<ion-icon name="link-outline"></ion-icon> Copiar link', 2000);
            });
    });
    </script>
    @endpush
</x-app-layout>
