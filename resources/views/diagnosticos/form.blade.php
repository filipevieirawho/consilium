<x-diagnosticos.layout :progressPct="5" progressLabel="Etapa 1a — Sobre você">
    <div class="bg-white sm:rounded-lg shadow-sm border border-gray-100 p-9">
        <!-- Header -->
        <div class="mb-8">
            <span class="text-xs font-semibold uppercase tracking-wider px-2 py-1 rounded-md bg-gold-light text-gold">Etapa 1 de 2</span>
            <h2 class="text-xl font-bold text-gray-900 mt-3">Sobre você</h2>
            <p class="text-sm text-gray-500 mt-1">Informe seus dados de contato para iniciar o check-up.</p>
        </div>

        <form method="POST" action="{{ route('diagnostico.saveForm', $token) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                <input type="text" name="nome" value="{{ old('nome', $diagnostico->nome) }}" required
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                    placeholder="Seu nome completo">
                @error('nome')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Empresa <span class="text-red-500">*</span></label>
                    <input type="text" name="empresa" value="{{ old('empresa', $diagnostico->empresa) }}" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                        placeholder="Nome da empresa">
                    @error('empresa')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                    <input type="text" name="cargo" value="{{ old('cargo', $diagnostico->cargo) }}"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                        placeholder="Ex: Diretor de Obras">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $diagnostico->email) }}" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                        placeholder="seu@email.com">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="text" name="telefone" value="{{ old('telefone', $diagnostico->telefone) }}"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gold focus:ring-gold px-4 py-2.5 text-sm"
                        placeholder="(11) 99999-9999">
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <a href="{{ route('diagnostico.landing', $token) }}" class="text-sm text-gray-500 hover:text-gray-700">
                    ← Voltar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold rounded-xl transition-all hover:shadow-md bg-gold hover:bg-gold-dark">
                    Continuar →
                </button>
            </div>
        </form>
    </div>
</x-diagnosticos.layout>
