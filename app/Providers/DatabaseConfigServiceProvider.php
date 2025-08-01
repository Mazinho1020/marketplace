<?php

namespace App\Providers;

use App\Services\Database\DatabaseEnvironmentService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

/**
 * Service Provider para configuração dinâmica de banco de dados
 * Usa funcionalidades nativas do Laravel
 */
class DatabaseConfigServiceProvider extends ServiceProvider
{
    /**
     * Registra o serviço.
     */
    public function register()
    {
        // Registrar o service como singleton
        $this->app->singleton(DatabaseEnvironmentService::class, function ($app) {
            return DatabaseEnvironmentService::getInstance();
        });
    }

    /**
     * Bootstrap do serviço.
     */
    public function boot()
    {
        // Configurar conexão dinâmica após Laravel estar carregado
        $this->configureDynamicDatabase();

        // Registrar comandos Artisan se estiver em modo console
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }

        // Registrar middleware se necessário
        $this->registerMiddleware();
    }

    /**
     * Configura conexão dinâmica usando o service
     */
    private function configureDynamicDatabase()
    {
        try {
            $service = DatabaseEnvironmentService::getInstance();

            // Testar conexão
            $connectionTest = $service->testConnection();

            Log::info('DatabaseConfigServiceProvider: Inicialização completa', [
                'environment' => $service->getCurrentEnvironment(),
                'connection_test' => $connectionTest,
                'config' => $service->getConfig()
            ]);
        } catch (\Exception $e) {
            Log::error('DatabaseConfigServiceProvider: Erro na inicialização', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Registra comandos Artisan personalizados
     */
    private function registerCommands()
    {
        // Futuramente: comandos para gerenciar configurações
        // $this->commands([
        //     Commands\DatabaseConfigCommand::class,
        //     Commands\DatabaseTestCommand::class,
        // ]);
    }

    /**
     * Registra middleware personalizado
     */
    private function registerMiddleware()
    {
        // Futuramente: middleware para validar conexão de banco por request
        // $router = $this->app['router'];
        // $router->aliasMiddleware('db.validate', Middleware\ValidateDatabaseConnection::class);
    }

    /**
     * Obtém informações de debug
     */
    public static function getDebugInfo(): array
    {
        try {
            $service = app(DatabaseEnvironmentService::class);
            return $service->getDebugInfo();
        } catch (\Exception $e) {
            return [
                'error' => 'Erro ao obter informações de debug',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Recarrega configuração
     */
    public static function reloadConfiguration(): bool
    {
        try {
            $service = app(DatabaseEnvironmentService::class);
            $service->reloadConfiguration();
            return true;
        } catch (\Exception $e) {
            Log::error('DatabaseConfigServiceProvider: Erro ao recarregar configuração', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
