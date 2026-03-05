# Sistema Web de Controle Patrimonial — LocarMais

> Laravel 12 · PHP 8.4 · Blade · Tailwind CSS · MySQL/MariaDB

---

## Índice

1. [Visão Geral](#visão-geral)
2. [Requisitos](#requisitos)
3. [Instalação e Configuração](#instalação-e-configuração)
4. [Perfis de Acesso](#perfis-de-acesso)
5. [Estrutura do Banco de Dados](#estrutura-do-banco-de-dados)
6. [Arquitetura da Aplicação](#arquitetura-da-aplicação)
7. [Módulos do Sistema](#módulos-do-sistema)
8. [Fluxo Principal: Chamado → Entrega → Termo](#fluxo-principal)
9. [Geração de PDF](#geração-de-pdf)
10. [Rotas da Aplicação](#rotas-da-aplicação)
11. [Dados de Teste (Seeders)](#dados-de-teste)
12. [Comandos Úteis](#comandos-úteis)
13. [Identidade Visual](#identidade-visual)
14. [Decisões Técnicas](#decisões-técnicas)

---

## Visão Geral

Sistema corporativo para gerenciamento do ciclo de vida de bens patrimoniais: cadastro,
atribuição a funcionários, abertura de chamados e geração de termos de responsabilidade em PDF.

**Módulos implementados:**

| Módulo              | Descrição                                                     |
|---------------------|---------------------------------------------------------------|
| Autenticação        | Login, registro, recuperação de senha (Laravel Breeze)        |
| Perfis de acesso    | Admin / Gestor / Funcionário com controle por middleware       |
| Patrimônios         | CRUD com controle de status                                   |
| Funcionários        | CRUD com vínculo opcional a usuário do sistema                |
| Departamentos       | Organização de funcionários por setor                         |
| Gestores            | Cadastro de gestores vinculados a departamentos               |
| Chamados            | Fluxo de solicitação e aprovação de patrimônios               |
| Responsabilidades   | Registro formal de entrega com geração de PDF                 |
| Dashboard           | KPIs + gráficos adaptados por perfil (Chart.js)               |

---

## Requisitos

- PHP >= 8.2
- Composer >= 2
- Node.js >= 18 + npm
- MySQL ou MariaDB

---

## Instalação e Configuração

```bash
# 1. Clonar o repositório e instalar dependências
composer install
npm install

# 2. Configurar o ambiente
cp .env.example .env
php artisan key:generate

# 3. Ajustar .env com as credenciais do banco de dados
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=controle_patrimonio
# DB_USERNAME=seu_usuario
# DB_PASSWORD=sua_senha

# 4. Criar o banco e rodar as migrations
php artisan migrate

# 5. Popular com dados de teste
php artisan db:seed

# 6. Compilar os assets
npm run build

# 7. Iniciar o servidor de desenvolvimento
php artisan serve
```

Acesse: **http://localhost:8000**

---

## Perfis de Acesso

O controle de acesso é implementado via coluna `role` na tabela `users` e o middleware `RoleMiddleware`.

| Perfil          | Valor no BD | Permissões                                                                          |
|-----------------|-------------|-------------------------------------------------------------------------------------|
| **Admin**       | `admin`     | Acesso total — todos os CRUDs, aprovação de chamados, responsabilidades             |
| **Gestor**      | `manager`   | Visualização de patrimônios, funcionários, departamentos, responsabilidades e chamados do departamento. Sem criar/editar/excluir responsabilidades nem aprovar chamados |
| **Funcionário** | `employee`  | Abrir chamados, visualizar seus próprios chamados                                   |

### Permissões detalhadas por módulo

| Módulo                                   | Admin | Gestor         | Funcionário |
|------------------------------------------|-------|----------------|-------------|
| Dashboard                                | ✅ Global | ✅ Departamento | ✅ Próprio |
| Patrimônios (CRUD completo)              | ✅    | ✅              | —           |
| Funcionários (CRUD completo)             | ✅    | ✅              | —           |
| Departamentos (CRUD completo)            | ✅    | 👁 index/show  | —           |
| Gestores (CRUD)                          | ✅    | —              | —           |
| Chamados — abrir                         | ✅    | ✅              | ✅          |
| Chamados — aprovar/negar/entregar        | ✅    | —              | —           |
| Responsabilidades — visualizar/PDF       | ✅    | ✅              | —           |
| Responsabilidades — criar/editar/excluir | ✅    | —              | —           |

**Credenciais de teste geradas pelos seeders:**

| Perfil       | E-mail                        | Senha      |
|--------------|-------------------------------|------------|
| Admin        | admin@patrimonio.test         | password   |
| Gestor       | gestor@patrimonio.test        | password   |
| Funcionário  | ana.silva@empresa.test        | password   |
| Funcionário  | bruno.oliveira@empresa.test   | password   |
| *(+8 outros)*| *\*@empresa.test*             | password   |

---

## Estrutura do Banco de Dados

> **Importante:** Os nomes das tabelas estão em **português** (preservados das migrations originais).
> Os nomes das classes PHP (Models, Controllers) estão em **inglês**.

### Diagrama de Entidades

```
users
 ├── id, name, email, password, role (admin|manager|employee)
 └── hasOne → funcionarios (Employee)

funcionarios
 ├── id, user_id (FK nullable), nome, email, cargo, departamento_id (FK nullable)
 ├── hasMany → chamados (Ticket)
 └── hasMany → responsabilidades (Responsibility)

departamentos
 ├── id, nome
 └── hasMany → funcionarios

patrimonios
 ├── id, codigo_patrimonio (unique), descricao, modelo, numero_serie
 ├── status (disponivel|em_uso|manutencao)
 ├── belongsToMany → chamados (pivot: chamado_patrimonio)
 └── hasMany → responsabilidades

chamados
 ├── id, funcionario_id (FK), descricao
 ├── status (open|approved|denied|delivered)
 ├── belongsTo → funcionario
 └── belongsToMany → patrimonios

responsabilidades
 ├── id, funcionario_id (FK), patrimonio_id (FK)
 ├── data_entrega, data_devolucao (nullable)
 ├── termo_responsabilidade, assinado (boolean)
 ├── belongsTo → funcionario (employee)
 └── belongsTo → patrimonio (asset)
```

### Migrations

| Arquivo                                  | Descrição                           |
|------------------------------------------|-------------------------------------|
| `..._create_users_table`                 | Tabela de usuários (Breeze)         |
| `..._create_funcionarios_table`          | Tabela de funcionários              |
| `..._create_patrimonios_table`           | Tabela de patrimônios               |
| `..._create_chamados_table`              | Tabela de chamados                  |
| `..._create_responsabilidades_table`     | Tabela de responsabilidades         |

---

## Arquitetura da Aplicação

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php       ← KPIs e gráficos adaptados por perfil
│   │   ├── AssetController.php           ← CRUD de patrimônios
│   │   ├── EmployeeController.php        ← CRUD de funcionários
│   │   ├── DepartmentController.php      ← CRUD de departamentos
│   │   ├── ManagerController.php         ← CRUD de gestores
│   │   ├── TicketController.php          ← Fluxo de chamados + ações
│   │   ├── ResponsibilityController.php  ← CRUD + geração de PDF
│   │   └── ProfileController.php         ← Breeze (perfil do usuário)
│   │
│   ├── Middleware/
│   │   └── RoleMiddleware.php            ← Controle de acesso por perfil
│   │
│   └── Requests/
│       ├── StoreAssetRequest.php / UpdateAssetRequest.php
│       ├── StoreEmployeeRequest.php / UpdateEmployeeRequest.php
│       ├── StoreTicketRequest.php / UpdateTicketRequest.php
│       ├── StoreResponsibilityRequest.php / UpdateResponsibilityRequest.php
│       ├── StoreManagerRequest.php / UpdateManagerRequest.php
│       └── StoreDepartmentRequest.php / UpdateDepartmentRequest.php
│
├── Models/
│   ├── User.php             ← role, isAdmin(), isManager(), isEmployee(), isAdminOrManager()
│   ├── Asset.php            ← statusLabels(), STATUS_* constants, relacionamentos
│   ├── Employee.php         ← relacionamentos (department, responsibilities, tickets)
│   ├── Department.php       ← relacionamentos (employees)
│   ├── Ticket.php           ← statusLabels(), STATUS_* constants, relacionamentos
│   └── Responsibility.php   ← casts de datas, relacionamentos (employee, asset)
│
└── Providers/
    └── AppServiceProvider.php  ← Paginator::useTailwind()

resources/views/
├── dashboard.blade.php           ← KPIs + Chart.js (visão por perfil)
├── components/
│   ├── status-badge.blade.php   ← Badge de status reutilizável
│   └── alert.blade.php          ← Alertas de sucesso/erro
├── assets/          index, create, edit, show
├── employees/       index, create, edit, show
├── departments/     index, show, create, edit
├── managers/        index, create, edit
├── tickets/         index, create, show
└── responsibilities/ index, create, edit, show, pdf

database/seeders/
├── DatabaseSeeder.php           ← Orquestra todos os seeders
├── UserSeeder.php               ← 1 Admin, 1 Gestor, 10 Funcionários
├── EmployeeSeeder.php           ← 10 Funcionários vinculados aos users
├── AssetSeeder.php              ← 20 Patrimônios com status variado
├── TicketSeeder.php             ← 15 Chamados com status variado
└── ResponsibilitySeeder.php     ← ~13 Responsabilidades (ativas + encerradas)
```

---

## Módulos do Sistema

### Patrimônios (`/assets`)

- **Status possíveis:** `disponivel` · `em_uso` · `manutencao`
- O status é atualizado automaticamente para `em_uso` ao criar uma responsabilidade
- O status volta para `disponivel` ao registrar a `data_devolucao`
- **Acesso:** Admin e Gestor (CRUD completo)

### Funcionários (`/employees`)

- Podem ser vinculados a um usuário do sistema via `user_id`
- Vinculados a departamentos via `departamento_id`
- Funcionários vinculados enxergam apenas seus próprios chamados
- **Acesso:** Admin e Gestor (CRUD completo)

### Departamentos (`/departments`)

- Organizam funcionários por setor
- **Acesso:** Admin (CRUD completo) · Gestor (somente visualização — index/show)

### Gestores (`/managers`)

- Usuários com perfil `manager` vinculados a um departamento
- **Acesso:** somente Admin

### Chamados (`/tickets`)

- **Abrir chamado:** qualquer usuário autenticado
- **Filtro por status** disponível na listagem
- Funcionários veem apenas seus próprios chamados
- Gestores veem chamados do seu departamento
- Admins veem todos
- **Aprovar / Negar / Registrar Entrega:** somente Admin

**Estados e transições:**

```
aberto → [aprovar] → aprovado → [entregar] → entregue
aberto → [negar]  → negado
```

> Ao executar **Registrar Entrega**, o sistema automaticamente:
> 1. Cria um registro em `responsabilidades`
> 2. Altera o status do patrimônio para `em_uso`
> 3. Altera o status do chamado para `entregue`

### Responsabilidades (`/responsibilities`)

- Podem ser criadas manualmente ou automaticamente via entrega de chamado
- Somente patrimônios com `status = disponivel` podem ser atribuídos
- Registrar `data_devolucao` devolve o patrimônio automaticamente
- **Download de PDF** disponível em cada registro
- **Criar / Editar / Excluir:** somente Admin
- **Visualizar / PDF:** Admin e Gestor

---

## Dashboard por Perfil

### Admin
- KPIs globais: total de patrimônios, funcionários, chamados abertos, atribuições ativas
- Gráfico de pizza: patrimônios por status (disponível / em uso / manutenção)
- Gráfico de barras: chamados por mês (últimos 6 meses)
- Tabela de últimos chamados abertos
- Tabela de breakdown por departamento (funcionários, patrimônios em uso, chamados abertos)

### Gestor
- KPIs do departamento: patrimônios em uso, funcionários, chamados abertos, **funcionários sem patrimônio** (badge vermelho se > 0, verde se todos cobertos)
- Gráfico de pizza: **cobertura de patrimônio** — funcionários com vs sem atribuição ativa (verde/vermelho)
- Gráfico de barras: chamados do departamento (últimos 6 meses)
- Tabela de **funcionários do departamento** com patrimônios ativos e chamados abertos por pessoa
- Tabela de últimos chamados abertos do departamento

### Funcionário
- KPIs próprios: patrimônios sob guarda, chamados abertos
- Tabela dos próprios chamados abertos

---

## Fluxo Principal

```
[Funcionário]                    [Admin]
     │                              │
     │  Abre chamado (/tickets/create)
     │  - Descreve necessidade       │
     │  - Seleciona patrimônio(s)    │
     │                              │
     │               Visualiza chamados abertos
     │               Aprova ou Nega
     │                              │
     │  [Se aprovado] Admin registra entrega
     │                              │
     │               Sistema cria automaticamente:
     │               ✔ Responsabilidade com termo
     │               ✔ Patrimônio → status: em_uso
     │               ✔ Chamado → status: entregue
     │                              │
     │               PDF disponível para download
```

---

## Geração de PDF

- Implementado com **barryvdh/laravel-dompdf v3.x**
- Template: `resources/views/responsibilities/pdf.blade.php`
  - CSS inline (compatível com dompdf, sem Tailwind)
  - Inclui dados do funcionário, patrimônio, período, termo e campos de assinatura
- Rota: `GET /responsibilities/{id}/pdf` → download `termo-responsabilidade-{id}.pdf`
- Papel A4 retrato

---

## Rotas da Aplicação

| Método | URI                                    | Nome                        | Acesso          |
|--------|----------------------------------------|-----------------------------|-----------------|
| GET    | `/dashboard`                           | `dashboard`                 | auth            |
| GET    | `/assets`                              | `assets.index`              | admin, manager  |
| GET    | `/assets/create`                       | `assets.create`             | admin, manager  |
| POST   | `/assets`                              | `assets.store`              | admin, manager  |
| GET    | `/assets/{id}`                         | `assets.show`               | admin, manager  |
| PATCH  | `/assets/{id}`                         | `assets.update`             | admin, manager  |
| DELETE | `/assets/{id}`                         | `assets.destroy`            | admin, manager  |
| GET    | `/employees`                           | `employees.index`           | admin, manager  |
| *(+ CRUD completo)*                    |                             |                 |
| GET    | `/departments`                         | `departments.index`         | admin, manager  |
| GET    | `/departments/{id}`                    | `departments.show`          | admin, manager  |
| GET    | `/departments/create`                  | `departments.create`        | admin           |
| POST   | `/departments`                         | `departments.store`         | admin           |
| PATCH  | `/departments/{id}`                    | `departments.update`        | admin           |
| DELETE | `/departments/{id}`                    | `departments.destroy`       | admin           |
| GET    | `/managers`                            | `managers.index`            | admin           |
| *(+ CRUD completo)*                    |                             |                 |
| GET    | `/tickets`                             | `tickets.index`             | auth (filtrado) |
| POST   | `/tickets`                             | `tickets.store`             | auth            |
| PATCH  | `/tickets/{id}/aprovar`                | `tickets.aprovar`           | admin           |
| PATCH  | `/tickets/{id}/negar`                  | `tickets.negar`             | admin           |
| PATCH  | `/tickets/{id}/entregar`               | `tickets.entregar`          | admin           |
| GET    | `/responsibilities`                    | `responsibilities.index`    | admin, manager  |
| GET    | `/responsibilities/{id}`               | `responsibilities.show`     | admin, manager  |
| GET    | `/responsibilities/{id}/pdf`           | `responsibilities.pdf`      | admin, manager  |
| GET    | `/responsibilities/create`             | `responsibilities.create`   | admin           |
| POST   | `/responsibilities`                    | `responsibilities.store`    | admin           |
| GET    | `/responsibilities/{id}/edit`          | `responsibilities.edit`     | admin           |
| PATCH  | `/responsibilities/{id}`               | `responsibilities.update`   | admin           |
| DELETE | `/responsibilities/{id}`               | `responsibilities.destroy`  | admin           |

---

## Dados de Teste

Após `php artisan db:seed`:

| Entidade          | Quantidade | Observações                                      |
|-------------------|------------|--------------------------------------------------|
| Usuários          | 12         | 1 admin, 1 gestor, 10 funcionários               |
| Funcionários      | 10         | Vinculados a usuários com role=employee          |
| Patrimônios       | 20         | Mix de notebooks, monitores, impressoras…        |
| Chamados          | 15         | Status variados (open/approved/denied/delivered) |
| Responsabilidades | ~13        | Ativas e encerradas (com devolução)              |

---

## Comandos Úteis

```bash
# Recriar banco do zero com dados de teste
php artisan migrate:fresh --seed

# Iniciar servidor na porta 8888
php artisan serve --port=8888

# Compilar assets (produção)
npm run build

# Compilar assets (desenvolvimento com watch)
npm run dev

# Verificar rotas registradas
php artisan route:list

# Limpar todos os caches
php artisan optimize:clear

# Gerar chave de aplicação
php artisan key:generate

# Ver logs em tempo real
php artisan pail

# Corrigir estilo de código (PSR-12)
./vendor/bin/pint

# Executar testes
php artisan test
```

> **Nota:** Após `migrate:fresh`, se surgirem erros 419 (CSRF), limpe as sessões antigas:
> ```bash
> php artisan tinker
> DB::table('sessions')->truncate();
> ```

---

## Identidade Visual

O sistema utiliza a identidade visual da **LocarMais**:

- **Logo:** `https://app.locarmais.com/consImages/escuro.png` (exibido na navbar)
- **Cor primária:** `#f43180` (rosa LocarMais), sobrescrevendo o `indigo` padrão do Tailwind
- **Nome do sistema:** `LocarMais` (definido em `APP_NAME` no `.env`)
- **Navbar:** sempre com fundo escuro (`bg-gray-900`), independente do modo claro/escuro
- **Modo escuro/claro:** toggle disponível na navbar com persistência em `localStorage`
  - Preferência do sistema operacional é detectada automaticamente na primeira visita
  - Script anti-flash no `<head>` evita piscar ao carregar a página

### Paleta de cores customizada (Tailwind `indigo`)

| Token        | Valor     |
|--------------|-----------|
| `indigo-50`  | `#fff0f6` |
| `indigo-100` | `#ffd6ea` |
| `indigo-200` | `#ffadd4` |
| `indigo-300` | `#ff84be` |
| `indigo-400` | `#f95ba2` |
| `indigo-500` | `#f43180` |
| `indigo-600` | `#d01568` |
| `indigo-700` | `#a30f52` |
| `indigo-800` | `#780a3c` |
| `indigo-900` | `#4d0626` |
| `indigo-950` | `#300318` |

---

## Decisões Técnicas

| Decisão | Justificativa |
|---|---|
| Roles via coluna `role` | Suficiente para 3 perfis fixos — sem overhead do `spatie/laravel-permission` |
| Tabelas em português, classes em inglês | Backward-compatible: migrations preservadas, código padronizado em inglês |
| Session driver `database` | Necessário truncar `sessions` após `migrate:fresh` se houver cookies antigos no browser |
| dompdf (não wkhtmltopdf) | Sem dependências do sistema operacional, instalação simples via Composer |
| Chart.js via CDN | Evita bundling complexo para 2 gráficos; carregado via `@stack('scripts')` |
| CSS inline no PDF | dompdf tem suporte limitado a CSS externo; inline garante fidelidade visual |
| Paginação Tailwind | `Paginator::useTailwind()` no `AppServiceProvider` integra nativamente com o design |
| `darkMode: 'class'` no Tailwind | Permite controle manual via toggle, independente do sistema operacional |
| Navbar sempre escura | Logo LocarMais (`escuro.png`) tem fundo escuro — navbar fixa em `bg-gray-900` garante legibilidade sempre |
| `unique()` com tabela em português | Form Requests usam o nome real da tabela no BD (ex: `unique('patrimonios')`) |
| JOINs com tabela em português | Queries raw no DashboardController referenciam nomes reais das tabelas |
