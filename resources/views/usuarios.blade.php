<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <ion-icon name="people-outline"></ion-icon> {{ __('Usuários') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success message --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Add User Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Adicionar Novo Usuário</h3>

                    <form method="POST" action="{{ route('usuarios.store') }}">
                        @csrf
                        <div class="flex flex-col md:flex-row gap-3 items-center">
                            <input type="text" name="name" required value="{{ old('name') }}"
                                class="w-full md:flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Nome completo">

                            <input type="email" name="email" required value="{{ old('email') }}"
                                class="w-full md:flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="E-mail">

                            <input type="password" name="password" required
                                class="w-full md:w-36 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Senha">

                            <select name="role"
                                class="w-full md:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="representante">Representante</option>
                                <option value="gestor">Gestor</option>
                                <option value="admin">Admin</option>
                            </select>

                            <button type="submit"
                                class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 whitespace-nowrap">Adicionar</button>
                        </div>
                        @if($errors->any())
                            <div class="mt-2">
                                @foreach($errors->all() as $error)
                                    <p class="text-sm text-red-500">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Users List --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nome</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        E-mail</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr class="{{ !$user->active ? 'opacity-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="ml-1 text-xs font-normal" style="color: #D0AE6D">(você)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <select data-user-id="{{ $user->id }}" onchange="updateRole(this)"
                                                class="role-select rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin
                                                </option>
                                                <option value="gestor" {{ $user->role === 'gestor' ? 'selected' : '' }}>Gestor
                                                </option>
                                                <option value="representante" {{ $user->role === 'representante' ? 'selected' : '' }}>Representante</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <select data-user-id="{{ $user->id }}" onchange="updateActive(this)"
                                                class="active-select rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                                <option value="1" {{ $user->active ? 'selected' : '' }}>Ativo</option>
                                                <option value="0" {{ !$user->active ? 'selected' : '' }}>Inativo</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($user->id !== auth()->id())
                                                <button type="button"
                                                    onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                    class="text-red-500 hover:text-red-700 text-sm font-medium">
                                                    Excluir
                                                </button>
                                            @else
                                                <span class="text-gray-300 text-sm">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum usuário
                                            encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeDeleteModal()"></div>
        <div class="relative bg-white rounded-lg shadow-xl p-6 mx-4 max-w-sm w-full">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmar exclusão</h3>
            <p class="text-sm text-gray-600 mb-6">Tem certeza que deseja excluir o usuário <strong
                    id="deleteUserName"></strong>? Esta ação não pode ser desfeita.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancelar</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Excluir</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            function openDeleteModal(userId, userName) {
                document.getElementById('deleteUserName').textContent = userName;
                document.getElementById('deleteForm').action = `/usuarios/${userId}`;
                const modal = document.getElementById('deleteModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeDeleteModal() {
                const modal = document.getElementById('deleteModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeDeleteModal();
            });

            function updateRole(selectElement) {
                const userId = selectElement.dataset.userId;
                const role = selectElement.value;

                fetch(`/usuarios/${userId}/role`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ role })
                })
                    .then(r => { if (!r.ok) throw new Error(); return r.json(); })
                    .then(() => { selectElement.style.outline = '2px solid #22c55e'; setTimeout(() => selectElement.style.outline = '', 1000); })
                    .catch(() => { alert('Erro ao atualizar. Tente novamente.'); location.reload(); });
            }

            function updateActive(selectElement) {
                const userId = selectElement.dataset.userId;
                const active = selectElement.value;

                const row = selectElement.closest('tr');
                row.classList.toggle('opacity-50', active === '0');

                fetch(`/usuarios/${userId}/active`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ active: parseInt(active) })
                })
                    .then(r => { if (!r.ok) throw new Error(); return r.json(); })
                    .then(() => { selectElement.style.outline = '2px solid #22c55e'; setTimeout(() => selectElement.style.outline = '', 1000); })
                    .catch(() => { alert('Erro ao atualizar status. Tente novamente.'); location.reload(); });
            }
        </script>
    @endpush
</x-app-layout>