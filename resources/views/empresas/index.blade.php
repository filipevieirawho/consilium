<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Empresas</h2>
            <a href="{{ route('empresas.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition"
               style="background-color: #D0AE6D;">
                <ion-icon name="add-outline" class="text-base"></ion-icon>
                Nova Empresa
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

            {{-- Search --}}
            <form method="GET" class="mb-5 flex gap-3">
                <div class="relative flex-1 max-w-sm">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Buscar empresa, CNPJ..."
                           class="block w-full pl-10 pr-4 py-2 text-sm rounded-lg border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <ion-icon name="search-outline" class="text-gray-400"></ion-icon>
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 text-sm text-white rounded-lg" style="background-color: #D0AE6D;">Buscar</button>
                @if(request('q'))
                    <a href="{{ route('empresas.index') }}" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Limpar</a>
                @endif
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Empresa</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">CNPJ</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Segmento</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Leads</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Diagnósticos</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($empresas as $empresa)
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('empresas.show', $empresa) }}'">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-900">{{ $empresa->nome_fantasia }}</div>
                                @if($empresa->razao_social && $empresa->razao_social !== $empresa->nome_fantasia)
                                    <div class="text-xs text-gray-400">{{ $empresa->razao_social }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-600">
                                {{ $empresa->cnpj ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', str_pad($empresa->cnpj, 14, '0', STR_PAD_LEFT)) : '—' }}
                            </td>
                            <td class="px-5 py-4 text-gray-600">{{ $empresa->segmento ?? '—' }}</td>
                            <td class="px-5 py-4 text-center font-medium text-gray-700">{{ $empresa->contacts_count }}</td>
                            <td class="px-5 py-4 text-center font-medium text-gray-700">{{ $empresa->diagnosticos_count }}</td>
                            <td class="px-5 py-4 text-right" onclick="event.stopPropagation()">
                                <div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                                    <button @click.stop="open = !open" class="text-gray-400 hover:text-gray-700 p-1 rounded-full hover:bg-gray-100">
                                        <ion-icon name="ellipsis-horizontal-sharp" class="text-lg block"></ion-icon>
                                    </button>
                                    <div x-show="open" style="display:none;" x-cloak x-transition
                                         class="absolute right-0 mt-1 w-36 bg-white rounded-md shadow-lg border border-gray-100 z-10 py-1">
                                        <a href="{{ route('empresas.edit', $empresa) }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Editar</a>
                                        <button type="button" @click.stop="open = false; openDeleteEmpresaModal('{{ route('empresas.destroy', $empresa) }}')"
                                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Excluir</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-gray-400">
                                <ion-icon name="business-outline" class="text-4xl mb-3 block mx-auto"></ion-icon>
                                <p class="text-sm">Nenhuma empresa cadastrada ainda.</p>
                                <a href="{{ route('empresas.create') }}" class="mt-3 inline-block text-sm font-medium" style="color: #D0AE6D;">Cadastrar primeira empresa →</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($empresas->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">{{ $empresas->links() }}</div>
                @endif
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
