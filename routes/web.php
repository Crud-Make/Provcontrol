<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FechamentoController;
use App\Http\Middleware\SetCurrentPosto;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', SetCurrentPosto::class])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/fechamentos', [FechamentoController::class, 'index'])->name('fechamentos.index');
    Route::get('/fechamentos/create', [FechamentoController::class, 'create'])->name('fechamentos.create');
    Route::post('/fechamentos/preview', [FechamentoController::class, 'preview'])->name('fechamentos.preview');
    Route::post('/fechamentos/atualizar', [FechamentoController::class, 'atualizar'])->name('fechamentos.atualizar');
    Route::post('/fechamentos', [FechamentoController::class, 'store'])->name('fechamentos.store');
    Route::get('/fechamentos/{fechamento}', [FechamentoController::class, 'show'])
        ->whereNumber('fechamento')
        ->name('fechamentos.show');
});
