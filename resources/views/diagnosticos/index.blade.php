<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-700">
                <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Diagnósticos
                </h2>
                <p class="text-[10px] text-gray-400 font-mono uppercase tracking-widest">Controle de aplicações</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Flash -->
            @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Top Bar -->
                    <div class="flex justify-between items-center mb-6 gap-4 flex-wrap">
                        <!-- Search & Filter -->
                        <form method="GET" action="{{ route('diagnosticos.index') }}" class="flex gap-3 flex-wrap flex-grow">
                            <input type="text" name="search" placeholder="Buscar por nome, empresa ou empreendimento..."
                                value="{{ request('search') }}"
                                class="w-full md:w-80 rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D] px-4 py-2 text-sm">
                            <select name="status" onchange="this.form.submit()"
                                class="rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D] pl-3 pr-8 py-2 text-sm">
                                <option value="">Todos os status</option>
                                <option value="em_andamento" {{ request('status') == 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
                                <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                            </select>
                            <button type="submit" class="bg-gray-800 text-white px-3 py-2 rounded-md hover:bg-gray-700 transition-colors flex items-center" title="Buscar">
                                <ion-icon name="search-outline" class="text-base"></ion-icon>
                            </button>
                            @if(request('search') || request('status'))
                            <a href="{{ route('diagnosticos.index') }}" class="text-sm text-gray-500 hover:text-gray-900 self-center">Limpar</a>
                            @endif
                        </form>

                        <!-- Generate link buttons -->
                        <div class="flex gap-3">
                            <button id="btn-abrir-campanha"
                                class="px-4 py-2 bg-white text-gray-700 font-medium rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 transition-colors flex items-center gap-2 text-sm">
                                <ion-icon name="flash-outline" class="text-xl text-yellow-500"></ion-icon> Link de Campanha
                            </button>
                            <button id="btn-gerar-link"
                                class="px-4 py-2 text-white font-medium rounded-md shadow-sm transition-colors flex items-center gap-2 text-sm"
                                style="background-color: #D0AE6D;">
                                <ion-icon name="add-outline" class="text-xl"></ion-icon> Gerar Diagnóstico
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa / Responsável</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empreendimento</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IPM</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($diagnosticos as $d)
                                <tr class="hover:bg-gray-50 cursor-pointer transition-colors"
                                    onclick="window.location='{{ route('diagnosticos.show', $d) }}'">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-xs text-gray-500">{{ $d->created_at->format('d/m/y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $d->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $d->empresa ?: '—' }}</div>
                                        <div class="text-xs text-gray-500">{{ $d->nome ?: '—' }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-700">{{ $d->nome_empreendimento ?: '—' }}</div>
                                        @if($d->cidade)<div class="text-xs text-gray-400">{{ $d->cidade }}</div>@endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($d->ipm !== null)
                                        @php
                                            $faixaColors = [
                                                'red'    => 'text-red-700 border-red-300 bg-red-50',
                                                'yellow' => 'text-yellow-700 border-yellow-300 bg-yellow-50',
                                                'green'  => 'text-green-700 border-green-300 bg-green-50',
                                            ];
                                            $fc = $faixaColors[$d->ipmFaixa()] ?? 'text-gray-500 border-gray-200 bg-gray-50';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg border text-sm font-bold {{ $fc }}">
                                            {{ number_format($d->ipm, 1) }}
                                        </span>
                                        @else
                                        <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($d->status === 'concluido')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border text-green-700 border-green-300 bg-green-50">Concluído</span>
                                        @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border text-yellow-700 border-yellow-300 bg-yellow-50">Em andamento</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($d->contact)
                                        <a href="{{ route('contacts.show', $d->contact) }}" onclick="event.stopPropagation()"
                                            class="text-xs text-[#D0AE6D] hover:underline">{{ $d->contact->name }}</a>
                                        @else
                                        <span class="text-xs text-gray-400">Avulso</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right">
                                        <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                                            <button @click.stop="open = !open" type="button" class="text-gray-400 hover:text-gray-700 transition-colors p-1 rounded-full hover:bg-gray-100 focus:outline-none" title="Opções">
                                                <ion-icon name="ellipsis-horizontal-sharp" class="text-lg block"></ion-icon>
                                            </button>
                                            
                                            <div x-show="open" style="display: none;" x-cloak
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute right-0 mt-1 w-36 bg-white rounded-md shadow-lg border border-gray-100 z-10 py-1">
                                                <button type="button" @click.stop="open = false; openDeleteModal('{{ route('diagnosticos.destroy', $d) }}')"
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                    Excluir
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-400 text-sm">
                                        Nenhum diagnóstico encontrado.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $diagnosticos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Link Modal -->
    <div id="modal-gerar-link" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #fdf8ed;">
                        <ion-icon name="link-outline" style="color: #D0AE6D; font-size: 1.25rem;"></ion-icon>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Gerar Link de Diagnóstico</h3>
                </div>
                <button id="btn-fechar-modal" class="text-gray-400 hover:text-gray-700">
                    <ion-icon name="close-outline" class="text-2xl"></ion-icon>
                </button>
            </div>

            <p class="text-sm text-gray-500 mb-5">
                Gere um link único para o diagnóstico. Opcionalmente, vincule a um lead existente.
            </p>

            <x-custom-combobox 
                id="select-empresa" 
                label="Empresa" 
                placeholder="Selecione a empresa..." 
                icon="business-outline"
                emptyMessage="Nenhuma empresa encontrada."
                helperText="Obrigatório para a escala B2B.">
                @foreach(\App\Models\Empresa::orderBy('nome_fantasia')->get() as $emp)
                    <li class="combo-option cursor-pointer select-none relative py-2.5 pl-4 pr-4 hover:bg-gray-50 text-gray-900" 
                        data-value="{{ $emp->id }}">
                        <span class="block font-medium item-name">{{ $emp->nome_fantasia }}</span>
                    </li>
                @endforeach
            </x-custom-combobox>

            <x-custom-combobox 
                id="select-contact" 
                label="Lead / Contato" 
                optional="true"
                placeholder="Buscar pelo nome..." 
                icon="person-outline"
                emptyMessage="Nenhum lead encontrado."
                onSelect="onContactSelect">
                @foreach($contacts as $c)
                    <li class="combo-option cursor-pointer select-none relative py-2.5 pl-4 pr-4 hover:bg-gray-50 text-gray-900" 
                        data-value="{{ $c->id }}" 
                        data-empresa-id="{{ $c->empresa_id }}">
                        <span class="block font-medium item-name">{{ $c->name }}</span>
                        @if($c->company)
                        <span class="block text-xs text-gray-500 mt-0.5">{{ $c->company }}</span>
                        @endif
                    </li>
                @endforeach
            </x-custom-combobox>

            <x-custom-combobox 
                id="select-questionario" 
                label="Modelo de Questionário" 
                optional="true"
                placeholder="Padrão (18 questões estáticas)" 
                icon="list-outline"
                emptyMessage="Nenhum questionário encontrado.">
                @foreach($questionarios as $q)
                    <li class="combo-option cursor-pointer select-none relative py-2.5 pl-4 pr-4 hover:bg-gray-50 text-gray-900" 
                        data-value="{{ $q->id }}">
                        <span class="block font-medium item-name">{{ $q->titulo }} ({{ $q->questoes_count }} questões)</span>
                    </li>
                @endforeach
            </x-custom-combobox>

            <div id="link-resultado" class="hidden mb-5 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wide">Link gerado:</p>
                <div class="flex items-center gap-2">
                    <input type="text" id="link-gerado" readonly
                        class="block flex-1 text-sm text-gray-800 bg-white border border-gray-200 rounded-lg px-3 py-2">
                    <button id="btn-copiar"
                        class="px-3 py-2 rounded-lg text-white text-sm font-medium"
                        style="background-color: #D0AE6D;">
                        Copiar
                    </button>
                </div>
                <p id="copy-feedback" class="text-xs text-green-600 mt-2 hidden">✓ Link copiado!</p>
            </div>

            <div class="flex gap-3">
                <button id="btn-gerar" class="flex-1 py-3 text-white font-semibold rounded-xl transition-all"
                    style="background-color: #D0AE6D;">
                    Gerar Link
                </button>
                <button id="btn-cancelar" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Link de Campanha -->
    <div id="modal-campanha" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #fdf8ed;">
                        <ion-icon name="flash-outline" style="color: #D0AE6D; font-size: 1.25rem;"></ion-icon>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Link de Campanha</h3>
                </div>
                <button id="btn-fechar-campanha" class="text-gray-400 hover:text-gray-700">
                    <ion-icon name="close-outline" class="text-2xl"></ion-icon>
                </button>
            </div>

            <p class="text-sm text-gray-500 mb-5">
                Gere um link público genérico. Os respondentes preencherão os dados da empresa e de contato na primeira etapa do check-up.
            </p>

            <div class="mb-5">
                <x-custom-combobox 
                    id="select-questionario-campanha" 
                    label="Modelo de Questionário" 
                    optional="true"
                    placeholder="Padrão (18 questões estáticas)" 
                    icon="list-outline"
                    emptyMessage="Nenhum questionário encontrado."
                    helperText="Selecione um questionário específico para esta campanha.">
                    @foreach($questionarios as $q)
                        <li class="combo-option cursor-pointer select-none relative py-2.5 pl-4 pr-4 hover:bg-gray-50 text-gray-900" 
                            data-value="{{ $q->id }}">
                            <span class="block font-medium item-name">{{ $q->titulo }} ({{ $q->questoes_count }} questões)</span>
                        </li>
                    @endforeach
                </x-custom-combobox>
            </div>

            <div id="link-resultado-campanha" class="hidden mb-5 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wide">Link de Campanha:</p>
                <div class="flex items-center gap-2">
                    <input type="text" id="link-gerado-campanha" readonly
                        class="block flex-1 text-sm text-gray-800 bg-white border border-gray-200 rounded-lg px-3 py-2">
                    <button id="btn-copiar-campanha-resultado"
                        class="px-3 py-2 rounded-lg text-white text-sm font-medium"
                        style="background-color: #D0AE6D;">
                        Copiar
                    </button>
                </div>
            </div>

            <button id="btn-gerar-link-campanha-action"
                class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white transition-all focus:outline-none"
                style="background-color: #D0AE6D;">
                Gerar Link de Campanha
            </button>
        </div>
    </div>

    {{-- Delete Diagnostic Confirmation Modal --}}
    <div id="deleteDiagnosticModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeDeleteModal()"></div>
        <div class="relative bg-white rounded-lg shadow-xl p-6 mx-4 max-w-sm w-full">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmar exclusão</h3>
            <p class="text-sm text-gray-600 mb-6">Tem certeza que deseja excluir permanentemente este diagnóstico? Esta ação não pode ser desfeita.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancelar</button>
                <form id="deleteDiagnosticForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Excluir</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Modal Logic para exclusão
        function openDeleteModal(actionUrl) {
            document.getElementById('deleteDiagnosticForm').action = actionUrl;
            const modal = document.getElementById('deleteDiagnosticModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteDiagnosticModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });

    (function () {
        const modal = document.getElementById('modal-gerar-link');
        const btnAbrir = document.getElementById('btn-gerar-link');
        const btnFechar = document.getElementById('btn-fechar-modal');
        const btnCancelar = document.getElementById('btn-cancelar');
        const btnGerar = document.getElementById('btn-gerar');
        const btnCopiar = document.getElementById('btn-copiar');
        const selectContact = document.getElementById('select-contact');
        const selectEmpresa = document.getElementById('select-empresa-modal');
        const selectQuestionario = document.getElementById('select-questionario');
        const linkResultado = document.getElementById('link-resultado');
        const linkGerado = document.getElementById('link-gerado');
        const copyFeedback = document.getElementById('copy-feedback');

        // Define global callback for Contact selection to auto-fill Empresa
        window.onContactSelect = function(val, opt) {
            const empId = opt.getAttribute('data-empresa-id');
            if (empId && empId !== "null" && empId !== "") {
                const selectEmpresa = document.getElementById('select-empresa');
                if (selectEmpresa) {
                    selectEmpresa.value = empId;
                    const empNameSpan = opt.querySelector('.text-xs');
                    
                    // We need to trigger the UI update for the Empresa combobox
                    const empContainer = document.getElementById('combo-container-select-empresa');
                    if (empContainer) {
                        const searchInput = empContainer.querySelector('.combo-search-input');
                        // Find the option in the Empresa combobox
                        const empOptions = empContainer.querySelectorAll('.combo-option');
                        empOptions.forEach(eOpt => {
                            if (eOpt.getAttribute('data-value') === empId) {
                                searchInput.value = eOpt.querySelector('.item-name').textContent.trim();
                            }
                        });
                    }
                }
            }
        };

        function abrirModal() { 
            modal.classList.remove('hidden'); 
            modal.classList.add('flex'); 
        }
        function fecharModal() { 
            modal.classList.add('hidden'); 
            modal.classList.remove('flex'); 
            linkResultado.classList.add('hidden'); 
            
            // Reset modal state
            document.getElementById('select-contact').value = "";
            document.getElementById('combo-search-select-contact').value = "";
            
            document.getElementById('select-empresa').value = "";
            document.getElementById('combo-search-select-empresa').value = "";
            
            document.getElementById('select-questionario').value = "";
            document.getElementById('combo-search-select-questionario').value = "";
            
            // Show all options
            document.querySelectorAll('#modal-gerar-link .combo-option').forEach(opt => opt.style.display = 'block');
            document.querySelectorAll('#modal-gerar-link .combo-empty').forEach(msg => msg.classList.add('hidden'));
            
            btnGerar.disabled = false;
            btnGerar.textContent = 'Gerar Link';
        }

        if (btnAbrir) btnAbrir.addEventListener('click', abrirModal);
        btnFechar.addEventListener('click', fecharModal);
        btnCancelar.addEventListener('click', fecharModal);
        modal.addEventListener('click', e => { if (e.target === modal) fecharModal(); });

        btnGerar.addEventListener('click', function () {
            const empId = document.getElementById('select-empresa').value;
            if (!empId) {
                alert('Por favor, selecione uma Empresa. Esta é uma exigência do novo modelo B2B.');
                return;
            }

            btnGerar.disabled = true;
            btnGerar.textContent = 'Gerando...';

            fetch(@json(route('diagnosticos.generateLink')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ 
                    contact_id: document.getElementById('select-contact').value || null,
                    empresa_id: empId || null,
                    questionario_id: document.getElementById('select-questionario').value || null
                }),
            })
            .then(r => r.json())
            .then(data => {
                linkGerado.value = data.url;
                linkResultado.classList.remove('hidden');
                btnGerar.disabled = false;
                btnGerar.textContent = 'Gerar Novo';
            })
            .catch(() => {
                alert('Erro ao gerar link. Tente novamente.');
                btnGerar.disabled = false;
                btnGerar.textContent = 'Gerar Link';
            });
        });

        btnCopiar.addEventListener('click', function () {
            linkGerado.select();
            document.execCommand('copy');
            copyFeedback.classList.remove('hidden');
            setTimeout(() => copyFeedback.classList.add('hidden'), 2500);
        });

        // ==========================================
        // Modal de Campanha Logic
        // ==========================================
        const modalCampanha = document.getElementById('modal-campanha');
        const btnAbrirCampanha = document.getElementById('btn-abrir-campanha');
        const btnFecharCampanha = document.getElementById('btn-fechar-campanha');
        const btnGerarCampanha = document.getElementById('btn-gerar-link-campanha-action');
        const selectQuestionarioCampanha = document.getElementById('select-questionario-campanha');
        const linkResultadoCampanha = document.getElementById('link-resultado-campanha');
        const linkGeradoCampanha = document.getElementById('link-gerado-campanha');
        const btnCopiarCampanhaResult = document.getElementById('btn-copiar-campanha-resultado');
        const baseUrlCampanha = "{{ url(route('diagnostico.novo')) }}";

        function abrirModalCampanha() {
            modalCampanha.classList.remove('hidden');
            modalCampanha.classList.add('flex');
        }

        function fecharModalCampanha() {
            modalCampanha.classList.add('hidden');
            modalCampanha.classList.remove('flex');
            linkResultadoCampanha.classList.add('hidden');
            selectQuestionarioCampanha.value = "";
            btnGerarCampanha.classList.remove('hidden');
        }

        if (btnAbrirCampanha) btnAbrirCampanha.addEventListener('click', abrirModalCampanha);
        if (btnFecharCampanha) btnFecharCampanha.addEventListener('click', fecharModalCampanha);
        modalCampanha.addEventListener('click', e => { if (e.target === modalCampanha) fecharModalCampanha(); });

        btnGerarCampanha.addEventListener('click', function() {
            const qId = document.getElementById('select-questionario-campanha').value;
            let finalUrl = baseUrlCampanha;
            if (qId) {
                finalUrl += '?q=' + qId;
            }
            
            linkGeradoCampanha.value = finalUrl;
            linkResultadoCampanha.classList.remove('hidden');
            btnGerarCampanha.classList.add('hidden');
        });

        btnCopiarCampanhaResult.addEventListener('click', function() {
            linkGeradoCampanha.select();
            document.execCommand('copy');
            alert('Link de campanha copiado com sucesso!');
        });

    })();
    </script>
    @endpush
</x-app-layout>
