---
name: fechamento-caixa
description: Especialista na feature de Fechamento de Caixa do ProvControl — os 3 blocos (concentrador, pagamentos eletrônicos, frentistas), fórmulas de cálculo, estrutura de código, fluxo preview/atualizar/fechar e depuração. Use ao mexer em fechamentos, leituras de bico, recebimentos, vendas de frentista ou nos totais e divergências ("por que a diferença deu X", "ajustar o preview", "criar fechamento").
---

# Fechamento de Caixa — ProvControl

Feature principal do sistema: substitui a planilha manual do Posto Jorro.

**Raiz:** `~/Documentos/Posto-Providencia/ProvControl` · tudo roda no Sail (`vendor/bin/sail ...`).
**Antes de editar PHP/Blade:** ler [CLAUDE.md](../../../ProvControl/CLAUDE.md) e a skill de arquitetura em `.cursor/skills/provcontrol-architecture/SKILL.md`.

## Fluxo real da feature

```
GET  /fechamentos/create   → escolhe data + turno
POST /fechamentos/preview  → Preparador cria rascunhos → Service calcula → JSON
     (operador digita encerrantes e valores na tela)
POST /fechamentos/atualizar→ persiste o que foi digitado → recalcula → JSON
POST /fechamentos          → prepara, recalcula e grava o Fechamento → redirect
GET  /fechamentos/{id}     → detalhes do fechamento gravado
```

`preview` e `atualizar` **não** gravam o fechamento — só materializam rascunhos e devolvem os números. Quem fecha é o `store`.

O [FechamentoPreparador](../../../ProvControl/app/Services/FechamentoPreparador.php) é **idempotente**: garante 1 leitura por bico ativo (com encerrante inicial herdado da última leitura conhecida), 1 venda por frentista ativo e 1 recebimento por forma de pagamento ativa. Fechamento nasce com status `aberto`.

## Os 3 blocos

### Bloco 1 — Venda Concentrador (bombas)

Model [Leitura](../../../ProvControl/app/Models/Leitura.php) · tabela `leituras` · uma linha por bico/turno/data.

| Campo no JSON | Origem |
|---|---|
| `produto` | `"{codigo do combustível} Bico {numero com 2 dígitos}"` |
| `leitura_inicial` / `leitura_final` | escala 3 (litros) |
| `litros_vendidos` | `final - inicial` |
| `preco_litro` | escala **4** |
| `valor_total` | `litros × preço` reescalado para 2 |
| `percentual` | `valor_total / total_concentrador` |

`Leitura::recalcular()` faz a conta e **lança `InvalidArgumentException` se `final < inicial`**. Leitura com status `rascunho` volta com `leitura_final` vazia (`''`) pro operador digitar.

### Bloco 2 — Pagamentos Eletrônicos

Model [Recebimento](../../../ProvControl/app/Models/Recebimento.php) · tabela `recebimentos` · relacionamento `formaPagamento`.

⚠️ **Existe um único campo `valor`.** Não há colunas "Inter pog"/"Bin" — se a planilha exibe duas colunas, isso não está modelado. No JSON, `valor` e `total` são o mesmo número (duplicados por compatibilidade com a view).

Taxas vêm de `formas_pagamento.taxa_percentual` (nunca hardcode):

| Forma | Tipo | Taxa |
|---|---|---|
| Cartão C | cartao | 0,70% |
| Cartão B | cartao | 2,50% |
| Pix | digital | — |
| APP Baratão | digital | 1,90% |
| APP Providência | digital | — |

`despesa = valor × taxa / 10.000` (arredondado), `0` quando a taxa é `null`. `percentual = valor / total_pagamentos`.

### Bloco 3 — Venda Frentistas

Model [FechamentoFrentista](../../../ProvControl/app/Models/FechamentoFrentista.php) · tabela `fechamento_frentistas`.

Métodos que compõem o **total informado** (declaratório, digitado pelo frentista):
`valor_pix` + `valor_cartao_credito` + `valor_cartao_debito` + `valor_moedas` + `valor_nota` + `valor_baratao` + `valor_produtos` + `valor_dinheiro`

`valor_cartao` é derivado (`credito + debito`) — não some duas vezes.

`valor_conferido` é o que **realmente entrou no caixa** e é campo separado. `atualizarValores()` grava `diferenca = falta_caixa = total_informado - valor_conferido` e define o status: `ok` quando zero, senão `divergente`.

## Fórmula canônica do fechamento

```
total_concentrador          = Σ leituras.valor_total
total_litros                = Σ leituras.litros_vendidos          (escala 3)
total_informado_frentistas  = Σ fechamento_frentistas.total_informado
total_frentistas            = Σ fechamento_frentistas.valor_conferido
diferenca                   = total_concentrador - total_frentistas
status                      = 'fechado' se diferenca == 0, senão 'divergente'
```

Três pegadinhas que já causaram bug:

1. **A ordem da subtração é `concentrador - conferido`**, não o inverso.
2. **`total_frentistas` usa `valor_conferido`**, não `total_informado`. O informado é declaratório e só serve para achar falta de caixa por frentista.
3. **Recebimentos eletrônicos já compõem o valor conferido** — somá-los de novo ao total dobra a receita.

`concentrador_x_frentista` e `diferenca` no JSON são o mesmo valor.

## Dinheiro e litros: inteiros escalados

Nunca `float`. Use [DecimalCalculator](../../../ProvControl/app/Services/DecimalCalculator.php) e a trait `InteractsWithScaledDecimals`. Escalas: **2** para dinheiro, **3** para litros/encerrantes, **4** para preço por litro e para o campo `taxa` do JSON.

A conta acontece em inteiros e só vira string na borda. Blade/Alpine/JS **não recalculam nada** — só fazem fetch e formatam.

## Estrutura de código

| Camada | Arquivos |
|---|---|
| Controller | [FechamentoController](../../../ProvControl/app/Http/Controllers/FechamentoController.php) — `index`, `create`, `preview`, `atualizar`, `store`, `show` |
| Form Requests | `PreviewFechamentoRequest`, `AtualizarFechamentoRequest`, `StoreFechamentoRequest` |
| Services | `FechamentoService` (cálculo + `fechar` + `atualizarDados`) · `FechamentoPreparador` (rascunhos) · `FechamentoMapper` (monta os 3 blocos do JSON) · `DecimalCalculator` · `PlanilhaJorroImporter` |
| Models | `Fechamento`, `FechamentoFrentista`, `Leitura`, `Recebimento`, `FormaPagamento`, `Frentista`, `Bico`, `Bomba`, `Combustivel`, `Turno`, `Posto`, `User` |
| Enums | `FechamentoStatus` (aberto/fechado/divergente) · `LeituraStatus` (rascunho/confirmado) · `FechamentoFrentistaStatus` (pendente/ok/divergente) |
| Views | `resources/views/fechamentos/{index,create,show}.blade.php` |
| Rotas | 6 rotas explícitas em `routes/web.php`, atrás de `auth` + `SetCurrentPosto` |

Multi-posto: toda query operacional passa por `->forPosto(currentPostoId())`; `index`/`create`/`show` também chamam `Gate::authorize`.

## Testes

```bash
vendor/bin/sail artisan test --compact --filter=Fechamento
```

| Arquivo | Cobre |
|---|---|
| `tests/Unit/FechamentoCalculoTest.php` | fórmulas e escalas |
| `tests/Unit/FechamentoTest.php` | model |
| `tests/Feature/FechamentoDiarioTest.php` | fluxo do dia |
| `tests/Feature/FechamentoNovoTest.php` | preparação de rascunhos |
| `tests/Feature/FechamentoEndpointContractTest.php` | contrato do JSON de `preview`/`atualizar` |
| `tests/Feature/FechamentoDadosReaisTest.php` | dados reais da planilha Jorro |

⚠️ Há 5 testes de `Fechamento` falhando por descompasso de fixture — o seeder importa o mês inteiro e os testes esperam um dia só. **Dívida conhecida, não regressão.**

Ao mudar o formato do JSON, atualize o `FechamentoEndpointContractTest` junto — ele é o contrato com o front.

## Depuração

| Sintoma | Onde olhar |
|---|---|
| Diferença com sinal invertido | ordem em `FechamentoService::calcularFechamento` (`concentrador - conferido`) |
| Total dos frentistas "alto demais" | provavelmente somando `total_informado` no lugar de `valor_conferido` |
| Centavo de diferença | arredondamento — confira a escala usada no `DecimalCalculator` |
| `InvalidArgumentException` no preview | encerrante final menor que o inicial |
| Bloco vazio no preview | rascunhos não criados: bico/frentista/forma de pagamento inativo ou de outro posto |
| Taxa errada | `formas_pagamento.taxa_percentual` no banco, não no código |
| View/rota não encontrada, erro 500 | `vendor/bin/sail artisan optimize:clear` |

```bash
vendor/bin/sail artisan route:list --path=fechamentos
vendor/bin/sail artisan tinker    # inspecionar Leitura/Recebimento/FechamentoFrentista
```

## Ao terminar

```bash
vendor/bin/sail bin pint --dirty --format agent
vendor/bin/sail artisan test --compact --filter=Fechamento
```

## Pontos ainda em aberto

- Divergência **não** exige observação hoje — `observacoes` é `nullable` no `StoreFechamentoRequest`. Se o usuário quiser essa trava, é mudança de regra.
- Só o mês 01 da planilha Jorro está importado; os outros 11 meses e as abas `AFERICAO`, `-26` e o resumo anual não estão modelados.
