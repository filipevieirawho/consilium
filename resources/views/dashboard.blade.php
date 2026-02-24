<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard de Leads') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('dashboard') }}" class="mb-6">
                        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
                            <!-- Left side: Search field only -->
                            <input type="text" name="search" placeholder="Buscar por nome, email ou mensagem..."
                                value="{{ request('search') }}"
                                class="w-full md:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            <!-- Right side: Filters and buttons -->
                            <div class="flex gap-3 items-center w-full md:w-auto">
                                <select name="year"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todos os anos</option>
                                    @for($year = date('Y'); $year >= 2026; $year--)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>

                                <select name="month"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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

                                <button type="submit"
                                    class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 whitespace-nowrap">Buscar</button>

                                @if(request('search') || request('year') || request('month'))
                                    <a href="{{ route('dashboard') }}"
                                        class="flex items-center text-gray-600 hover:text-gray-900 whitespace-nowrap">Limpar</a>
                                @endif
                            </div>
                        </div>
                    </form>

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

                fetch(`/contacts/${contactId}/status`, {
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