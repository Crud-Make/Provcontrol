# ⛽ ProvControl

**Sistema de Gestão para Postos de Combustível**

---

## 🎯 O Que é o ProvControl?

O **ProvControl** é um sistema web desenvolvido para **automatizar o controle de caixa e vendas de postos de combustível**. Ele substitui planilhas manuais por um sistema integrado que gerencia:

- 📊 **Vendas do Concentrador** (bombas)
- 👨‍💼 **Vendas dos Frentistas**
- 💳 **Pagamentos Eletrônicos** (cartões, Pix, apps)
- 📅 **Fechamento Diário Automático**
- 📈 **Relatórios Gerenciais**
- 💰 **Cálculo de Participação de Lucro**

---

## 🚀 Tecnologias Utilizadas

| Tecnologia | Versão | Descrição |
|------------|--------|-----------|
| **Laravel** | 13.x | Framework PHP |
| **PHP** | 8.3 | Linguagem de programação |
| **MySQL** | 8.0 | Banco de dados |
| **Docker** | - | Containerização |
| **Laravel Sail** | - | Ambiente de desenvolvimento |
| **Tailwind CSS** | 3.x | Estilização |
| **Alpine.js** | 3.x | Interatividade |
| **PHPUnit** | - | Testes automatizados |
| **GitHub Actions** | - | CI/CD |

---

## 📊 Funcionalidades

### 1. Controle de Vendas do Concentrador
- Registro de leituras diárias de cada bomba
- Cálculo automático de litros vendidos
- Cálculo automático do valor total
- Histórico completo de leituras

### 2. Controle de Vendas dos Frentistas
- Registro de vendas por frentista
- Múltiplos métodos de pagamento (Pix, Cartão, Dinheiro, Notas)
- Acompanhamento individual de desempenho
- Cálculo de participação de lucro (0,3% sobre litros vendidos)

### 3. Gestão de Pagamentos Eletrônicos
- Controle de vendas por cartão (crédito/débito)
- Taxas automáticas (0,7% a 2,5%)
- Controle de vendas via Pix
- Integração com apps (Baratao, Providencia)

### 4. Fechamento Diário
- Cálculo automático do total do concentrador
- Soma das vendas dos frentistas
- Cálculo de diferença (falta/sobra)
- Geração de resumo diário

### 5. Relatórios e Métricas
- Dashboard com indicadores principais
- Participação de lucro dos frentistas
- Resumo mensal
- Metas de vendas

---

## 🏗️ Arquitetura

O sistema segue o padrão **MVC (Model-View-Controller)** com camadas adicionais:

📁 ProvControl/
├── app/
│ ├── Models/ # Representação das tabelas
│ ├── Http/ # Controllers e Requests
│ └── Services/ # Lógica de negócio
├── database/
│ ├── migrations/ # Estrutura do banco
│ └── seeders/ # Dados iniciais
├── tests/ # Testes automatizados
└── resources/views/ # Telas do sistema



### Padrões Utilizados
- ✅ **Repository Pattern** - Separação da lógica de dados
- ✅ **Service Layer** - Centralização da lógica de negócio
- ✅ **Action Classes** - Operações únicas e específicas
- ✅ **DTO (Data Transfer Object)** - Transferência de dados
- ✅ **TDD** - Desenvolvimento orientado a testes

---

## 📦 Instalação

### Pré-requisitos

- Docker
- PHP 8.3+
- Composer
- Node.js 18+

### Passo a Passo

```bash
# 1. Clonar o repositório
git clone https://github.com/Crud-Make/Provcontrol.git
cd Provcontrol

# 2. Subir os containers com Sail
./vendor/bin/sail up -d

# 3. Instalar dependências
./vendor/bin/sail composer install

# 4. Configurar o ambiente
cp .env.example .env
./vendor/bin/sail php artisan key:generate

# 5. Rodar as migrations
./vendor/bin/sail php artisan migrate

# 6. Rodar os testes
./vendor/bin/sail php artisan test

# 7. Acessar o sistema
# http://localhost



# Rodar todos os testes
./vendor/bin/sail php artisan test

# Rodar testes unitários
./vendor/bin/sail php artisan test --testsuite=Unit

# Rodar testes de funcionalidade
./vendor/bin/sail php artisan test --testsuite=Feature


🔄 CI/CD
O projeto utiliza GitHub Actions para integração e entrega contínua.

Fluxo Automatizado:
text
Push no GitHub
     ↓
Rodar Testes (PHPUnit)
     ↓
Análise de Código (Pint + Larastan)
     ↓
Build da Imagem Docker
     ↓
Deploy em Produção (SSH)
Configuração
Para habilitar o deploy, configure os seguintes secrets no GitHub:

Secret	Descrição
VPS_HOST	IP ou domínio do servidor
VPS_USER	Usuário SSH
SSH_PRIVATE_KEY	Chave privada SSH
📊 Estrutura do Banco de Dados
Tabela	Descrição
produtos	Tipos de combustível
bombas	Bombas do posto
leituras_concentrador	Leituras diárias das bombas
vendas_frentistas	Vendas registradas por frentista
fechamentos_diarios	Resumo do fechamento diário
Relacionamentos
text
produtos ───┬── bombas ───┬── leituras_concentrador
            │            │
            │            └── vendas_frentistas
            │
            └── fechamentos_diarios
📝 Comandos Úteis
bash
# Subir os containers
sail up -d

# Parar os containers
sail down

# Ver containers rodando
sail ps

# Entrar no container PHP
sail shell

# Entrar no MySQL
sail mysql

# Rodar testes
sail test

# Criar migration
sail php artisan make:migration nome

# Rodar migrations
sail php artisan migrate

# Limpar cache
sail php artisan cache:clear
📂 Estrutura do Projeto
text
ProvControl/
├── app/                          # Código principal
│   ├── Http/                     # Controllers, Requests
│   ├── Models/                   # Models do banco
│   └── Services/                 # Lógica de negócio
├── config/                       # Configurações
├── database/
│   ├── migrations/               # Estrutura das tabelas
│   └── seeders/                  # Dados iniciais
├── tests/
│   ├── Unit/                     # Testes unitários
│   └── Feature/                  # Testes de funcionalidade
├── resources/views/              # Telas (Blade)
├── routes/                       # Rotas
├── .github/workflows/            # CI/CD
├── Dockerfile.prod               # Build de produção
├── docker-compose.prod.yml       # Docker em produção
└── docker-compose.yml            # Docker em desenvolvimento
🤝 Como Contribuir
Fork o projeto

Crie uma branch para sua feature (git checkout -b feature/nova)

Commit suas mudanças (git commit -m 'feat: adiciona nova funcionalidade')

Push para a branch (git push origin feature/nova)

Abra um Pull Request

📝 Licença
Este projeto está sob a licença MIT.

👨‍💻 Autores
Crud-Make - Desenvolvedor

📧 Contato
GitHub: Crud-Make

Projeto: ProvControl

⭐ Agradecimentos
Laravel Framework

Comunidade Open Source

Posto Jorro (pelo suporte e dados reais)

Feito com ❤️ para automatizar e simplificar a gestão de postos de combustível.

text

---

## 🚀 DEPOIS DE CRIAR, ENVIAR PARA O GITHUB

```bash
# 1. Adicionar o README
git add README.md

# 2. Commit
git commit -m "docs: atualiza README com descrição completa do sistema"

# 3. Enviar
git push origin main
