<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 h-10">
            <div class="flex items-center justify-center w-8 h-8 text-gold">
                <ion-icon name="receipt-outline" class="text-2xl"></ion-icon>
            </div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard de Leads') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
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
                                    class="w-full md:w-96 rounded-md border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2 text-sm">

                                <!-- Filters and buttons -->
                                <div class="flex gap-3 items-center flex-wrap">
                                    <!-- Year Select -->
                                    <select name="year" onchange="this.form.submit()"
                                        class="w-36 rounded-md border-gray-300 shadow-sm focus:border-gold focus:ring-gold pl-3 pr-8 py-2 text-sm">
                                        <option value="">Todos os anos</option>
                                        @for($year = date('Y'); $year >= 2026; $year--)
                                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>

                                    <!-- Month Select -->
                                    <select name="month" onchange="this.form.submit()"
                                        class="w-36 rounded-md border-gray-300 shadow-sm focus:border-gold focus:ring-gold pl-3 pr-8 py-2 text-sm">
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

                                    <!-- Status Select -->
                                    <select name="status" onchange="this.form.submit()"
                                        class="w-36 rounded-md border-gray-300 shadow-sm focus:border-gold focus:ring-gold pl-3 pr-8 py-2 text-sm">
                                        <option value="">Todos os status</option>
                                        <option value="Cliente Potencial" {{ request('status') == 'Cliente Potencial' ? 'selected' : '' }}>Cliente Potencial</option>
                                        <option value="Contactado" {{ request('status') == 'Contactado' ? 'selected' : '' }}>Contactado</option>
                                        <option value="Proposta Enviada" {{ request('status') == 'Proposta Enviada' ? 'selected' : '' }}>Proposta Enviada</option>
                                        <option value="Negociação" {{ request('status') == 'Negociação' ? 'selected' : '' }}>Negociação</option>
                                        <option value="Stand By" {{ request('status') == 'Stand By' ? 'selected' : '' }}>
                                            Stand By</option>
                                    </select>

                                    <!-- Search Button -->
                                    <button type="submit"
                                        class="bg-gray-800 text-white px-3 py-2 rounded-md hover:bg-gray-700 font-medium transition-colors flex items-center justify-center flex-shrink-0"
                                        title="Buscar">
                                        <ion-icon name="search-outline" class="text-base"></ion-icon>
                                    </button>

                                    <!-- Clear Filters -->
                                    @if(request('search') || request('year') || request('month') || request('status'))
                                        <a href="{{ route('dashboard') }}"
                                            class="ml-2 text-sm text-gray-500 hover:text-gray-900 whitespace-nowrap transition-colors">Limpar</a>
                                    @endif
                                </div>
                            </div>
                        </form>

                        <!-- Add Lead Button -->
                        <div class="w-full lg:w-auto flex justify-end flex-shrink-0">
                            <button type="button" x-data="" @click.prevent="$dispatch('open-modal', 'add-manual-lead')"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-md shadow-sm transition-colors bg-gold">
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
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                            <div class="text-xs">{{ $contact->created_at->format('d/m/y') }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">
                                                {{ $contact->created_at->format('H:i') }}
                                            </div>
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $statusColors = [
                                                    'Cliente Potencial' => 'text-[#2892D7] border-[#2892D7] bg-[#2892D7]/05',
                                                    'Contactado' => 'text-[#00c49a] border-[#00c49a] bg-[#00c49a]/05',
                                                    'Proposta Enviada' => 'text-gold border-gold bg-gold-light',
                                                    'Negociação' => 'text-gold border-gold bg-gold-light',
                                                    'Stand By' => 'text-[#6b7280] border-[#6b7280] bg-[#6b7280]/05',
                                                ];
                                                $statusClass = $statusColors[$contact->status] ?? 'text-gray-500 border-gray-300 bg-gray-50';
                                            @endphp
                                            <div
                                                class="inline-flex items-center justify-center px-2 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase border {{ $statusClass }}">
                                                {{ $contact->status }}
                                            </div>
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
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-gold-light">
                        <ion-icon name="receipt-outline" class="text-2xl text-gold"></ion-icon>
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
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gold focus:ring-gold sm:text-sm px-4 py-2"
                        placeholder="Ex: João Silva">
                </div>

                <!-- Empresa -->
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                    <input type="text" name="company" id="company"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gold focus:ring-gold sm:text-sm px-4 py-2"
                        placeholder="Ex: Nome da Empresa">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" id="email"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gold focus:ring-gold sm:text-sm px-4 py-2"
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
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gold focus:ring-gold sm:text-sm px-4 py-2"
                        placeholder="(11) 99999-9999">
                </div>

                <!-- Mensagem (Optional) -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Observação Inicial /
                        Detalhes</label>
                    <textarea name="message" id="message" rows="3"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gold focus:ring-gold sm:text-sm px-4 py-2"
                        placeholder="Como esse lead chegou ou o que ele precisa..."></textarea>
                </div>

                <!-- Opt-in -->
                <div class="flex items-center">
                    <input id="opt_in" name="opt_in" type="checkbox" value="1"
                        class="h-4 w-4 rounded border-gray-300 text-gold focus:ring-gold">
                    <label for="opt_in" class="ml-2 block text-sm text-gray-900">
                        Aceita receber novidades (Opt-in)
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex justify-end">
                    <button type="submit" form="new-lead-form"
                        class="px-4 py-2 text-white font-medium rounded-md shadow-sm transition-colors inline-flex items-center justify-center bg-gold hover:bg-gold-dark">
                        Salvar Lead
                    </button>
                </div>
            </form>
        </div>
    </x-modal>


</x-app-layout>