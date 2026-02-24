<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Lead: ') }} {{ $contact->name }}
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

                <!-- Assignment and Status Forms (Left Col, 1/3 width) -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Gestão do Lead</h3>

                            <form action="{{ route('contacts.updateDetails', $contact) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <!-- Status -->
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status do
                                        Lead</label>
                                    <select name="status" id="status"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                                        <option value="novo" {{ $contact->status === 'novo' ? 'selected' : '' }}>🔵 Novo
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
                                    <select name="user_id" id="user_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D]">
                                        <option value="">-- Não atribuído --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $contact->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ ucfirst($user->role) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Internal Notes -->
                                <div class="mb-5">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Anotações
                                        Internas</label>
                                    <textarea name="notes" id="notes" rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D]"
                                        placeholder="Adicione notas sobre reuniões, interesses, propostas...">{{ old('notes', $contact->notes) }}</textarea>
                                </div>

                                <button type="submit"
                                    class="w-full bg-gray-900 text-white font-semibold py-2 px-4 rounded-md hover:bg-gray-800 transition-colors">
                                    Salvar Alterações
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Data Overview (Right Col, 2/3 width) -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Dados do Formulário</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                                <div class="sm:col-span-2 mt-2">
                                    <span class="block text-sm font-medium text-gray-500 mb-1">Mensagem Recebida</span>
                                    <div class="p-4 bg-gray-50 rounded-md text-gray-700 whitespace-pre-line border">
                                        {{ $contact->message }}
                                    </div>
                                </div>
                                <div class="sm:col-span-2 mt-2">
                                    <span class="block text-sm text-gray-400">Enviado em:
                                        {{ $contact->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>