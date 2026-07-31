# SPEC-007: Plano de Estabilização e Normalização

---
id: SPEC-2026-04-09-007
title: Estabilização e Normalização do Ambiente Docker
domain: operacao
status: completed
priority: high
owner: team-core
created_at: 2026-04-09
updated_at: 2026-04-09
source: devorq
related_tasks: []
related_files: ["Dockerfile", "docker-compose.yml", ".env", "docs/specs/006-docker-deployment.md"]
---

## 1. Objetivo
Estabilizar a aplicação NandoRAG no ambiente Docker, corrigindo falhas de conexão, dependências ausentes e alinhando o código com as especificações oficiais.

## 2. Diagnóstico Atual
- **Erro 500**: Detectado nos logs de acesso. Provável causa: `DB_HOST` incorreto (`127.0.0.1` em vez de `db`) e ausência da extensão `redis`.
- **Horizon Offline**: O container `horizon` encerra (Exit 0) por falta da extensão PHP `redis`.
- **Inconsistência**: Discrepância entre o que o `laravel-boost` detecta (MySQL) e o que está configurado (PostgreSQL/pgvector).
- **Segurança**: `APP_KEY` exposta no `docker-compose.yml`.

## 3. Plano de Ação (FAZER)

### Fase 1: Ajuste de Infraestrutura
1.  **Atualizar Dockerfile**:
    - Instalar extensão `redis` via PECL ou pacotes.
    - Garantir instalação de `libpq-dev` para PostgreSQL.
2.  **Ajustar docker-compose.yml**:
    - Remover `APP_KEY` hardcoded (usar do `.env`).
    - Adicionar o serviço `horizon` corretamente com dependências.
    - Configurar rede interna unificada.

### Fase 2: Normalização de Configurações
1.  **Configurar .env.docker**:
    - Criar arquivo dedicado para o Docker baseado no `docs/specs/006-docker-deployment.md`.
    - Garantir `DB_HOST=db`, `REDIS_HOST=redis` e `OLLAMA_BASE_URL=http://host.docker.internal:11434`.
2.  **Sincronizar DB**:
    - Garantir que todas as migrações (Batch 1) foram refletidas e o banco está saudável.

### Fase 3: Validação
1.  Executar `./bin/devorq context` para validar detecção.
2.  Reiniciar containers: `docker compose up -d --build`.
3.  Verificar logs de erro: `docker compose logs -f app`.

## 4. NÃO FAZER
- NÃO alterar a lógica de RAG ou Chat nesta fase.
- NÃO modificar estilos CSS ou temas Filament.
- NÃO remover volumes de dados existentes do banco.

## 5. DONE CRITERIA
- [x] Aplicação responde com status 200 em `localhost:9090`.
- [x] Horizon está `Up` e processando jobs (verificar com `php artisan horizon:status`).
- [x] Nenhuma exceção de conexão com banco ou redis nos logs.
- [x] Suporte a PDF habilitado via `poppler-utils`.
- [x] Conectividade com Ollama via `host.docker.internal` (Linux Gateway).
- [x] Todos os arquivos de infraestrutura commitados.
