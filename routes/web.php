<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChamadoController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\EquipeUsuarioController;
use App\Http\Controllers\AdminEquipeController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('chamados', ChamadoController::class);
    Route::resource('equipes', EquipeController::class);
    Route::post('/chamados/{chamado}/mensagens', [ChamadoController::class, 'storeMensagem'])->name('mensagens.store');
    Route::post('/chamados/{chamado}/anexos', [ChamadoController::class, 'adicionarAnexo'])->name('chamados.anexos.store');

    

    
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/atribuir-usuarios', [EquipeUsuarioController::class, 'edit'])->name('equipes.usuarios.edit');
    Route::post('/atribuir-usuarios', [EquipeUsuarioController::class, 'update'])->name('equipes.usuarios.update');
});

Route::get('/atribuir-usuarios', [EquipeController::class, 'atribuirUsuariosForm'])
    ->middleware('auth')
    ->name('equipes.atribuir');

Route::middleware(['auth','admin'])->group(function () {
    Route::get('/admin/equipes', [\App\Http\Controllers\AdminEquipeController::class, 'index'])
        ->name('admin.equipes.index');
    Route::get('/admin/equipes', [AdminEquipeController::class, 'index'])->name('admin.equipes.index');
    Route::post('/admin/equipes/atribuir', [AdminEquipeController::class, 'atribuir'])->name('admin.equipes.atribuir');
    Route::post('/admin/equipes/remover',  [AdminEquipeController::class, 'remover'])->name('admin.equipes.remover');
});

require __DIR__.'/auth.php';
