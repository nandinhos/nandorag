# NandoRAG - Arquitetura Técnica de Alto Nível

## 1. Visão Geral
O **NandoRAG** é uma ferramenta de Recuperação Aumentada por Geração (RAG) self-hosted. Ele permite que usuários importem documentos pessoais (PDF, MD, TXT), gerenciem o conhecimento via uma interface Filament e interajam com esses dados através de uma interface de chat moderna, utilizando modelos de IA locais via Ollama.

## 2. Stack Tecnológica
- **Backend**: Laravel 13 (PHP 8.4+)
- **Frontend Admin**: Filament 5 (TALL Stack)
- **Banco de Dados**: PostgreSQL + pgvector (Armazenamento de Embeddings)
- **IA/ML**: Ollama (nomic-embed-text para vetores, llama3.2:3b para chat)
- **Orquestração de IA**: `laravel/ai`
- **Testes**: Pest 4

## 3. Pipeline de Dados (RAG Flow)

### A. Ingestão (Import)
1. O usuário faz o upload via `DocumentResource` (Filament).
2. O `DocumentImportService` extrai o texto bruto.
3. Se for PDF, utiliza `smalot/pdfparser` ou `spatie/pdf-to-text`.

### B. Processamento (Chunking)
- **Serviço**: `ChunkingService`
- **Estratégia**: Fixed-size overlapping chunks.
- **Configuração**: 
    - Tamanho: 512 tokens (default).
    - Sobreposição (Overlap): 50 tokens.
- **Rastreabilidade**: Mapeia o chunk de volta à linha (L1-L10) ou página original do documento.

### C. Vetorização (Embedding)
- **Serviço**: `EmbeddingService`
- **Modelo**: `nomic-embed-text` (Ollama).
- **Dimensões**: 768.
- **Armazenamento**: Tabela `document_chunks` com coluna do tipo `vector(768)`.

### D. Recuperação (Retrieval)
- **Serviço**: `RagRetrievalService`
- **Algoritmo**: Cosine Similarity.
- **Threshold**: >= 0.5 (configurável).
- **Limite**: Top 10 chunks mais relevantes.

### E. Geração (Chat)
- **Agente**: `ChatAgent`
- **Modelo**: `llama3.2:3b`
- **Contexto**: Injeta os chunks recuperados no System Prompt para fundamentar a resposta da IA.

## 4. Auditoria de Contratos (Contracts & Abstractions)

### Estado Atual: **Acoplamento Forte**
Atualmente, o projeto não utiliza interfaces para seus serviços core. 
- `DocumentImportService` depende diretamente de classes concretas.
- `EmbeddingService` está amarrado à implementação do Ollama.

### Recomendação de Melhoria (Contratos)
Devemos introduzir uma camada `App\Contracts` para permitir o "Vendor Swap" no futuro (ex: trocar Ollama por OpenAI ou Pinecone):
1. `App\Contracts\EmbeddingEngine`: Interface para geração de vetores.
2. `App\Contracts\DocumentParser`: Interface para extração de texto de diferentes formatos.
3. `App\Contracts\VectorStore`: Interface para busca semântica.

## 5. Segurança e Conformidade
- **Segurança**: Arquivos de storage (`storage/app/documents`) devem ser protegidos contra acesso público direto.
- **Privacidade**: Todos os dados sensíveis permanecem no ambiente local do usuário (Ollama Local).
- **Validação**: O `ChunkingService` protege contra documentos vazios e erros de encoding.

## 6. Próximos Passos de Otimização
- [ ] Implementar Cache de Embeddings para evitar re-vetorização de chunks idênticos.
- [ ] Adicionar suporte a Reranking para melhorar a precisão da recuperação.
- [ ] Implementar suporte a OCR via Tesseract para PDFs baseados em imagem.
