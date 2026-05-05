@php
$totalQuestions = $total ?? 18;
$pct = 10 + (($num / $totalQuestions) * 80);
$opcoes = [
    0 => ['label' => 'Inexistente ou desconhecido', 'desc' => 'Não existe prática estruturada ou a informação não está disponível.', 'colors' => ['bg'=>'#fef2f2','border'=>'#fca5a5','sel'=>'#ef4444','num'=>'#ef4444']],
    1 => ['label' => 'Existe de forma informal',    'desc' => 'A prática ocorre, mas depende de pessoas e não é consistente.',             'colors' => ['bg'=>'#fff7ed','border'=>'#fdba74','sel'=>'#f97316','num'=>'#f97316']],
    2 => ['label' => 'Formalizado, pouco utilizado para decisão', 'desc' => 'Existe processo definido, mas não direciona decisões relevantes de forma consistente.', 'colors' => ['bg'=>'#fefce8','border'=>'#fde047','sel'=>'#ca8a04','num'=>'#ca8a04']],
    3 => ['label' => 'Formalizado e utilizado para decisão', 'desc' => 'A prática é estruturada e utilizada ativamente para decisões de prazo, custo e margem.', 'colors' => ['bg'=>'#f0fdf4','border'=>'#86efac','sel'=>'#16a34a','num'=>'#16a34a']],
];
$respostaValor = $respostaAtual ? (int) $respostaAtual->resposta : null;
$nextUrl = $num < $totalQuestions ? route('diagnostico.pergunta', [$token, $num + 1]) : route('diagnostico.finalizar', $token);
@endphp

<x-diagnosticos.layout :progressPct="(int) $pct" progressLabel="Pergunta {{ $num }} de {{ $totalQuestions }}">
    <!-- Dimension badge -->
    <div class="flex items-center justify-between mb-6">
        <span class="text-xs font-semibold uppercase tracking-wider px-3 py-1 rounded-full text-white" style="background-color: #D0AE6D;">
            {{ $dimensaoNome }}
        </span>
        <span class="text-sm font-medium text-gray-400">{{ $num }}/{{ $totalQuestions }}</span>
    </div>

    <!-- Question -->
    <div class="bg-white sm:rounded-lg shadow-sm border border-gray-100 mb-4" style="padding: 35px;">
        <h2 class="text-lg font-semibold text-gray-900 leading-relaxed mb-8">
            {{ $perguntaAtual['texto'] }}
        </h2>

        <!-- Options -->
        <div id="options-container" class="space-y-3">
            @foreach($opcoes as $valor => $opcao)
            @php $sel = ($respostaValor === $valor); @endphp
            <div id="opt-{{ $valor }}"
                 class="flex items-start gap-4 p-4 rounded-lg border cursor-pointer select-none transition-all duration-200"
                 style="background-color:{{ $sel ? $opcao['colors']['sel'] : $opcao['colors']['bg'] }};border-color:{{ $sel ? $opcao['colors']['sel'] : $opcao['colors']['border'] }};color:{{ $sel ? '#fff' : 'inherit' }};{{ $sel ? 'box-shadow:0 4px 12px rgba(0,0,0,0.15);' : '' }}"
                 data-valor="{{ $valor }}">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center font-bold text-lg"
                     style="background-color:{{ $sel ? 'rgba(255,255,255,0.2)' : '#fff' }};color:{{ $sel ? '#fff' : $opcao['colors']['num'] }};">
                    {{ $valor }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-sm">{{ $opcao['label'] }}</div>
                    <div class="text-xs mt-0.5 opacity-70">{{ $opcao['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Save status -->
        <div id="save-status" class="h-5 mt-4 text-center text-xs">
            @if($respostaAtual)
            <span style="color:#16a34a;">✓ Resposta salva</span>
            @endif
        </div>

        <!-- Navigation -->
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
            @if($num > 1)
            <a href="{{ route('diagnostico.pergunta', [$token, $num - 1]) }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Anterior
            </a>
            @else
            <a href="{{ route('diagnostico.instrucoes', $token) }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Voltar
            </a>
            @endif

            <button id="next-btn"
                class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold rounded-xl transition-all"
                style="background-color:#D0AE6D;{{ is_null($respostaValor) ? 'opacity:0.4;cursor:not-allowed;' : '' }}"
                {{ is_null($respostaValor) ? 'disabled' : '' }}>
                {{ $num < $totalQuestions ? 'Próxima →' : 'Ver Resultado' }}
            </button>
        </div>
    </div>

    {{-- Script inline – does NOT use @push so it renders inside the slot correctly --}}
    <script>
    (function () {
        var saving   = false;
        var answered = {{ is_null($respostaValor) ? 'false' : 'true' }};
        var nextUrl  = '{{ $nextUrl }}';
        var saveUrl  = '{{ route('diagnostico.saveAnswer', $token) }}';
        var csrf     = document.querySelector('meta[name="csrf-token"]').content;

        var optColors = {
            0: { bg: '#fef2f2', border: '#fca5a5', sel: '#ef4444', num: '#ef4444' },
            1: { bg: '#fff7ed', border: '#fdba74', sel: '#f97316', num: '#f97316' },
            2: { bg: '#fefce8', border: '#fde047', sel: '#ca8a04', num: '#ca8a04' },
            3: { bg: '#f0fdf4', border: '#86efac', sel: '#16a34a', num: '#16a34a' },
        };

        function applySelected(val) {
            for (var v = 0; v <= 3; v++) {
                var el  = document.getElementById('opt-' + v);
                if (!el) continue;
                var num = el.querySelector('div');
                var cfg = optColors[v];
                if (v === val) {
                    el.style.backgroundColor = cfg.sel;
                    el.style.borderColor     = cfg.sel;
                    el.style.color           = '#fff';
                    el.style.boxShadow       = '0 4px 12px rgba(0,0,0,0.15)';
                    if (num) { num.style.backgroundColor = 'rgba(255,255,255,0.2)'; num.style.color = '#fff'; }
                } else {
                    el.style.backgroundColor = cfg.bg;
                    el.style.borderColor     = cfg.border;
                    el.style.color           = '';
                    el.style.boxShadow       = '';
                    if (num) { num.style.backgroundColor = '#fff'; num.style.color = cfg.num; }
                }
            }
        }

        function setStatus(state) {
            var el = document.getElementById('save-status');
            if (state === 'saving') el.innerHTML = '<span style="color:#9ca3af">Salvando...</span>';
            if (state === 'saved')  el.innerHTML = '<span style="color:#16a34a">✓ Resposta salva</span>';
            if (state === 'error')  el.innerHTML = '<span style="color:#ef4444">Erro ao salvar. Tente novamente.</span>';
        }

        function setNextEnabled(enabled) {
            var btn = document.getElementById('next-btn');
            btn.disabled         = !enabled;
            btn.style.opacity    = enabled ? '1' : '0.4';
            btn.style.cursor     = enabled ? 'pointer' : 'not-allowed';
        }

        // Apply pre-selection for returning visitors
        var pre = {{ is_null($respostaValor) ? 'null' : (int)$respostaValor }};
        if (pre !== null) applySelected(pre);

        // Attach click handlers to each option div
        document.querySelectorAll('#options-container [data-valor]').forEach(function (el) {
            el.addEventListener('click', function () {
                if (saving) return;
                var val = parseInt(this.dataset.valor);
                applySelected(val);
                setStatus('saving');
                saving = true;
                setNextEnabled(false);

                // Build payload
                var payload = { 
                    resposta: val,
                    num: {{ $num }}
                };
                
                @if(isset($perguntaAtual['questao_id']))
                    payload.questao_id = {{ $perguntaAtual['questao_id'] }};
                @else
                    payload.pergunta = {{ $num }};
                @endif

                fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(payload),
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    saving = false;
                    if (data.ok) {
                        answered = true;
                        if (data.next_url) nextUrl = data.next_url;
                        setStatus('saved');
                        setNextEnabled(true);
                    } else {
                        setStatus('error');
                        setNextEnabled(answered);
                    }
                })
                .catch(function () {
                    saving = false;
                    setStatus('error');
                    setNextEnabled(answered);
                });
            });
        });

        // Next button navigation
        document.getElementById('next-btn').addEventListener('click', function () {
            if (nextUrl) window.location.href = nextUrl;
        });
    })();
    </script>
</x-diagnosticos.layout>

