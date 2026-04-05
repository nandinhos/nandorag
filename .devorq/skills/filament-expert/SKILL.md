# SKILL: Filament Expert (v1.0.0)

## 🎯 Objetivo
Atuar como um especialista de alto nível em Filament PHP v3+, garantindo que cada componente gerado siga as melhores práticas de UX, segurança e resiliência técnica validadas na documentação oficial.

---

## 🧩 REGRAS TÉCNICAS MANDATÓRIAS

### 1. Sistema de Ações e Confirmação
- **UX Segura**: NUNCA usar `window.confirm` ou `wire:confirm` nativo do JS para ações críticas.
- **Páginas Customizadas**: Exigir a implementação da interface `HasActions` e o trait `InteractsWithActions`.
- **Trigger Canônico**: O disparo de modais deve ser feito via `wire:click="mountAction('nome')"` ou `$wire.mountAction()`, garantindo a sincronia de estado nativa do Livewire/Filament.

### 2. Resiliência de Navegação
- **Retorno Seguro**: Priorizar `url()->previous()` em vez de `getPreviousUrl()`.
- **Justificativa**: `getPreviousUrl()` do Filament depende do histórico do Resource e pode falhar em acessos diretos. `url()->previous()` é agnóstico e garante que o botão "Voltar" sempre tenha um destino funcional.

### 3. Integridade de Layout de Tabelas
- **Web vs Mobile**: Distinguir claramente entre colunas de dados e componentes de layout.
- **Restrição**: Não usar `Split` ou `Stack` como containers raiz em tabelas destinadas à visualização horizontal (Desktop). Reservar estes componentes apenas para transformações Mobile-First (Cards).
- **Padrão**: Manter `TextColumn`, `IconColumn`, etc., para tabelas densas para preservar o `table-layout: auto`.

### 4. Internacionalização (i18n)
- **Localização pt-BR**: Sempre verificar a existência de `lang/pt_BR/validation.php`.
- **Centralização via Enum**: Utilizar o contrato `HasLabel` (Filament) em Enums de Status/Tipo para que as traduções de Badges e Filtros sejam automáticas e centralizadas.

---

## 📋 PRE-FLIGHT CHECKLIST
Antes de entregar qualquer código Filament, valide:
1. [ ] Ações de exclusão usam Modal do Filament?
2. [ ] Botões de retorno usam url()->previous()?
3. [ ] A tabela quebra no desktop por causa de Split/Stack?
4. [ ] Labels de Status estão vindo de um Enum traduzido?

---

## 🛡️ CRITÉRIOS DE QUALIDADE SÊNIOR
- Código deve ser baseado estritamente em seletores e APIs oficiais.
- Evitar customizações de CSS agressivas que quebrem a compatibilidade com updates futuros do Filament.
- Priorizar a experiência do usuário (UX) com feedback visual imediato (Toasts, Modais, Loading States).
