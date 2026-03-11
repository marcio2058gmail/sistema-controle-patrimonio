<?php

use App\Http\Controllers\ManutencaoController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResponsibilityController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// -------------------------------------------------------
// Rotas de empresa (seleção/switch) — sem company.select
// -------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/companies/select', [CompanyController::class, 'select'])->name('companies.select');
    Route::post('/companies/switch', [CompanyController::class, 'switch'])->name('companies.switch');
});

// Gestão de usuários — admin e super_admin (company.select ignora super_admin automaticamente)
Route::middleware(['auth', 'company.select', 'role:admin,super_admin'])->group(function () {
    Route::resource('users', UserManagementController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

// Gestão de empresas — super_admin apenas (sem company.select)
Route::middleware(['auth', 'role:super_admin'])->prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::post('/', [CompanyController::class, 'store'])->name('store');
    Route::patch('/{company}', [CompanyController::class, 'update'])->name('update');
    Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
    Route::get('/{company}/users', [CompanyController::class, 'users'])->name('users');
    Route::post('/{company}/users', [CompanyController::class, 'addUser'])->name('addUser');
    Route::delete('/{company}/users', [CompanyController::class, 'removeUser'])->name('removeUser');
});

// Planos SaaS — super_admin apenas
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::resource('plans', PlanController::class)->except(['show']);

    // Compatibilidade retroativa — redireciona para o novo módulo admin
    Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'create', 'store']);
    Route::post('/subscriptions/{company}/cancel', [SubscriptionController::class, 'cancel'])
        ->name('subscriptions.cancel');
});

// -------------------------------------------------------
// Módulo Admin — Gestão de Assinaturas (SuperAdmin)
// -------------------------------------------------------
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Assinaturas
        Route::get('/subscriptions',                           [AdminSubscriptionController::class, 'index'])   ->name('subscriptions.index');
        Route::get('/subscriptions/create',                    [AdminSubscriptionController::class, 'create'])  ->name('subscriptions.create');
        Route::post('/subscriptions',                          [AdminSubscriptionController::class, 'store'])   ->name('subscriptions.store');
        Route::get('/subscriptions/{subscription}',            [AdminSubscriptionController::class, 'show'])    ->name('subscriptions.show');
        Route::patch('/subscriptions/{subscription}/plan',     [AdminSubscriptionController::class, 'changePlan'])   ->name('subscriptions.changePlan');
        Route::patch('/subscriptions/{subscription}/status',   [AdminSubscriptionController::class, 'changeStatus']) ->name('subscriptions.changeStatus');
        Route::post('/subscriptions/{company}/cancel',         [AdminSubscriptionController::class, 'cancel'])  ->name('subscriptions.cancel');

        // Faturas por empresa
        Route::get('/companies/{company}/invoices',            [AdminInvoiceController::class, 'index'])        ->name('subscriptions.invoices.index');
        Route::post('/companies/{company}/invoices',           [AdminInvoiceController::class, 'store'])        ->name('subscriptions.invoices.store');
        Route::patch('/invoices/{invoice}/mark-paid',          [AdminInvoiceController::class, 'markPaid'])     ->name('subscriptions.invoices.markPaid');
        Route::patch('/invoices/{invoice}/cancel',             [AdminInvoiceController::class, 'cancel'])       ->name('subscriptions.invoices.cancel');
    });

// -------------------------------------------------------
// Profile (sem company.select — usuário pode editar perfil a qualquer momento)
// -------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// -------------------------------------------------------
// Rotas de negócio — requerem empresa selecionada
// -------------------------------------------------------

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'company.select'])
    ->name('dashboard');

// Admin e Manager
Route::middleware(['auth', 'company.select', 'role:admin,manager'])->group(function () {
    Route::resource('departments', DepartmentController::class)->only(['index', 'show']);
    Route::resource('assets', AssetController::class)->except(['index', 'show']);
    Route::resource('employees', EmployeeController::class)->only(['index', 'show']);

    // Admin-only
    Route::middleware('role:admin')->group(function () {
        Route::resource('employees', EmployeeController::class)->except(['index', 'show']);
        Route::resource('responsibilities', ResponsibilityController::class)->except(['index', 'show']);
        Route::resource('departments', DepartmentController::class)->except(['index', 'show']);
        Route::patch('/tickets/{ticket}/aprovar', [TicketController::class, 'aprovar'])->name('tickets.aprovar');
        Route::patch('/tickets/{ticket}/negar', [TicketController::class, 'negar'])->name('tickets.negar');
        Route::patch('/tickets/{ticket}/entregar', [TicketController::class, 'entregar'])->name('tickets.entregar');
        Route::resource('manutencoes', ManutencaoController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['manutencoes' => 'manutencao']);
    });

    // Inventários — admin e manager
    Route::resource('inventories', InventoryController::class)->only(['index', 'store', 'show']);
    Route::post('/inventories/{inventory}/close', [InventoryController::class, 'close'])
        ->name('inventories.close');
    Route::patch('/inventories/items/{item}', [InventoryController::class, 'updateItem'])
        ->name('inventories.items.update');
});

// Chamados e termos — todos os autenticados com empresa
Route::middleware(['auth', 'company.select'])->group(function () {
    Route::resource('tickets', TicketController::class)->only(['index', 'store', 'show']);
    Route::resource('responsibilities', ResponsibilityController::class)->only(['index', 'show']);
    Route::resource('assets', AssetController::class)->only(['index', 'show']);
    Route::get('/responsibilities/{responsibility}/pdf', [ResponsibilityController::class, 'gerarPdf'])
        ->name('responsibilities.pdf');
    Route::post('/responsibilities/{responsibility}/assinar', [ResponsibilityController::class, 'assinar'])
        ->name('responsibilities.assinar');
    Route::post('/responsibilities/{responsibility}/devolver', [ResponsibilityController::class, 'devolver'])
        ->name('responsibilities.devolver');
});


require __DIR__.'/auth.php';
