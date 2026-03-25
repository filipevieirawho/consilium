<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Modelos de Questionário</h2>
            <a href="{{ route('questionarios.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg"
               style="background-color: #D0AE6D;">
                <ion-icon name="add-outline" class="text-base"></ion-icon>
                Novo Modelo
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <div class="space-y-3">
                @forelse($questionarios as $q)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-xs"
                             style="background-color: #D0AE6D;">
                            {{ substr($q->modelo_id, -4) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-gray-900 truncate">{{ $q->titulo }}</p>
                                @if(!$q->is_active)
                                <span class="text-[10px] font-bold px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded-md uppercase tracking-wide">Inativo</span>
                                @else
                                <span class="text-[10px] font-bold px-1.5 py-0.5 bg-green-100 text-green-700 rounded-md uppercase tracking-wide">Ativo</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400">{{ $q->modelo_id }} · {{ $q->questoes_count }} questão(s)</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('questionarios.show', $q) }}"
                           class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Ver
                        </a>
                        <a href="{{ route('questionarios.edit', $q) }}"
                           class="px-3 py-1.5 text-xs font-medium text-white rounded-lg"
                           style="background-color: #D0AE6D;">
                            Editar
                        </a>
                        <div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                            <button @click.stop="open = !open" class="text-gray-400 hover:text-gray-700 p-1.5 rounded hover:bg-gray-100">
                                <ion-icon name="ellipsis-horizontal-sharp" class="text-base block"></ion-icon>
                            </button>
                            <div x-show="open" style="display:none;" x-cloak x-transition
                                 class="absolute right-0 mt-1 w-36 bg-white rounded-md shadow-lg border border-gray-100 z-10 py-1">
                                <button type="button" @click.stop="open = false; openDeleteModal('{{ route('questionarios.destroy', $q) }}')"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Excluir</button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-16 text-center text-gray-400">
                    <ion-icon name="document-text-outline" class="text-4xl mb-3 block mx-auto"></ion-icon>
                    <p class="text-sm mb-3">Nenhum modelo de questionário criado ainda.</p>
                    <a href="{{ route('questionarios.create') }}" class="text-sm font-medium" style="color: #D0AE6D;">Criar primeiro modelo →</a>
                </div>
                @endforelse
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
