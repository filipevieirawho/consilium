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
                                <input type="text" name="search" placeholder="Buscar por nome, e-mail, nota ou mensagem"
                                    value="{{ request('search') }}"
                                    class="w-full md:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2">

                                <!-- Filters and buttons -->
                                <div class="flex gap-3 items-center flex-wrap">
                                    <!-- Year Select -->
                                    <select name="year"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-4 pr-10 py-2">
                                        <option value="">Todos os anos</option>
                                        @for($year = date('Y'); $year >= 2026; $year--)
                                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>

                                    <!-- Month Select -->
                                    <select name="month"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-4 pr-10 py-2">
                                        <option value="">Todos os meses</option>
                                        <option value="1" {{ request('month') == '1' ? 'selected' : '' }}>Janeiro</option>
                                        <option value="2" {{ request('month') == '2' ? 'selected' : '' }}>Fevereiro
                                        </option>
                                        <option value="3" {{ request('month') == '3' ? 'selected' : '' }}>Março</option>
                                        <option value="4" {{ request('month') == '4' ? 'selected' : '' }}>Abril</option>
                                        <option value="5" {{ request('month') == '5' ? 'selected' : '' }}>Maio</option>
                                        <option value="6" {{ request('month') == '6' ? 'selected' : '' }}>Junho</option>
                                        <option value="7" {{ request('month') == '7' ? 'selected' : '' }}>Julho</option>
                                        <option value="8" {{ request('month') == '8' ? 'selected' : '' }}>Agosto</option>
                                        <option value="9" {{ request('month') == '9' ? 'selected' : '' }}>Setembro
                                        </option>
                                        <option value="10" {{ request('month') == '10' ? 'selected' : '' }}>Outubro
                                        </option>
                                        <option value="11" {{ request('month') == '11' ? 'selected' : '' }}>Novembro
                                        </option>
                                        <option value="12" {{ request('month') == '12' ? 'selected' : '' }}>Dezembro
                                        </option>
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
                            <button type="button" x-data="" @click.prevent="$dispatch('open-modal', 'add-manual-lead')"
                                class="px-4 py-2 text-white font-medium rounded-md shadow-sm transition-colors flex items-center gap-2"
                                style="background-color: #D0AE6D; hover:background-color: #b5955a;">
                                <ion-icon name="add-outline" class="text-xl"></ion-icon> Novo Lead
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
                                        Empresa</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contato</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mensagem</th>
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
                                            {{ $contact->company ?: '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>{{ $contact->email }}</div>
                                            <div class="text-xs">{{ $contact->phone }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"
                                            title="{{ $contact->message }}">
                                            {{ $contact->message }}
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

    <!-- Manual Lead Creation Modal -->
    <x-modal name="add-manual-lead" focusable>
        <div class="px-6 py-6 sm:p-8">
            <!-- Header Area -->
            <div class="flex items-start justify-between mb-6">
                <!-- Title & Icon -->
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full"
                        style="background-color: #fdf8ed;">
                        <ion-icon name="person-add-outline" class="text-2xl" style="color: #D0AE6D;"></ion-icon>
                    </div>
                    <h3 class="text-xl font-semibold leading-6 text-gray-900 text-left" id="modal-title">
                        Novo Lead
                    </h3>
                </div>

                <!-- Close Button (X) -->
                <button type="button" x-on:click="$dispatch('close')"
                    class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none -mt-1 -mr-2 p-2">
                    <ion-icon name="close-outline" class="text-3xl"></ion-icon>
                </button>
            </div>

            <!-- Form -->
            <form id="new-lead-form" method="POST" action="{{ route('contacts.storeManual') }}"
                class="space-y-6 text-left">
                @csrf

                <!-- Nome -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" name="name" id="name" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2"
                        placeholder="Ex: João Silva">
                </div>

                <!-- Empresa -->
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                    <input type="text" name="company" id="company"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2"
                        placeholder="Ex: Nome da Empresa">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" id="email"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2"
                        placeholder="joao@exemplo.com">
                </div>

                <!-- Telefone/WhatsApp -->
                <div x-data="{ 
                    phone: '', 
                    formatPhone() {
                        let x = this.phone.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
                        if (!x[2]) {
                            this.phone = x[1];
                        } else {
                            this.phone = !x[3] ? '(' + x[1] + ') ' + x[2] : '(' + x[1] + ') ' + x[2] + '-' + x[3];
                        }
                    } 
                }">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone / WhatsApp</label>
                    <input type="text" name="phone" id="phone" x-model="phone" @input="formatPhone" maxlength="15"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2"
                        placeholder="(11) 99999-9999">
                </div>

                <!-- Mensagem (Optional) -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Observação Inicial /
                        Detalhes</label>
                    <textarea name="message" id="message" rows="3"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2"
                        placeholder="Como esse lead chegou ou o que ele precisa..."></textarea>
                </div>

                <!-- Opt-in -->
                <div class="flex items-center">
                    <input id="opt_in" name="opt_in" type="checkbox" value="1"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="opt_in" class="ml-2 block text-sm text-gray-900">
                        Aceita receber novidades (Opt-in)
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex justify-end">
                    <button type="submit" form="new-lead-form"
                        class="px-4 py-2 text-white font-medium rounded-md shadow-sm transition-colors inline-flex items-center justify-center"
                        style="background-color: #D0AE6D; hover:background-color: #b5955a;">
                        Salvar Lead
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

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