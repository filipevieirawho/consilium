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

    <!-- TomSelect -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-control { border-radius: 0.5rem; padding: 0.625rem 1rem; border-color: #d1d5db; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); }
        .ts-control.focus { border-color: #D0AE6D; box-shadow: 0 0 0 1px #D0AE6D; }
        .ts-dropdown { border-radius: 0.5rem; border-color: #d1d5db; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        .ts-dropdown .option { padding: 0.5rem 1rem; }
        .ts-dropdown .active { background-color: #fdf8ed; color: #b5955a; }
    </style>
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
    @stack('scripts')
</body>

</html>