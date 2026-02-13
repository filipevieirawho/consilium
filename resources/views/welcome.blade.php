<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consilium | Consultoria em Engenharia</title>
    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Outfit:wght@200;300;400;500;600&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="antialiased bg-[#0e0e0e] text-white font-sans overflow-x-hidden selection:bg-[#c9a66b] selection:text-black">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 flex justify-between items-center px-8 py-6 mix-blend-difference text-white">
        <a href="#">
            <img src="{{ asset('assets/images/consilium-logo-text.png') }}" alt="CONSILIUM" class="w-28">
        </a>
        <div class="hidden md:flex space-x-8 text-sm uppercase tracking-widest font-light">
            <a href="#sobre" class="hover:text-[#c9a66b] transition-colors duration-300">Sobre</a>
            <a href="#dimensions-slider" class="hover:text-[#c9a66b] transition-colors duration-300">Dimensões</a>
            <a href="#quemsomos" class="hover:text-[#c9a66b] transition-colors duration-300">Quem Somos</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="h-screen flex flex-col justify-center items-center relative overflow-hidden">
        <!-- Hero Background Image -->
        <img src="{{ asset('assets/images/hero-background-new.jpg') }}" alt="Hero Background"
            class="absolute inset-0 w-full h-full object-cover opacity-60 z-0">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#0e0e0e] z-10"></div>
        <!-- Logo Animation Container -->
        <div class="z-20 text-center space-y-8 opacity-0" id="hero-content">
            <img src="{{ asset('assets/images/hero-logo-transparent.png') }}" alt="Consilium Logo"
                class="w-32 md:w-48 mx-auto mb-8" id="hero-logo">

            <h1 class="text-4xl md:text-7xl font-light leading-tight">
                <span class="block overflow-hidden"><span class="inline-block hero-text">A <strong
                            class="font-bold">experiência</strong></span></span>
                <span class="block overflow-hidden"><span class="inline-block hero-text text-[#c9a66b]">cria a
                        <strong class="font-bold">estratégia.</strong></span></span>
            </h1>
            <p class="text-lg md:text-2xl font-light tracking-wide mt-4 opacity-0 hero-subtext">
                O <strong class="font-bold text-white">alinhamento</strong> constrói o <strong
                    class="font-bold text-[#c9a66b]">resultado.</strong>
            </p>
        </div>
        <div class="absolute bottom-10 animate-bounce z-20">
            <svg class="w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 14l-7 7m0 0l-7-7m7 7V3">
                </path>
            </svg>
        </div>
    </section>

    <!-- Intro / Philosophy -->
    <section id="sobre"
        class="min-h-screen py-24 px-8 md:px-24 flex flex-col justify-center relative border-t border-white/5 overflow-hidden">

        <!-- Background Image -->
        <div class="absolute top-0 left-0 right-0 -bottom-[40px] z-0 opacity-20 bg-no-repeat bg-bottom bg-center pointer-events-none"
            style="background-image: url('{{ asset('assets/images/about-background.svg') }}'); background-size: 110% auto; background-position: center bottom;">
        </div>

        <div class="max-w-4xl mx-auto space-y-16 relative z-10">
            <span class="block text-[#c9a66b] uppercase tracking-[0.2em] text-sm font-bold mb-4">Sobre Nós</span>
            <div class="space-y-6 text-lg md:text-2xl font-light leading-relaxed text-gray-300 reveal-text">
                <p><span class="opacity-80">Somos uma empresa especializada em consultoria estratégica aplicada à
                        engenharia. Atuamos como
                        parceiros</span> <span class="underline text-[#c9a66b]">técnicos</span> <span
                        class="opacity-80">e</span> <span class="underline text-[#c9a66b]">intelectuais</span><span
                        class="opacity-80">,
                        apoiando nossos clientes na</span> <span class="font-semibold">tomada de decisões</span> <span
                        class="opacity-80">que impactam
                        diretamente a</span> <span class="font-semibold">eficiência</span><span
                        class="opacity-80">,</span> <span class="font-semibold">segurança</span> <span
                        class="opacity-80">e</span>
                    <span class="font-semibold">sustentabilidade</span> <span class="opacity-80">de seus
                        projetos.</span>
                </p>
                <p><span class="font-semibold">Na CONSILIUM, nossa engenharia vai além da execução.</span> <span
                        class="opacity-80">Entendemos
                        que o sucesso de um
                        projeto não é uma
                        solução mágica, mas o resultado de um processo estratégico e colaborativo, impulsionado pela
                        experiência.</span></p>
            </div>
    </section>

    <!-- Full Width Pillars Section -->
    <section id="sobre-pilares" class="w-full">

        <!-- Compreender (Light) -->
        <div class="w-full bg-[#f5f5f5] text-[#0e0e0e] py-24 border-t border-black/5">
            <div class="container mx-auto px-8 flex flex-col md:flex-row items-center justify-between">
                <div class="space-y-2 md:w-3/5">
                    <span class="text-xs font-bold tracking-[0.2em] uppercase text-[#c9a66b]">Compreender</span>
                    <h3 class="text-5xl md:text-7xl tracking-tight">Clareza para <span
                            class="font-cormorant font-bold text-[#c9a66b]">decidir.</span></h3>
                    <p class="text-lg text-gray-600 mt-4 md:max-w-xl">Enxergar o que importa <span
                            class="font-bold border-b border-black/20">antes</span> de agir.</p>
                </div>
                <div class="mt-12 md:mt-0 md:w-2/5 flex justify-center md:justify-end">
                    <!-- Large Arrow Icon (Outline) -->
                    <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="0.5" class="text-gray-300 w-32 h-32 md:w-48 md:h-48 opacity-50">
                        <path d="M12 4V20M12 20L5 13M12 20L19 13" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Estruturar (Dark) -->
        <div class="w-full bg-[#0e0e0e] text-white py-24 border-t border-white/5">
            <div class="container mx-auto px-8 flex flex-col md:flex-row items-center justify-between">
                <div class="space-y-2 md:w-3/5 order-1">
                    <span
                        class="text-xs font-bold tracking-[0.2em] uppercase text-[#c9a66b] opacity-80">Estruturar</span>
                    <h3 class="text-5xl md:text-7xl tracking-tight">Método para <span
                            class="font-cormorant font-bold text-[#c9a66b]">executar.</span></h3>
                    <p class="text-lg text-gray-400 mt-4 md:max-w-xl">Transformar decisão em prática <span
                            class="font-bold text-white border-b border-white/20">consistente</span>.</p>
                </div>
                <div class="mt-12 md:mt-0 md:w-2/5 flex justify-center md:justify-end order-2">
                    <!-- Large Arrow Icon (Dark/Subtle) -->
                    <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="0.5" class="text-white w-32 h-32 md:w-48 md:h-48 opacity-10">
                        <path d="M12 4V20M12 20L5 13M12 20L19 13" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Amadurecer (Gold) -->
        <div class="w-full bg-[#c9a66b] text-[#0e0e0e] py-24">
            <div class="container mx-auto px-8 flex flex-col md:flex-row items-center justify-between">
                <div class="space-y-2 md:w-3/5">
                    <span class="text-xs font-bold tracking-[0.2em] uppercase text-black/60">Amadurecer</span>
                    <h3 class="text-5xl md:text-7xl tracking-tight">Experiência para <span
                            class="font-cormorant font-bold text-white">sustentar.</span></h3>
                    <p class="text-lg text-black/70 mt-4 md:max-w-xl">Manter o <span
                            class="font-bold border-b border-black/20 text-black">valor</span> quando o cenário muda.
                    </p>
                </div>
                <div class="mt-12 md:mt-0 md:w-2/5 flex justify-center md:justify-end">
                    <!-- Large Arrow Icon (Tone-on-tone) -->
                    <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="0.5" class="text-black w-32 h-32 md:w-48 md:h-48 opacity-10">
                        <path d="M12 4V20M12 20L5 13M12 20L19 13" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>
        </div>

    </section>



    <!-- Dimensions Intro -->


    <!-- Dimensions Slider Section -->
    <div id="dimensions-slider"
        class="relative bg-[#0e0e0e] border-t border-white/5 h-screen flex items-center overflow-hidden">

        <div class="container mx-auto px-8 flex flex-col md:flex-row h-full">

            <!-- LEFT COLUMN: Visuals (Fixed Position relative to container) -->
            <div class="w-full md:w-1/2 h-full flex items-center justify-center relative order-1">
                <div class="relative w-[300px] md:w-[500px] aspect-square flex items-center justify-center"
                    id="dimensions-rotator">
                    <!-- Base Image -->
                    <img src="{{ asset('assets/images/dimensions-base-new.png') }}" alt="Base Dimension"
                        class="absolute inset-0 w-full h-full object-contain opacity-40 mix-blend-screen"
                        id="slider-base">


                    <!-- Overlays (Absolute Stack) -->
                    <img src="{{ asset('assets/images/dimensions-intro.png') }}"
                        class="absolute inset-0 w-full h-full object-contain dimension-overlay opacity-1"
                        id="overlay-intro">
                    <img src="{{ asset('assets/images/dimensions-gestao.png') }}"
                        class="absolute inset-0 w-full h-full object-contain dimension-overlay opacity-0"
                        style="width: 97%; left: 1.5%;" id="overlay-gestao">
                    <img src="{{ asset('assets/images/dimensions-metodologia.png') }}"
                        class="absolute inset-0 w-full h-full object-contain dimension-overlay opacity-0"
                        style="transform: rotate(180deg);" id="overlay-metodologia">
                    <img src="{{ asset('assets/images/dimensions-fundamentos.png') }}"
                        class="absolute inset-0 w-full h-full object-contain dimension-overlay opacity-0"
                        id="overlay-fundamentos">
                </div>
            </div>

            <!-- RIGHT COLUMN: Text Content (Stacked) -->
            <div class="w-full md:w-1/2 h-full link-area relative order-2 flex items-center">

                <!-- Slide 0: Intro -->
                <div class="dimension-slide absolute inset-0 flex flex-col justify-center items-start text-left opacity-1 px-8 z-10"
                    id="slide-intro">
                    <h2 class="text-4xl md:text-6xl font-cormorant font-bold text-[#c9a66b] leading-tight mb-8">
                        Nossas Dimensões
                    </h2>
                    <p class="text-xl font-light text-gray-300 max-w-lg mb-8">
                        Nossas dimensões são estruturadas com pilares que fundamentam a <span
                            class="font-semibold">Jornada do Empreendimento</span> e <span class="underline">camadas
                            estratégicas</span> de
                        informação que se desdobram, permitindo decisões claras em cada etapa.
                    </p>
                    <div class="flex flex-row gap-12">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600 font-outfit">1.</span>
                            <h4 class="text-[#c9a66b] font-medium text-lg">Gestão</h4>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600 font-outfit">2.</span>
                            <h4 class="text-[#c9a66b] font-medium text-lg">Metodológica</h4>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600 font-outfit">3.</span>
                            <h4 class="text-[#c9a66b] font-medium text-lg">Fundamentos</h4>
                        </div>
                    </div>
                </div>

                <!-- Slide 1: Gestão -->
                <div class="dimension-slide absolute inset-0 flex flex-col justify-center opacity-0 px-8"
                    id="slide-gestao">
                    <div class="flex flex-col items-start gap-2 mb-6">
                        <span class="text-gray-600 text-xl font-outfit font-light tracking-tight mb-2">#1</span>
                        <h2 class="text-5xl md:text-6xl font-cormorant text-[#c9a66b]">Gestão</h2>
                    </div>
                    <p class="text-xl font-light text-gray-300 mb-8 max-w-lg opacity-80">Define o escopo estratégico e
                        técnico. O
                        que precisa ser entregue e como estruturamos essa entrega?</p>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">Viabilidade</h4>
                            <p class="text-xs text-gray-400 mt-1">Análise fundamentada.</p>
                        </div>
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">Orçamento</h4>
                            <p class="text-xs text-gray-400 mt-1">Evolução precisa.</p>
                        </div>
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">Planejamento</h4>
                            <p class="text-xs text-gray-400 mt-1">Metas claras.</p>
                        </div>
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">Acompanhamento</h4>
                            <p class="text-xs text-gray-400 mt-1">Monitoramento real.</p>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Metodologia -->
                <div class="dimension-slide absolute inset-0 flex flex-col justify-center opacity-0 px-8"
                    id="slide-metodologia">
                    <div class="flex flex-col items-start gap-2 mb-6">
                        <span class="text-gray-600 text-xl font-outfit font-light tracking-tight mb-2">#2</span>
                        <h2 class="text-5xl md:text-6xl font-cormorant text-[#c9a66b]">Metodológica</h2>
                    </div>
                    <p class="text-xl font-light text-gray-300 mb-8 max-w-lg opacity-80">Garante que o definido na
                        Gestão seja
                        executado com rigor contínuo.</p>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">PLAN</h4>
                            <p class="text-xs text-gray-400 mt-1">Mapeamento de valor.</p>
                        </div>
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">DO</h4>
                            <p class="text-xs text-gray-400 mt-1">Execução disciplinada.</p>
                        </div>
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">CHECK</h4>
                            <p class="text-xs text-gray-400 mt-1">Controle por métricas.</p>
                        </div>
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">ACT</h4>
                            <p class="text-xs text-gray-400 mt-1">Ajuste contínuo.</p>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: Fundamentos -->
                <div class="dimension-slide absolute inset-0 flex flex-col justify-center opacity-0 px-8"
                    id="slide-fundamentos">
                    <div class="flex flex-col items-start gap-2 mb-6">
                        <span class="text-gray-600 text-xl font-outfit font-light tracking-tight mb-2">#3</span>
                        <h2 class="text-5xl md:text-6xl font-cormorant text-[#c9a66b]">Fundamentos</h2>
                    </div>
                    <p class="text-xl font-light text-gray-300 mb-8 max-w-lg opacity-80">Expressa a identidade e os
                        valores que
                        sustentam nossa reputação.</p>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">Experiência</h4>
                            <p class="text-xs text-gray-400 mt-1">Antecipar riscos.</p>
                        </div>
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">Clareza</h4>
                            <p class="text-xs text-gray-400 mt-1">Confiança total.</p>
                        </div>
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">Propósito</h4>
                            <p class="text-xs text-gray-400 mt-1">Compromisso genuíno.</p>
                        </div>
                        <div class="border-l border-[#c9a66b]/30 pl-4">
                            <h4 class="text-[#c9a66b] font-medium">Integridade</h4>
                            <p class="text-xs text-gray-400 mt-1">Ética inegociável.</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Navigation Dots -->
        <div class="absolute right-8 top-1/2 -translate-y-1/2 flex flex-col gap-4 z-20">
            <button
                class="w-2 h-2 rounded-full bg-white/20 hover:bg-[#c9a66b] transition duration-300 slider-dot active"
                data-index="0"></button>
            <button class="w-2 h-2 rounded-full bg-white/20 hover:bg-[#c9a66b] transition duration-300 slider-dot"
                data-index="1"></button>
            <button class="w-2 h-2 rounded-full bg-white/20 hover:bg-[#c9a66b] transition duration-300 slider-dot"
                data-index="2"></button>
            <button class="w-2 h-2 rounded-full bg-white/20 hover:bg-[#c9a66b] transition duration-300 slider-dot"
                data-index="3"></button>
        </div>

    </div>

    <!-- Who We Are -->
    <section id="quemsomos" class="py-32 bg-[#161616] relative overflow-hidden">
        <div class="container mx-auto px-8 relative z-10">
            <h2 class="text-sm tracking-[0.4em] uppercase text-[#c9a66b] mb-12 text-center md:text-left">Liderança</h2>

            <div class="flex flex-col md:flex-row gap-16 items-start">
                <div class="w-full md:w-1/3 space-y-4">
                    <h3 class="text-5xl md:text-6xl font-cormorant font-medium">Jorge Moura</h3>
                    <p class="text-gray-400 font-light text-lg">Diretor / CEO / Founder</p>
                    <div class="w-16 h-1 bg-[#c9a66b] mt-4"></div>
                </div>

                <div
                    class="w-full md:w-2/3 space-y-8 font-light text-gray-300 leading-relaxed text-sm md:text-base columns-1 md:columns-2 gap-12">
                    <p>Engenheiro Civil formado pela Universidade FUMEC, Jorge Moura possui mais de 30 anos de
                        experiência em planejamento, custos e gestão técnica de obras, atuando ao longo de toda a
                        jornada dos empreendimentos — da concepção ao pós-obra.</p>
                    <p>Sua atuação é focada em reduzir incertezas, elevar a maturidade de gestão e transformar projetos
                        complexos em empreendimentos previsíveis.</p>
                    <p>Sua trajetória teve início na ENCOL S.A., onde vivenciou um ambiente pioneiro em processos
                        integrados. Em 1997, integrou a PATRIMAR Engenharia. Em 2000, tornou-se sócio-diretor da FS,
                        atendendo clientes como VALE e USIMINAS.</p>
                    <p>Retornou à PATRIMAR em 2013 liderando custos em mais de 35 projetos. À frente da CONSILIUM,
                        combina análise rigorosa e visão sistêmica para transformar diagnósticos em soluções
                        sustentáveis.</p>
                </div>
            </div>
        </div>
        <div class="absolute -right-24 -bottom-24 w-96 h-96 bg-[#c9a66b]/5 rounded-full blur-3xl"></div>
    </section>

    <!-- Contact Section -->
    <section id="contato" class="py-24 bg-[#0e0e0e] border-t border-white/5 relative">
        <div class="container mx-auto px-8 relative z-10">
            <div class="max-w-2xl mx-auto">
                <h2 class="text-sm tracking-[0.4em] uppercase text-[#c9a66b] mb-12 text-center">Contato</h2>
                <h3 class="text-4xl md:text-5xl font-cormorant text-center mb-12">Vamos conversar sobre o <span
                        class="text-[#c9a66b]">futuro</span> do seu
                    projeto?</h3>

                <!-- Form Container -->
                <div id="form-container" class="transition-opacity duration-500">
                    <form id="contact-form" class="space-y-6">
                        @csrf

                        <!-- Name (Full Width) -->
                        <div class="space-y-2">
                            <label for="name" class="text-xs uppercase tracking-widest text-gray-500">Nome</label>
                            <input type="text" id="name" name="name" required
                                class="w-full bg-white/5 border border-white/10 rounded-sm px-4 py-3 text-white focus:outline-none focus:border-[#c9a66b] transition duration-300">
                        </div>

                        <!-- Email & WhatsApp (Side by Side) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="email"
                                    class="text-xs uppercase tracking-widest text-gray-500">E-mail</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full bg-white/5 border border-white/10 rounded-sm px-4 py-3 text-white focus:outline-none focus:border-[#c9a66b] transition duration-300">
                            </div>
                            <div class="space-y-2">
                                <label for="phone"
                                    class="text-xs uppercase tracking-widest text-gray-500">WhatsApp</label>
                                <input type="tel" id="phone" name="phone" placeholder="(31) 9876-5432" maxlength="15"
                                    required
                                    class="w-full bg-white/5 border border-white/10 rounded-sm px-4 py-3 text-white focus:outline-none focus:border-[#c9a66b] transition duration-300">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="message"
                                class="text-xs uppercase tracking-widest text-gray-500">Mensagem</label>
                            <textarea id="message" name="message" rows="4" required
                                class="w-full bg-white/5 border border-white/10 rounded-sm px-4 py-3 text-white focus:outline-none focus:border-[#c9a66b] transition duration-300"></textarea>
                        </div>

                        <div class="flex items-center gap-3">
                            <!-- Custom Checkbox -->
                            <input type="checkbox" id="opt_in" name="opt_in" value="1"
                                class="w-4 h-4 rounded border-gray-600 text-[#c9a66b] focus:ring-[#c9a66b] bg-white/5 accent-[#c9a66b]">
                            <label for="opt_in" class="text-xs text-gray-500">Aceito receber comunicações sobre a
                                Consilium.</label>
                        </div>

                        <div class="pt-4 text-center">
                            <button type="submit" id="submit-btn"
                                class="bg-[#c9a66b] text-black uppercase tracking-widest text-sm font-bold px-12 py-4 rounded-sm hover:bg-[#b08d55] transition duration-300">Enviar
                                Mensagem</button>
                        </div>

                        <div id="form-error" class="text-center text-sm mt-4 text-red-500 hidden"></div>
                    </form>
                </div>

                <!-- Success Message (Hidden by default) -->
                <div id="success-message"
                    class="hidden flex-col items-center justify-center space-y-6 py-12 animate-fade-in">
                    <div class="w-20 h-20 rounded-full bg-[#c9a66b]/20 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-[#c9a66b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-4xl font-cormorant text-white">Mensagem Enviada!</h3>
                    <p class="text-gray-400 font-light text-center max-w-md">Obrigado pelo seu contato. Nossa equipe
                        retornará em breve.</p>
                    <button onclick="location.reload()"
                        class="text-sm uppercase tracking-widest text-[#c9a66b] hover:text-white transition mt-8 border-b border-[#c9a66b] hover:border-white pb-1">Enviar
                        nova mensagem</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black py-16 border-t border-white/5 text-center md:text-left">
        <div class="container mx-auto px-8 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="space-y-4">
                <img src="{{ asset('assets/images/consilium-logo-text.png') }}" alt="CONSILIUM" class="w-28">
                <p class="text-xs text-gray-500">Consultoria Estratégica em Engenharia.</p>
            </div>

            <div class="space-y-4">
                <h4 class="text-white text-sm uppercase tracking-widest">Contato</h4>
                <p class="text-gray-500 text-sm">contato@consilium.eng.br</p>
                <!-- Add phone if available or placeholder -->
            </div>

            <div class="space-y-4">
                <h4 class="text-white text-sm uppercase tracking-widest">Endereço</h4>
                <p class="text-gray-500 text-sm">Escritório Central<br>Belo Horizonte, MG</p>
            </div>

            <div class="space-y-4">
                <h4 class="text-white text-sm uppercase tracking-widest">Social</h4>
                <div class="flex justify-center md:justify-start gap-4">
                    <a href="#" class="text-gray-500 hover:text-[#c9a66b] transition">LinkedIn</a>
                    <a href="#" class="text-gray-500 hover:text-[#c9a66b] transition">Instagram</a>
                </div>
            </div>
        </div>
        <div class="container mx-auto px-8 mt-16 pt-8 border-t border-white/5 text-center text-xs text-gray-700">
            &copy; {{ date('Y') }} Consilium. Todos os direitos reservados.
        </div>
    </footer>

    <script>
        // Phone Mask Logic
        document.getElementById('phone').addEventListener('input', function (e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });

        document.getElementById('contact-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const btn = document.getElementById('submit-btn');
            const formContainer = document.getElementById('form-container');
            const successMessage = document.getElementById('success-message');
            const errorMsg = document.getElementById('form-error');
            const originalBtnText = btn.innerText;

            // Disable button
            btn.disabled = true;
            btn.innerText = 'Enviando...';
            errorMsg.classList.add('hidden');

            const formData = new FormData(form);

            fetch("{{ route('contact.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        // Success: Hide form, show success message
                        formContainer.classList.add('hidden');
                        successMessage.classList.remove('hidden');
                        successMessage.classList.add('flex'); // Enable flex layout
                        form.reset();
                    } else {
                        throw new Error('Erro ao enviar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorMsg.innerText = 'Ocorreu um erro ao enviar. Tente novamente.';
                    errorMsg.classList.remove('hidden');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = originalBtnText;
                }); });
    </script>
</body>

</html>