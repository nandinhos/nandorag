# SPEC - Padronização Visual Filament (DNA Neo-Brutalist)

## 🎯 Objetivo
Uniformizar a interface do Filament utilizando o padrão "Header Amarelo" consolidado no Dashboard e otimizar a experiência mobile para visualização "No Scroll". Esta spec serve como base para a extração do futuro **KIT CUSTOM FILAMENT**.

---

## 🎨 O QUE IMPLEMENTAR (O DESIGN)

### 1. Header Global de Tabelas e Recursos
- **Cor de Fundo**: `bg-neo-yellow` (#FACC15).
- **Borda**: Inferior de `4px solid #000`.
- **Tipografia**: Título em `Oswald` (font-heading), Uppercase, Bold.
- **Sombra**: `shadow-neo` (4px 4px 0px 0px #000) aplicada ao container da tabela.

### 1.1 Feedback Visual de Seleção (Checkboxes)
- **Estado Inativo**: Fundo branco, borda preta de 3px, sombra `shadow-neo-xs`.
- **Estado Ativo (Checked)**: Fundo `bg-neo-teal`, exibição obrigatória de ícone de check (✓) centralizado.
- **Destaque de Linha**: A linha selecionada deve receber um overlay de `bg-neo-yellow` com 20% de opacidade para confirmação visual dupla.

### 3. Botões de Navegação e Ações de Formulário
- **Regra Geral**: Todo conteúdo de botão (texto e ícones/SVG) deve ser **Preto (#000)** para contraste máximo sobre as cores vibrantes.
  
- **Botão Voltar (Header)**:
  - **Estilo**: Fundo `bg-neo-purple` (#9370DB), Oswald, Uppercase.
  - **Ícone**: `Heroicon::ChevronLeft` (Cor: Preto).
  - **Sombra**: `shadow-neo-xs`.

- **Botão Cancelar (Footer/Ação)**:
  - **Estilo**: Fundo `bg-neo-magenta` (#E879F9), Oswald, Uppercase.
  - **Finalidade**: Interromper criação/edição.
  - **Mapeamento**: Corresponde à cor `danger` do sistema.

### 4. Módulo de Chat (Específico)
- **Botões de Ação (+ New Chat, Send, Create)**:
  - **Cor**: `bg-neo-green` (#00FF7F).
  - **Texto/Ícone**: Preto (#000).
  - **Finalidade**: Destacar as ações de entrada e criação de dados.

- **Navegação e Fluxo de Conversa**:
  - **Itens de Lista (Sidebar)**: Mantém `bg-neo-teal` (#22D3EE) quando selecionado.
  - **Mensagens do Usuário**: Utilizam `bg-neo-teal` (#22D3EE) com texto em negrito.
  - **Mensagens da IA / Fontes**: Utilizam fundo **Branco (#FFF)** para distinção clara do interlocutor.


---

## 🛠️ COMO IMPLEMENTAR (A TÉCNICA)

### 1. Globalização via CSS (Vite/Tailwind)
Em `resources/css/filament/admin/theme.css`, adicionaremos regras específicas para os seletores nativos do Filament v5:

```css
/* Customização Global de Headers de Tabela */
.fi-ta-header {
    background-color: var(--color-neo-yellow) !important;
    border-bottom: 4px solid var(--color-neo-black) !important;
}

.fi-ta-header-cell {
    background-color: transparent !important; /* Herda do pai amarelo */
    border-bottom: none !important;
}

/* Otimização Mobile No-Scroll */
@media (max-width: 767px) {
    .fi-ta-ctn {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }

    .fi-ta-content {
        display: block !important;
    }

    .fi-ta-table {
        display: block !important;
    }

    .fi-ta-row {
        display: block !important;
        margin-bottom: 1.5rem !important;
        background-color: white !important;
        border: 4px solid black !important;
        box-shadow: var(--shadow-neo) !important;
    }

    .fi-ta-cell {
        display: flex !important;
        justify-content: space-between !important;
        border: none !important;
        border-bottom: 1px solid rgba(0,0,0,0.1) !important;
        padding: 0.75rem !important;
    }
    
    .fi-ta-header-ctn {
        display: none !important; /* Esconde headers de coluna no mobile */
    }
}
```

### 2. Hook de Renderização no Provider
No `AdminPanelProvider.php`, utilizaremos os `RenderHooks` para injetar elementos de design system se necessário, ou configurar o `Panel` para desabilitar o scroll horizontal nativo via CSS injetado.

### 3. Extração do DNA (Futuro KIT)
- Criar um diretório `lib/filament-kit` onde as classes CSS serão organizadas por componentes.
- Preparar um arquivo `filament-kit.js` ou similar para facilitar a portabilidade dessas regras para outros projetos.

---

## ✅ CRITÉRIOS DE ACEITE
- [ ] Todas as tabelas de Resources (Documentos, Chats, etc.) devem exibir o header amarelo.
- [ ] No mobile, as tabelas devem se transformar em cards sem necessidade de scroll lateral.
- [ ] A tipografia Oswald deve ser aplicada consistentemente em todos os títulos de componentes.
- [ ] O código deve ser puramente baseado nos seletores nativos do Filament v5 para garantir compatibilidade.

---

## 📅 PRÓXIMOS PASSOS
1. Aplicar as alterações experimentais no `theme.css`.
2. Validar visualmente em telas desktop e mobile.
3. Ajustar o `AdminPanelProvider` para hooks globais de estilo.
