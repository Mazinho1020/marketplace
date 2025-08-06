<?php

use App\Http\Controllers\Admin\Permission\PermissionController;
use App\Http\Controllers\Admin\Permission\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Grupo de rotas específicas de permissões (evitando conflitos)
Route::prefix('admin/permission-management')->name('admin.permission.')->middleware(['auth'])->group(function () {

    // Usuários - Gestão de Permissões
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:usuarios.listar')
            ->name('index');

        Route::get('/create', [UserController::class, 'create'])
            ->middleware('permission:usuarios.criar')
            ->name('create');

        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:usuarios.criar')
            ->name('store');

        Route::get('/{user}', [UserController::class, 'show'])
            ->middleware('permission:usuarios.visualizar')
            ->name('show');

        Route::get('/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:usuarios.editar')
            ->name('edit');

        Route::put('/{user}', [UserController::class, 'update'])
            ->middleware('permission:usuarios.editar')
            ->name('update');

        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:usuarios.excluir')
            ->name('destroy');

        // Gestão de permissões do usuário
        Route::get('/{user}/permissions', [UserController::class, 'permissions'])
            ->middleware('permission:usuarios.gerenciar_permissoes')
            ->name('permissions');

        Route::post('/{user}/permissions', [UserController::class, 'updatePermissions'])
            ->middleware('permission:usuarios.gerenciar_permissoes')
            ->name('permissions.update');
    });

    // Permissões
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])
            ->middleware('permission:configuracoes.seguranca')
            ->name('index');

        Route::get('/create', [PermissionController::class, 'create'])
            ->middleware('permission:configuracoes.seguranca')
            ->name('create');

        Route::post('/', [PermissionController::class, 'store'])
            ->middleware('permission:configuracoes.seguranca')
            ->name('store');

        Route::get('/{permission}', [PermissionController::class, 'show'])
            ->middleware('permission:configuracoes.seguranca')
            ->name('show');
    });

    // Papéis
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('permission:configuracoes.seguranca')
            ->name('index');

        Route::get('/create', [RoleController::class, 'create'])
            ->middleware('permission:configuracoes.seguranca')
            ->name('create');

        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission:configuracoes.seguranca')
            ->name('store');

        Route::get('/{role}', [RoleController::class, 'show'])
            ->middleware('permission:configuracoes.seguranca')
            ->name('show');

        Route::get('/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:configuracoes.seguranca')
            ->name('edit');

        Route::put('/{role}', [RoleController::class, 'update'])
            ->middleware('permission:configuracoes.seguranca')
            ->name('update');

        Route::delete('/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:configuracoes.seguranca')
            ->name('destroy');
    });
});

// API Routes
Route::prefix('api/admin')->name('api.admin.')->middleware(['auth'])->group(function () {
    Route::get('/my-permissions', [PermissionController::class, 'myPermissions'])
        ->name('my-permissions');
});
