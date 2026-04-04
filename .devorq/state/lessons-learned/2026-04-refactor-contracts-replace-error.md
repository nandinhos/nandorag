# Lição Aprendida - Refatoração de Alto Nível e Integridade de Substituição

## Contexto
- Quando: 2026-04-04 14:35
- Onde: `app/Services/EmbeddingService.php`
- Tipo: bug / improvement

## Problema
Ocorreu um `ParseError` (Unmatched `}`) após uma operação de `replace` múltipla no serviço core. O código ficou com fragmentos duplicados ao final do arquivo, interrompendo o pipeline de testes.

## Causa Raiz
As operações de `replace` cirúrgico falharam ao lidar com blocos de código com indentações complexas e comentários, resultando em uma substituição parcial e sobreposição de fechamento de classes.

## Solução
O arquivo foi restaurado e corrigido utilizando `write_file` com a versão completa e validada da refatoração (SPEC-001), eliminando as redundâncias.

## Prevenção
- [ ] **Regra em /quality-gate**: Executar `php -l [arquivo]` obrigatoriamente após qualquer operação de `replace`.
- [ ] **Prática Recomendada**: Utilizar `write_file` para refatorações estruturais (acima de 30% do arquivo) em vez de múltiplos `replace`.
- [ ] **Validação em /pre-flight**: Garantir que o ambiente de teste tenha os artefatos básicos (arquivos em storage) para evitar falsos negativos.

## Referências
- NandoRAG SPEC-001 / SPEC-002
- DEVORQ v2.1 Protocol
