# Sistema de Controle Patrimonial — LocarMais

Sistema web **multi-tenant** para gestão do ciclo de vida de bens patrimoniais: cadastro de ativos, atribuição a funcionários via Termos de Responsabilidade, fluxo completo de chamados de solicitação e controle de manutenções.

## Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Back-end | Laravel 12 / PHP 8.2+ |
| Front-end | Blade + Tailwind CSS v4 + Alpine.js |
| Build | Vite |
| Banco de Dados | MySQL / MariaDB |
| Autenticação | Laravel Breeze (sessão) |
| PDF | barryvdh/laravel-dompdf v3.x |
| Gráficos | Chart.js v4.4 (CDN) |

## Funcionalidades

- **Multi-tenancy** — cada usuário pertence a uma ou mais Empresas; todos os dados são isolados por empresa
- **Dashboard** — KPIs e gráficos Chart.js adaptados por perfil (global para Admin, departamento para Gestor)
- **Patrimônios** — CRUD completo com status (`disponivel`, `em_uso`, `manutencao`) e campos financeiros (valor, data de aquisição, garantia, etc.)
- **Manutenções** — registro de ordens de serviço (preventivas/corretivas) vinculadas a patrimônios
- **Funcionários** — Cadastro vinculado a conta de usuário e departamento
- **Departamentos** — Organização de funcionários por setor
- **Chamados** — Fluxo: solicitação → aprovação/negação → entrega; entrega cria automaticamente Termo de Responsabilidade
- **Termos de Responsabilidade** — Registro formal de entrega com assinatura digital, devolução e geração de PDF
- **Gestão de Usuários** — Cadastro e vinculação de usuários às empresas (admin e super_admin)
- **Gestão de Empresas** — CRUD de empresas com modelo de PDF customizável (somente Super Admin)
- **Controle de Acesso por Papel** — quatro níveis: `super_admin`, `admin`, `manager`, `employee`

## Papéis de Acesso

| Papel | Escopo | Permissões |
|---|---|---|
| `super_admin` | Global | Gerencia todas as empresas, acesso total, não requer empresa selecionada |
| `admin` | Por empresa | CRUD completo, aprovação de chamados, gestão de usuários da empresa |
| `manager` | Por empresa | Visualização e operação restrita ao próprio departamento |
| `employee` | Por empresa | Abrir e acompanhar próprios chamados |

## Instalação

### Pré-requisitos
- PHP 8.2+
- Composer
- Node.js 20+ / npm
- MySQL 8+ ou MariaDB

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
| Super Admin | superadmin@patrimonio.test | password |
| Admin | admin@patrimonio.test | password |
| Gestor | gestor@patrimonio.test | password |
| Funcionário | ana.silva@empresa.test | password |

> Os seeders criam 1 super_admin, 1 admin, 1 gestor, 10 funcionários e 1 empresa de demonstração com patrimônios e chamados de exemplo.

## Estrutura de Diretórios Relevante

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php       ← KPIs adaptados por perfil
│   │   ├── AssetController.php           ← CRUD de patrimônios
│   │   ├── EmployeeController.php        ← CRUD de funcionários
│   │   ├── DepartmentController.php      ← CRUD de departamentos
│   │   ├── TicketController.php          ← Fluxo de chamados
│   │   ├── ResponsibilityController.php  ← CRUD + assinatura + PDF
│   │   ├── ManutencaoController.php      ← CRUD de manutenções
│   │   ├── CompanyController.php         ← Seleção e CRUD de empresas
│   │   └── UserManagementController.php  ← Gestão de usuários por empresa
│   ├── Middleware/
│   │   ├── RoleMiddleware.php            ← Controle de acesso por perfil
│   │   └── EnsureCompanySelected.php     ← Garante empresa ativa na sessão
│   └── Requests/                         ← Form Requests (Store/Update por módulo)
└── Models/
    ├── User, Asset, Employee, Department
    ├── Ticket, Responsibility
    ├── Company                           ← Multi-tenant (tabela: empresas)
    └── Manutencao                        ← Ordens de serviço

resources/views/
├── dashboard.blade.php
├── assets/              # index, create, edit, show
├── employees/           # index, create, edit, show
├── departments/         # index, create, edit, show
├── tickets/             # index, create, show
├── responsibilities/    # index, create, edit, show, pdf
├── manutencoes/         # index (modal inline)
├── companies/           # select, index (super_admin)
└── users/               # index (gestão de usuários da empresa)

database/
├── migrations/          # 27 migrations
└── seeders/             # DatabaseSeeder, UserSeeder, EmpresaSeeder,
                         # DepartamentoSeeder, FuncionarioSeeder,
                         # PatrimonioSeeder, ChamadoSeeder, ResponsabilidadeSeeder
```

## Fluxo Multi-Tenant

```
Login → selecionar empresa → navegar pelo sistema (dados isolados por empresa)
         (automático se o usuário pertence a apenas uma empresa)
```

Super Admin pode acessar o sistema sem selecionar empresa ou alternar entre todas livremente via `/companies`.

## Executar em Desenvolvimento

```bash
# Terminal 1 — servidor PHP
php artisan serve --port=8888

# Terminal 2 — compilação contínua de assets
npm run dev
```

## Documentação Detalhada

Consulte [DOCUMENTATION.md](DOCUMENTATION.md) para detalhes sobre arquitetura, rotas, modelos e fluxos de negócio.

## Licença

Software proprietário. Todos os direitos reservados.
