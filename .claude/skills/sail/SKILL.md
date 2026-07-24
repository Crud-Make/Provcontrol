---
name: sail
description: Gerencia containers Docker e Laravel Sail do ProvControl — subir/parar/reiniciar containers, ver logs, entrar no PHP ou MySQL, diagnosticar problemas de Docker e rodar comandos artisan/composer/npm dentro do Sail. Use ao trabalhar com o ambiente Docker do ProvControl ("sobe o sail", "ver os containers", "entrar no mysql", "container com erro").
---

# Sail & Docker — ProvControl

Especialista em Docker e Laravel Sail para o ProvControl.

**Raiz do projeto:** `~/Documentos/Posto-Providencia/ProvControl` — rode os comandos a partir daí.
**`sail`** = alias para `./vendor/bin/sail`. Se `sail: command not found`, use `./vendor/bin/sail` ou `source ~/.bashrc`.

## Fluxo padrão ao invocar esta skill

1. Verifique o que está rodando: `docker ps` e `./vendor/bin/sail ps`.
2. Reporte o status de cada serviço (Up / erro / porta) e as URLs de acesso.
3. Se algo estiver com problema, siga a seção **Resolvendo problemas**.

## Comandos básicos

```bash
sail up -d          # sobe containers em segundo plano (detached)
sail down           # para containers e remove a rede
sail down -v        # para e remove também os volumes (apaga dados!)
sail ps             # lista containers do projeto (nome, status, portas)
sail restart        # reinicia todos os serviços
sail logs           # logs de todos os serviços
sail logs mysql     # logs de um serviço específico (mysql, laravel.test, redis...)
```

## Entrar nos containers

```bash
sail shell          # abre bash no container PHP (laravel.test)
sail root-shell     # bash como root no container PHP
sail mysql          # abre o cliente MySQL (usuário: sail / senha: password)
sail tinker         # console interativo do Laravel
```

## Rodar comandos dentro do Sail

```bash
sail php artisan migrate
sail php artisan test
sail php artisan route:list
sail php artisan optimize:clear   # limpa todos os caches
sail composer install
sail npm install
sail npm run dev
```

## Comandos Docker diretos

Nome dos containers segue o padrão `provcontrol-<serviço>-1`.

```bash
docker ps -a                                   # todos os containers
docker images                                  # imagens locais
docker logs provcontrol-laravel.test-1         # logs de um container
docker exec -it provcontrol-laravel.test-1 bash
docker stats                                   # uso de CPU/memória
```

## Serviços do ProvControl

| Serviço        | Container                     | Imagem                        | Porta(s)      | Função              |
|----------------|-------------------------------|-------------------------------|---------------|---------------------|
| laravel.test   | provcontrol-laravel.test-1    | sail-8.5/app                  | 80, 5173      | PHP + Laravel + Vite|
| mysql          | provcontrol-mysql-1           | mysql/mysql-server:8.0        | 3306          | Banco de dados      |
| phpmyadmin     | provcontrol-phpmyadmin-1      | phpmyadmin/phpmyadmin         | 8080          | Gerenciar o banco   |
| redis          | provcontrol-redis-1           | redis:alpine                  | 6379          | Cache / filas       |
| mailpit        | provcontrol-mailpit-1         | axllent/mailpit               | 1025, 8025    | Captura de e-mails  |
| meilisearch    | provcontrol-meilisearch-1     | getmeili/meilisearch          | 7700          | Busca               |
| selenium       | provcontrol-selenium-1        | selenium/standalone-chromium  | —             | Testes de browser   |

## URLs dos serviços

| Serviço        | URL                       | Credencial      |
|----------------|---------------------------|-----------------|
| Site           | http://localhost          | —               |
| Vite (dev)     | http://localhost:5173     | —               |
| PHPMyAdmin     | http://localhost:8080     | sail / password |
| Mailpit        | http://localhost:8025     | —               |
| Meilisearch    | http://localhost:7700     | —               |

## Checklist "está tudo funcionando?"

```bash
docker ps                          # 1. Docker rodando
sail ps                            # 2. Containers do projeto Up
sail php artisan --version         # 3. PHP/Laravel respondem
sail mysql -e "SELECT 1"           # 4. MySQL responde
curl -I http://localhost           # 5. Site respondendo
```

## Resolvendo problemas comuns

**`sail: command not found`**
```bash
./vendor/bin/sail up -d      # usa o caminho completo
source ~/.bashrc             # ou recarrega o alias
```

**`permission denied` (Docker)**
```bash
sudo usermod -aG docker $USER   # depois faça logout/login
```

**Docker não está rodando**
```bash
sudo systemctl status docker
sudo systemctl start docker
```

**`port already in use`** (ex.: 80 ou 8080)
```bash
sudo lsof -i :80             # descubra quem usa a porta
sudo systemctl stop apache2  # pare o serviço conflitante
```
Alternativa: mude `APP_PORT` no `.env` e rode `sail up -d`.

**Container quebrado / recriar do zero**
```bash
sail down
sail up -d --build
```

**Reset total (⚠️ apaga o banco)** — confirme com o usuário antes:
```bash
sail down -v
sail up -d --build
sail php artisan migrate:fresh --seed
```

## Notas

- Mudanças no **código PHP/JS** são sincronizadas automaticamente — não precisa reiniciar containers.
- Mudanças no **`docker-compose.yml`** exigem `sail down && sail up -d --build`.
- Antes de qualquer comando `artisan`, garanta que os containers estão Up (`sail ps`; se não, `sail up -d`).
