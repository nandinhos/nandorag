# SPEC-003: Otimização de Resiliência da IA

## 1. Contexto Técnico
O provedor local (Ollama) está sofrendo com timeouts de 60s em hardware local. O provedor MiniMax (Cloud) está estável. Precisamos aumentar a tolerância a falhas e o tempo de espera para o processamento local.

## 2. Mudanças de Arquitetura

### A. Configuração de Timeout
- Aumentar o timeout das requisições cURL para 180 segundos no `OllamaService`.

### B. Gestão de Provedores (laravel/ai)
- Garantir que as chaves do MiniMax estejam configuradas corretamente no `.env` e integradas ao `ChatAgent`.

### C. Fallback Strategy
- Implementar no `ChatAgent` um mecanismo de retentativa ou fallback automático para o MiniMax caso o Ollama exceda o tempo de resposta.

## 3. Plano de Implementação

### Fase 1: Ajuste de Timeout
1. Modificar `app/Services/OllamaService.php` para usar `Http::timeout(180)`.

### Fase 2: Configuração de Provedor
1. Validar as chaves de API do MiniMax no `config/ai.php`.

## 4. Estratégia de Testes
- Simular timeout no Ollama e validar se o sistema reporta o erro de forma mais amigável ou alterna para o MiniMax.
