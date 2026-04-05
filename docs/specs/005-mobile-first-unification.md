# SPEC-005: Unificação de Experiência Mobile-First & DNA Visual

## 1. Contexto e Análise Profunda
O **NandoRAG** possui dois estilos de tabela: uma customizada no Dashboard (Neo-Brutalista vibrante) e a nativa do Filament (padrão mais sóbrio). O objetivo desta fase é unificar o DNA visual, levando a estética "Yellow Header" para os componentes nativos, garantindo que em mobile a visualização mude completamente para evitar scroll horizontal.

## 2. Unificação do DNA Visual (Filament Native)

### A. Tabela Neo-Brutalista (Native Implementation)
- **Header**: O cabeçalho do recurso (`DocumentResource`) deve ser customizado via classes Tailwind para replicar o `bg-neo-yellow` e a borda grossa.
- **Badges**: Padronizar todos os badges de status para usarem as classes `neo-border-sm shadow-neo-xs`.
- **Typo**: Forçar `font-mono` para dados técnicos e `font-heading` para títulos dentro do Table Builder.

### B. Mobile "No-Scroll" Strategy (Stack Layout)
- Em dispositivos móveis, a tabela tradicional será ocultada.
- Utilizaremos o recurso `content()` ou `layout()` do Filament para renderizar os dados em formato de **Cards Empilhados** em telas pequenas.
- **DNA do Card**: Borda grossa, sombra dura, título do documento em destaque.

## 3. Plano de Implementação: Módulo de Documentos (v2)

### 3.1 Customização Estética (Filament Table)
- Aplicar `extraAttributes(['class' => 'neo-table'])` no Table Builder.
- Adicionar no `theme.css` a estilização global para `.neo-table` replicar o visual do Dashboard.

### 3.2 Layout Responsivo (Mobile Cards)
- Utilizar `Filament\Tables\Columns\Layout\Stack` ou `Split` para definir como os dados aparecem em telas pequenas.
- **Mobile View**: Card com [Extensão] [Nome] no topo, [Status] e [Chunks] alinhados abaixo.

## 4. Unificação de Experiência (Global)

### 4.1 Componente "Yellow Header"
- Extrair o header do Dashboard para um componente Blade reutilizável ou aplicar via `renderHook` no Filament.

## 5. Arquivos Afetados
- `app/Filament/Admin/Resources/Documents/Tables/DocumentsTable.php`
- `resources/css/filament/admin/theme.css`
- `resources/views/filament/admin/pages/dashboard.blade.php`
