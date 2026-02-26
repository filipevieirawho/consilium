<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <ion-icon name="receipt-outline"></ion-icon> {{ $contact->name }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Voltar para Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Sidebar (Left Col, 1/3 width) -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Dados do Lead</h3>

                            <div class="space-y-4 mb-6">
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Nome</span>
                                    <span class="block text-base text-gray-900">{{ $contact->name }}</span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">E-mail</span>
                                    <a href="mailto:{{ $contact->email }}"
                                        class="block text-base text-[#D0AE6D] hover:text-[#b89555] hover:underline transition-colors">{{ $contact->email }}</a>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Telefone</span>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->phone) }}"
                                        target="_blank"
                                        class="block text-base text-[#D0AE6D] hover:text-[#b89555] hover:underline transition-colors">{{ $contact->phone }}</a>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Opt-in Novidades</span>
                                    <span
                                        class="block text-base {{ $contact->opt_in ? 'text-[#D0AE6D] font-bold' : 'text-gray-500' }}">
                                        {{ $contact->opt_in ? 'Sim' : 'Não' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500 mb-1">Mensagem Recebida</span>
                                    <div class="p-3 bg-gray-50 rounded-md text-sm text-gray-700 border">
                                        {!! nl2br(e(trim($contact->message))) !!}
                                    </div>
                                </div>
                                    <div>
                                        <span class="block text-xs text-gray-400">Enviado em:
                                            {{ $contact->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>

                                <form action="{{ route('contacts.updateDetails', $contact) }}" method="POST"
                                    class="pt-4 border-t border-gray-200">
                                    @csrf
                                    @method('PATCH')

                                    <!-- Status -->
                                    <div class="mb-4">
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status
                                            do
                                            Lead</label>
                                        <select name="status" id="status" onchange="this.form.submit()"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                                            <option value="novo" {{ $contact->status === 'novo' ? 'selected' : '' }}>🔵
                                                Novo
                                            </option>
                                            <option value="contactado" {{ $contact->status === 'contactado' ? 'selected' : '' }}>🟡 Contactado</option>
                                            <option value="ganho" {{ $contact->status === 'ganho' ? 'selected' : '' }}>🟢
                                                Ganho</option>
                                            <option value="perdido" {{ $contact->status === 'perdido' ? 'selected' : '' }}>🔴
                                                Perdido</option>
                                        </select>
                                    </div>

                                    <!-- Owner -->
                                    <div class="mb-4">
                                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Dono
                                            (Responsável)</label>
                                        <select name="user_id" id="user_id" onchange="this.form.submit()"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                                            <option value="">-- Não atribuído --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $contact->user_id == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content (Right Col, 2/3 width) -->
                    <div class="md:col-span-2 space-y-6">

                        <!-- History and Notes Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <div class="flex items-center justify-between border-b pb-2 mb-4 relative" x-data="{ openOptions: false }" @click.outside="openOptions = false">
                                    <h3 class="text-lg font-semibold text-gray-900">Histórico do Lead</h3>
                                    <button @click="openOptions = !openOptions" type="button" class="text-gray-400 hover:text-gray-700 transition-colors p-1 rounded-full hover:bg-gray-200 focus:outline-none flex items-center" title="Opções do Lead">
                                        <ion-icon name="ellipsis-horizontal-sharp" class="text-xl block"></ion-icon>
                                    </button>
                                    <div x-show="openOptions" style="display: none;" x-cloak
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute right-0 mt-2 top-full w-48 bg-white rounded-md shadow-lg border border-gray-100 z-10 py-1">
                                        <button type="button" @click="openOptions = false; openDeleteLeadModal()"
                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            Excluir
                                        </button>
                                    </div>
                                </div>

                                <!-- Note Input Form -->
                                <form action="{{ route('contacts.storeNote', $contact) }}" method="POST" class="mb-6">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea name="note" id="note" rows="3" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D]"
                                            placeholder="Adicione observações sobre reuniões, negociações ou interesses relativas a este lead..."></textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit"
                                            class="bg-[#D0AE6D] text-white font-medium py-2 px-4 rounded-md hover:bg-[#b89555] transition-colors">
                                            Adicionar Anotação
                                        </button>
                                    </div>
                                </form>

                                <!-- Timeline of Notes -->
                                <div class="space-y-4">
                                    @foreach($contact->contactNotes as $note)
                                        <div x-data="{ editing: false, open: false }"
                                            class="bg-gray-50 border {{ $note->is_pinned ? 'border-[#D0AE6D] shadow-sm' : 'border-gray-200' }} rounded-md p-4 relative transition-all">

                                            <!-- Modo Visualização -->
                                            <div x-show="!editing">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div class="text-gray-800 pr-8 w-full">
                                                        @if($note->is_pinned)
                                                            <ion-icon name="pin"
                                                                class="text-[#D0AE6D] mr-1 align-text-bottom text-lg"
                                                                title="Anotação Fixada"></ion-icon>
                                                        @endif
                                                        <span>{!! nl2br(e(trim($note->note))) !!}</span>
                                                    </div>

                                                    <!-- Dropdown Menu de Ações -->
                                                    <div class="relative ml-2 shrink-0">
                                                        <button @click="open = !open" @click.away="open = false"
                                                            class="text-gray-400 hover:text-gray-700 transition-colors p-1 rounded-full hover:bg-gray-200 focus:outline-none">
                                                            <ion-icon name="ellipsis-horizontal-sharp"
                                                                class="text-xl block"></ion-icon>
                                                        </button>

                                                        <div x-show="open" x-cloak
                                                            class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-100 z-10 py-1"
                                                            style="display: none;">

                                                            <button @click="editing = true; open = false"
                                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                                Editar
                                                            </button>

                                                            <form
                                                                action="{{ route('contacts.togglePinNote', [$contact, $note]) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                                    {{ $note->is_pinned ? 'Desafixar esta anotação' : 'Fixar esta anotação' }}
                                                                </button>
                                                            </form>

                                                            <button type="button"
                                                                @click="open = false; openDeleteNoteModal('{{ route('contacts.destroyNote', [$contact, $note]) }}')"
                                                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                                Excluir
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Rodapé da Nota -->
                                                <p class="text-xs text-gray-500">
                                                    @php
                                                        $date = $note->created_at->isToday()
                                                            ? 'Hoje às ' . $note->created_at->format('H:i')
                                                            : ($note->created_at->isYesterday()
                                                                ? 'Ontem às ' . $note->created_at->format('H:i')
                                                                : $note->created_at->format('d/m/Y \à\s H:i'));
                                                    @endphp
                                                    {{ $date }} &middot; <span
                                                        class="font-medium text-gray-700">{{ $note->user->name ?? 'Usuário Desconhecido' }}</span>
                                                    @if($note->created_at->ne($note->updated_at))
                                                        <span class="text-gray-400 italic ml-1">(editado)</span>
                                                    @endif
                                                </p>
                                            </div>

                                            <!-- Modo Edição -->
                                            <div x-show="editing" x-cloak style="display: none;">
                                                <form action="{{ route('contacts.updateNote', [$contact, $note]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <textarea name="note" rows="3" required
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D] mb-3 text-sm">{{ $note->note }}</textarea>
                                                    <div class="flex justify-end gap-3 items-center">
                                                        <button type="button" @click="editing = false"
                                                            class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">Cancelar</button>
                                                        <button type="submit"
                                                            class="bg-[#D0AE6D] text-white text-sm font-medium py-1.5 px-4 rounded-md hover:bg-[#b89555] transition-colors shadow-sm">Salvar
                                                            Alteração</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="bg-gray-50 border rounded-md p-4">
                                        @php
                                            $diffDays = $contact->created_at->diffInDays(now());
                                            if ($contact->created_at->isToday()) {
                                                $dateStr = 'Hoje às ' . $contact->created_at->format('H:i');
                                            } elseif ($contact->created_at->isYesterday()) {
                                                $dateStr = 'Ontem às ' . $contact->created_at->format('H:i');
                                            } elseif ($diffDays < 7) {
                                                $dayName = $contact->created_at->locale('pt_BR')->translatedFormat('l');
                                                $isMasculine = in_array($contact->created_at->dayOfWeekIso, [6, 7]);
                                                $prefix = $isMasculine ? 'Último ' : 'Última ';
                                                $dateStr = $prefix . $dayName . ' às ' . $contact->created_at->format('H:i');
                                            } else {
                                                $dateStr = $contact->created_at->format('d/m/Y \à\s H:i');
                                            }
                                        @endphp
                                        <p class="text-sm font-bold text-[#D0AE6D] mb-1 flex items-center gap-1">
                                            Negócio criado <ion-icon name="checkmark-done-outline"
                                                class="text-lg"></ion-icon>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $dateStr }} &middot; <span
                                                class="font-medium text-gray-700">Sistema</span>
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    {{-- Delete Note Confirmation Modal --}}
    <div id="deleteNoteModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeDeleteNoteModal()"></div>
        <div class="relative bg-white rounded-lg shadow-xl p-6 mx-4 max-w-sm w-full">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmar exclusão</h3>
            <p class="text-sm text-gray-600 mb-6">Tem certeza que deseja excluir esta anotação permanentemente? Esta ação não pode ser desfeita.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteNoteModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancelar</button>
                <form id="deleteNoteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Excluir</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Lead Confirmation Modal --}}
    <div id="deleteLeadModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeDeleteLeadModal()"></div>
        <div class="relative bg-white rounded-lg shadow-xl p-6 mx-4 max-w-sm w-full">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Excluir Lead</h3>
            <p class="text-sm text-gray-600 mb-6">Tem certeza que deseja excluir <strong>{{ $contact->name }}</strong> permanentemente? Todo o histórico de notas e informações serão apagados. Esta ação não pode ser desfeita.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteLeadModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">Cancelar</button>
                <form id="deleteLeadForm" action="{{ route('contacts.destroy', $contact) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">Excluir</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Note Delete Modal
            function openDeleteNoteModal(actionUrl) {
                document.getElementById('deleteNoteForm').action = actionUrl;
                const modal = document.getElementById('deleteNoteModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeDeleteNoteModal() {
                const modal = document.getElementById('deleteNoteModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Lead Delete Modal
            function openDeleteLeadModal() {
                const modal = document.getElementById('deleteLeadModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeDeleteLeadModal() {
                const modal = document.getElementById('deleteLeadModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeDeleteNoteModal();
                    closeDeleteLeadModal();
                }
            });
        </script>
    @endpush
</x-app-layout>