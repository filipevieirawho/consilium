<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <ion-icon name="receipt-outline" class="text-[#D0AE6D] text-2xl"></ion-icon> {{ $contact->name }}
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
                                    <span class="block text-sm font-medium text-gray-500">Empresa</span>
                                    <span class="block text-base text-gray-900">{{ $contact->company ?: '-' }}</span>
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
                                        <span class="block text-xs text-gray-400">Recebido em:
                                            {{ $contact->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>

                                <form action="{{ route('contacts.updateDetails', $contact) }}" method="POST"
                                    class="pt-4 border-t border-gray-200">
                                    @csrf
                                    @method('PATCH')

                                    <!-- Status -->
                                    <div class="mb-4">
                                        <label for="status" class="block text-sm font-medium text-gray-500 mb-1">Status
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
                                        <label for="user_id" class="block text-sm font-medium text-gray-500 mb-1">Dono
                                            (Responsável)</label>
                                        <select name="user_id" id="user_id" onchange="this.form.submit()"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                                            <option value="">-- Não atribuído --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $contact->user_id == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
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
                                <div class="flex items-center justify-between border-b pb-2 mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Histórico do Lead</h3>
                                    
                                    <div class="relative" x-data="{ openOptions: false }" @click.outside="openOptions = false">
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
                                            class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-100 z-10 py-1">
                                            <button type="button" @click="openOptions = false; $dispatch('open-modal', 'confirm-lead-deletion')"
                                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                Excluir
                                            </button>
                                        </div>
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
                                            Adicionar Nota
                                        </button>
                                    </div>
                                </form>



                            </div>
                        </div>

                        <!-- Timeline Section -->
                        <div class="pt-2 relative">
                            <!-- Vertical Line connecting everything -->
                            <div class="absolute left-6 top-3 bottom-0 w-px bg-gray-200" style="margin-left: -1px;"></div>

                            <div class="space-y-6">
                        @foreach($timeline as $item)
                            @php
                                $isNote = class_basename($item) === 'ContactNote';
                                
                                $dateStr = '';
                                $diffDays = $item->created_at->diffInDays(now());
                                if ($item->created_at->isToday()) {
                                    $dateStr = 'Hoje às ' . $item->created_at->format('H:i');
                                } elseif ($item->created_at->isYesterday()) {
                                    $dateStr = 'Ontem às ' . $item->created_at->format('H:i');
                                } elseif ($diffDays < 7) {
                                    $dayName = $item->created_at->locale('pt_BR')->translatedFormat('l');
                                    $isMasculine = in_array($item->created_at->dayOfWeekIso, [6, 7]);
                                    $prefix = $isMasculine ? 'Último ' : 'Última ';
                                    $dateStr = $prefix . $dayName . ' às ' . $item->created_at->format('H:i');
                                } else {
                                    $dateStr = $item->created_at->format('d/m/Y \à\s H:i');
                                }

                                $userName = $item->user->name ?? 'Sistema';
                            @endphp

                            <div class="relative pl-14">
                                <!-- Dot Marker -->
                                <div class="absolute left-4 w-4 h-4 rounded-full border-4 border-white z-10 shadow-sm
                                    {{ $isNote ? 'top-2 md:top-3 bg-[#D0AE6D]' : 'top-1.5 bg-gray-300' }}"></div>

                                @if($isNote)
                                    <!-- Note Card -->
                                    <div x-data="{ editing: false, note: @js($item) }"
                                        class="bg-[#fdf8ed] border border-[#eaddc5] rounded-lg p-5 transition-all relative group shadow-sm {{ $item->is_pinned ? 'ring-2 ring-[#D0AE6D]' : '' }}">
                                        
                                        @if($item->is_pinned)
                                            <div class="absolute -top-3 -right-3 bg-white rounded-full p-1.5 shadow-sm border border-gray-100" title="Fixado no topo">
                                                <ion-icon name="pin" class="text-[#D0AE6D] text-lg block"></ion-icon>
                                            </div>
                                        @endif

                                        <div x-show="!editing">
                                            <div class="flex justify-between items-start group">
                                                <div class="text-sm text-gray-800 whitespace-pre-wrap leading-relaxed pr-8"
                                                    style="word-break: break-word;">{!! nl2br(e(trim($item->note))) !!}</div>

                                                <div class="relative opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0"
                                                    x-data="{ open: false }" @click.outside="open = false">
                                                    <button @click="open = !open" type="button"
                                                        class="text-gray-400 hover:text-gray-600 focus:outline-none p-1 rounded-full hover:bg-[#f5ebd3] transition-colors" title="Opções">
                                                        <ion-icon name="ellipsis-horizontal-sharp" class="text-lg block"></ion-icon>
                                                    </button>

                                                    <div x-show="open" style="display: none;" x-cloak
                                                        x-transition:enter="transition ease-out duration-100"
                                                        x-transition:enter-start="transform opacity-0 scale-95"
                                                        x-transition:enter-end="transform opacity-100 scale-100"
                                                        x-transition:leave="transition ease-in duration-75"
                                                        x-transition:leave-start="transform opacity-100 scale-100"
                                                        x-transition:leave-end="transform opacity-0 scale-95"
                                                        class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-20 overflow-hidden">
                                                        
                                                        <button type="button" @click="editing = true; open = false;"
                                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                            Editar
                                                        </button>

                                                        <form action="{{ route('contacts.togglePinNote', [$contact, $item->id]) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                                {{ $item->is_pinned ? 'Desafixar esta nota' : 'Fixar esta nota' }}
                                                            </button>
                                                        </form>

                                                        <button type="button"
                                                            @click="open = false; openDeleteNoteModal('{{ route('contacts.destroyNote', [$contact, $item->id]) }}')"
                                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                            Excluir
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                                <div class="flex items-center gap-1.5">
                                                    <ion-icon name="document-text-outline" class="text-gray-400 text-sm"></ion-icon>
                                                    <span class="font-medium text-gray-700">{{ $userName }}</span>
                                                </div>
                                                <span class="text-gray-500">{{ $dateStr }}</span>
                                                @if($item->created_at->ne($item->updated_at))
                                                    <span class="italic text-gray-400">&middot; editado</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div x-show="editing" x-cloak style="display: none;">
                                            <form action="{{ route('contacts.updateNote', [$contact, $item->id]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <textarea name="note" rows="3" required
                                                    class="w-full rounded-md border-[#eaddc5] shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D] mb-3 text-sm bg-white"
                                                    x-model="note.note"></textarea>
                                                <div class="flex justify-end gap-3 items-center">
                                                    <button type="button" @click="editing = false"
                                                        class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">Cancelar</button>
                                                    <button type="submit"
                                                        class="bg-[#D0AE6D] text-white text-sm font-medium py-1.5 px-4 rounded-md hover:bg-[#b89555] transition-colors shadow-sm">Salvar Alteração</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <!-- Activity Item -->
                                    <div class="pt-1 pb-2 flex flex-col md:flex-row md:items-center gap-1 md:gap-3 text-sm">
                                        <div class="text-gray-800 font-medium">
                                            @if($item->type === 'status_change')
                                                @if(is_null($item->old_value))
                                                    Status inicial definido como <strong class="text-[#D0AE6D]">{{ ucfirst($item->new_value) }}</strong>
                                                @else
                                                    Status alterado de <span class="text-gray-500">{{ ucfirst($item->old_value) }}</span> 
                                                    <ion-icon name="arrow-forward-outline" class="align-middle text-gray-400 mx-0.5"></ion-icon> 
                                                    <strong class="text-[#D0AE6D]">{{ ucfirst($item->new_value) }}</strong>
                                                @endif
                                            @elseif($item->type === 'owner_change')
                                                Proprietário: <span class="text-gray-500">{{ \App\Models\User::find($item->old_value)->name ?? 'Não atribuído' }}</span> 
                                                <ion-icon name="arrow-forward-outline" class="align-middle text-gray-400 mx-0.5"></ion-icon> 
                                                <strong>{{ \App\Models\User::find($item->new_value)->name ?? 'Não atribuído' }}</strong>
                                            @elseif($item->type === 'lead_created')
                                                <span class="flex items-center gap-1.5 text-gray-600"><ion-icon name="flag-outline" class="text-gray-400 text-lg flex-shrink-0"></ion-icon> Início do Histórico</span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center gap-1.5 text-xs text-gray-400">
                                            <span class="hidden md:inline">&middot;</span>
                                            <span>{{ $userName }}</span>
                                            <span>&middot;</span>
                                            <span class="whitespace-nowrap">{{ $dateStr }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        

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
    <x-modal name="confirm-lead-deletion" focusable>
        <form method="POST" action="{{ route('contacts.destroy', $contact) }}" class="p-6 text-left">
            @csrf
            @method('DELETE')

            <h2 class="text-lg font-medium text-gray-900">
                Excluir Lead
            </h2>

            <p class="mt-1 text-sm text-gray-600 mb-6">
                Tem certeza que deseja excluir <strong>{{ $contact->name }}</strong> permanentemente? Todo o histórico de notas e informações serão apagados. Esta ação não pode ser desfeita.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium">
                    Cancelar
                </x-secondary-button>

                <x-danger-button class="px-4 py-2 text-sm font-medium">
                    Excluir
                </x-danger-button>
            </div>
        </form>
    </x-modal>

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

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeDeleteNoteModal();
                }
            });
        </script>
    @endpush
</x-app-layout>