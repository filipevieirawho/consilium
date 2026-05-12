<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check-up de Consistência da Margem — Consilium</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/consilium-logo-icon.png') }}">

    <!-- Scripts/Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .brand-gold { color: #D0AE6D; }
        .bg-brand-gold { background-color: #D0AE6D; }
        .border-brand-gold { border-color: #D0AE6D; }
        .progress-bar-fill { background: linear-gradient(90deg, #b5955a, #D0AE6D); transition: width 0.5s ease; }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <!-- Top bar -->
    <header class="bg-white border-b border-gray-200 py-4 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <img src="{{ asset('assets/images/logo-horizontal-crop.png') }}" alt="Consilium" class="h-7 w-auto">
            @if(isset($progressLabel))
                <span class="text-xs text-gray-500 font-medium">{{ $progressLabel }}</span>
            @endif
        </div>
    </header>

    <!-- Progress bar (optional) -->
    @if(isset($progressPct))
    <div class="w-full bg-gray-100 h-1 no-print">
        <div class="progress-bar-fill h-1 rounded-r" style="width: {{ $progressPct }}%"></div>
    </div>
    @endif

    <!-- Content -->
    <main class="flex-grow flex flex-col items-center justify-start py-12 px-4">
        <div class="w-full max-w-2xl">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 border-t border-gray-100">
        <p class="text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Consilium. Todas as informações são tratadas com confidencialidade.
        </p>
    </footer>

    @stack('scripts')
</body>
</html>
