---
name: setup-provcontrol
description: Configura e sobe o ambiente de desenvolvimento do ProvControl (Laravel + Docker + Sail). Use ao clonar o projeto, ao começar a trabalhar nele, ou quando os containers/banco precisarem ser reinicializados ("subir o ambiente", "resetar o banco", "preparar o ProvControl").
---

# Setup do ambiente ProvControl

Assistente especialista em Laravel para preparar o ambiente de desenvolvimento do **ProvControl** com Docker + Laravel Sail.

## Contexto do projeto

- Raiz do projeto: `~/Documentos/Posto-Providencia/ProvControl`
- Stack: Laravel 13, PHP 8.5, MySQL, Laravel Sail
- Todos os comandos `artisan`/`composer` rodam **dentro** do Sail.

## Passos

Execute na ordem. Antes de cada comando destrutivo (migrate:fresh), confirme com o usuário se o banco atual pode ser apagado.

1. **Ir para a raiz do projeto**
   ```bash
   cd ~/Documentos/Posto-Providencia/ProvControl
   ```

2. **Verificar Docker** — confirme que o daemon está rodando:
   ```bash
   docker ps
   ```
   Se falhar, oriente o usuário a iniciar o Docker Desktop / serviço antes de continuar.

3. **Verificar/criar `.env`** — se `.env` não existir, copie de `.env.example`:
   ```bash
   [ -f .env ] || cp .env.example .env
   ```

4. **Subir os containers**:
   ```bash
   ./vendor/bin/sail up -d
   ```
   (Se o alias `sail` já estiver configurado no shell, `sail up -d` funciona igual.)

5. **Verificar serviços** — todos os containers devem estar `Up`:
   ```bash
   ./vendor/bin/sail ps
   ```

6. **Instalar dependências** (só se `vendor/` estiver ausente ou desatualizado):
   ```bash
   ./vendor/bin/sail composer install
   ```

7. **Gerar a APP_KEY** (só se `APP_KEY=` estiver vazio no `.env`):
   ```bash
   ./vendor/bin/sail php artisan key:generate
   ```

8. **Rodar migrations + seeders** — ⚠️ `migrate:fresh` APAGA todos os dados. Confirme com o usuário antes:
   ```bash
   ./vendor/bin/sail php artisan migrate:fresh --seed
   ```
   Para preservar dados existentes, use apenas `sail php artisan migrate` no lugar.

9. **Rodar os testes** para validar o setup:
   ```bash
   ./vendor/bin/sail php artisan test
   ```

## Ao final

Reporte ao usuário: status dos containers, se as migrations rodaram, e o resultado dos testes (passou/falhou, com a saída real se houver falhas). A aplicação normalmente fica acessível em `http://localhost` (porta definida por `APP_PORT` no `.env`).

## Comandos úteis do dia a dia

```bash
./vendor/bin/sail up -d              # sobe o ambiente
./vendor/bin/sail down               # derruba o ambiente
./vendor/bin/sail php artisan migrate:fresh --seed   # reseta o banco
./vendor/bin/sail php artisan test   # roda os testes
./vendor/bin/sail php artisan tinker # console interativo
```
