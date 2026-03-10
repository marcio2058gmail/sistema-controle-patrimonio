# Sistema Web de Controle Patrimonial — LocarMais

> Laravel 12 · PHP 8.2+ · Blade · Tailwind CSS v4 · MySQL/MariaDB · Multi-Tenant

---

## Índice

1. [Visão Geral](#visão-geral)
2. [Requisitos](#requisitos)
3. [Instalação e Configuração](#instalação-e-configuração)
4. [Perfis de Acesso](#perfis-de-acesso)
5. [Arquitetura Multi-Tenant](#arquitetura-multi-tenant)
6. [Estrutura do Banco de Dados](#estrutura-do-banco-de-dados)
7. [Arquitetura da Aplicação](#arquitetura-da-aplicação)
8. [Módulos do Sistema](#módulos-do-sistema)
9. [Fluxo Principal: Chamado → Entrega → Termo](#fluxo-principal)
10. [Geração de PDF](#geração-de-pdf)
11. [Rotas da Aplicação](#rotas-da-aplicação)
12. [Dados de Teste (Seeders)](#dados-de-teste)
13. [Comandos Úteis](#comandos-úteis)
14. [Identidade Visual](#identidade-visual)
15. [Decisões Técnicas](#decisões-técnicas)

---

## Visão Geral

Sistema corporativo para gerenciamento do ciclo de vida de bens patrimoniais: cadastro,
atribuição a funcionários, abertura de chamados e geração de termos de responsabilidade em PDF.

**Módulos implementados:**

| Módulo              | Descrição                                                                      |
|---------------------|--------------------------------------------------------------------------------|
| Autenticação        | Login, registro, recuperação de senha (Laravel Breeze)                         |
| Perfis de acesso    | Super Admin / Admin / Gestor / Funcionário com controle por middleware          |
| Empresas            | CRUD de empresas com isolamento de dados por tenant (somente Super Admin)      |
| Patrimônios         | CRUD com controle de status e campos financeiros                               |
| Manutenções         | Ordens de serviço vinculadas a patrimônios (preventiva/corretiva)              |
| Funcionários        | CRUD com vínculo opcional a usuário e departamento                             |
| Departamentos       | Organização de funcionários por setor                                          |
| Gestão de Usuários  | Cadastro e vinculação de usuários às empresas (admin e super_admin)            |
| Chamados            | Fluxo de solicitação e aprovação de patrimônios                                |
| Responsabilidades   | Registro formal de entrega com assinatura digital, devolução e PDF             |
| Dashboard           | KPIs + gráficos adaptados por perfil (Chart.js)                                |

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

O controle de acesso é implementado em dois níveis:
- **Role global** (`role` na tabela `users`): `super_admin` ou fallback padrão
- **Role por empresa** (`role` na pivot `empresa_user`): `admin`, `manager`, `employee`

O middleware `RoleMiddleware` leva em conta o contexto da empresa ativa (`session('empresa_ativa_id')`).

| Perfil          | Valor no BD   | Escopo   | Permissões                                                                              |
|-----------------|---------------|----------|------------------------------------------------------------------------------------------|
| **Super Admin** | `super_admin` | Global   | Acesso total a todas as empresas, CRUD de empresas, não requer empresa selecionada        |
| **Admin**       | `admin`       | Empresa  | Acesso total na empresa: todos os CRUDs, aprovação de chamados, gestão de usuários       |
| **Gestor**      | `manager`     | Empresa  | Visualização e operação restrita ao próprio departamento. Sem aprovar chamados            |
| **Funcionário** | `employee`    | Empresa  | Abrir chamados, visualizar seus próprios chamados                                        |

### Permissões detalhadas por módulo

| Módulo                                   | Super Admin | Admin | Gestor         | Funcionário |
|------------------------------------------|-------------|-------|----------------|-------------|
| Empresas (CRUD)                          | ✅ Total    | —     | —              | —           |
| Usuários (CRUD na empresa)               | ✅           | ✅     | —              | —           |
| Dashboard                                | ✅ Global   | ✅ Global | ✅ Departamento | —           |
| Patrimônios (CRUD completo)              | ✅           | ✅     | ✅              | 👁 disponíveis |
| Manutenções (CRUD)                       | ✅           | ✅     | —              | —           |
| Funcionários (CRUD completo)             | ✅           | ✅     | ✅ próprio dept  | —           |
| Departamentos (CRUD completo)            | ✅           | ✅     | 👁 index/show  | —           |
| Chamados — abrir                         | ✅           | ✅     | ✅              | ✅           |
| Chamados — aprovar/negar/entregar        | ✅           | ✅     | —              | —           |
| Responsabilidades — visualizar/PDF/assinar | ✅         | ✅     | ✅              | —           |
| Responsabilidades — devolver             | ✅           | ✅     | ✅              | —           |
| Responsabilidades — criar/editar/excluir | ✅           | ✅     | —              | —           |

**Credenciais de teste geradas pelos seeders:**

| Perfil        | E-mail                        | Senha      |
|---------------|-------------------------------|------------|
| Super Admin   | superadmin@patrimonio.test    | password   |
| Admin         | admin@patrimonio.test         | password   |
| Gestor        | gestor@patrimonio.test        | password   |
| Funcionário   | ana.silva@empresa.test        | password   |
| Funcionário   | bruno.oliveira@empresa.test   | password   |
| *(+8 outros)* | *\*@empresa.test*             | password   |

---

## Arquitetura Multi-Tenant

O sistema adota **multi-tenancy baseado em sessão**: empresa ativa é armazenada em `session('empresa_ativa_id')` e todos os `scopeForCompany` nos Models filtram automaticamente os dados.

### Fluxo de seleção de empresa

```
Login (Laravel Breeze)
    └── Middleware EnsureCompanySelected
            ├── Super Admin? → acesso livre (sem empresa ou qualquer empresa)
            ├── 1 empresa disponível? → auto-seleciona e continua
            ├── N empresas? → redireciona para /companies/select
            └── Empresa na sessão válida? → continua normalmente
```

### Pivots de empresa

- **`empresa_user`** — vincula usuários a empresas com o papel (`role`) dentro de cada empresa
- Cada recurso (`patrimonios`, `funcionarios`, `departamentos`) possui coluna `empresa_id`
- Role resolvido pelo método `User::roleInCompany(?int $companyId)` consultando a pivot

### Super Admin

- `role = 'super_admin'` é um papel **global** na tabela `users` (não via pivot)
- Dispensão do middleware `company.select`
- Acessa CRUD de empresas via `/companies`
- Pode alternar entre empresas a qualquer momento
- `isAdmin()` retorna `true` para super_admin (para fins de permissão)

---

## Estrutura do Banco de Dados

> **Importante:** Os nomes das tabelas estão em **português** (preservados das migrations originais).
> Os nomes das classes PHP (Models, Controllers) estão em **inglês**.

### Diagrama de Entidades

```
users
 ├── id, name, email, cpf, rg, ctps, password, role (super_admin|admin|manager|employee)
 ├── hasOne  → funcionarios (Employee)
 └── belongsToMany → empresas (pivot: empresa_user com role por empresa)

empresas
 ├── id, nome, cnpj, telefone, email, ativa, modelo_pdf
 ├── belongsToMany → users (pivot: empresa_user)
 ├── hasMany → funcionarios
 ├── hasMany → patrimonios
 └── hasMany → departamentos

empresa_user  [pivot]
 ├── empresa_id (FK), user_id (FK)
 └── role (admin|manager|employee)

funcionarios
 ├── id, user_id (FK nullable), nome, email, cargo, departamento_id (FK nullable), empresa_id (FK)
 ├── hasMany → chamados (Ticket)
 └── hasMany → responsabilidades (Responsibility)

departamentos
 ├── id, nome, empresa_id (FK)
 └── hasMany → funcionarios

patrimonios
 ├── id, codigo_patrimonio (unique), descricao, modelo, numero_serie, empresa_id (FK)
 ├── status (disponivel|em_uso|manutencao)
 ├── valor_aquisicao, data_aquisicao, fornecedor, numero_nota_fiscal, garantia_ate, valor_atual
 ├── belongsToMany → chamados (pivot: chamado_patrimonio)
 ├── hasMany → responsabilidades
 └── hasMany → manutencoes

chamados
 ├── id, funcionario_id (FK), descricao
 ├── status (open|approved|denied|delivered)
 ├── belongsTo → funcionario
 └── belongsToMany → patrimonios

responsabilidades  [termos]
 ├── id, funcionario_id (FK), patrimonio_id (FK)
 ├── data_entrega, data_devolucao (nullable), observacao_devolucao (nullable)
 ├── termo_responsabilidade (nullable), assinado (boolean)
 ├── belongsTo → funcionario (employee)
 └── belongsTo → patrimonio (asset)

manutencoes
 ├── id, patrimonio_id (FK), tipo (preventiva|corretiva)
 ├── status (agendada|em_andamento|concluida|cancelada)
 ├── descricao, data_abertura, data_conclusao (nullable)
 ├── custo (nullable), tecnico_fornecedor (nullable), observacoes (nullable)
 └── belongsTo → patrimonio
```

### Migrations

| Arquivo                                           | Descrição                                          |
|---------------------------------------------------|----------------------------------------------------|
| `..._create_users_table`                          | Tabela de usuários (Breeze)                        |
| `..._create_funcionarios_table`                   | Tabela de funcionários                             |
| `..._create_patrimonios_table`                    | Tabela de patrimônios                              |
| `..._create_chamados_table`                       | Tabela de chamados                                 |
| `..._create_responsabilidades_table`              | Tabela de responsabilidades                        |
| `..._add_role_to_users_table`                     | Coluna `role` em users                             |
| `..._add_patrimonio_id_to_chamados_table`         | FK de patrimônio em chamados                       |
| `..._add_user_id_to_funcionarios_table`           | FK de user em funcionários                         |
| `..._create_chamado_patrimonio_table`             | Pivot chamado ↔ patrimônio                         |
| `..._create_departamentos_table`                  | Tabela de departamentos                            |
| `..._create_termos_table`                         | Renomeia/cria termos (responsabilidades)           |
| `..._add_signature_to_termos_table`               | Campo `assinado` nos termos                        |
| `..._create_empresas_table`                       | Tabela de empresas (multi-tenant)                  |
| `..._create_empresa_user_table`                   | Pivot empresa ↔ user com role                      |
| `..._add_empresa_id_to_*`                         | FK `empresa_id` em funcionarios, patrimonios, depts|
| `..._add_super_admin_role_to_users`               | Suporte ao papel `super_admin`                     |
| `..._add_cpf_to_users_table`                      | Campo `cpf` em users                               |
| `..._add_observacao_devolucao_to_termos_table`    | Observação na devolução                            |
| `..._make_termo_responsabilidade_nullable`        | Torna o campo de termo opcional                    |
| `..._add_rg_ctps_to_users_table`                  | Campos `rg` e `ctps` em users                      |
| `..._add_modelo_pdf_to_empresas_table`            | Modelo de PDF customizável por empresa             |
| `..._add_financial_fields_to_patrimonios_table`   | Campos financeiros no patrimônio                   |
| `..._create_manutencoes_table`                    | Tabela de manutenções                              |

---

## Arquitetura da Aplicação

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php       ← KPIs e gráficos adaptados por perfil
│   │   ├── AssetController.php           ← CRUD de patrimônios (com campos financeiros)
│   │   ├── EmployeeController.php        ← CRUD de funcionários
│   │   ├── DepartmentController.php      ← CRUD de departamentos
│   │   ├── ManutencaoController.php      ← CRUD de manutenções (modal inline)
│   │   ├── TicketController.php          ← Fluxo de chamados + ações
│   │   ├── ResponsibilityController.php  ← CRUD + assinar + devolver + PDF
│   │   ├── CompanyController.php         ← Seleção e CRUD de empresas
│   │   ├── UserManagementController.php  ← Gestão de usuários por empresa
│   │   └── ProfileController.php         ← Breeze (perfil do usuário)
│   │
│   ├── Middleware/
│   │   ├── RoleMiddleware.php            ← Controle de acesso por perfil (company-aware)
│   │   └── EnsureCompanySelected.php     ← Garante empresa ativa na sessão
│   │
│   └── Requests/
│       ├── StoreAssetRequest.php / UpdateAssetRequest.php
│       ├── StoreEmployeeRequest.php / UpdateEmployeeRequest.php
│       ├── StoreTicketRequest.php / UpdateTicketRequest.php
│       ├── StoreResponsibilityRequest.php / UpdateResponsibilityRequest.php
│       ├── StoreManutencaoRequest.php / UpdateManutencaoRequest.php
│       └── StoreDepartmentRequest.php / UpdateDepartmentRequest.php
│
├── Models/
│   ├── User.php           ← role, isSuperAdmin(), isAdmin(), isManager(), isEmployee(),
│   │                         roleInCompany(), activeCompany(), empresas()
│   ├── Company.php        ← tabela: empresas; users(), employees(), assets(), departments()
│   ├── Asset.php          ← statusLabels(), STATUS_* constants, scopeForCompany(), campos financeiros
│   ├── Employee.php       ← relacionamentos (department, company, responsibilities, tickets)
│   ├── Department.php     ← relacionamentos (employees, company)
│   ├── Ticket.php         ← statusLabels(), STATUS_* constants, relacionamentos
│   ├── Responsibility.php ← casts de datas, relacionamentos (employee, asset)
│   └── Manutencao.php     ← TIPOS, STATUS constants, relacionamento (patrimonio)
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
├── manutencoes/     index (criação/edição via modais inline)
├── tickets/         index, create, show
├── responsibilities/ index, create, edit, show, pdf
├── companies/       select (seleção pós-login), index (super_admin)
└── users/           index (gestão de usuários da empresa)

database/seeders/
├── DatabaseSeeder.php           ← Orquestra todos os seeders
├── UserSeeder.php               ← 1 Admin, 1 Gestor, 10 Funcionários
├── EmpresaSeeder.php            ← 1 Super Admin + 1 Empresa de Demonstração + vinculações
├── DepartamentoSeeder.php       ← Departamentos vinculados à empresa
├── FuncionarioSeeder.php        ← 10 Funcionários vinculados aos users e empresa
├── PatrimonioSeeder.php         ← 20 Patrimônios com status variado e empresa
├── ChamadoSeeder.php            ← 15 Chamados com status variado
└── ResponsabilidadeSeeder.php   ← ~13 Responsabilidades (ativas + encerradas)
```

---

## Módulos do Sistema

### Patrimônios (`/assets`)

- **Status possíveis:** `disponivel` · `em_uso` · `manutencao`
- O status é atualizado automaticamente para `em_uso` ao criar uma responsabilidade
- O status volta para `disponivel` ao registrar a `data_devolucao`
- **Campos financeiros:** `valor_aquisicao`, `data_aquisicao`, `fornecedor`, `numero_nota_fiscal`, `garantia_ate`, `valor_atual`
- Todos os dados são filtrados por `empresa_id` via `scopeForCompany`
- **Acesso:** Admin e Gestor (CRUD completo) · Funcionário (somente listagem de disponíveis)

### Funcionários (`/employees`)

- Podem ser vinculados a um usuário do sistema via `user_id`
- Vinculados a departamentos via `departamento_id` e à empresa via `empresa_id`
- Funcionários vinculados enxergam apenas seus próprios chamados
- **Acesso:** Admin (CRUD completo) · Gestor (CRUD restrito ao próprio departamento)

### Departamentos (`/departments`)

- Organizam funcionários por setor, isolados por empresa
- **Acesso:** Admin (CRUD completo) · Gestor (somente visualização — index/show)

### Manutenções (`/manutencoes`)

- Ordens de serviço vinculadas a um patrimônio específico
- **Tipos:** `preventiva` · `corretiva`
- **Status:** `agendada` · `em_andamento` · `concluida` · `cancelada`
- Campos: descrição, data de abertura/conclusão, custo, técnico/fornecedor, observações
- Criação e edição via modais inline (sem página separada)
- **Acesso:** Admin apenas

### Gestão de Usuários (`/users`)

- Cadastro de novos usuários e vinculação à empresa ativa com um papel específico
- Permite editar nome, email, CPF, RG, CTPS, senha e papel na empresa
- Não é possível remover a própria conta
- **Acesso:** Admin e Super Admin

### Empresas (`/companies`)

- CRUD completo de empresas (nome, CNPJ, telefone, email, status ativo)
- Campo `modelo_pdf` para customizar o layout do Termo de Responsabilidade
- Gerenciamento de usuários vinculados por empresa
- Seleção de empresa ativa via `/companies/select` (pós-login, quando necessário)
- **Acesso:** somente Super Admin (CRUD); todos os usuários (seleção/switch)

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
- **Assinar:** botão para registrar assinatura digital do termo (`assinado = true`)
- **Devolver:** registra `data_devolucao` + `observacao_devolucao` e devolve o patrimônio automaticamente
- Registrar `data_devolucao` altera o status do patrimônio para `disponivel`
- **Download de PDF** disponível em cada registro (template customizável por empresa via `modelo_pdf`)
- **Criar / Editar / Excluir:** Admin e Super Admin
- **Visualizar / PDF / Assinar / Devolver:** Admin, Super Admin e Gestor

---

## Dashboard por Perfil

> O item **Dashboard** no menu lateral é exibido apenas para **Admin** e **Super Admin**. Gestores e funcionários são redirecionados para `/tickets` após o login, mas o dashboard permanece acessível pela URL direta.

### Super Admin / Admin
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

| Método | URI                                          | Nome                          | Acesso                  |
|--------|----------------------------------------------|-------------------------------|-------------------------|
| GET    | `/dashboard`                                 | `dashboard`                   | auth + company.select   |
| GET    | `/companies/select`                          | `companies.select`            | auth                    |
| POST   | `/companies/switch`                          | `companies.switch`            | auth                    |
| GET    | `/companies`                                 | `companies.index`             | super_admin             |
| POST   | `/companies`                                 | `companies.store`             | super_admin             |
| PATCH  | `/companies/{company}`                       | `companies.update`            | super_admin             |
| DELETE | `/companies/{company}`                       | `companies.destroy`           | super_admin             |
| GET    | `/companies/{company}/users`                 | `companies.users`             | super_admin             |
| POST   | `/companies/{company}/users`                 | `companies.addUser`           | super_admin             |
| DELETE | `/companies/{company}/users`                 | `companies.removeUser`        | super_admin             |
| GET    | `/users`                                     | `users.index`                 | admin, super_admin      |
| POST   | `/users`                                     | `users.store`                 | admin, super_admin      |
| PATCH  | `/users/{user}`                              | `users.update`                | admin, super_admin      |
| DELETE | `/users/{user}`                              | `users.destroy`               | admin, super_admin      |
| GET    | `/assets`                                    | `assets.index`                | auth (filtrado)         |
| GET    | `/assets/create`                             | `assets.create`               | admin, manager          |
| POST   | `/assets`                                    | `assets.store`                | admin, manager          |
| GET    | `/assets/{id}`                               | `assets.show`                 | auth                    |
| PATCH  | `/assets/{id}`                               | `assets.update`               | admin, manager          |
| DELETE | `/assets/{id}`                               | `assets.destroy`              | admin, manager          |
| GET    | `/employees`                                 | `employees.index`             | admin, manager          |
| *(+ CRUD completo)*                          |                               |                         |
| GET    | `/departments`                               | `departments.index`           | admin, manager          |
| GET    | `/departments/{id}`                          | `departments.show`            | admin, manager          |
| GET    | `/departments/create`                        | `departments.create`          | admin                   |
| POST   | `/departments`                               | `departments.store`           | admin                   |
| PATCH  | `/departments/{id}`                          | `departments.update`          | admin                   |
| DELETE | `/departments/{id}`                          | `departments.destroy`         | admin                   |
| GET    | `/manutencoes`                               | `manutencoes.index`           | admin                   |
| POST   | `/manutencoes`                               | `manutencoes.store`           | admin                   |
| PATCH  | `/manutencoes/{id}`                          | `manutencoes.update`          | admin                   |
| DELETE | `/manutencoes/{id}`                          | `manutencoes.destroy`         | admin                   |
| GET    | `/tickets`                                   | `tickets.index`               | auth (filtrado)         |
| POST   | `/tickets`                                   | `tickets.store`               | auth                    |
| GET    | `/tickets/{id}`                              | `tickets.show`                | auth                    |
| PATCH  | `/tickets/{id}/aprovar`                      | `tickets.aprovar`             | admin                   |
| PATCH  | `/tickets/{id}/negar`                        | `tickets.negar`               | admin                   |
| PATCH  | `/tickets/{id}/entregar`                     | `tickets.entregar`            | admin                   |
| GET    | `/responsibilities`                          | `responsibilities.index`      | admin, manager          |
| GET    | `/responsibilities/{id}`                     | `responsibilities.show`       | admin, manager          |
| GET    | `/responsibilities/{id}/pdf`                 | `responsibilities.pdf`        | admin, manager          |
| POST   | `/responsibilities/{id}/assinar`             | `responsibilities.assinar`    | admin, manager          |
| POST   | `/responsibilities/{id}/devolver`            | `responsibilities.devolver`   | admin, manager          |
| GET    | `/responsibilities/create`                   | `responsibilities.create`     | admin                   |
| POST   | `/responsibilities`                          | `responsibilities.store`      | admin                   |
| GET    | `/responsibilities/{id}/edit`                | `responsibilities.edit`       | admin                   |
| PATCH  | `/responsibilities/{id}`                     | `responsibilities.update`     | admin                   |
| DELETE | `/responsibilities/{id}`                     | `responsibilities.destroy`    | admin                   |

---

## Dados de Teste

Após `php artisan db:seed`:

| Entidade          | Quantidade | Observações                                                   |
|-------------------|------------|---------------------------------------------------------------|
| Usuários          | 13         | 1 super_admin, 1 admin, 1 gestor, 10 funcionários             |
| Empresas          | 1          | "Empresa Demonstração" com todos os usuários vinculados         |
| Funcionários      | 10         | Vinculados a usuários com role=employee                        |
| Departamentos     | Variado    | Vinculados à empresa e associados aos funcionários             |
| Patrimônios       | 20         | Mix de notebooks, monitores, impressoras…                     |
| Chamados          | 15         | Status variados (open/approved/denied/delivered)              |
| Responsabilidades | ~13        | Ativas e encerradas (com devolução)                           |

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
| Multi-tenancy por sessão | `session('empresa_ativa_id')` + `scopeForCompany` nos Models garante isolamento de dados sem complexidade de banco separado por tenant |
| Role global + role por empresa | Super Admin precisa de um role global (único no sistema); demais papéis são por empresa via pivot `empresa_user` |
| Roles via coluna `role` + pivot | Suficiente para os papéis fixos — sem overhead do `spatie/laravel-permission` |
| Middleware `EnsureCompanySelected` | Centraliza a lógica de seleção: auto-select, redirect para tela de escolha, validação da empresa na sessão |
| Tabelas em português, classes em inglês | Backward-compatible: migrations preservadas, código padronizado em inglês |
| Session driver `database` | Necessário truncar `sessions` após `migrate:fresh` se houver cookies antigos no browser |
| dompdf (não wkhtmltopdf) | Sem dependências do sistema operacional, instalação simples via Composer |
| `modelo_pdf` na empresa | Permite customizar o template do PDF por tenant sem alterar código |
| Chart.js via CDN | Evita bundling complexo para 2 gráficos; carregado via `@stack('scripts')` |
| CSS inline no PDF | dompdf tem suporte limitado a CSS externo; inline garante fidelidade visual |
| Paginação Tailwind | `Paginator::useTailwind()` no `AppServiceProvider` integra nativamente com o design |
| `darkMode: 'class'` no Tailwind | Permite controle manual via toggle, independente do sistema operacional |
| Navbar sempre escura | Logo LocarMais (`escuro.png`) tem fundo escuro — navbar fixa em `bg-gray-900` garante legibilidade sempre |
| `unique()` com tabela em português | Form Requests usam o nome real da tabela no BD (ex: `unique('patrimonios')`) |
| JOINs com tabela em português | Queries raw no DashboardController referenciam nomes reais das tabelas |
| Modais via Alpine.js `$dispatch` | Botões no `<x-slot name="header">` ficam fora do escopo `x-data`; `$dispatch` + listener `.window` resolvem a comunicação entre escopos |
| Criação inline (modais) em vez de páginas separadas | Patrimônio, Funcionário, Departamento, Responsável, Manutenção e Usuário são criados via modal na própria página de listagem |
| Logo redireciona por perfil | Admin/Super Admin → `/dashboard`; Gestor/Funcionário → `/tickets` |
| Redirect pós-login por perfil | `AuthenticatedSessionController` envia admin/super_admin para dashboard e demais perfis para tickets |
| Campos financeiros em patrimônios | Necessidade de rastreamento de valor, garantia e fornecedor para gestão completa do ciclo de vida |
