<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Consilium') }} | Gestão de Leads</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/consilium-logo-icon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex flex-col">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="py-6 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-500">
                    <div>
                        <strong class="text-gray-800">Consilium.</strong> 2026 &copy; Todos os direitos reservados.
                    </div>
                    <div>
                        v.1.0
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.custom-combobox-container').forEach(function(container) {
                const searchInput = container.querySelector('.combo-search-input');
                const dropdown = container.querySelector('.combo-dropdown-list');
                const options = dropdown.querySelectorAll('.combo-option');
                const emptyMsg = container.querySelector('.combo-empty');
                const hiddenInput = container.querySelector('input[type="hidden"]');
                const onSelectFnName = hiddenInput.getAttribute('data-onselect');
                
                const creatableItem = container.querySelector('.combo-creatable');
                const creatableTerm = container.querySelector('.creatable-term');
                const isCreatable = hiddenInput.getAttribute('data-creatable') === 'true';
                const onCreateFnName = hiddenInput.getAttribute('data-oncreate');

                searchInput.addEventListener('focus', () => dropdown.classList.remove('hidden'));
                
                document.addEventListener('click', (e) => {
                    if (!container.contains(e.target)) dropdown.classList.add('hidden');
                });

                searchInput.addEventListener('input', (e) => {
                    const term = e.target.value.toLowerCase();
                    dropdown.classList.remove('hidden');
                    let hasVisible = false;
                    let perfectMatch = false;
                    
                    options.forEach(opt => {
                        const itemNameEl = opt.querySelector('.item-name');
                        const text = (itemNameEl ? itemNameEl.textContent : opt.textContent).trim().toLowerCase();
                        if (text.includes(term)) {
                            opt.style.display = 'block';
                            hasVisible = true;
                            if (text === term) perfectMatch = true;
                        } else {
                            opt.style.display = 'none';
                        }
                    });
                    
                    if(emptyMsg) emptyMsg.classList.toggle('hidden', hasVisible || (isCreatable && term.length > 0));

                    if (isCreatable && term.length > 0 && !perfectMatch) {
                        creatableItem.classList.remove('hidden');
                        creatableTerm.textContent = searchInput.value;
                    } else if (creatableItem) {
                        creatableItem.classList.add('hidden');
                    }
                });

                if (creatableItem) {
                    creatableItem.addEventListener('click', () => {
                        const term = searchInput.value;
                        if (onCreateFnName && window[onCreateFnName]) {
                            window[onCreateFnName](term, container);
                        }
                        dropdown.classList.add('hidden');
                    });
                }

                dropdown.addEventListener('click', (e) => {
                    const opt = e.target.closest('.combo-option');
                    if (!opt) return;

                    const val = opt.getAttribute('data-value');
                    hiddenInput.value = val;
                    
                    if(val === "") {
                        searchInput.value = "";
                    } else {
                        const nameSpan = opt.querySelector('.item-name') || opt;
                        searchInput.value = nameSpan.textContent.trim();
                    }
                    
                    dropdown.classList.add('hidden');

                    if (onSelectFnName && window[onSelectFnName]) {
                        window[onSelectFnName](val, opt);
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>