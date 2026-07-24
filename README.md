# ProvControl

Sistema web para controle de caixa e vendas de postos de combustível.

## Escopo

- Leituras e vendas do concentrador.
- Valores informados e conferidos por frentista.
- Pagamentos eletrônicos e taxas.
- Fechamento diário e dashboard gerencial.
- Isolamento explícito por posto.

## Stack oficial

- PHP 8.5 e Laravel 13.
- MySQL 8.
- PHPUnit 12.
- Blade, Alpine.js 3, Tailwind CSS 4 e Vite 8.
- Docker com Laravel Sail.

## Arquitetura

A fonte oficial é a skill
[`provcontrol-architecture`](.cursor/skills/provcontrol-architecture/SKILL.md), resumida em
[`reference.md`](.cursor/skills/provcontrol-architecture/reference.md).

- Fluxo: Form Request → Controller → Model/Service → Response.
- Sem Actions, DTOs ou repositories no MVP.
- Auth web por sessão nativa do Laravel.
- Multi-posto por `currentPostoId()` e filtro/scope explícito.
- Sem Global Scope ou `PostoContext`.
- Modelo de dados: `database/migrations/` e `app/Models/`.
- Regras do agente: [`AGENTS.md`](AGENTS.md).

### Regras do fechamento

- `total_informado` soma os meios declarados pelo frentista.
- `valor_conferido` é mantido separado.
- Recebimentos eletrônicos não são somados novamente.
- Diferença geral = total do concentrador − total conferido.
- Dinheiro usa decimal no banco e inteiro/string decimal no PHP, nunca `float`.

## Instalação

Pré-requisitos: Docker, Composer e Node.js 22.

```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
npm ci
npm run build
```

A aplicação fica disponível em `http://localhost`.

O seeder cria o usuário local `admin@provcontrol.com` com senha `password`.
Troque essas credenciais fora do ambiente local.

## Gates de qualidade

```bash
./vendor/bin/sail artisan test
./vendor/bin/sail bin pint --test
npm run build
```

O workflow `.github/workflows/ci.yml` executa migrations no MySQL 8, PHPUnit,
Pint bloqueante e build Vite com PHP 8.5. Deploy e Larastan não fazem parte do
MVP atual.

## Dados principais

- `postos` e `posto_user`: acesso por usuário.
- `turnos`, `bombas`, `bicos`, `combustiveis` e `leituras`: operação.
- `fechamentos`, `fechamento_frentistas` e `recebimentos`: caixa diário.
- Toda consulta operacional aplica `posto_id` explicitamente.

## Contribuição

1. Crie uma branch curta por mudança.
2. Siga a skill `provcontrol-architecture` e o `AGENTS.md`.
3. Execute todos os gates de qualidade.
4. Descreva impacto, testes e migrations no pull request.

Projeto mantido por Crud-Make, com dados de referência do Posto Jorro.
