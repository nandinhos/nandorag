# SPEC - Definição da Skill: filament-expert

## 🎯 Objetivo
Transformar o orquestrador em um especialista de alto nível em Filament PHP v3+, garantindo que cada componente gerado siga as melhores práticas de UX, segurança e resiliência técnica validadas na documentação oficial.

---

## 🧩 REGRAS TÉCNICAS GLOBAIS (O "CÉREBRO" DA SKILL)

### 1. Sistema de Ações e Confirmação
- **Mandato**: NUNCA usar `window.confirm` ou `wire:confirm` nativo do JS para ações críticas.
- **Implementação**: Em páginas customizadas, exigir a implementação da interface `HasActions` e o trait `InteractsWithActions`.
- **Trigger**: O disparo de modais deve ser feito via `wire:click="mountAction('nome')"` ou `$wire.mountAction()`, garantindo sincronia de estado com o Livewire.

### 2. Resiliência de Navegação
- **Mandato**: Priorizar `url()->previous()` em vez de `getPreviousUrl()`.
- **Justificativa**: `getPreviousUrl()` do Filament é dependente do contexto do Resource e pode falhar em navegações profundas ou páginas customizadas. `url()->previous()` é agnóstico e resiliente.

### 3. Integridade de Layout de Tabelas
- **Alerta de Conflito**: O uso de componentes `Split` ou `Stack` em Tabelas Filament altera o `display` do container para `block/grid`, o que impede a visualização horizontal (web) clássica.
- **Recomendação**: Usar colunas padrão (`TextColumn`, `IconColumn`) para tabelas de dados densos e reservar `Split/Stack` apenas para layouts tipo "Cards" ou Mobile-Only.

### 4. Setup Inicial de Projetos pt-BR
- **Requisito**: Sempre verificar a existência de `lang/pt_BR/validation.php`.
- **Padrão**: Utilizar o contrato `HasLabel` em Enums para que as traduções de Badges sejam automáticas e centralizadas.

---

## 🛠️ ARTEFATOS DA SKILL
- **Localização**: `.devorq/skills/filament-expert/`
- **Arquivos**:
  - `SKILL.md`: As instruções de sistema para o LLM.
  - `PROMPTS/`: Templates para geração de Resources, Pages e Widgets.
  - `CHECKLIST.md`: Validações de pré-flight específicas para Filament.

---

## ✅ CRITÉRIOS DE ACEITE DA SKILL
- [ ] O agente deve recusar a criação de exclusão sem modal Filament.
- [ ] O agente deve sugerir automaticamente a tradução via Enums.
- [ ] O agente deve detectar e alertar sobre o uso indevido de Split em tabelas web.

---

## 📅 PRÓXIMOS PASSOS
1. Criar o diretório da skill em `.devorq/skills/filament-expert`.
2. Implementar o `SKILL.md` com as diretrizes acima.
3. Vincular a skill ao agente `filament` em `.devorq/agents/filament/`.
