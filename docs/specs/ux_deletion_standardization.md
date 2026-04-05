# SPEC - Padronização de Experiência de Exclusão (UX)

## 🎯 Objetivo
Substituir alertas nativos de confirmação (ex: `window.confirm`) por modais de exclusão integrados ao Filament, garantindo uma experiência de usuário sênior, segura e visualmente coerente com o Design System Neo-Brutalist.

---

## 🎨 O QUE IMPLEMENTAR (REQUISITOS)

### 1. Componente de Confirmação
- **Tipo**: Modal de Confirmação do Filament.
- **Visual**:
  - **Título**: "Excluir [Recurso]" (ex: Excluir Conversa).
  - **Descrição**: "Tem certeza que deseja excluir este item? Esta ação não pode ser desfeita."
  - **Botão de Confirmação**: Cor `danger` (Magenta/Vermelho), Label "Confirmar Exclusão".
  - **Botão de Cancelamento**: Cor `gray` (Branco), Label "Cancelar".

### 2. Substituição no Módulo de Chat
- Remover o atributo `wire:confirm` do botão de exclusão na sidebar de conversas.
- Utilizar a API de `Actions` do Filament/Livewire para disparar um modal de confirmação antes de executar o método `deleteChat`.

---

## 🛠️ COMO IMPLEMENTAR (A TÉCNICA)

### 1. API de Actions do Filament (no Blade)
Em vez de um botão simples com `wire:click`, utilizaremos uma `Action` do Filament que suporta o método `requiresConfirmation()`:

```php
// No componente Livewire (Chats.php)
public function deleteAction(): Action
{
    return Action::make('delete')
        ->requiresConfirmation()
        ->modalHeading('Excluir Conversa')
        ->modalDescription('Tem certeza que deseja apagar esta conversa?')
        ->modalSubmitActionLabel('Confirmar Exclusão')
        ->color('danger')
        ->action(fn (array $arguments) => $this->deleteChat($arguments['chatId']));
}
```

---

## ✅ CRITÉRIOS DE ACEITE
- [ ] Nenhum alerta nativo do navegador deve ser disparado ao excluir itens.
- [ ] O modal de exclusão deve seguir o estilo visual do sistema (border radius zero, bordas pretas).
- [ ] A exclusão só deve ocorrer após a confirmação explícita no modal.

---

## 📅 PRÓXIMOS PASSOS
1. Configurar o suporte a `Actions` no componente `Chats.php`.
2. Atualizar a view `chats.blade.php` para renderizar a action de exclusão.
3. Validar visualmente o modal.
