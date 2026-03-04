# Sistema de Controle Patrimonial

Sistema web para gestão de patrimônio organizacional — cadastro de bens, atribuição a funcionários via Termos de Responsabilidade e fluxo completo de chamados de solicitação.

## Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Back-end | Laravel 12 / PHP 8.2 |
| Front-end | Blade + Tailwind CSS v4 + Alpine.js |
| Build | Vite |
| Banco de Dados | MySQL |
| Autenticação | Laravel Breeze (sessão) |
| PDF | barryvdh/laravel-dompdf v3.1 |
| Gráficos | Chart.js v4.4 (CDN) |

## Funcionalidades

- **Dashboard** — KPIs em tempo real (patrimônios, funcionários, chamados abertos, responsabilidades ativas) e gráficos Chart.js de evolução mensal
- **Patrimônios** — CRUD completo com controle de status (`disponivel`, `em_uso`, `manutencao`)
- **Funcionários** — Cadastro vinculado à conta de usuário do sistema
- **Chamados** — Fluxo de solicitação → aprovação/negação → entrega; ao entregar, cria automaticamente um Termo de Responsabilidade
- **Termos de Responsabilidade** — Registro de vínculo patrimônio ↔ funcionário com geração de PDF
- **Controle de Acesso por Papel** — três níveis: `admin`, `gestor`, `funcionario`

## Papéis de Acesso

| Papel | Permissões |
|---|---|
| `admin` | Acesso total: CRUD de todos os módulos, aprovar/negar chamados |
| `gestor` | Igual ao admin, exceto gerenciamento de usuários |
| `funcionario` | Visualizar patrimônios, abrir e acompanhar próprios chamados |

## Instalação

### Pré-requisitos
- PHP 8.2+
- Composer
- Node.js 20+ / npm
- MySQL 8+

### Passos

```bash
# 1. Clonar o repositório
git clone <url-do-repositorio> controle-patrimonio
cd controle-patrimonio

# 2. Instalar dependências PHP
composer install

# 3. Instalar dependências JS e compilar assets
npm install && npm run build

# 4. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 5. Configurar banco de dados no .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=controle_patrimonio
# DB_USERNAME=seu_usuario
# DB_PASSWORD=sua_senha

# 6. Executar migrations e seeders
php artisan migrate --seed
```

## Credenciais de Teste (Seeders)

| Papel | E-mail | Senha |
|---|---|---|
| Admin | admin@patrimonio.test | password |
| Gestor | gestor@patrimonio.test | password |
| Funcionário | ana.silva@empresa.test | password |

> Os seeders criam 1 admin, 1 gestor e 10 funcionários com patrimônios e chamados de exemplo.

## Estrutura de Diretórios Relevante

```
app/
├── Http/
│   ├── Controllers/          # Dashboard, Patrimonio, Funcionario, Chamado, Responsabilidade
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Requests/             # 8 Form Requests (Store/Update por módulo)
└── Models/                   # User, Patrimonio, Funcionario, Chamado, Responsabilidade

resources/views/
├── dashboard.blade.php
├── patrimonios/              # index, create, edit, show
├── funcionarios/             # index, create, edit, show
├── chamados/                 # index, create, show
└── responsabilidades/        # index, create, edit, show, pdf

database/
├── migrations/               # 8 migrations (incl. role, patrimonio_id, user_id)
└── seeders/                  # 5 seeders orquestrados pelo DatabaseSeeder
```

## Executar em Desenvolvimento

```bash
# Terminal 1 — servidor PHP
php artisan serve

# Terminal 2 — compilação contínua de assets
npm run dev
```

## Documentação Detalhada

Consulte [DOCUMENTATION.md](DOCUMENTATION.md) para detalhes sobre arquitetura, rotas, modelos e fluxos de negócio.

## Licença

Software proprietário. Todos os direitos reservados.
