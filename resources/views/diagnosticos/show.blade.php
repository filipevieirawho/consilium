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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <ion-icon name="analytics-outline" class="text-[#D0AE6D] text-2xl"></ion-icon>
                Diagnóstico — {{ $diagnostico->empresa ?: 'Sem empresa' }}
            </h2>
            <a href="{{ route('diagnosticos.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <ion-icon name="arrow-back-outline"></ion-icon> Voltar à lista
            </a>
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
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <div class="flex items-start justify-between mb-5">
                        <h3 class="font-semibold text-gray-900">Dados do diagnóstico</h3>
                        <a href="{{ route('diagnostico.result', $diagnostico->token) }}" target="_blank"
                            class="text-xs flex items-center gap-1 px-3 py-1.5 rounded-lg text-white"
                            style="background-color: #D0AE6D;">
                            <ion-icon name="open-outline"></ion-icon> Ver formulário
                        </a>
                    </div>

                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Respondente</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->nome ?: '—' }}</dd></div>
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Cargo</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->cargo ?: '—' }}</dd></div>
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Empresa</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->empresa ?: '—' }}</dd></div>
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">E-mail</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->email ?: '—' }}</dd></div>
                        @if($diagnostico->telefone)<div><dt class="text-xs text-gray-400 uppercase tracking-wide">Telefone</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->telefone }}</dd></div>@endif
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Empreendimento</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->nome_empreendimento ?: '—' }}</dd></div>
                        @if($diagnostico->cidade)<div><dt class="text-xs text-gray-400 uppercase tracking-wide">Cidade</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->cidade }}</dd></div>@endif
                        @if($diagnostico->tipologia)<div><dt class="text-xs text-gray-400 uppercase tracking-wide">Tipologia</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->tipologia }}</dd></div>@endif
                        @if($diagnostico->estagio_obra !== null)<div><dt class="text-xs text-gray-400 uppercase tracking-wide">Estágio</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->estagio_obra }}%</dd></div>@endif
                        @if($diagnostico->prazo_inicial)<div><dt class="text-xs text-gray-400 uppercase tracking-wide">Prazo inicial</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->prazo_inicial }} meses</dd></div>@endif
                        @if($diagnostico->prazo_atual)<div><dt class="text-xs text-gray-400 uppercase tracking-wide">Prazo atual</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->prazo_atual }} meses</dd></div>@endif
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Data</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $diagnostico->created_at->format('d/m/Y H:i') }}</dd></div>
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide">Status</dt>
                            <dd class="mt-0.5">
                                @if($diagnostico->status === 'concluido')
                                <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-md border text-green-700 border-green-300 bg-green-50">Concluído</span>
                                @else
                                <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-md border text-yellow-700 border-yellow-300 bg-yellow-50">Em andamento</span>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <!-- Link -->
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Link do diagnóstico</label>
                        <div class="flex items-center gap-2 mt-1">
                            <input type="text" id="diag-url" readonly value="{{ route('diagnostico.landing', $diagnostico->token) }}"
                                class="block flex-1 text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                            <button onclick="document.getElementById('diag-url').select(); document.execCommand('copy'); this.textContent='✓'; setTimeout(() => this.textContent='Copiar', 2000)"
                                class="px-3 py-2 text-xs rounded-lg text-white whitespace-nowrap"
                                style="background-color: #D0AE6D;">Copiar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vincular a lead -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Vincular a um lead</h3>
                <form method="POST" action="{{ route('diagnosticos.vincular', $diagnostico) }}" class="flex items-end gap-4 flex-wrap">
                    @csrf @method('PATCH')
                    <div class="flex-1 min-w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lead</label>
                        <select name="contact_id"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D] pl-4 pr-8 py-2.5 text-sm">
                            <option value="">Nenhum (diagnóstico avulso)</option>
                            @foreach($contacts as $c)
                            <option value="{{ $c->id }}" {{ $diagnostico->contact_id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} {{ $c->company ? '— ' . $c->company : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2.5 text-white font-medium rounded-lg text-sm" style="background-color: #D0AE6D;">
                        Salvar vínculo
                    </button>
                </form>
            </div>

            <!-- Questionnaire answers -->
            @if($diagnostico->respostas->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-5">Respostas do questionário</h3>

                @php
                $dimensoesNomes = \App\Services\IpmCalculator::NOMES_DIMENSAO;
                $dimensoesGrupos = [];
                foreach($perguntas as $num => $p) {
                    $dimensoesGrupos[$p['dimensao']][] = $num;
                }
                @endphp

                @foreach($dimensoesGrupos as $dim => $nums)
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background-color: #D0AE6D;">{{ $dim }}</div>
                        <h4 class="font-medium text-gray-800 text-sm">{{ $dimensoesNomes[$dim] }}</h4>
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
            </div>
            @endif

        </div>
    </div>

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
        $dimNames = ['','Viabl.','Proj.','Orçam.','Plan.','Fin.','Conf.'];
        $dimFullNames = ['','Viabilidade','Projetos','Orçamento','Planejamento','Financeiro','Conformidade'];
        foreach($dimensoes as $d => $dim) {
            $radarLabels[] = $dimNames[$d];
            $radarFullNames[] = $dimFullNames[$d];
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
