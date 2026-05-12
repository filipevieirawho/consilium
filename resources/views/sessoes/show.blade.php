<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between h-10"
             x-data="{ activeTab: 'consolidado' }">
            <div class="flex items-center gap-3">
                <a href="{{ route('sessoes.index') }}" class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-700">
                    <ion-icon name="arrow-back-outline" class="text-2xl"></ion-icon>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $sessao->titulo }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btn-copiar-link"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    <ion-icon name="link-outline"></ion-icon>
                    Copiar link
                </button>
                <form method="POST" action="{{ route('sessoes.toggle', $sessao) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg transition-colors
                               {{ $sessao->is_active ? 'text-red-600 bg-red-50 hover:bg-red-100' : 'text-green-700 bg-green-50 hover:bg-green-100' }}">
                        <ion-icon name="{{ $sessao->is_active ? 'pause-circle-outline' : 'play-circle-outline' }}"></ion-icon>
                        {{ $sessao->is_active ? 'Encerrar sessão' : 'Reativar sessão' }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ activeTab: 'consolidado' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                {{ session('success') }}
            </div>
            @endif

            <!-- Status bar -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-5 flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $sessao->is_active ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                    <span class="text-xs font-bold uppercase tracking-wide text-gray-500">
                        {{ $sessao->is_active ? 'Ativa' : 'Encerrada' }}
                    </span>
                </div>
                <div class="h-4 w-px bg-gray-200"></div>
                <div class="text-sm text-gray-500">
                    <span class="font-bold text-gray-900">{{ $concluidos_count }}</span> concluído(s)
                    <span class="text-gray-300 mx-1">/</span>
                    <span class="text-gray-400">{{ $total }}</span> iniciado(s)
                </div>
                @if(isset($scoreAlinhamento))
                <div class="h-4 w-px bg-gray-200"></div>
                <div class="text-sm text-gray-500">
                    Alinhamento:
                    <span class="font-bold {{ $scoreAlinhamento >= 70 ? 'text-green-600' : ($scoreAlinhamento >= 50 ? 'text-yellow-600' : 'text-red-500') }}">
                        {{ $scoreAlinhamento }}%
                    </span>
                </div>
                @endif
                @if($sessao->questionario)
                <div class="h-4 w-px bg-gray-200"></div>
                <span class="text-xs text-gray-400">{{ $sessao->questionario->nome }}</span>
                @endif
                <div class="ml-auto text-xs text-gray-400">Criada em {{ $sessao->created_at->format('d/m/Y') }}</div>
            </div>

            @if($concluidos_count === 0)
            <!-- Aguardando respostas -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-16 text-center">
                <ion-icon name="hourglass-outline" class="text-5xl text-gray-200 block mx-auto mb-4"></ion-icon>
                <h3 class="text-base font-semibold text-gray-600 mb-1">Aguardando respostas</h3>
                <p class="text-sm text-gray-400 mb-6 max-w-sm mx-auto">
                    Compartilhe o link com os participantes. O painel consolidado aparecerá assim que houver ao menos uma resposta concluída.
                </p>
                <div class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5">
                    <ion-icon name="link-outline" class="text-gray-400 flex-shrink-0"></ion-icon>
                    <span class="text-xs font-mono text-gray-600">{{ route('sessao.landing', $sessao->token) }}</span>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ route('sessao.landing', $sessao->token) }}')"
                        class="text-gold hover:text-gold-dark ml-1 flex-shrink-0">
                        <ion-icon name="copy-outline" class="text-sm"></ion-icon>
                    </button>
                </div>
            </div>

            @else

            <!-- Abas -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-1.5 flex gap-1">
                <button type="button" @click="activeTab = 'consolidado'"
                        :class="activeTab === 'consolidado' ? 'bg-gold-light text-gold font-bold' : 'text-gray-500 hover:bg-gray-50'"
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-2.5 rounded-md text-sm transition-colors">
                    <ion-icon name="analytics-outline" class="text-lg"></ion-icon>
                    Resultado Consolidado
                </button>
                <button type="button" @click="activeTab = 'individuais'"
                        :class="activeTab === 'individuais' ? 'bg-gold-light text-gold font-bold' : 'text-gray-500 hover:bg-gray-50'"
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-2.5 rounded-md text-sm transition-colors">
                    <ion-icon name="list-outline" class="text-lg"></ion-icon>
                    Respostas Individuais
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full
                                 {{ 'bg-gray-100 text-gray-500' }}"
                          :class="activeTab === 'individuais' ? 'bg-gold/20 text-gold' : 'bg-gray-100 text-gray-500'">
                        {{ $concluidos_count }}
                    </span>
                </button>
            </div>

            <!-- ── Aba: Resultado Consolidado ─────────────────────────────── -->
            <div x-show="activeTab === 'consolidado'" class="space-y-6">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Radar de convergência -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="mb-4">
                            <h3 class="text-sm font-bold text-gray-900">Radar de Convergência</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Cada linha = um respondente. Linha dourada = média do grupo.</p>
                        </div>
                        <div class="relative" style="height: 300px;">
                            <canvas id="radarChart"></canvas>
                        </div>
                    </div>

                    <!-- IPM por Respondente -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col">
                        <div class="mb-4">
                            <h3 class="text-sm font-bold text-gray-900">IPM por Respondente</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Distribuição dos scores individuais.</p>
                        </div>

                        @php
                            $ipmMedia = $ipms->count() > 0 ? round($ipms->avg(), 1) : null;
                            $ipmMin   = $ipms->count() > 0 ? round($ipms->min(), 1) : null;
                            $ipmMax   = $ipms->count() > 0 ? round($ipms->max(), 1) : null;
                        @endphp

                        <div class="grid grid-cols-3 gap-3 flex-1">
                            <div class="bg-gray-50 rounded-lg border border-gray-100 flex flex-col items-center justify-center text-center p-3">
                                <div class="text-2xl font-bold text-gray-500">{{ $ipmMin }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-wide mt-1">Mínimo</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg border border-gray-100 flex flex-col items-center justify-center text-center p-3">
                                <div class="text-2xl font-bold {{ $ipmMedia <= 40 ? 'text-red-600' : ($ipmMedia <= 70 ? 'text-yellow-500' : 'text-green-600') }}">{{ $ipmMedia }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-wide mt-1">Média</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg border border-gray-100 flex flex-col items-center justify-center text-center p-3">
                                <div class="text-2xl font-bold text-gray-500">{{ $ipmMax }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-wide mt-1">Máximo</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Mapa de divergência por questão -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <div class="mb-5 flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Mapa de Percepções por Questão</h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Distribuição das respostas por questão.
                                <span class="text-red-500 font-medium">Vermelho</span> = alta divergência de percepção.
                            </p>
                        </div>
                        <div class="flex items-center gap-3 text-[10px] text-gray-400 flex-shrink-0">
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-sm bg-red-300 inline-block"></span>0</span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-sm bg-orange-300 inline-block"></span>1</span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-sm bg-yellow-300 inline-block"></span>2</span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-sm bg-green-400 inline-block"></span>3</span>
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

                            <div class="flex items-center gap-3 py-2 px-3 rounded-lg {{ $q['divergente'] ? '' : 'hover:bg-gray-50' }} group">
                                @if($q['divergente'])
                                    <ion-icon name="warning-outline" class="text-red-500 flex-shrink-0 text-base"></ion-icon>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-700 leading-relaxed truncate group-hover:whitespace-normal">
                                        {{ $q['texto'] }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-0.5 flex-shrink-0">
                                    @php $maxCount = max($q['dist']); @endphp

                                    {{-- 0 — Vermelho --}}
                                    @php $count = $q['dist'][0]; $dominant = $maxCount > 0 && $count === $maxCount; @endphp
                                    <div class="w-8 h-6 rounded flex items-center justify-center text-[10px] font-bold
                                                {{ $dominant ? 'bg-red-500 text-white' : 'bg-red-50 text-red-500' }}">
                                        {{ $count > 0 ? $count : '' }}
                                    </div>

                                    {{-- 1 — Laranja --}}
                                    @php $count = $q['dist'][1]; $dominant = $maxCount > 0 && $count === $maxCount; @endphp
                                    <div class="w-8 h-6 rounded flex items-center justify-center text-[10px] font-bold
                                                {{ $dominant ? 'bg-orange-500 text-white' : 'bg-orange-50 text-orange-500' }}">
                                        {{ $count > 0 ? $count : '' }}
                                    </div>

                                    {{-- 2 — Amarelo --}}
                                    @php $count = $q['dist'][2]; $dominant = $maxCount > 0 && $count === $maxCount; @endphp
                                    <div class="w-8 h-6 rounded flex items-center justify-center text-[10px] font-bold
                                                {{ $dominant ? 'bg-yellow-500 text-white' : 'bg-yellow-50 text-yellow-600' }}">
                                        {{ $count > 0 ? $count : '' }}
                                    </div>

                                    {{-- 3 — Verde --}}
                                    @php $count = $q['dist'][3]; $dominant = $maxCount > 0 && $count === $maxCount; @endphp
                                    <div class="w-8 h-6 rounded flex items-center justify-center text-[10px] font-bold
                                                {{ $dominant ? 'bg-green-500 text-white' : 'bg-green-50 text-green-600' }}">
                                        {{ $count > 0 ? $count : '' }}
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($q['avg'] !== null)
                                        <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-md bg-white border
                                                      {{ $q['avg'] >= 2.5 ? 'text-green-600 border-green-200' : ($q['avg'] >= 1.5 ? 'text-yellow-600 border-yellow-200' : 'text-red-500 border-red-200') }}">
                                            {{ number_format($q['avg'], 1) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>{{-- /consolidado --}}

            <!-- ── Aba: Respostas Individuais ─────────────────────────────── -->
            <div x-show="activeTab === 'individuais'" x-cloak class="space-y-3">

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Respostas Individuais</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Anônimas — acesse cada resultado completo individualmente.</p>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-50">
                        @foreach($concluidos->sortByDesc('ipm')->values() as $i => $d)
                        @php
                            $faixa = $d->ipm <= 40 ? 'red' : ($d->ipm <= 70 ? 'yellow' : 'green');
                            $faixaLabel = $d->ipm <= 40 ? 'Comprometida' : ($d->ipm <= 70 ? 'Instável' : 'Consistente');
                            $faixaCor   = $d->ipm <= 40 ? 'text-red-600 bg-red-50 border-red-200' : ($d->ipm <= 70 ? 'text-yellow-700 bg-yellow-50 border-yellow-200' : 'text-green-700 bg-green-50 border-green-200');
                            $barCor     = $d->ipm <= 40 ? 'bg-red-400' : ($d->ipm <= 70 ? 'bg-yellow-400' : 'bg-green-400');
                        @endphp
                        <div class="px-6 py-4 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                            <!-- Número anônimo -->
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                                {{ $i + 1 }}
                            </div>

                            <!-- IPM + barra -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1.5">
                                    <span class="text-sm font-bold text-gray-900">IPM {{ $d->ipm }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded border {{ $faixaCor }}">
                                        {{ $faixaLabel }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="{{ $barCor }} h-1.5 rounded-full" style="width: {{ $d->ipm }}%"></div>
                                </div>
                            </div>

                            <!-- Data -->
                            <div class="text-xs text-gray-400 flex-shrink-0 hidden sm:block">
                                {{ $d->updated_at->format('d/m H:i') }}
                            </div>

                            <!-- Link resultado -->
                            <a href="{{ route('diagnostico.result', $d->token) }}" target="_blank"
                               class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gold bg-gold-light hover:bg-gold/20 rounded-lg transition-colors border border-gold/30">
                                Ver resultado
                                <ion-icon name="open-outline" class="text-xs"></ion-icon>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>{{-- /individuais --}}

            @endif {{-- concluidos > 0 --}}

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

        const COLORS = [
            'rgba(156,163,175,0.45)',
            'rgba(167,139,250,0.45)',
            'rgba(96,165,250,0.45)',
            'rgba(52,211,153,0.45)',
            'rgba(251,191,36,0.45)',
            'rgba(248,113,113,0.45)',
            'rgba(129,140,248,0.45)',
            'rgba(34,197,94,0.45)',
        ];

        const datasets = seriesIndividuais.map((data, i) => ({
            label: `Resp. ${i + 1}`,
            data,
            borderColor: COLORS[i % COLORS.length],
            backgroundColor: COLORS[i % COLORS.length],
            borderWidth: 1.5,
            pointRadius: 2,
            pointHoverRadius: 4,
            order: 1,
        }));

        datasets.push({
            label: 'Média',
            data: serieMedia,
            borderColor: '#D0AE6D',
            backgroundColor: 'rgba(208,174,109,0.08)',
            borderWidth: 2.5,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: '#D0AE6D',
            order: 0,
        });

        new Chart(document.getElementById('radarChart'), {
            type: 'radar',
            data: { labels: dimensoes, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    r: {
                        min: 0,
                        max: 100,
                        ticks: { stepSize: 25, font: { size: 9 }, color: '#9ca3af', backdropColor: 'transparent' },
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        pointLabels: { font: { size: 10, weight: '600' }, color: '#374151' },
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
