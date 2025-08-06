<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupAutoPermissions extends Command
{
    protected $signature = 'permissions:setup {--force : Sobrescrever configurações existentes}';
    protected $description = 'Configura o sistema de permissões automáticas para todo o site';

    public function handle()
    {
        $this->info('🚀 Configurando Sistema de Permissões Automáticas...');

        // 1. Atualizar arquivo de rotas dos comerciantes
        $this->updateComerciantesRoutes();

        // 2. Registrar o middleware no Kernel
        $this->updateKernel();

        // 3. Registrar o provider no config/app.php
        $this->updateAppConfig();

        $this->info('✅ Sistema de permissões automáticas configurado!');
        $this->line('');
        $this->info('📋 Próximos passos:');
        $this->line('1. Execute: php artisan config:cache');
        $this->line('2. Execute: php artisan route:cache');
        $this->line('3. Teste as rotas protegidas');
        $this->line('');
        $this->info('🎯 Como funciona:');
        $this->line('- Todas as rotas de comerciantes agora verificam permissões automaticamente');
        $this->line('- As permissões são determinadas baseadas na rota e método HTTP');
        $this->line('- Use @permission("recurso.acao") nas views para controlar exibição');
        $this->line('- Use @empresaPermission("acao", $empresaId) para permissões de empresa');
    }

    protected function updateComerciantesRoutes()
    {
        $this->info('📝 Atualizando rotas de comerciantes...');

        $routeFile = base_path('routes/comerciantes.php');

        if (!File::exists($routeFile)) {
            $this->warn("Arquivo {$routeFile} não encontrado. Criando...");
            $this->createComerciantesRouteFile($routeFile);
        }

        // Verificar se já tem a configuração
        $content = File::get($routeFile);

        if (str_contains($content, 'comerciantes.protected')) {
            $this->line('✅ Rotas já configuradas');
            return;
        }

        $this->line('🔧 Aplicando middleware automático...');

        // Backup do arquivo original
        File::copy($routeFile, $routeFile . '.backup');

        // Atualizar conteúdo
        $newContent = $this->getUpdatedRoutesContent($content);
        File::put($routeFile, $newContent);

        $this->line('✅ Rotas atualizadas');
    }

    protected function createComerciantesRouteFile($path)
    {
        $content = <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;
use App\Comerciantes\Controllers\DashboardController;
use App\Comerciantes\Controllers\EmpresaController;

/*
|--------------------------------------------------------------------------
| Rotas de Comerciantes
|--------------------------------------------------------------------------
*/

// Rotas de autenticação (sem proteção automática)
Route::prefix('comerciantes')->name('comerciantes.')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// Rotas protegidas com permissões automáticas
Route::prefix('comerciantes')->name('comerciantes.')->middleware(['comerciantes.protected'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Empresas
    Route::resource('empresas', EmpresaController::class);
    Route::prefix('empresas/{empresa}')->name('empresas.')->group(function () {
        Route::get('usuarios', [EmpresaController::class, 'usuarios'])->name('usuarios.index');
        Route::post('usuarios', [EmpresaController::class, 'adicionarUsuario'])->name('usuarios.store');
        Route::put('usuarios/{usuario}', [EmpresaController::class, 'editarUsuario'])->name('usuarios.update');
        Route::delete('usuarios/{usuario}', [EmpresaController::class, 'removerUsuario'])->name('usuarios.destroy');
    });
    
});
PHP;

        File::put($path, $content);
    }

    protected function getUpdatedRoutesContent($content)
    {
        // Procurar o grupo principal de rotas autenticadas
        $pattern = '/Route::middleware\(\[([^\]]*)\]\)->group\(function \(\) \{/';

        if (preg_match($pattern, $content)) {
            // Substituir middleware existente
            $content = preg_replace(
                $pattern,
                "Route::middleware(['comerciantes.protected'])->group(function () {",
                $content
            );
        } else {
            // Adicionar middleware ao grupo existente
            $content = str_replace(
                "Route::prefix('comerciantes')->name('comerciantes.')->group(function () {",
                "Route::prefix('comerciantes')->name('comerciantes.')->middleware(['comerciantes.protected'])->group(function () {",
                $content
            );
        }

        return $content;
    }

    protected function updateKernel()
    {
        $this->info('⚙️ Atualizando bootstrap/app.php...');

        $appFile = base_path('bootstrap/app.php');
        $content = File::get($appFile);

        if (str_contains($content, 'auto.permission')) {
            $this->line('✅ Bootstrap já configurado');
            return;
        }

        // Backup
        File::copy($appFile, $appFile . '.backup.' . date('Y-m-d-H-i-s'));

        // Adicionar middleware ao array de alias
        $middlewarePattern = '/(\$middleware->alias\(\[.*?)\];/s';

        if (preg_match($middlewarePattern, $content, $matches)) {
            $middlewareArray = $matches[1];

            if (!str_contains($middlewareArray, 'auto.permission')) {
                $newMiddleware = $middlewareArray . "\n            'auto.permission' => \\App\\Http\\Middleware\\AutoPermissionCheck::class,";
                $content = str_replace($middlewareArray, $newMiddleware, $content);

                File::put($appFile, $content);
                $this->line('✅ Middleware adicionado ao bootstrap/app.php');
            }
        }
    }

    protected function updateAppConfig()
    {
        $this->info('🔧 Atualizando configuração da aplicação...');

        $configFile = config_path('app.php');
        $content = File::get($configFile);

        if (str_contains($content, 'PermissionServiceProvider')) {
            $this->line('✅ Provider já registrado');
            return;
        }

        // Adicionar provider
        $providerPattern = '/(App\\\\Providers\\\\RouteServiceProvider::class,)/';
        $replacement = '$1' . "\n        App\\Providers\\PermissionServiceProvider::class,";

        $content = preg_replace($providerPattern, $replacement, $content);

        File::put($configFile, $content);
        $this->line('✅ Provider registrado');
    }
}
