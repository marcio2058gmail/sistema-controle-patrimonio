<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResponsibilityController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// User profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes for Admin and Manager
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::resource('departments', DepartmentController::class)->only(['index', 'show']);
    Route::resource('assets', AssetController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('responsibilities', ResponsibilityController::class);
    Route::get('/responsibilities/{responsibility}/pdf', [ResponsibilityController::class, 'gerarPdf'])
        ->name('responsibilities.pdf');

    // Admin-only actions and management
    Route::middleware('role:admin')->group(function () {
        Route::resource('departments', DepartmentController::class)->except(['index', 'show']);
        Route::patch('/tickets/{ticket}/aprovar', [TicketController::class, 'aprovar'])->name('tickets.aprovar');
        Route::patch('/tickets/{ticket}/negar', [TicketController::class, 'negar'])->name('tickets.negar');
        Route::patch('/tickets/{ticket}/entregar', [TicketController::class, 'entregar'])->name('tickets.entregar');
        Route::resource('managers', ManagerController::class)
            ->parameters(['managers' => 'manager'])
            ->except(['show']);
    });
});

// Tickets: visible to all authenticated users
Route::middleware('auth')->group(function () {
    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show']);
});

require __DIR__.'/auth.php';
