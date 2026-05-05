<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <ion-icon name="file-tray-full-outline" class="text-[#D0AE6D] text-2xl"></ion-icon>
            Modelos de Questionários
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Top Actions Bar -->
                    <div class="flex justify-between items-center mb-6 gap-4 flex-wrap">
                        <!-- Search -->
                        <form method="GET" action="{{ route('questionarios.index') }}" class="flex gap-3 flex-wrap flex-grow">
                            <input type="text" name="search" placeholder="Buscar por título ou código..."
                                value="{{ request('search') }}"
                                class="w-full md:w-80 rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D] px-4 py-2 text-sm">
                            <button type="submit" class="bg-gray-800 text-white px-3 py-2 rounded-md hover:bg-gray-700 transition-colors flex items-center" title="Buscar">
                                <ion-icon name="search-outline" class="text-base"></ion-icon>
                            </button>
                            @if(request('search'))
                            <a href="{{ route('questionarios.index') }}" class="text-sm text-gray-500 hover:text-gray-900 self-center">Limpar</a>
                            @endif
                        </form>

                        <!-- Action buttons -->
                        <div class="flex gap-3">
                            <a href="{{ route('questionarios.create') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-md shadow-sm transition-colors"
                               style="background-color: #D0AE6D; hover:background-color: #b5955a;">
                                <ion-icon name="add-outline" class="text-xl"></ion-icon>
                                Novo Modelo
                            </a>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cód. Modelo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Questões</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($questionarios as $q)
                                <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('questionarios.show', $q) }}'">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                        {{ $q->modelo_id }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $q->titulo }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $q->questoes_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($q->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Inativo
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
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
                                                <a href="{{ route('questionarios.edit', $q) }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                    Editar
                                                </a>
                                                <button type="button" @click.stop="open = false; openDeleteModal('{{ route('questionarios.destroy', $q) }}')"
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                    Excluir
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 text-sm">
                                        <ion-icon name="document-text-outline" class="text-4xl mb-3 block mx-auto text-gray-400"></ion-icon>
                                        <p class="mb-3">Nenhum modelo de questionário criado ainda.</p>
                                        <a href="{{ route('questionarios.create') }}" class="font-medium" style="color: #D0AE6D;">Criar primeiro modelo →</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $questionarios->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteQuestionarioModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeDeleteModal()"></div>
        <div class="relative bg-white rounded-lg shadow-xl p-6 mx-4 max-w-sm w-full">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmar exclusão</h3>
            <p class="text-sm text-gray-600 mb-6">Todas as questões deste questionário serão excluídas. Esta ação não pode ser desfeita.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancelar</button>
                <form id="deleteQuestionarioForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Excluir</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openDeleteModal(url) {
            document.getElementById('deleteQuestionarioForm').action = url;
            const m = document.getElementById('deleteQuestionarioModal');
            m.classList.remove('hidden'); m.classList.add('flex');
        }
        function closeDeleteModal() {
            const m = document.getElementById('deleteQuestionarioModal');
            m.classList.add('hidden'); m.classList.remove('flex');
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDeleteModal(); });
    </script>
    @endpush
</x-app-layout>
