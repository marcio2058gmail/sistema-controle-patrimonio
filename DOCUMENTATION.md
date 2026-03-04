# Sistema Web de Controle Patrimonial

> Laravel 12 · PHP 8.2 · Blade · Tailwind CSS · SQLite/MySQL

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

---

## Visão Geral

Sistema corporativo para gerenciamento do ciclo de vida de bens patrimoniais: cadastro,
atribuição a funcionários, abertura de chamados e geração de termos de responsabilidade em PDF.

**Módulos implementados:**

| Módulo              | Descrição                                                 |
|---------------------|-----------------------------------------------------------|
| Autenticação        | Login, registro, recuperação de senha (Laravel Breeze)    |
| Perfis de acesso    | Admin / Gestor / Funcionário com controle por middleware   |
| Patrimônios         | CRUD com controle de status                               |
| Funcionários        | CRUD com vínculo opcional a usuário do sistema            |
| Chamados            | Fluxo de solicitação e aprovação de patrimônios           |
| Responsabilidades   | Registro formal de entrega com geração de PDF             |
| Dashboard           | KPIs + gráficos (Chart.js)                                |

---

## Requisitos

- PHP >= 8.2
- Composer >= 2
- Node.js >= 18 + npm
- SQLite (padrão dev) ou MySQL/PostgreSQL

---

## Instalação e Configuração

```bash
# 1. Clonar o repositório e instalar dependências
composer install
npm install

# 2. Configurar o ambiente
cp .env.example .env
php artisan key:generate

# 3. Banco de dados (SQLite — padrão)
touch database/database.sqlite
php artisan migrate

# 4. Popular com dados de teste
php artisan db:seed

# 5. Iniciar o servidor de desenvolvimento
php artisan serve
npm run dev
```

Acesse: **http://localhost:8000**

---

## Perfis de Acesso

O controle de acesso é implementado via coluna `role` na tabela `users` e o middleware `RoleMiddleware`.

| Perfil        | Valor no BD   | Permissões                                                               |
|---------------|---------------|--------------------------------------------------------------------------|
| **Admin**     | `admin`       | Acesso total — CRUDs, aprovação de chamados, entrega, responsabilidades |
| **Gestor**    | `gestor`      | Igual ao Admin                                                           |
| **Funcionário**| `funcionario`| Abrir chamados, visualizar seus próprios chamados                        |

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

### Diagrama de Entidades

```
users
 ├── id, name, email, password, role (admin|gestor|funcionario)
 └── hasOne → funcionarios

funcionarios
 ├── id, user_id (FK nullable), nome, email, cargo
 ├── hasMany → chamados
 └── hasMany → responsabilidades

patrimonios
 ├── id, codigo_patrimonio (unique), descricao, modelo, numero_serie
 ├── status (disponivel|em_uso|manutencao)
 ├── hasMany → chamados
 └── hasMany → responsabilidades

chamados
 ├── id, funcionario_id (FK), patrimonio_id (FK nullable)
 ├── descricao, status (aberto|aprovado|negado|entregue)
 ├── belongsTo → funcionario
 └── belongsTo → patrimonio

responsabilidades
 ├── id, funcionario_id (FK), patrimonio_id (FK)
 ├── data_entrega, data_devolucao (nullable)
 ├── termo_responsabilidade, assinado (boolean)
 ├── belongsTo → funcionario
 └── belongsTo → patrimonio
```

### Migrations Criadas

| Arquivo                                             | Descrição                               |
|-----------------------------------------------------|-----------------------------------------|
| `..._create_users_table`                            | Tabela de usuários (Breeze)             |
| `..._create_funcionarios_table`                     | Tabela de funcionários                  |
| `..._create_patrimonios_table`                      | Tabela de patrimônios                   |
| `..._create_chamados_table`                         | Tabela de chamados                      |
| `..._create_responsabilidades_table`                | Tabela de responsabilidades             |
| `2026_03_04_200000_add_role_to_users_table`         | Adiciona coluna `role` em users         |
| `2026_03_04_200001_add_patrimonio_id_to_chamados`   | Adiciona FK `patrimonio_id` em chamados |
| `2026_03_04_200002_add_user_id_to_funcionarios`     | Adiciona FK `user_id` em funcionarios   |

---

## Arquitetura da Aplicação

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php         ← KPIs e gráficos
│   │   ├── PatrimonioController.php        ← CRUD de patrimônios
│   │   ├── FuncionarioController.php       ← CRUD de funcionários
│   │   ├── ChamadoController.php           ← Fluxo de chamados + ações
│   │   ├── ResponsabilidadeController.php  ← CRUD + geração de PDF
│   │   └── ProfileController.php           ← Breeze (perfil do usuário)
│   │
│   ├── Middleware/
│   │   └── RoleMiddleware.php              ← Controle de acesso por perfil
│   │
│   └── Requests/
│       ├── StorePatrimonioRequest.php
│       ├── UpdatePatrimonioRequest.php
│       ├── StoreFuncionarioRequest.php
│       ├── UpdateFuncionarioRequest.php
│       ├── StoreChamadoRequest.php
│       ├── UpdateChamadoRequest.php
│       ├── StoreResponsabilidadeRequest.php
│       └── UpdateResponsabilidadeRequest.php
│
├── Models/
│   ├── User.php           ← + role, isAdmin(), isGestor(), isFuncionario()
│   ├── Patrimonio.php     ← + statusLabels(), scopeDisponivel(), relacionamentos
│   ├── Funcionario.php    ← + relacionamentos
│   ├── Chamado.php        ← + statusLabels(), constantes de status
│   └── Responsabilidade.php ← + casts de datas, relacionamentos
│
└── Providers/
    └── AppServiceProvider.php  ← Paginator::useTailwind()

resources/views/
├── dashboard.blade.php          ← KPIs + Chart.js
├── components/
│   ├── status-badge.blade.php  ← Badge de status reutilizável
│   └── alert.blade.php         ← Alertas de sucesso/erro
├── patrimonios/    index, create, edit, show
├── funcionarios/   index, create, edit, show
├── chamados/       index, create, show
└── responsabilidades/ index, create, edit, show, pdf

database/seeders/
├── DatabaseSeeder.php        ← Orquestra todos os seeders
├── UserSeeder.php            ← 1 Admin, 1 Gestor, 10 Funcionários
├── FuncionarioSeeder.php     ← 10 Funcionários vinculados aos users
├── PatrimonioSeeder.php      ← 20 Patrimônios com status variado
├── ChamadoSeeder.php         ← 15 Chamados com status variado
└── ResponsabilidadeSeeder.php ← ~13 Responsabilidades (ativas + encerradas)
```

---

## Módulos do Sistema

### Patrimônios (`/patrimonios`)

- **Status possíveis:** `disponivel` · `em_uso` · `manutencao`
- O status é atualizado automaticamente para `em_uso` ao criar uma responsabilidade
- O status volta para `disponivel` ao registrar a `data_devolucao`
- **Acesso:** Admin e Gestor

### Funcionários (`/funcionarios`)

- Podem ser vinculados a um usuário do sistema via `user_id`
- Funcionários vinculados enxergam apenas seus próprios chamados
- **Acesso:** Admin e Gestor (CRUD) · Funcionário (view implícita via chamados)

### Chamados (`/chamados`)

- **Abrir chamado:** qualquer usuário autenticado
- **Filtro por status** disponível na listagem
- Funcionários veem apenas seus próprios chamados
- Admins/Gestores veem todos
- **Acesso às ações:** somente Admin e Gestor

**Estados e transições:**

```
aberto → [aprovar] → aprovado → [entregar] → entregue
aberto → [negar]  → negado
```

> Ao executar **Registrar Entrega**, o sistema automaticamente:
> 1. Cria um registro em `responsabilidades`
> 2. Altera o status do patrimônio para `em_uso`
> 3. Altera o status do chamado para `entregue`

### Responsabilidades (`/responsabilidades`)

- Podem ser criadas manualmente ou automaticamente via entrega de chamado
- Somente patrimônios com `status = disponivel` podem ser atribuídos
- Registrar `data_devolucao` devolve o patrimônio automaticamente
- **Download de PDF** disponível em cada registro
- **Acesso:** Admin e Gestor

---

## Fluxo Principal

```
[Funcionário]                    [Gestor/Admin]
     │                                │
     │  Abre chamado (/chamados/create)
     │  - Descreve necessidade         │
     │  - Seleciona patrimônio (opt.)  │
     │                                │
     │                 Visualiza chamados abertos
     │                 Aprova ou Nega
     │                                │
     │  [Se aprovado] Gestor registra entrega
     │                                │
     │                 Sistema cria automaticamente:
     │                 ✔ Responsabilidade com termo
     │                 ✔ Patrimônio → status: em_uso
     │                 ✔ Chamado → status: entregue
     │                                │
     │                 PDF disponível para download
```

---

## Geração de PDF

- Implementado com **barryvdh/laravel-dompdf v3.x**
- Template: `resources/views/responsabilidades/pdf.blade.php`
  - CSS inline (compatível com dompdf, sem Tailwind)
  - Inclui dados do funcionário, patrimônio, período, termo e campos de assinatura
- Rota: `GET /responsabilidades/{id}/pdf` → download `termo-responsabilidade-{id}.pdf`
- Papel A4 retrato

---

## Rotas da Aplicação

| Método     | URI                                   | Nome                       | Acesso              |
|------------|---------------------------------------|----------------------------|---------------------|
| GET        | `/dashboard`                          | `dashboard`                | auth                |
| GET        | `/patrimonios`                        | `patrimonios.index`        | admin, gestor       |
| POST       | `/patrimonios`                        | `patrimonios.store`        | admin, gestor       |
| GET        | `/patrimonios/create`                 | `patrimonios.create`       | admin, gestor       |
| GET        | `/patrimonios/{id}`                   | `patrimonios.show`         | admin, gestor       |
| PATCH      | `/patrimonios/{id}`                   | `patrimonios.update`       | admin, gestor       |
| DELETE     | `/patrimonios/{id}`                   | `patrimonios.destroy`      | admin, gestor       |
| GET        | `/funcionarios`                       | `funcionarios.index`       | admin, gestor       |
| *(+ CRUD completo)*                                            |                            |                     |
| GET        | `/chamados`                           | `chamados.index`           | auth (filtrado)     |
| POST       | `/chamados`                           | `chamados.store`           | auth                |
| PATCH      | `/chamados/{id}/aprovar`              | `chamados.aprovar`         | admin, gestor       |
| PATCH      | `/chamados/{id}/negar`                | `chamados.negar`           | admin, gestor       |
| PATCH      | `/chamados/{id}/entregar`             | `chamados.entregar`        | admin, gestor       |
| GET        | `/responsabilidades`                  | `responsabilidades.index`  | admin, gestor       |
| *(+ CRUD completo)*                                            |                            |                     |
| GET        | `/responsabilidades/{id}/pdf`         | `responsabilidades.pdf`    | admin, gestor       |

> Total: **52 rotas** registradas (`php artisan route:list`)

---

## Dados de Teste

Após `php artisan db:seed`:

| Entidade          | Quantidade | Observações                              |
|-------------------|------------|------------------------------------------|
| Usuários          | 12         | 1 admin, 1 gestor, 10 funcionários       |
| Funcionários      | 10         | Vinculados a usuários com role=funcionario|
| Patrimônios       | 20         | Mix de notebooks, monitores, impressoras…|
| Chamados          | 15         | Status variados (aberto/aprovado/negado/entregue)|
| Responsabilidades | ~13        | Ativas e encerradas (com devolução)      |

---

## Comandos Úteis

```bash
# Recriar banco do zero com dados de teste
php artisan migrate:fresh --seed

# Verificar rotas registradas
php artisan route:list

# Limpar caches
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

---

## Decisões Técnicas

| Decisão | Justificativa |
|---|---|
| Roles via coluna `role` | Suficiente para 3 perfis fixos — sem overhead do `spatie/laravel-permission` |
| `patrimonio_id` via nova migration | Preserva histórico de migrations, evita reescrita |
| dompdf (não wkhtmltopdf) | Sem dependências do sistema operacional, instalação simples via Composer |
| Chart.js via CDN | Evita bundling complexo para 2 gráficos; carregado via `@stack('scripts')` |
| CSS inline no PDF | dompdf tem suporte limitado a CSS externo; inline garante fidelidade visual |
| Paginação Tailwind | `Paginator::useTailwind()` no `AppServiceProvider` integra nativamente com o design |
