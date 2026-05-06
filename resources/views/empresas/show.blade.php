<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between h-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('empresas.index') }}" class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-700">
                    <ion-icon name="arrow-back-outline" class="text-2xl"></ion-icon>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $empresa->nome_fantasia }}</h2>
            </div>
            <a href="{{ route('empresas.edit', $empresa) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg"
               style="background-color: #D0AE6D;">
                <ion-icon name="create-outline"></ion-icon> Editar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{{ session('success') }}</div>
            @endif

            {{-- Info Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">CNPJ</p>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $empresa->cnpj ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', str_pad($empresa->cnpj, 14, '0', STR_PAD_LEFT)) : '—' }}
                    </p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Segmento</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $empresa->segmento ?? '—' }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Porte / Tipo</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $empresa->porte ?? '—' }} @if($empresa->tipo_unidade) · {{ $empresa->tipo_unidade }} @endif</p>
                </div>
            </div>

            @if($empresa->rua)
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Endereço</p>
                <p class="text-sm text-gray-800">
                    {{ $empresa->rua }}{{ $empresa->numero ? ', '.$empresa->numero : '' }}{{ $empresa->complemento ? ' '.$empresa->complemento : '' }} —
                    {{ $empresa->bairro }}, {{ $empresa->cidade }}/{{ $empresa->estado }} — CEP {{ $empresa->cep }}
                </p>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Leads --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 text-sm">Contacts / Leads</h3>
                        <span class="text-xs text-gray-400">{{ $empresa->contacts->count() }} registros</span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($empresa->contacts as $c)
                        <a href="{{ route('contacts.show', $c) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition">
                            <div class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-amber-700">{{ strtoupper(substr($c->name, 0, 1)) }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $c->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $c->email }}</p>
                            </div>
                        </a>
                        @empty
                        <div class="px-5 py-8 text-center text-gray-400 text-sm">Nenhum lead vinculado.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Diagnosticos --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 text-sm">Diagnósticos</h3>
                        <span class="text-xs text-gray-400">{{ $empresa->diagnosticos->count() }} registros</span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($empresa->diagnosticos as $d)
                        <a href="{{ route('diagnosticos.show', $d) }}" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $d->nome_empreendimento ?? 'Sem nome' }}</p>
                                <p class="text-xs text-gray-400">{{ $d->created_at->format('d/m/Y') }}</p>
                            </div>
                            @if($d->ipm)
                            <span class="text-sm font-bold"
                                  style="color: {{ $d->ipm >= 70 ? '#22c55e' : ($d->ipm >= 40 ? '#f59e0b' : '#ef4444') }};">
                                IPM {{ number_format($d->ipm, 0) }}
                            </span>
                            @else
                            <span class="text-xs text-gray-400 italic">em andamento</span>
                            @endif
                        </a>
                        @empty
                        <div class="px-5 py-8 text-center text-gray-400 text-sm">Nenhum diagnóstico vinculado.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
