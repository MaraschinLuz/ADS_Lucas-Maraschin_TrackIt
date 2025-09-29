<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChamadoController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\EquipeUsuarioController;
use App\Http\Controllers\AdminEquipeController;
use App\Http\Controllers\AdminUserController; // << FALTAVA

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/**
 * Área autenticada (todos os papéis)
 */
Route::middleware('auth')->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chamados
    Route::resource('chamados', ChamadoController::class);
    Route::post('/chamados/{chamado}/mensagens', [ChamadoController::class, 'storeMensagem'])->name('mensagens.store');
    Route::post('/chamados/{chamado}/anexos', [ChamadoController::class, 'adicionarAnexo'])->name('chamados.anexos.store');

    // Equipes (técnicos/usuários usam essas telas gerais)
    Route::resource('equipes', EquipeController::class);

    // Logs
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

    // Atribuir usuários às equipes (fluxo antigo — mantenha se usar)
    Route::get('/atribuir-usuarios', [EquipeUsuarioController::class, 'edit'])->name('equipes.usuarios.edit');
    Route::post('/atribuir-usuarios', [EquipeUsuarioController::class, 'update'])->name('equipes.usuarios.update');

    // (Se ainda usa esse atalho, deixe-o. Caso contrário, remova para evitar duplicidade.)
    Route::get('/atribuir-usuarios-form', [EquipeController::class, 'atribuirUsuariosForm'])->name('equipes.atribuir');
});

/**
 * Área do ADMIN (proteção por middleware 'admin')
 */
Route::middleware(['auth','admin'])->group(function () {
    // Gerir equipes (admin)
    Route::get('/admin/equipes', [AdminEquipeController::class, 'index'])->name('admin.equipes.index');
    Route::post('/admin/equipes/atribuir', [AdminEquipeController::class, 'atribuir'])->name('admin.equipes.atribuir');
    Route::post('/admin/equipes/remover',  [AdminEquipeController::class, 'remover'])->name('admin.equipes.remover');

    // Criar usuário técnico (admin)
    Route::get('/admin/usuarios/tecnico/create', [AdminUserController::class, 'createTecnico'])->name('admin.usuarios.tecnico.create');
    Route::post('/admin/usuarios/tecnico',       [AdminUserController::class, 'storeTecnico'])->name('admin.usuarios.tecnico.store');

    // Promover/Demover (admin)
    Route::get('/admin/usuarios/{user}/role',    [AdminUserController::class, 'editRole'])->name('admin.usuarios.role.edit');
    Route::put('/admin/usuarios/{user}/role',    [AdminUserController::class, 'updateRole'])->name('admin.usuarios.role.update');
});

require __DIR__.'/auth.php';
