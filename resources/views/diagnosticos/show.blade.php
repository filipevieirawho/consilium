@php
$ipm = $resultado ? $resultado['ipm'] : null;
$faixa = $resultado ? $resultado['faixa'] : null;
$dimensoes = $resultado ? $resultado['dimensoes'] : [];
$dimensoesFracas = $resultado ? $resultado['dimensoes_fracas'] : [];

$faixaConfig = [
    'red'    => ['bg' => '#fef2f2', 'border' => '#ef4444', 'text' => '#dc2626', 'label' => 'Comprometida'],
    'yellow' => ['bg' => '#fffbeb', 'border' => '#f59e0b', 'text' => '#d97706', 'label' => 'Instável'],
    'green'  => ['bg' => '#f0fdf4', 'border' => '#22c55e', 'text' => '#16a34a', 'label' => 'Consistente'],
];
$cfg = $faixa ? $faixaConfig[$faixa] : null;

// Build answers map
$respostaMap = [];
foreach($diagnostico->respostas as $r) {
    $respostaMap[$r->pergunta] = $r->resposta;
}

$opcaoLabels = [
    0 => 'Inexistente',
    1 => 'Informal',
    2 => 'Formalizado, pouco usado',
    3 => 'Formalizado e utilizado',
];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('diagnosticos.index') }}" class="text-gray-400 hover:text-gray-700">
                    <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Diagnóstico — {{ $diagnostico->empresa ?: 'Sem empresa' }}
                    </h2>
                    @if($diagnostico->contact)
                        <p class="text-[10px] text-gray-400 font-mono uppercase tracking-widest">Lead: {{ $diagnostico->contact->name }}</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="copyToClipboard(this, '{{ route('diagnostico.landing', $diagnostico->token) }}')"
                    title="Copiar Link"
                    class="p-2 rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-[#D0AE6D] hover:border-[#D0AE6D] transition-all duration-200 flex items-center justify-center transform active:scale-95">
                    <ion-icon name="link-outline" class="text-xl"></ion-icon>
                </button>
                <a href="{{ route('diagnostico.landing', $diagnostico->token) }}" target="_blank"
                    class="text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 px-4 py-2 rounded-lg text-white"
                    style="background-color: #D0AE6D;">
                    <ion-icon name="open-outline" class="text-sm"></ion-icon> Ver formulário
                </a>
                <a href="{{ route('diagnostico.result', $diagnostico->token) }}" target="_blank"
                    class="text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 px-4 py-2 rounded-lg border border-[#D0AE6D] text-[#D0AE6D] hover:bg-[#D0AE6D]/05 transition-colors">
                    <ion-icon name="eye-outline" class="text-sm"></ion-icon> Resultado Público
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            <!-- Top row: IPM, Radar + Status -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <!-- IPM Score -->
                <div class="md:col-span-1">
                    @if($resultado)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 p-6 text-center h-full flex flex-col items-center justify-center"
                         style="border-color: {{ $cfg['border'] }};">
                         <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4 text-center w-full">Previsibilidade de Margem</h3>
                         <div class="w-16 h-16 rounded-full border flex items-center justify-center mb-4 mx-auto"
                              style="border-color: {{ $cfg['border'] }}; background: {{ $cfg['bg'] }};">
                             <ion-icon name="{{ $cfg['bg'] == '#fef2f2' ? 'alert-circle-outline' : ($cfg['bg'] == '#fffbeb' ? 'warning-outline' : 'checkmark-circle-outline') }}" style="font-size: 2rem; color: {{ $cfg['text'] }};"></ion-icon>
                         </div>
                        <div class="text-5xl font-extrabold mb-1" style="color: {{ $cfg['text'] }};">{{ $ipm }}</div>
                        <div class="text-sm font-bold uppercase tracking-widest mb-1" style="color: {{ $cfg['text'] }};">IPM</div>
                        <div class="text-xs font-semibold px-3 py-1 rounded-full mt-2" style="background-color: {{ $cfg['bg'] }}; color: {{ $cfg['text'] }};">{{ $cfg['label'] }}</div>
                    </div>
                    @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 p-6 text-center h-full flex flex-col items-center justify-center">
                        <ion-icon name="time-outline" class="text-4xl text-gray-300 mb-3"></ion-icon>
                        <div class="text-gray-500 text-sm font-medium">Aguardando conclusão</div>
                        <div class="text-xs text-gray-400 mt-1">{{ $diagnostico->respostas->count() }}/18 respostas</div>
                    </div>
                    @endif
                </div>

                <!-- Radar Chart -->
                <div class="md:col-span-1">
                    @if($resultado)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6 h-full flex flex-col items-center justify-center">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4 text-center">Desempenho por Dimensão</h3>
                        <div class="w-full relative" style="max-width: 200px; aspect-ratio: 1;">
                            <canvas id="radarChart"></canvas>
                        </div>
                    </div>
                    @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 p-6 text-center h-full flex flex-col items-center justify-center">
                        <ion-icon name="pie-chart-outline" class="text-4xl text-gray-300 mb-3"></ion-icon>
                        <div class="text-gray-500 text-sm font-medium">Gráfico indisponível</div>
                    </div>
                    @endif
                </div>

                <!-- Data summary -->
                <div class="md:col-span-2 bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 relative">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-semibold text-gray-900">Resumo de Informações</h3>
                        @if($diagnostico->status === 'concluido')
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md border text-green-700 border-green-300 bg-green-50">Concluído</span>
                        @else
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md border text-yellow-700 border-yellow-300 bg-yellow-50">Em andamento</span>
                        @endif
                    </div>

                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        @if($diagnostico->questionario)
                        <div class="col-span-2">
                            <dt class="text-xs text-gray-400 uppercase tracking-wide">Modelo Aplicado</dt>
                            <dd class="font-bold text-[#D0AE6D] mt-0.5">{{ $diagnostico->questionario->titulo }}</dd>
                        </div>
                        @else
                        <div class="col-span-2">
                            <dt class="text-xs text-gray-400 uppercase tracking-wide">Modelo Aplicado</dt>
                            <dd class="font-medium text-gray-400 mt-0.5 italic">Padrão Consilium (18 questões)</dd>
                        </div>
                        @endif

                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Respondente</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->nome ?: '—' }}</dd></div>
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Empresa</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->empresa ?: '—' }}</dd></div>
                        
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Cidade</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->cidade ?: '—' }}</dd></div>
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Data</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->created_at->format('d/m/Y H:i') }}</dd></div>
                        
                        <div class="col-span-2 mt-2 pt-2 border-t border-gray-50 flex items-start gap-8">
                            <div class="flex-1 max-w-xs">
                                <dt class="text-xs text-gray-400 uppercase tracking-wide flex items-center gap-2">
                                    Lead Vinculado
                                    <span id="vincular-status" class="hidden text-[10px] font-bold text-green-600 uppercase">✓ Salvo</span>
                                </dt>
                                <dd class="mt-0.5">
                                    <select id="contact_id_select"
                                        class="block w-full rounded-md border-gray-200 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D] text-[11px] py-1">
                                        <option value="">Nenhum (avulso)</option>
                                        @foreach($contacts as $c)
                                        <option value="{{ $c->id }}" {{ $diagnostico->contact_id == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }} {{ $c->company ? '— ' . $c->company : '' }}
                                        </option>
                                        @endforeach
                                    </select>
                                </dd>
                            </div>
                            @if($diagnostico->status !== 'concluido')
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide">Progresso</dt>
                                <dd class="text-xs font-semibold text-gray-600 mt-0.5">
                                    @php
                                        $respondidas = $diagnostico->respostas->count();
                                        $totalQ = $diagnostico->questionario ? $diagnostico->questionario->questoes->count() : 18;
                                    @endphp
                                    {{ $respondidas }}/{{ $totalQ }}
                                </dd>
                            </div>
                            @endif
                        </div>
                    </dl>


                </div>
            </div>



            <!-- Respostas do questionário -->
            @if($diagnostico->respostas->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-5">Respostas do questionário</h3>

                @if($diagnostico->questionario)
                    {{-- MODO DINÂMICO --}}
                    @php
                        $respostasPorQuestao = $diagnostico->respostas->keyBy('questao_id');
                        $grupos = $diagnostico->questionario->questoes->groupBy('dimensao_nome');
                    @endphp

                    @foreach($grupos as $dimNome => $questoes)
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-6 rounded-full" style="background-color: #D0AE6D;"></div>
                            <h4 class="font-bold text-gray-800 text-sm">{{ $dimNome }}</h4>
                        </div>
                        <div class="space-y-3">
                            @foreach($questoes as $idx => $q)
                                @php
                                    $resp = $respostasPorQuestao->get($q->id);
                                    $val = $resp ? $resp->resposta : null;
                                    $lColors = [0 => 'bg-red-100 text-red-700', 1 => 'bg-orange-100 text-gray-800', 2 => 'bg-yellow-100 text-gray-800', 3 => 'bg-green-100 text-green-700'];
                                @endphp
                                <div class="flex items-start gap-4 py-3 border-b border-gray-50 last:border-0 pl-4">
                                    <span class="flex-shrink-0 w-6 h-6 rounded bg-gray-100 text-[10px] font-bold flex items-center justify-center text-gray-400">{{ $idx + 1 }}</span>
                                    <p class="flex-1 text-sm text-gray-700 leading-relaxed">{{ $q->texto }}</p>
                                    @if($val !== null)
                                        <div class="flex-shrink-0 text-right">
                                            <span class="px-2 py-1 rounded text-[10px] font-bold {{ $lColors[$val] }}">
                                                {{ $val }} — {{ $opcaoLabels[$val] }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="flex-shrink-0 text-[10px] text-gray-300 italic">não respondida</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                @else
                    {{-- MODO LEGADO --}}
                    @php
                        $dimensoesNomesLocal = \App\Services\IpmCalculator::NOMES_DIMENSAO;
                        $dimensoesGrupos = [];
                        foreach($perguntas as $num => $p) {
                            $dimensoesGrupos[$p['dimensao']][] = $num;
                        }
                    @endphp

                    @foreach($dimensoesGrupos as $dim => $nums)
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background-color: #D0AE6D;">{{ $dim }}</div>
                            <h4 class="font-medium text-gray-800 text-sm">{{ $dimensoesNomesLocal[$dim] }}</h4>
                        </div>
                        <div class="space-y-2 pl-8">
                            @foreach($nums as $num)
                            @php
                            $resposta = $respostaMap[$num] ?? null;
                            $labelColors = [0 => 'bg-red-100 text-red-700', 1 => 'bg-orange-100 text-gray-800', 2 => 'bg-yellow-100 text-gray-800', 3 => 'bg-green-100 text-green-700'];
                            @endphp
                            <div class="flex items-start gap-3 py-2 border-b border-gray-50 last:border-0">
                                <span class="flex-shrink-0 w-6 h-6 rounded text-xs flex items-center justify-center font-bold text-gray-400 bg-gray-100">{{ $num }}</span>
                                <p class="flex-1 text-sm text-gray-700">{{ $perguntas[$num]['texto'] }}</p>
                                @if($resposta !== null)
                                <span class="flex-shrink-0 px-2 py-0.5 rounded text-xs font-bold {{ $labelColors[$resposta] }}">
                                    {{ $resposta }} — {{ $opcaoLabels[$resposta] }}
                                </span>
                                @else
                                <span class="flex-shrink-0 text-xs text-gray-400 italic">não respondida</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control {
            border-color: #e5e7eb !important; /* gray-200 */
            border-radius: 0.5rem !important; /* rounded-lg */
            padding: 0.375rem 0.75rem !important;
            font-size: 0.75rem !important; /* text-xs */
            box-shadow: none !important;
        }
        .ts-wrapper.focus .ts-control {
            border-color: #D0AE6D !important;
            ring-color: #D0AE6D !important;
            box-shadow: 0 0 0 1px #D0AE6D !important;
        }
        .ts-dropdown {
            border-radius: 0.5rem !important;
            margin-top: 4px !important;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1) !important;
            border-color: #f3f4f6 !important;
        }
        .ts-dropdown .active {
            background-color: #D0AE6D !important;
            color: white !important;
        }
        .ts-control .item {
            font-size: 0.75rem !important;
            color: #374151 !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Tom Select
        new TomSelect("#contact_id_select", {
            create: false,
            dropdownParent: 'body',
            sortField: {
                field: "text",
                direction: "asc"
            },
            onChange: function(value) {
                vincularLead(value);
            }
        });
    });

    function vincularLead(contactId) {
        const statusEl = document.getElementById('vincular-status');
        
        fetch("{{ route('diagnosticos.vincular', $diagnostico) }}", {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ contact_id: contactId })
        })
        .then(response => response.json())
        .then(data => {
            statusEl.classList.remove('hidden');
            setTimeout(() => statusEl.classList.add('hidden'), 3000);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao vincular lead.');
        });
    }

    function copyToClipboard(btn, text) {
        navigator.clipboard.writeText(text);
        
        const icon = btn.querySelector('ion-icon');
        const originalName = icon.getAttribute('name');
        
        // Feedback visual
        icon.setAttribute('name', 'checkmark-outline');
        btn.classList.add('bg-green-50', 'text-green-600', 'border-green-200', 'scale-110');
        btn.classList.remove('bg-white', 'text-gray-500', 'border-gray-200');
        
        setTimeout(() => {
            icon.setAttribute('name', originalName);
            btn.classList.remove('bg-green-50', 'text-green-600', 'border-green-200', 'scale-110');
            btn.classList.add('bg-white', 'text-gray-500', 'border-gray-200');
        }, 1500);
    }
    </script>
    @endpush

    @if($resultado)
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('radarChart');
        if (!ctx) return;

        @php
        $radarLabels = [];
        $radarData = [];
        $radarFullNames = [];
        
        foreach($dimensoes as $dim) {
            $fullName = $dim['nome'];
            // Create a short label (e.g. "Viabilidade e Premissas" -> "Viab.")
            $shortName = mb_substr($fullName, 0, 5) . '.';
            if (mb_strlen($fullName) <= 6) $shortName = $fullName;
            
            // Special cases for legacy or well known names
            $map = [
                'Viabilidade e Premissas' => 'Viabl.',
                'Projetos' => 'Proj.',
                'Orçamento' => 'Orçam.',
                'Planejamento' => 'Plan.',
                'Sustentação Financeira' => 'Finan.',
                'Confiabilidade da Informação' => 'Confi.',
            ];
            if (isset($map[$fullName])) $shortName = $map[$fullName];

            $radarLabels[] = $shortName;
            $radarFullNames[] = $fullName;
            $radarData[] = $dim['score'];
        }
        @endphp

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: @json($radarLabels),
                datasets: [{
                    label: 'Pontuação',
                    data: @json($radarData),
                    backgroundColor: 'rgba(208, 174, 109, 0.2)',
                    borderColor: '#D0AE6D',
                    pointBackgroundColor: '#D0AE6D',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#D0AE6D',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    r: {
                        angleLines: { color: 'rgba(0, 0, 0, 0.05)' },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        pointLabels: {
                            font: { size: 10, family: "'Inter', sans-serif", weight: '600' },
                            color: '#6b7280'
                        },
                        ticks: {
                            display: false,
                            min: 0,
                            max: 100,
                            stepSize: 25
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 10,
                        titleFont: { size: 12, family: "'Inter', sans-serif" },
                        bodyFont: { size: 13, family: "'Inter', sans-serif", weight: 'bold' },
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                const fullNames = @json($radarFullNames);
                                return fullNames[context[0].dataIndex];
                            },
                            label: function(context) {
                                return context.raw + '/100';
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
    @endpush
    @endif
</x-app-layout>

