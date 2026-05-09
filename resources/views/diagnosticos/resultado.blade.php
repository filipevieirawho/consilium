@php
$ipm = $resultado['ipm'];
$faixa = $resultado['faixa'];
$texto = $resultado['texto'];
$dimensoes = $resultado['dimensoes'];
$dimensoesFracas = $resultado['dimensoes_fracas'];

$faixaConfig = [
    'red'    => ['bg' => '#fef2f2', 'border' => '#ef4444', 'text' => '#dc2626', 'label' => 'Previsibilidade Comprometida', 'icon' => 'alert-circle-outline'],
    'yellow' => ['bg' => '#fffbeb', 'border' => '#f59e0b', 'text' => '#d97706', 'label' => 'Previsibilidade Instável', 'icon' => 'warning-outline'],
    'green'  => ['bg' => '#f0fdf4', 'border' => '#22c55e', 'text' => '#16a34a', 'label' => 'Previsibilidade Consistente', 'icon' => 'checkmark-circle-outline'],
];
$cfg = $faixaConfig[$faixa];
@endphp

<x-diagnosticos.layout :progressPct="100" progressLabel="Resultado">
    <style>
        @page {
            margin: 1.5cm;
        }
        .print-only, .print-footer {
            display: none !important;
        }
        @media print {
            .print-only, .print-footer {
                display: block !important;
            }
            .print-header {
                border-bottom: 2px solid #D0AE6D !important;
                padding-bottom: 1rem !important;
                margin-bottom: 1.5rem !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            /* Compact and normalized sections for PDF */
            .bg-white, .border-2, .dimension-block { 
                padding: 1.5rem !important; 
                margin-bottom: 1rem !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background-color: white !important;
            }
            .grid { gap: 1rem !important; }
            dl.grid { 
                gap: 0.5rem 1rem !important; 
                padding: 0 !important;
                margin: 0 !important;
            }
            h3 { margin-bottom: 0.5rem !important; }
            
            .print-footer {
                margin-top: 2rem;
                padding-top: 1rem;
                border-top: 1px solid #eee;
                text-align: justify;
                font-size: 8px;
                line-height: 1.5;
                color: #9ca3af;
            }
            /* Basic resets */
            header, nav, footer, .no-print, button, a, #vincular-status, .custom-combobox-container {
                display: none !important;
            }
            
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Remove gray backgrounds globally for print */
            .bg-gray-50, .bg-gray-100, .bg-gray-50\/50, .py-12, main { 
                background-color: white !important; 
            }

            .py-12 { padding: 0 !important; }
            .max-w-2xl { max-width: 100% !important; padding: 0 !important; }
            
            /* Specific Grid adjustment for PDF */
            .grid {
                display: grid !important;
                gap: 1.5rem !important;
            }
            
            /* IPM and Radar occupy 1 col each (side by side) */
            .grid-cols-1.md\:grid-cols-2 {
                grid-template-columns: 1fr 1fr !important;
            }

            /* Ensure boxes look good */
            .bg-white { background: white !important; border: 1px solid #eee !important; }
            .shadow-sm, .shadow-md { box-shadow: none !important; }
            
            /* Radar Chart sizing */
            canvas {
                max-width: 100% !important;
                height: auto !important;
                margin: 0 auto;
            }

            /* Fix colors for boxes */
            .bg-red-50 { background-color: #fef2f2 !important; }
            .bg-yellow-50 { background-color: #fffbeb !important; }
            .bg-green-50 { background-color: #f0fdf4 !important; }
            
            /* Avoid page breaks inside sections */
            .bg-white, .questao-row, .mb-6, .mb-8, .dimension-block, .dimension-row {
                page-break-inside: avoid !important;
            }
        }
    </style>

    <!-- Print Header -->
    <div class="print-only print-header pt-10">
        <div class="flex justify-between items-start mb-4">
            <img src="{{ asset('assets/images/logo-header-print.png') }}" alt="Consilium" class="h-10 w-auto">
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-300">{{ $diagnostico->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <h1 class="text-xl font-black text-gray-900 tracking-tight">
            {{ $diagnostico->questionario ? $diagnostico->questionario->titulo : 'Check-up de Consistência da Margem' }}
        </h1>
    </div>

    <div class="flex justify-end mb-4 no-print">
        <button onclick="window.print()"
            class="text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 px-4 py-2 rounded-lg text-white transition-all transform active:scale-95"
            style="background-color: #111827;">
            <ion-icon name="document-text-outline" class="text-sm"></ion-icon> Baixar PDF
        </button>
    </div>

    <!-- Enterprise data summary -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6 mb-8">
        <h3 class="font-semibold text-gray-700 mb-4 text-xs uppercase tracking-wide flex items-center gap-2">
            <ion-icon name="business-outline" class="text-gold-500"></ion-icon>
            Dados do diagnóstico
        </h3>
        <dl class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 text-sm">
            <div><dt class="text-gray-400 text-[10px] uppercase">Respondente</dt><dd class="font-medium text-gray-800">{{ $diagnostico->nome ?: '—' }}</dd></div>
            <div><dt class="text-gray-400 text-[10px] uppercase">Empresa</dt><dd class="font-medium text-gray-800">{{ $diagnostico->empresa ?: '—' }}</dd></div>
            <div><dt class="text-gray-400 text-[10px] uppercase">Empreendimento</dt><dd class="font-medium text-gray-800">{{ $diagnostico->nome_empreendimento ?: '—' }}</dd></div>
            @if($diagnostico->cidade)<div><dt class="text-gray-400 text-[10px] uppercase">Cidade</dt><dd class="font-medium text-gray-800">{{ $diagnostico->cidade }}</dd></div>@endif
            @if($diagnostico->estagio_obra !== null)<div><dt class="text-gray-400 text-[10px] uppercase">Estágio da obra</dt><dd class="font-medium text-gray-800">{{ $diagnostico->estagio_obra }}%</dd></div>@endif
            <div><dt class="text-gray-400 text-[10px] uppercase">Data</dt><dd class="font-medium text-gray-800">{{ $diagnostico->updated_at->format('d/m/Y') }}</dd></div>
        </dl>
    </div>

    <!-- Top row: IPM & Radar Chart -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- IPM Card -->
        <div class="sm:rounded-lg border-2 p-8 text-center flex flex-col items-center justify-center h-full"
             style="background-color: {{ $cfg['bg'] }}; border-color: {{ $cfg['border'] }};">
            <h3 class="text-xs font-bold uppercase tracking-widest mb-4 w-full" style="color: {{ $cfg['text'] }}; opacity: 0.8;">Previsibilidade de Margem</h3>
            <div class="flex justify-center mb-4">
                <div class="w-20 h-20 rounded-full border flex items-center justify-center"
                     style="border-color: {{ $cfg['border'] }}; background: white;">
                    <ion-icon name="{{ $cfg['icon'] }}" style="font-size: 2.5rem; color: {{ $cfg['text'] }};"></ion-icon>
                </div>
            </div>
            <div class="text-6xl font-extrabold mb-1" style="color: {{ $cfg['text'] }};">{{ $ipm }}</div>
            <div class="text-lg font-bold uppercase tracking-widest mb-2" style="color: {{ $cfg['text'] }};">IPM</div>
            <div class="text-base font-semibold" style="color: {{ $cfg['text'] }};">{{ $cfg['label'] }}</div>
        </div>

        <!-- Radar Chart Card -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-8 flex flex-col items-center justify-center h-full">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-6 text-center w-full">Desempenho por Dimensão</h3>
            <div class="w-full relative flex-1 flex items-center justify-center" style="max-height: 250px; aspect-ratio: 1;">
                <canvas id="radarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Interpretive text -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <ion-icon name="reader-outline" style="color: #D0AE6D;"></ion-icon>
            Análise do resultado
        </h3>
        <p class="text-gray-700 leading-relaxed text-sm">{{ $texto }}</p>
    </div>

    <!-- Dimension scores -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
            <ion-icon name="bar-chart-outline" style="color: #D0AE6D;"></ion-icon>
            Pontuação por dimensão
        </h3>

        <div class="space-y-4">
            @foreach($dimensoes as $dim)
            <div class="dimension-row">
                <div class="flex justify-between items-center mb-1">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-800">{{ $dim['nome'] }}</span>
                        @if($dim['fraca'])
                        <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-md bg-red-100 text-red-600">Atenção</span>
                        @endif
                    </div>
                    <span class="text-sm font-bold" style="color: {{ $dim['fraca'] ? '#ef4444' : ($dim['score'] >= 70 ? '#22c55e' : '#f59e0b') }};">{{ $dim['score'] }}/100</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full transition-all duration-700"
                         style="width: {{ $dim['score'] }}%; background-color: {{ $dim['fraca'] ? '#ef4444' : ($dim['score'] >= 70 ? '#22c55e' : '#f59e0b') }};"></div>
                </div>
                <div class="text-xs text-gray-400 mt-0.5">Peso: {{ round($dim['peso'] * 100) }}%</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Weak dimensions callout -->
    @if(!empty($dimensoesFracas))
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <ion-icon name="alert-circle-outline" class="text-lg" style="color: #ef4444;"></ion-icon>
            Dimensões com maior fragilidade
        </h3>
        <div class="bg-red-50 border border-red-100 rounded-xl p-5 mb-6">
            <ul class="space-y-2">
                @foreach($dimensoesFracas as $fraca)
                <li class="flex items-center gap-2 text-sm text-red-700 font-semibold">
                    <ion-icon name="chevron-forward-outline"></ion-icon>
                    {{ $fraca }}
                </li>
                @endforeach
            </ul>
        </div>
        <div class="pt-5 border-t border-gray-100">
            <p class="text-sm text-gray-500 italic leading-relaxed">
                "Este resultado representa um retrato do momento atual do empreendimento. Assim como um exame, sua validade está associada ao momento em que foi realizado. Recomenda-se sua reaplicação periódica ou em marcos relevantes da obra."
            </p>
        </div>
    </div>
    @endif

    <!-- Connection phrase moved up or kept near bottom? User said: "Dimensões com maior fragilidade + connection phrase" -->
    <!-- Commercial trigger -->
    <div class="bg-white overflow-hidden sm:rounded-lg border-2 p-6 mb-8" style="border-color: #D0AE6D;">
        <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
            <ion-icon name="analytics-outline" style="color: #D0AE6D;"></ion-icon>
            Próximo passo
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            Aprofundar a análise dessas fragilidades permite identificar causas e definir ações concretas para proteção da margem.
        </p>
        <a href="https://consilium.eng.br/contato" target="_blank"
            class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold rounded-xl transition-all hover:shadow-md text-sm"
            style="background-color: #D0AE6D;">
            Falar com um especialista
            <ion-icon name="arrow-forward-outline"></ion-icon>
        </a>
    </div>

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
            $shortName = mb_substr($fullName, 0, 5) . '.';
            if (mb_strlen($fullName) <= 6) $shortName = $fullName;

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
                maintainAspectRatio: false,
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

    <!-- Print Disclaimer Footer -->
    <div class="print-footer">
        <p>
            Os dados cadastrados nesta plataforma são de caráter estritamente confidencial e de uso exclusivo dos usuários autorizados. A integridade e a privacidade dessas informações são de suma importância para a Consilium Engenharia & Consultoria. O compartilhamento indevido de dados, relatórios ou credenciais de acesso pode expor informações sensíveis a riscos significativos, podendo resultar em violações de privacidade e em responsabilidades legais para o usuário envolvido. Sua atenção e cooperação são essenciais para garantir a segurança e a integridade das informações aqui armazenadas.
        </p>
    </div>
</x-diagnosticos.layout>
