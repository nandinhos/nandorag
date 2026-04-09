<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NandoRAG — Seu Assistente de IA para Conhecimento Pessoal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/landing.css'])
</head>
<body class="min-h-screen">

    {{-- ══ NAVBAR ══════════════════════════════════════════════════════════════ --}}
    <nav style="background:#fff;border-bottom:4px solid #000;position:sticky;top:0;z-index:50;box-shadow:0 4px 0 #000">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <div style="width:52px;height:52px;background:#22D3EE;border:4px solid #000;display:flex;align-items:center;justify-content:center;box-shadow:3px 3px 0 #000">
                        <svg class="w-7 h-7" fill="none" stroke="#000" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <span class="font-heading text-2xl tracking-widest uppercase">NandoRAG</span>
                </div>
                <div class="hidden md:flex items-center gap-5">
                    <a href="#features"    class="font-heading text-xs uppercase tracking-widest hover:text-neo-magenta transition-colors">Recursos</a>
                    <a href="#how-it-works" class="font-heading text-xs uppercase tracking-widest hover:text-neo-magenta transition-colors">Pipeline</a>
                    <a href="#stack"       class="font-heading text-xs uppercase tracking-widest hover:text-neo-magenta transition-colors">Stack</a>
                    <a href="/admin"       class="btn-neo bg-neo-teal px-6 py-3 text-xs font-heading uppercase tracking-widest"
                                          style="box-shadow:3px 3px 0 #000">Entrar →</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- ══ HERO ════════════════════════════════════════════════════════════════ --}}
    <section style="padding:5rem 0 4rem;overflow:hidden;position:relative">
        {{-- Floating shapes --}}
        <div class="float-animation" style="position:absolute;top:5rem;left:2rem;width:5rem;height:5rem;background:#FACC15;border:4px solid #000;box-shadow:4px 4px 0 #000;transform:rotate(12deg);opacity:.85"></div>
        <div class="float-animation" style="position:absolute;top:7rem;right:4rem;width:4rem;height:4rem;background:#E879F9;border:4px solid #000;box-shadow:4px 4px 0 #000;transform:rotate(-10deg);opacity:.85;animation-delay:.7s"></div>
        <div class="float-animation" style="position:absolute;bottom:4rem;left:25%;width:3rem;height:3rem;background:#22D3EE;border:4px solid #000;box-shadow:3px 3px 0 #000;transform:rotate(45deg);opacity:.7;animation-delay:1.4s"></div>
        <div class="float-animation" style="position:absolute;top:12rem;right:28%;width:2.5rem;height:2.5rem;background:#00FF7F;border:3px solid #000;box-shadow:2px 2px 0 #000;opacity:.6;animation-delay:2s"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-8 relative">
            <div class="text-center max-w-5xl mx-auto">

                {{-- Badge --}}
                <div style="display:inline-flex;align-items:center;gap:.75rem;background:#fff;border:4px solid #000;padding:.5rem 1.25rem;margin-bottom:2.5rem;box-shadow:4px 4px 0 #000">
                    <span style="width:.625rem;height:.625rem;background:#00FF7F;border:2px solid #000;display:inline-block;animation:pulse 2s infinite"></span>
                    <span class="font-heading text-xs tracking-widest uppercase">Powered by Ollama · 100% Local</span>
                </div>

                {{-- Headline --}}
                <h1 class="font-heading" style="font-size:clamp(3rem,9vw,6rem);line-height:1;margin-bottom:1.5rem;text-transform:uppercase;letter-spacing:.03em">
                    Seu Conhecimento,<br>
                    <span style="background:#22D3EE;padding:0 .25em;display:inline-block;transform:rotate(-.5deg);border:4px solid #000;box-shadow:6px 6px 0 #000">Potencializado</span><br>
                    por IA
                </h1>

                {{-- Sub --}}
                <p class="font-mono" style="font-size:.9rem;color:#333;max-width:36rem;margin:0 auto 2.5rem;line-height:1.7">
                    Transforme documentos em conhecimento conversacional.
                    NandoRAG é seu assistente RAG para consultas em bases pessoais de conhecimento.
                </p>

                {{-- CTAs --}}
                <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;margin-bottom:3.5rem">
                    <a href="/admin" class="btn-neo bg-neo-teal font-heading text-sm uppercase tracking-widest"
                       style="padding:.875rem 2.5rem;box-shadow:5px 5px 0 #000;display:inline-flex;align-items:center;gap:.5rem">
                        Começar Agora
                        <svg style="width:1.125rem;height:1.125rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#how-it-works" class="btn-neo bg-white font-heading text-sm uppercase tracking-widest"
                       style="padding:.875rem 2.5rem;box-shadow:5px 5px 0 #000">
                        Ver Pipeline
                    </a>
                </div>

                {{-- Hero stats — colored cards --}}
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.75rem;max-width:40rem;margin:0 auto">
                    <div style="background:#22D3EE;border:4px solid #000;padding:1.25rem .75rem;box-shadow:4px 4px 0 #000;text-align:center">
                        <div class="font-heading" style="font-size:2rem;line-height:1;font-weight:700">100%</div>
                        <div class="font-mono" style="font-size:.65rem;text-transform:uppercase;letter-spacing:.1em;margin-top:.25rem;opacity:.7">Local</div>
                    </div>
                    <div style="background:#FACC15;border:4px solid #000;padding:1.25rem .75rem;box-shadow:4px 4px 0 #000;text-align:center">
                        <div class="font-heading" style="font-size:2rem;line-height:1;font-weight:700">0€</div>
                        <div class="font-mono" style="font-size:.65rem;text-transform:uppercase;letter-spacing:.1em;margin-top:.25rem;opacity:.7">Custo API</div>
                    </div>
                    <div style="background:#E879F9;border:4px solid #000;padding:1.25rem .75rem;box-shadow:4px 4px 0 #000;text-align:center">
                        <div class="font-heading" style="font-size:2rem;line-height:1;font-weight:700">∞</div>
                        <div class="font-mono" style="font-size:.65rem;text-transform:uppercase;letter-spacing:.1em;margin-top:.25rem;opacity:.7">Privado</div>
                    </div>
                    <div style="background:#00FF7F;border:4px solid #000;padding:1.25rem .75rem;box-shadow:4px 4px 0 #000;text-align:center">
                        <div class="font-heading" style="font-size:2rem;line-height:1;font-weight:700">∞</div>
                        <div class="font-mono" style="font-size:.65rem;text-transform:uppercase;letter-spacing:.1em;margin-top:.25rem;opacity:.7">Docs</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- stripe --}}
    <div class="sep-stripe"></div>

    {{-- ══ FEATURES ════════════════════════════════════════════════════════════ --}}
    <section id="features" style="padding:5rem 0;background:#F0EAD6">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            {{-- title block --}}
            <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:3rem;flex-wrap:wrap;gap:1rem">
                <div>
                    <div style="display:inline-flex;align-items:center;gap:.5rem;margin-bottom:.75rem">
                        <span style="background:#000;color:#fff;font-family:'Oswald',sans-serif;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.125rem .5rem">01</span>
                        <span class="font-heading" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.15em">Recursos</span>
                    </div>
                    <h2 class="font-heading" style="font-size:clamp(2rem,5vw,3.5rem);text-transform:uppercase;letter-spacing:.03em;line-height:1">
                        O que o NandoRAG<br>oferece
                    </h2>
                </div>
                <p class="font-mono" style="font-size:.8rem;max-width:22rem;color:#444;line-height:1.7">
                    Tudo que você precisa para gerenciar<br>seu conhecimento com IA local.
                </p>
            </div>

            {{-- feature grid — each card gets a full colored bg --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(17rem,1fr));gap:1rem">

                {{-- 1 teal --}}
                <div style="background:#22D3EE;border:4px solid #000;padding:1.75rem;box-shadow:4px 4px 0 #000;transition:all .12s ease"
                     class="card-neo">
                    <div style="width:2.75rem;height:2.75rem;background:#000;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem">
                        <svg style="width:1.25rem;height:1.25rem" fill="none" stroke="#22D3EE" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-heading" style="font-size:1.25rem;text-transform:uppercase;margin-bottom:.5rem">Processamento de PDF</h3>
                    <p class="font-mono" style="font-size:.8rem;line-height:1.65;opacity:.75">
                        Upload e extração inteligente de texto com chunking otimizado e rastreamento de página.
                    </p>
                </div>

                {{-- 2 yellow --}}
                <div style="background:#FACC15;border:4px solid #000;padding:1.75rem;box-shadow:4px 4px 0 #000;transition:all .12s ease"
                     class="card-neo">
                    <div style="width:2.75rem;height:2.75rem;background:#000;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem">
                        <svg style="width:1.25rem;height:1.25rem" fill="none" stroke="#FACC15" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                        </svg>
                    </div>
                    <h3 class="font-heading" style="font-size:1.25rem;text-transform:uppercase;margin-bottom:.5rem">Embeddings Locais</h3>
                    <p class="font-mono" style="font-size:.8rem;line-height:1.65;opacity:.75">
                        Vetores gerados via Ollama com nomic-embed-text. Totalmente privado, nenhum dado externo.
                    </p>
                </div>

                {{-- 3 magenta --}}
                <div style="background:#E879F9;border:4px solid #000;padding:1.75rem;box-shadow:4px 4px 0 #000;transition:all .12s ease"
                     class="card-neo">
                    <div style="width:2.75rem;height:2.75rem;background:#000;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem">
                        <svg style="width:1.25rem;height:1.25rem" fill="none" stroke="#E879F9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="font-heading" style="font-size:1.25rem;text-transform:uppercase;margin-bottom:.5rem">Chat Inteligente</h3>
                    <p class="font-mono" style="font-size:.8rem;line-height:1.65;opacity:.75">
                        Converse com seus documentos usando LLMs locais como Llama 3.2, Qwen3 ou Mistral.
                    </p>
                </div>

                {{-- 4 green --}}
                <div style="background:#00FF7F;border:4px solid #000;padding:1.75rem;box-shadow:4px 4px 0 #000;transition:all .12s ease"
                     class="card-neo">
                    <div style="width:2.75rem;height:2.75rem;background:#000;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem">
                        <svg style="width:1.25rem;height:1.25rem" fill="none" stroke="#00FF7F" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                    </div>
                    <h3 class="font-heading" style="font-size:1.25rem;text-transform:uppercase;margin-bottom:.5rem">Vector Store</h3>
                    <p class="font-mono" style="font-size:.8rem;line-height:1.65;opacity:.75">
                        PostgreSQL + pgvector para busca semântica com similaridade coseno ≥ 0.5, top 10 chunks.
                    </p>
                </div>

                {{-- 5 salmon --}}
                <div style="background:#FDA4AF;border:4px solid #000;padding:1.75rem;box-shadow:4px 4px 0 #000;transition:all .12s ease"
                     class="card-neo">
                    <div style="width:2.75rem;height:2.75rem;background:#000;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem">
                        <svg style="width:1.25rem;height:1.25rem" fill="none" stroke="#FDA4AF" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="font-heading" style="font-size:1.25rem;text-transform:uppercase;margin-bottom:.5rem">Privacidade Total</h3>
                    <p class="font-mono" style="font-size:.8rem;line-height:1.65;opacity:.75">
                        Nenhum dado sai do seu servidor. 100% offline. Seus documentos ficam onde você controla.
                    </p>
                </div>

                {{-- 6 white / outline --}}
                <div style="background:#fff;border:4px solid #000;padding:1.75rem;box-shadow:4px 4px 0 #000;transition:all .12s ease"
                     class="card-neo">
                    <div style="width:2.75rem;height:2.75rem;background:#000;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem">
                        <svg style="width:1.25rem;height:1.25rem" fill="none" stroke="#fff" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                    </div>
                    <h3 class="font-heading" style="font-size:1.25rem;text-transform:uppercase;margin-bottom:.5rem">Admin Panel</h3>
                    <p class="font-mono" style="font-size:.8rem;line-height:1.65;opacity:.75">
                        Filament 5 totalmente personalizado com design neo-brutalist. CRUD, chat, ajuda em um só lugar.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="sep-stripe"></div>

    {{-- ══ PIPELINE / HOW IT WORKS ════════════════════════════════════════════ --}}
    <section id="how-it-works" style="padding:5rem 0;background:#fff">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div style="text-align:center;margin-bottom:3.5rem">
                <div style="display:inline-flex;align-items:center;gap:.5rem;margin-bottom:.75rem">
                    <span style="background:#000;color:#fff;font-family:'Oswald',sans-serif;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.125rem .5rem">02</span>
                    <span class="font-heading" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.15em">Pipeline</span>
                </div>
                <h2 class="font-heading" style="font-size:clamp(2rem,5vw,3.5rem);text-transform:uppercase;letter-spacing:.03em;line-height:1">Como Funciona</h2>
                <p class="font-mono" style="font-size:.8rem;color:#444;margin-top:.75rem">
                    Pipeline completo de Retrieval-Augmented Generation
                </p>
            </div>

            {{-- Steps — 4 cards, each fully colored, connected --}}
            <div style="display:grid;grid-template-columns:1fr auto 1fr auto 1fr auto 1fr;align-items:center;gap:.5rem">

                <div style="background:#22D3EE;border:4px solid #000;padding:1.5rem 1rem;box-shadow:4px 4px 0 #000;text-align:center">
                    <div style="width:3rem;height:3rem;background:#000;color:#22D3EE;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-family:'Oswald',sans-serif;font-size:1.5rem;font-weight:700">
                        1
                    </div>
                    <h3 class="font-heading" style="font-size:1.1rem;text-transform:uppercase;margin-bottom:.375rem">Upload</h3>
                    <p class="font-mono" style="font-size:.7rem;opacity:.75">Envie PDFs,<br>Markdown ou TXT</p>
                </div>

                <svg style="width:2rem;height:2rem;flex-shrink:0" fill="none" stroke="#000" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>

                <div style="background:#FACC15;border:4px solid #000;padding:1.5rem 1rem;box-shadow:4px 4px 0 #000;text-align:center">
                    <div style="width:3rem;height:3rem;background:#000;color:#FACC15;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-family:'Oswald',sans-serif;font-size:1.5rem;font-weight:700">
                        2
                    </div>
                    <h3 class="font-heading" style="font-size:1.1rem;text-transform:uppercase;margin-bottom:.375rem">Chunking</h3>
                    <p class="font-mono" style="font-size:.7rem;opacity:.75">512 tokens,<br>50 de overlap</p>
                </div>

                <svg style="width:2rem;height:2rem;flex-shrink:0" fill="none" stroke="#000" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>

                <div style="background:#E879F9;border:4px solid #000;padding:1.5rem 1rem;box-shadow:4px 4px 0 #000;text-align:center">
                    <div style="width:3rem;height:3rem;background:#000;color:#E879F9;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-family:'Oswald',sans-serif;font-size:1.5rem;font-weight:700">
                        3
                    </div>
                    <h3 class="font-heading" style="font-size:1.1rem;text-transform:uppercase;margin-bottom:.375rem">Embedding</h3>
                    <p class="font-mono" style="font-size:.7rem;opacity:.75">nomic-embed-text<br>768 dims</p>
                </div>

                <svg style="width:2rem;height:2rem;flex-shrink:0" fill="none" stroke="#000" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>

                <div style="background:#00FF7F;border:4px solid #000;padding:1.5rem 1rem;box-shadow:4px 4px 0 #000;text-align:center">
                    <div style="width:3rem;height:3rem;background:#000;color:#00FF7F;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-family:'Oswald',sans-serif;font-size:1.5rem;font-weight:700">
                        4
                    </div>
                    <h3 class="font-heading" style="font-size:1.1rem;text-transform:uppercase;margin-bottom:.375rem">Chat IA</h3>
                    <p class="font-mono" style="font-size:.7rem;opacity:.75">llama3.2:3b<br>com contexto RAG</p>
                </div>
            </div>
        </div>
    </section>

    <div class="sep-stripe"></div>

    {{-- ══ TECH STACK ════════════════════════════════════════════════════════ --}}
    <section id="stack" style="padding:4rem 0;background:#F0EAD6">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div style="text-align:center;margin-bottom:2.5rem">
                <div style="display:inline-flex;align-items:center;gap:.5rem;margin-bottom:.75rem">
                    <span style="background:#000;color:#fff;font-family:'Oswald',sans-serif;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.125rem .5rem">03</span>
                    <span class="font-heading" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.15em">Stack</span>
                </div>
                <h2 class="font-heading" style="font-size:2.5rem;text-transform:uppercase">Tecnologias</h2>
            </div>

            <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:.75rem">
                @php
                $techs = [
                    ['Laravel 13',    '#22D3EE'],
                    ['Filament 5',    '#FACC15'],
                    ['Livewire 4',    '#E879F9'],
                    ['PostgreSQL',    '#22D3EE'],
                    ['pgvector',      '#00FF7F'],
                    ['Ollama',        '#FDA4AF'],
                    ['Tailwind CSS',  '#FACC15'],
                    ['Alpine.js',     '#E879F9'],
                ];
                @endphp

                @foreach($techs as [$name, $color])
                <div class="btn-neo font-heading text-sm uppercase tracking-wider"
                     style="background:{{ $color }};border:3px solid #000;padding:.625rem 1.5rem;box-shadow:3px 3px 0 #000;
                            transition:all .1s ease;cursor:default">
                    {{ $name }}
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══ CTA ════════════════════════════════════════════════════════════════ --}}
    <section style="padding:5rem 0;background:#22D3EE;border-top:4px solid #000">
        <div class="max-w-4xl mx-auto px-4 sm:px-8 text-center">
            <div style="display:inline-flex;align-items:center;gap:.5rem;margin-bottom:1.5rem">
                <span style="background:#000;color:#22D3EE;font-family:'Oswald',sans-serif;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.125rem .5rem">04</span>
                <span class="font-heading" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.15em">Vamos Começar</span>
            </div>
            <h2 class="font-heading" style="font-size:clamp(2.5rem,7vw,5rem);text-transform:uppercase;line-height:1;margin-bottom:1.5rem">
                Pronto para<br>Transformar?
            </h2>
            <p class="font-mono" style="font-size:.9rem;max-width:30rem;margin:0 auto 2.5rem;line-height:1.7;opacity:.8">
                Comece a usar NandoRAG hoje mesmo. Totalmente gratuito, open source e 100% na sua máquina.
            </p>
            <a href="/admin" class="btn-neo bg-white font-heading text-sm uppercase tracking-widest"
               style="padding:1rem 3rem;box-shadow:6px 6px 0 #000;font-size:.875rem;display:inline-flex;align-items:center;gap:.75rem">
                Acessar o Painel
                <svg style="width:1.25rem;height:1.25rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </section>

    {{-- ══ FOOTER ════════════════════════════════════════════════════════════ --}}
    <footer style="background:#fff;border-top:4px solid #000;padding:2rem 0">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div style="display:flex;align-items:center;gap:.75rem">
                <div style="width:2.5rem;height:2.5rem;background:#22D3EE;border:3px solid #000;display:flex;align-items:center;justify-content:center;box-shadow:2px 2px 0 #000">
                    <svg style="width:1rem;height:1rem" fill="none" stroke="#000" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <span class="font-heading text-lg tracking-widest uppercase">NandoRAG</span>
            </div>
            <p class="font-mono text-xs" style="opacity:.5">&copy; 2026 NandoRAG · Open Source · Self-Hosted</p>
            <a href="https://github.com" class="btn-neo bg-white font-heading text-xs uppercase tracking-wider"
               style="padding:.375rem 1rem;box-shadow:2px 2px 0 #000">GitHub</a>
        </div>
    </footer>

</body>
</html>
