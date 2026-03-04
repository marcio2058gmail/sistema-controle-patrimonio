<?php

use App\Http\Controllers\ChamadoController;
use App\Http\Controllers\GestorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\PatrimonioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResponsabilidadeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Perfil do usuário
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas para Admin e Gestor
Route::middleware(['auth', 'role:admin,gestor'])->group(function () {
    Route::resource('departamentos', DepartamentoController::class);
    Route::resource('patrimonios', PatrimonioController::class);
    Route::resource('funcionarios', FuncionarioController::class);
    Route::resource('responsabilidades', ResponsabilidadeController::class);
    Route::get('/responsabilidades/{responsabilidade}/pdf', [ResponsabilidadeController::class, 'gerarPdf'])
        ->name('responsabilidades.pdf');

    // Ações e gestão exclusivas do Admin
    Route::middleware('role:admin')->group(function () {
        Route::patch('/chamados/{chamado}/aprovar', [ChamadoController::class, 'aprovar'])->name('chamados.aprovar');
        Route::patch('/chamados/{chamado}/negar', [ChamadoController::class, 'negar'])->name('chamados.negar');
        Route::patch('/chamados/{chamado}/entregar', [ChamadoController::class, 'entregar'])->name('chamados.entregar');
        Route::resource('gestores', GestorController::class)
            ->parameters(['gestores' => 'gestor'])
            ->except(['show']);
    });
});

// Chamados: visible a todos os autenticados, criação por todos
Route::middleware('auth')->group(function () {
    Route::resource('chamados', ChamadoController::class)->only(['index', 'create', 'store', 'show']);
});

require __DIR__.'/auth.php';
