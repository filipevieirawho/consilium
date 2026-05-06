<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 h-10">
            <div class="flex items-center justify-center w-8 h-8 text-[#D0AE6D]">
                <ion-icon name="business-outline" class="text-2xl"></ion-icon>
            </div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Empresas
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                {{ session('success') }}
            </div>
            @endif

            {{-- Search & Table Card --}}
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <!-- Search & Filter -->
                    <div class="flex justify-between items-center mb-6 gap-4 flex-wrap">
                        <form method="GET" action="{{ route('empresas.index') }}" class="flex gap-3 flex-wrap flex-grow">
                            <input type="text" name="q" placeholder="Buscar empresa, CNPJ..."
                                value="{{ request('q') }}"
                                class="w-full md:w-80 rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D] px-4 py-2 text-sm">
                            <select name="segmento" onchange="this.form.submit()"
                                class="rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D] pl-3 pr-8 py-2 text-sm">
                                <option value="">Todos os segmentos</option>
                                @foreach($segmentos as $seg)
                                    <option value="{{ $seg }}" {{ request('segmento') == $seg ? 'selected' : '' }}>{{ $seg }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-gray-800 text-white px-3 py-2 rounded-md hover:bg-gray-700 transition-colors flex items-center" title="Buscar">
                                <ion-icon name="search-outline" class="text-base"></ion-icon>
                            </button>
                            @if(request('q') || request('segmento'))
                            <a href="{{ route('empresas.index') }}" class="text-sm text-gray-500 hover:text-gray-900 self-center">Limpar</a>
                            @endif
                        </form>

                        <div class="flex gap-3">
                            <a href="{{ route('empresas.create') }}"
                               class="px-4 py-2 text-white font-medium rounded-md shadow-sm transition-colors flex items-center gap-2 text-sm"
                               style="background-color: #D0AE6D;">
                                <ion-icon name="add-outline" class="text-xl"></ion-icon> Nova Empresa
                            </a>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto pb-24 -mb-24">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CNPJ</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Segmento</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Leads</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnósticos</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($empresas as $empresa)
                                <tr class="hover:bg-gray-50 cursor-pointer transition-colors" onclick="window.location='{{ route('empresas.show', $empresa) }}'">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $empresa->nome_fantasia }}</div>
                                        @if($empresa->razao_social && $empresa->razao_social !== $empresa->nome_fantasia)
                                            <div class="text-xs text-gray-400">{{ $empresa->razao_social }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $empresa->cnpj ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', str_pad($empresa->cnpj, 14, '0', STR_PAD_LEFT)) : '—' }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $empresa->segmento ?? '—' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-700">{{ $empresa->contacts_count }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-700">{{ $empresa->diagnosticos_count }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right" onclick="event.stopPropagation()">
                                        <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                                            <button @click.stop="open = !open" type="button" class="text-gray-400 hover:text-gray-700 transition-colors p-1 rounded-full hover:bg-gray-100 focus:outline-none" title="Opções">
                                                <ion-icon name="ellipsis-horizontal-sharp" class="text-lg block"></ion-icon>
                                            </button>
                                            <div x-show="open" style="display:none;" x-cloak
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute right-0 top-full mt-1 w-36 bg-white rounded-md shadow-lg border border-gray-100 z-50 py-1">
                                                <a href="{{ route('empresas.edit', $empresa) }}"
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Editar</a>
                                                <button type="button" @click.stop="open = false; openDeleteEmpresaModal('{{ route('empresas.destroy', $empresa) }}')"
                                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Excluir</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">
                                        <ion-icon name="business-outline" class="text-4xl mb-3 block mx-auto"></ion-icon>
                                        <p class="text-sm">Nenhuma empresa cadastrada ainda.</p>
                                        <a href="{{ route('empresas.create') }}" class="mt-3 inline-block text-sm font-medium" style="color: #D0AE6D;">Cadastrar primeira empresa →</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($empresas->hasPages())
                    <div class="mt-4">
                        {{ $empresas->links() }}
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteEmpresaModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeDeleteEmpresaModal()"></div>
        <div class="relative bg-white rounded-lg shadow-xl p-6 mx-4 max-w-sm w-full">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmar exclusão</h3>
            <p class="text-sm text-gray-600 mb-6">Tem certeza que deseja excluir esta empresa? Esta ação não pode ser desfeita.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteEmpresaModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancelar</button>
                <form id="deleteEmpresaForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Excluir</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openDeleteEmpresaModal(url) {
            document.getElementById('deleteEmpresaForm').action = url;
            const m = document.getElementById('deleteEmpresaModal');
            m.classList.remove('hidden'); m.classList.add('flex');
        }
        function closeDeleteEmpresaModal() {
            const m = document.getElementById('deleteEmpresaModal');
            m.classList.add('hidden'); m.classList.remove('flex');
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDeleteEmpresaModal(); });
    </script>
    @endpush
</x-app-layout>
