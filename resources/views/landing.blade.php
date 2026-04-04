<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NandoRAG - Seu Assistente de IA para Conhecimento Pessoal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        heading: ['Oswald', 'sans-serif'],
                        body: ['Space Mono', 'monospace'],
                    },
                    colors: {
                        cream: '#F0EAD6',
                        'cream-dark': '#E5E0CC',
                        teal: '#22D3EE',
                        yellow: '#FDE047',
                        magenta: '#E879F9',
                        black: '#000000',
                    },
                    boxShadow: {
                        'neo': '4px 4px 0 #000',
                        'neo-lg': '6px 6px 0 #000',
                        'neo-xl': '8px 8px 0 #000',
                    },
                    borderWidth: {
                        'neo': '2px',
                    }
                }
            }
        }
    </script>
    <style>
        * {
            border-color: #000 !important;
        }
        
        body {
            font-family: 'Space Mono', monospace;
            background-color: #F0EAD6;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .card-neo {
            border: 2px solid #000;
            box-shadow: 4px 4px 0 #000;
            transition: all 0.15s ease;
        }
        
        .card-neo:hover {
            box-shadow: 6px 6px 0 #000;
            transform: translate(-2px, -2px);
        }
        
        .btn-neo {
            border: 2px solid #000;
            box-shadow: 3px 3px 0 #000;
            transition: all 0.1s ease;
            font-family: 'Oswald', sans-serif;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .btn-neo:hover {
            box-shadow: 5px 5px 0 #000;
            transform: translate(-2px, -2px);
        }
        
        .btn-neo:active {
            box-shadow: 1px 1px 0 #000;
            transform: translate(2px, 2px);
        }
        
        .btn-teal {
            background-color: #22D3EE;
        }
        
        .btn-yellow {
            background-color: #FDE047;
        }
        
        .input-neo {
            border: 2px solid #000;
            box-shadow: 2px 2px 0 #000;
        }
        
        .input-neo:focus {
            outline: none;
            box-shadow: 4px 4px 0 #000;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #F0EAD6 0%, #E5E0CC 100%);
        }
        
        .hero-pattern {
            background-image: 
                linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        .glow-teal {
            box-shadow: 0 0 30px rgba(34, 211, 238, 0.3);
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(34, 211, 238, 0.3); }
            50% { box-shadow: 0 0 40px rgba(34, 211, 238, 0.5); }
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white border-b-2 border-black sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-teal border-2 border-black flex items-center justify-center shadow-neo">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-heading text-black">NandoRAG</span>
                </div>
                <div class="hidden md:flex items-center gap-4">
                    <a href="#features" class="text-sm hover:underline">Recursos</a>
                    <a href="#how-it-works" class="text-sm hover:underline">Como Funciona</a>
                    <a href="#pricing" class="text-sm hover:underline">Preços</a>
                    <a href="http://localhost:8000/admin" class="btn-neo btn-teal px-6 py-2 text-sm">
                        Entrar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative py-20 lg:py-32 hero-pattern overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-20 left-10 w-32 h-32 bg-yellow border-2 border-black shadow-neo rotate-12 opacity-50 float-animation" style="animation-delay: 0s;"></div>
        <div class="absolute top-40 right-20 w-24 h-24 bg-magenta border-2 border-black shadow-neo -rotate-12 opacity-50 float-animation" style="animation-delay: 0.5s;"></div>
        <div class="absolute bottom-20 left-1/4 w-16 h-16 bg-teal border-2 border-black shadow-neo rotate-45 opacity-50 float-animation" style="animation-delay: 1s;"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 bg-white border-2 border-black px-4 py-2 mb-8 shadow-neo">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-xs font-heading">Powered by Ollama</span>
                </div>
                
                <!-- Main Headline -->
                <h1 class="text-5xl md:text-7xl lg:text-8xl text-black mb-6 leading-tight">
                    Seu Conhecimento,<br>
                    <span class="text-teal">Potencializado por IA</span>
                </h1>
                
                <!-- Subheadline -->
                <p class="text-lg md:text-xl text-gray-700 max-w-3xl mx-auto mb-10">
                    Transforme documentos em conhecimento conversacional. 
                    NandoRAG é seu assistente de IA para consultas em bases de conhecimento pessoais.
                    PDFs, textos e documentos become fontes de sabedoria.
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="http://localhost:8000/admin" class="btn-neo btn-teal px-8 py-4 text-lg">
                        Começar Agora
                        <svg class="inline-block w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#how-it-works" class="btn-neo bg-white px-8 py-4 text-lg">
                        Ver Demo
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                    <div class="bg-white border-2 border-black p-4 shadow-neo">
                        <div class="text-3xl font-heading text-teal">100%</div>
                        <div class="text-xs text-gray-600">Local</div>
                    </div>
                    <div class="bg-white border-2 border-black p-4 shadow-neo">
                        <div class="text-3xl font-heading text-teal">0€</div>
                        <div class="text-xs text-gray-600">Custo API</div>
                    </div>
                    <div class="bg-white border-2 border-black p-4 shadow-neo">
                        <div class="text-3xl font-heading text-teal">∞</div>
                        <div class="text-xs text-gray-600">Privacidade</div>
                    </div>
                    <div class="bg-white border-2 border-black p-4 shadow-neo">
                        <div class="text-3xl font-heading text-teal">∞</div>
                        <div class="text-xs text-gray-600">Documentos</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-cream-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl text-black mb-4">Recursos Premium</h2>
                <p class="text-gray-700 max-w-2xl mx-auto">
                    Tudo que você precisa para gerenciar seu conhecimento com IA
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white border-2 border-black p-6 shadow-neo card-neo">
                    <div class="w-12 h-12 bg-teal border-2 border-black flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl mb-2">Processamento de PDF</h3>
                    <p class="text-sm text-gray-600">
                        Upload e processamento de documentos PDF com extração inteligente de texto e chunking otimizado.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white border-2 border-black p-6 shadow-neo card-neo">
                    <div class="w-12 h-12 bg-yellow border-2 border-black flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl mb-2">Embeddings Locais</h3>
                    <p class="text-sm text-gray-600">
                        Geração de embeddings usando Ollama com nomic-embed-text. Totalmente local e privado.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white border-2 border-black p-6 shadow-neo card-neo">
                    <div class="w-12 h-12 bg-magenta border-2 border-black flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl mb-2">Chat Inteligente</h3>
                    <p class="text-sm text-gray-600">
                        Converse com seus documentos usando LLMs locais como Llama 3.2 ou Qwen3.
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-white border-2 border-black p-6 shadow-neo card-neo">
                    <div class="w-12 h-12 bg-teal border-2 border-black flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl mb-2">Armazenamento Vetorial</h3>
                    <p class="text-sm text-gray-600">
                        PostgreSQL com pgvector para busca semântica rápida e precisa.
                    </p>
                </div>
                
                <!-- Feature 5 -->
                <div class="bg-white border-2 border-black p-6 shadow-neo card-neo">
                    <div class="w-12 h-12 bg-yellow border-2 border-black flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl mb-2">Privacidade Total</h3>
                    <p class="text-sm text-gray-600">
                        Seus dados nunca saem do seu servidor. 100% offline e privado.
                    </p>
                </div>
                
                <!-- Feature 6 -->
                <div class="bg-white border-2 border-black p-6 shadow-neo card-neo">
                    <div class="w-12 h-12 bg-magenta border-2 border-black flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl mb-2">Admin Panel</h3>
                    <p class="text-sm text-gray-600">
                        Filament 5 com design customizado para gerenciamento completo.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl text-black mb-4">Como Funciona</h2>
                <p class="text-gray-700 max-w-2xl mx-auto">
                    Pipeline completo de Retrieval-Augmented Generation
                </p>
            </div>
            
            <div class="grid md:grid-cols-5 gap-4 items-center">
                <!-- Step 1 -->
                <div class="bg-white border-2 border-black p-6 shadow-neo text-center">
                    <div class="w-12 h-12 bg-teal border-2 border-black flex items-center justify-center mx-auto mb-4 font-heading text-xl">
                        1
                    </div>
                    <h3 class="font-heading text-lg mb-2">Upload</h3>
                    <p class="text-xs text-gray-600">Envie seus PDFs e documentos</p>
                </div>
                
                <!-- Arrow -->
                <div class="hidden md:block text-center">
                    <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
                
                <!-- Step 2 -->
                <div class="bg-white border-2 border-black p-6 shadow-neo text-center">
                    <div class="w-12 h-12 bg-yellow border-2 border-black flex items-center justify-center mx-auto mb-4 font-heading text-xl">
                        2
                    </div>
                    <h3 class="font-heading text-lg mb-2">Chunking</h3>
                    <p class="text-xs text-gray-600">Texto extraído e dividido</p>
                </div>
                
                <!-- Arrow -->
                <div class="hidden md:block text-center">
                    <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
                
                <!-- Step 3 -->
                <div class="bg-white border-2 border-black p-6 shadow-neo text-center">
                    <div class="w-12 h-12 bg-magenta border-2 border-black flex items-center justify-center mx-auto mb-4 font-heading text-xl">
                        3
                    </div>
                    <h3 class="font-heading text-lg mb-2">Embedding</h3>
                    <p class="text-xs text-gray-600">Vetores gerados localmente</p>
                </div>
                
                <!-- Arrow -->
                <div class="hidden md:block text-center">
                    <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
                
                <!-- Step 4 -->
                <div class="bg-white border-2 border-black p-6 shadow-neo text-center col-span-1 md:col-span-5 max-w-md mx-auto mt-8">
                    <div class="w-12 h-12 bg-teal border-2 border-black flex items-center justify-center mx-auto mb-4 font-heading text-xl">
                        4
                    </div>
                    <h3 class="font-heading text-lg mb-2">Chat com IA</h3>
                    <p class="text-xs text-gray-600">Pergunte e receba respostas com fontes</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Stack -->
    <section class="py-16 bg-cream-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl text-black mb-4">Stack Tecnológica</h2>
            </div>
            
            <div class="flex flex-wrap justify-center gap-6">
                <!-- Laravel -->
                <div class="bg-white border-2 border-black px-6 py-3 shadow-neo flex items-center gap-3">
                    <span class="font-heading text-lg">Laravel 13</span>
                </div>
                
                <!-- Filament -->
                <div class="bg-white border-2 border-black px-6 py-3 shadow-neo flex items-center gap-3">
                    <span class="font-heading text-lg">Filament 5</span>
                </div>
                
                <!-- Livewire -->
                <div class="bg-white border-2 border-black px-6 py-3 shadow-neo flex items-center gap-3">
                    <span class="font-heading text-lg">Livewire 4</span>
                </div>
                
                <!-- PostgreSQL -->
                <div class="bg-white border-2 border-black px-6 py-3 shadow-neo flex items-center gap-3">
                    <span class="font-heading text-lg">PostgreSQL</span>
                </div>
                
                <!-- Ollama -->
                <div class="bg-white border-2 border-black px-6 py-3 shadow-neo flex items-center gap-3">
                    <span class="font-heading text-lg">Ollama</span>
                </div>
                
                <!-- Tailwind -->
                <div class="bg-white border-2 border-black px-6 py-3 shadow-neo flex items-center gap-3">
                    <span class="font-heading text-lg">Tailwind</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-teal">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl text-black mb-6">
                Pronto para Transformar seu Conhecimento?
            </h2>
            <p class="text-black text-lg mb-8 max-w-2xl mx-auto">
                Comece a usar NandoRAG hoje mesmo. Totalmente gratuito e de código aberto.
            </p>
            <a href="http://localhost:8000/admin" class="btn-neo bg-white px-10 py-5 text-xl inline-block">
                Acessar Painel Admin
                <svg class="inline-block w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t-2 border-black py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-teal border-2 border-black flex items-center justify-center">
                        <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <span class="font-heading">NandoRAG</span>
                </div>
                <div class="text-sm text-gray-600">
                    &copy; 2026 NandoRAG. Open Source.
                </div>
                <div class="flex gap-4">
                    <a href="https://github.com/nandinhos/nandorag" class="text-sm hover:underline">GitHub</a>
                    <a href="#" class="text-sm hover:underline">Documentação</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
