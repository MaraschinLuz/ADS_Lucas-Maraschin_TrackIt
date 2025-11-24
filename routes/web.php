<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChamadoController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\EquipeUsuarioController;
use App\Http\Controllers\AdminEquipeController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\EquipeChatController;
use App\Http\Controllers\EquipeMembrosController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    Route::get('/chamados/kanban', [ChamadoController::class, 'kanban'])->name('chamados.kanban');
    Route::resource('chamados', ChamadoController::class);
    Route::post('/chamados/{chamado}/mensagens', [ChamadoController::class, 'storeMensagem'])->name('mensagens.store');
    Route::post('/chamados/{chamado}/anexos',    [ChamadoController::class, 'adicionarAnexo'])->name('chamados.anexos.store');
    
    Route::post('/chamados/{chamado}/notas',     [ChamadoController::class, 'storeNotaInterna'])->name('chamados.notas.store');
    Route::delete('/chamados/{chamado}/notas/{nota}', [ChamadoController::class, 'deleteNotaInterna'])->name('chamados.notas.delete');
    Route::post('/chamados/{chamado}/follow',    [ChamadoController::class, 'follow'])->name('chamados.follow');
    Route::delete('/chamados/{chamado}/follow',  [ChamadoController::class, 'unfollow'])->name('chamados.unfollow');

    
    Route::resource('equipes', EquipeController::class);

    
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

    
    Route::get('/atribuir-usuarios',      [EquipeUsuarioController::class, 'edit'])->name('equipes.usuarios.edit');
    Route::post('/atribuir-usuarios',     [EquipeUsuarioController::class, 'update'])->name('equipes.usuarios.update');
    Route::get('/atribuir-usuarios-form', [EquipeController::class, 'atribuirUsuariosForm'])->name('equipes.atribuir');

    
    Route::get('/equipe/chat', [EquipeChatController::class, 'index'])->name('equipe.chat');
    Route::get('/equipe/chat/messages', [EquipeChatController::class, 'list'])->name('equipe.chat.list');
    Route::post('/equipe/chat/messages', [EquipeChatController::class, 'store'])->name('equipe.chat.store');
    Route::get('/equipe/membros', [EquipeMembrosController::class, 'index'])->name('equipe.membros');

    
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read_all');
});


Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/usuarios', [AdminUserController::class, 'index'])
        ->name('usuarios.index');
        
        Route::get('/equipes',               [AdminEquipeController::class, 'index'])->name('equipes.index');
        Route::post('/equipes/atribuir',     [AdminEquipeController::class, 'atribuir'])->name('equipes.atribuir');
        Route::post('/equipes/remover',      [AdminEquipeController::class, 'remover'])->name('equipes.remover');

        
        Route::get('/usuarios/tecnico/create', [AdminUserController::class, 'createTecnico'])->name('usuarios.tecnico.create');
        Route::post('/usuarios/tecnico',       [AdminUserController::class, 'storeTecnico'])->name('usuarios.tecnico.store');

        Route::get('/usuarios/{user}/role',    [AdminUserController::class, 'editRole'])->name('usuarios.role.edit');
        Route::put('/usuarios/{user}/role',    [AdminUserController::class, 'updateRole'])->name('usuarios.role.update');
    });

require __DIR__ . '/auth.php';
