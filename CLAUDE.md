# ProvControl — Regras de Desenvolvimento

Sistema de fechamento de caixa para postos de combustível. Laravel MVP pragmático — **não é DDD**.

> Regras completas do agente: [AGENTS.md](AGENTS.md) · Arquitetura canônica: [provcontrol-architecture](.cursor/skills/provcontrol-architecture/SKILL.md)

## Antes de tocar em qualquer PHP, Blade, rota, migration, seeder ou teste

1. Leia [.cursor/skills/provcontrol-architecture/SKILL.md](.cursor/skills/provcontrol-architecture/SKILL.md) — camadas, proibições, checklist
2. Leia as regras relevantes de [.cursor/skills/laravel-best-practices/](.cursor/skills/laravel-best-practices/SKILL.md) — só o que você vai tocar (`rules/eloquent.md`, `rules/validation.md`, `rules/testing.md`, …)
3. Blade: [.claude/skills/blade/SKILL.md](.claude/skills/blade/SKILL.md)
4. Modelo de dados real: [database/migrations/](database/migrations/) e [app/Models/](app/Models/)

Use o MCP `laravel-boost`: `search-docs` antes de codar, `database-schema` antes de migration/model.

## Fonte de verdade do domínio: a planilha do Posto Jorro

Toda regra de negócio vem de [database/data/Posto-Jorro-2026.xlsx](database/data/Posto-Jorro-2026.xlsx) — a planilha real que o posto usa. **Em dúvida sobre cálculo, nomenclatura ou estrutura, a planilha decide**, não a intuição.

| Aba | Conteúdo | No sistema? |
|---|---|---|
| `Mes, 01.` … `Mes, 12.` | Caixa diário: encerrantes, preço/L, venda por bico, pagamentos eletrônicos, venda por frentista | ✅ mês 01 apenas |
| `POSTO JORRO 2026` | Resumo mensal: lucro/L, lucro por bico, margem %, compra, custo, estoque | ❌ |
| `AFERICAO` | Aferição de bicos por mês, faixa de dias, alta/baixa | ❌ |
| `-26` | Empréstimos e parcelas: total, a pagar, pago, parcela por mês | ❌ |

Só a aba do mês 01 foi exportada para [database/data/mes_01.csv](database/data/mes_01.csv), que alimenta o [PlanilhaJorroImporter](app/Services/PlanilhaJorroImporter.php) → [FechamentoPlanilhaSeeder](database/seeders/FechamentoPlanilhaSeeder.php). Os outros 11 meses e as 3 abas auxiliares ainda **não** estão modelados — não invente essas tabelas sem o usuário pedir.

Nomes na planilha vêm com grafia irregular (`G,C. Bico 01`, `DS:.10`, `Ds:.500`, `Cartao,C.`). O mapeamento fiel está nas constantes `BICO_COMBUSTIVEL` e `PAGAMENTO_NOMES` do importer — atualize lá, não espalhe `str_replace` pelo código.

## Stack fixa (não trocar sem aprovação)

PHP 8.5 · Laravel 13 · MySQL 8 em **todos** os ambientes · PHPUnit 12 · Blade + Alpine.js + Chart.js + Tailwind 4 + Vite.

## Tudo roda dentro do Sail

O repositório é o subdiretório `ProvControl/`. O PHP do host é 8.3 e **não serve** — nunca rode `php`/`composer`/`npm` direto.

```bash
vendor/bin/sail up -d
vendor/bin/sail artisan test --compact --filter=NomeDoTeste
vendor/bin/sail bin pint --dirty --format agent    # obrigatório após editar PHP
vendor/bin/sail npm run build
```

## Camadas permitidas

```
Form Request → Controller → Service/Model → View | JSON
```

- **Controller** — fino: autoriza (`Gate::authorize`), chama service, retorna. Dependências por property promotion `private readonly`. Nunca `$request->validate()` inline.
- **Form Request** — um por operação (`StoreFechamentoRequest`, `PreviewFechamentoRequest`, …). Mensagens em português.
- **Service** — caso de uso gordo ou reutilizado. Passou de ~200 linhas → extrair métodos privados, **não** Actions.
- **Model** — relacionamentos, casts, scopes explícitos. Regra trivial (`litros = final - inicial`) pode ficar como accessor.

`declare(strict_types=1);` em todo PHP tocado. Tipos explícitos em parâmetros e retornos.

## Multi-posto: escopo sempre explícito

Sem Global Scope, sem `PostoContext`, sem contexto implícito.

```php
$fechamentos = Fechamento::query()
    ->forPosto(currentPostoId())   // trait BelongsToPosto
    ->get();
```

Middleware [SetCurrentPosto](app/Http/Middleware/SetCurrentPosto.php) define o posto ativo · [currentPostoId()](app/helpers.php#L8) lê da sessão · Policies também barram recurso de outro posto.

## Dinheiro e litros: inteiros escalados

Nunca use `float` para valor monetário ou volume. Use [DecimalCalculator](app/Services/DecimalCalculator.php) e a trait [InteractsWithScaledDecimals](app/Models/Concerns/InteractsWithScaledDecimals.php) — os valores circulam como inteiros escalados e só viram string na borda (`formatMoney()`).

## Fórmula canônica do fechamento

- O total informado pelos frentistas é **declaratório** e fica separado do valor conferido
- Recebimentos eletrônicos **já compõem** o valor conferido — não somar de novo
- `diferenca_geral = total_concentrador - total_conferido`
- A fórmula vive no [FechamentoService](app/Services/FechamentoService.php). Blade/Alpine/JS **não recalculam** — só fazem fetch e formatam.

## Proibido no MVP

`app/Actions/` · `app/DTOs/` · Repositories · wrapper `ApiResponse` · Global Scope de posto · `PostoContext` · regra de negócio duplicada em JS · Larastan/Livewire.

Não crie pasta base nova nem adicione dependência sem aprovação. Não crie arquivo de documentação sem o usuário pedir.

## Rotas

Explícitas em [routes/web.php](routes/web.php) — nunca `Route::resource` sem implementar todos os métodos (rota fantasma quebra o `RotasAplicacaoTest`). Links sempre via `route()`.

## Testes

PHPUnit, não Pest. Maioria feature. Use factories e seus states. Cobrir happy path, falha e borda.

```bash
vendor/bin/sail artisan test --compact --filter=FechamentoDiarioTest
```

Não remova testes existentes sem aprovação. Ao terminar a feature, pergunte se o usuário quer rodar a suíte inteira.

> ✅ Os testes de `Fechamento` estão verdes (2026-07-24). As expectativas foram realinhadas ao seed do mês inteiro. Atenção: o **Dia 01 é balanceado** (informado == conferido); a falta de caixa real da planilha está no **Dia 02** (Filip: informado 3443.02 × conferido 3446.49 → -3.47), por isso `test_total_conferido_preserva_falta_informada_na_planilha` usa o Dia 02.

## Checklist antes de entregar

- [ ] Skill de arquitetura lida
- [ ] Form Request criado/atualizado · `declare(strict_types=1)` · property promotion
- [ ] Query operacional com `forPosto(currentPostoId())` ou Policy
- [ ] Rota explícita e linkada no menu responde 200
- [ ] JS da feature só na página que o usa (`@push('vite')`), fora do `app.js` global
- [ ] `vendor/bin/sail bin pint --dirty --format agent`
- [ ] `vendor/bin/sail artisan test --compact --filter=<TesteRelacionado>`

## Comunicação

Responder em **português (pt-BR)**. Ser conciso — foco no que importa, sem explicar o óbvio.
