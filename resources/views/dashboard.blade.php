<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <ion-icon name="receipt-outline"></ion-icon> {{ __('Dashboard de Leads') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Top Actions Bar -->
                    <div class="flex justify-between items-center mb-6 gap-4 flex-wrap"
                        x-data="{ showNewLeadModal: false }">

                        <!-- Search & Filters Form -->
                        <form method="GET" action="{{ route('dashboard') }}" class="flex-grow">
                            <div class="flex flex-wrap gap-4 items-center">
                                
                                <!-- Search -->
                                <input type="text" name="search" placeholder="Buscar por nome, email ou mensagem..."
                                    value="{{ request('search') }}"
                                    class="w-full md:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2">

                                <!-- Filters and buttons -->
                                <div class="flex gap-3 items-center flex-wrap">
                                    <!-- Year Select -->
                                    <select name="year"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2">
                                        <option value="">Todos os anos</option>
                                        @for($year = date('Y'); $year >= 2026; $year--)
                                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>

                                    <!-- Month Select -->
                                    <select name="month"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2">
                                        <option value="">Todos os meses</option>
                                        <option value="1" {{ request('month') == '1' ? 'selected' : '' }}>Janeiro</option>
                                        <option value="2" {{ request('month') == '2' ? 'selected' : '' }}>Fevereiro</option>
                                        <option value="3" {{ request('month') == '3' ? 'selected' : '' }}>Março</option>
                                        <option value="4" {{ request('month') == '4' ? 'selected' : '' }}>Abril</option>
                                        <option value="5" {{ request('month') == '5' ? 'selected' : '' }}>Maio</option>
                                        <option value="6" {{ request('month') == '6' ? 'selected' : '' }}>Junho</option>
                                        <option value="7" {{ request('month') == '7' ? 'selected' : '' }}>Julho</option>
                                        <option value="8" {{ request('month') == '8' ? 'selected' : '' }}>Agosto</option>
                                        <option value="9" {{ request('month') == '9' ? 'selected' : '' }}>Setembro</option>
                                        <option value="10" {{ request('month') == '10' ? 'selected' : '' }}>Outubro</option>
                                        <option value="11" {{ request('month') == '11' ? 'selected' : '' }}>Novembro</option>
                                        <option value="12" {{ request('month') == '12' ? 'selected' : '' }}>Dezembro</option>
                                    </select>

                                    <!-- Search Button -->
                                    <button type="submit"
                                        class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 whitespace-nowrap font-medium transition-colors">
                                        Buscar
                                    </button>

                                    <!-- Clear Filters -->
                                    @if(request('search') || request('year') || request('month'))
                                        <a href="{{ route('dashboard') }}"
                                            class="ml-2 text-sm text-gray-500 hover:text-gray-900 whitespace-nowrap transition-colors">Limpar</a>
                                    @endif
                                </div>
                            </div>
                        </form>

                        <!-- Add Lead Button -->
                        <div class="w-full lg:w-auto flex justify-end flex-shrink-0">
                            <button type="button" @click="showNewLeadModal = true"
                                class="px-4 py-2 text-white font-medium rounded-md shadow-sm transition-colors flex items-center gap-2"
                                style="background-color: #D0AE6D; hover:background-color: #b5955a;">
                                <ion-icon name="add-outline" class="text-xl"></ion-icon> Lead
                            </button>
                        </div>

                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nome</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contato</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mensagem</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Opt-in</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($contacts as $contact)
                                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                                        onclick="window.location='{{ route('contacts.show', $contact) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $contact->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $contact->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>{{ $contact->email }}</div>
                                            <div class="text-xs">{{ $contact->phone }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"
                                            title="{{ $contact->message }}">
                                            {{ $contact->message }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($contact->opt_in)
                                                <span class="font-bold" style="color: #D0AE6D">Sim</span>
                                            @else
                                                <span class="text-gray-400">Não</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" onclick="event.stopPropagation()">
                                            <select data-contact-id="{{ $contact->id }}" onchange="updateStatus(this)"
                                                class="status-select rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500
                                                                                {{ $contact->status === 'novo' ? 'bg-blue-50 text-blue-700' : '' }}
                                                                                {{ $contact->status === 'contactado' ? 'bg-yellow-50 text-yellow-700' : '' }}
                                                                                {{ $contact->status === 'perdido' ? 'bg-red-50 text-red-700' : '' }}
                                                                                {{ $contact->status === 'ganho' ? 'bg-green-50 text-green-700' : '' }}
                                                                            ">
                                                <option value="novo" {{ $contact->status === 'novo' ? 'selected' : '' }}>🔵
                                                    Novo</option>
                                                <option value="contactado" {{ $contact->status === 'contactado' ? 'selected' : '' }}>🟡 Contactado</option>
                                                <option value="perdido" {{ $contact->status === 'perdido' ? 'selected' : '' }}>🔴 Perdido</option>
                                                <option value="ganho" {{ $contact->status === 'ganho' ? 'selected' : '' }}>🟢
                                                    Ganho</option>
                                            </select>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum contato
                                            encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $contacts->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Manual Lead Creation Modal (Teleported to body level in Alpine to avoid stacking context issues, or at least placed at root here) -->
    <!-- Note: x-data was initialized on the wrapper div above, we can just duplicate it or attach it to window. -->
    <template x-teleport="body">
        <div x-data="{ showNewLeadModal: false }" x-show="showNewLeadModal"
            @open-new-lead-modal.window="showNewLeadModal = true" class="relative z-50" aria-labelledby="modal-title"
            role="dialog" aria-modal="true" style="display: none;">

            <!-- Backdrop -->
            <div x-show="showNewLeadModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
            </div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full justify-center p-4 text-center sm:items-center sm:p-0">
                    <!-- Modal Panel -->
                    <div x-show="showNewLeadModal" @click.away="showNewLeadModal = false"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10"
                                    style="background-color: #fdf8ed;">
                                    <ion-icon name="person-add-outline" class="text-xl"
                                        style="color: #D0AE6D;"></ion-icon>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Novo Lead
                                    </h3>
                                    <div class="mt-4">
                                        <form id="new-lead-form" method="POST"
                                            action="{{ route('contacts.storeManual') }}" class="space-y-4">
                                            @csrf

                                            <!-- Nome -->
                                            <div>
                                                <label for="name"
                                                    class="block text-sm font-medium text-gray-700">Nome</label>
                                                <input type="text" name="name" id="name" required
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    placeholder="Ex: João Silva">
                                            </div>

                                            <!-- Email -->
                                            <div>
                                                <label for="email"
                                                    class="block text-sm font-medium text-gray-700">E-mail</label>
                                                <input type="email" name="email" id="email"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    placeholder="joao@exemplo.com">
                                            </div>

                                            <!-- Telefone/WhatsApp -->
                                            <div>
                                                <label for="phone"
                                                    class="block text-sm font-medium text-gray-700">Telefone /
                                                    WhatsApp</label>
                                                <input type="text" name="phone" id="phone"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    placeholder="(11) 99999-9999">
                                            </div>

                                            <!-- Mensagem (Optional) -->
                                            <div>
                                                <label for="message"
                                                    class="block text-sm font-medium text-gray-700">Observação Inicial /
                                                    Detalhes</label>
                                                <textarea name="message" id="message" rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    placeholder="Como esse lead chegou ou o que ele precisa..."></textarea>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" form="new-lead-form"
                                class="inline-flex w-full justify-center rounded-md border border-transparent px-3 py-2 text-sm font-semibold shadow-sm sm:ml-3 sm:w-auto text-white"
                                style="background-color: #D0AE6D; hover:background-color: #b5955a;">
                                Salvar Lead
                            </button>
                            <button type="button" @click="showNewLeadModal = false"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    @push('scripts')
        <script>
            function updateStatus(selectElement) {
                const contactId = selectElement.dataset.contactId;
                const status = selectElement.value;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                // Update colors immediately
                selectElement.className = selectElement.className.replace(/bg-\w+-50|text-\w+-700/g, '');
                const colorMap = {
                    'novo': 'bg-blue-50 text-blue-700',
                    'contactado': 'bg-yellow-50 text-yellow-700',
                    'perdido': 'bg-red-50 text-red-700',
                    'ganho': 'bg-green-50 text-green-700'
                };
                selectElement.classList.add(...colorMap[status].split(' '));

                fetch(`/lead/${contactId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Erro ao atualizar status');
                        return response.json();
                    })
                    .then(data => {
                        // Brief visual feedback
                        selectElement.style.outline = '2px solid #D0AE6D';
                        setTimeout(() => selectElement.style.outline = '', 1000);
                    })
                    .catch(error => {
                        alert('Erro ao atualizar o status. Tente novamente.');
                        location.reload();
                    });
            }
        </script>
    @endpush
</x-app-layout>