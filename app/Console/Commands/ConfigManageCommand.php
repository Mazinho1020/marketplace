<?php

namespace App\Console\Commands;

use App\Models\Config\{ConfigDefinition, ConfigEnvironment, ConfigGroup, ConfigSite};
use App\Services\Config\ConfigManager;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Comando para gerenciar configurações via CLI
 * 
 * Exemplos de uso:
 * php artisan config:manage get app_name
 * php artisan config:manage set app_name "Novo Nome" --site=pdv
 * php artisan config:manage list --group=geral
 * php artisan config:manage clear-cache
 */
class ConfigManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'config:manage 
                            {action : Ação a executar (get, set, list, clear-cache)}
                            {key? : Chave da configuração (para get/set)}
                            {value? : Valor da configuração (para set)}
                            {--site= : Código do site específico}
                            {--environment= : Código do ambiente específico}
                            {--group= : Código do grupo (para list)}
                            {--empresa= : ID da empresa (padrão: primeira encontrada)}';

    /**
     * The console command description.
     */
    protected $description = 'Gerencia configurações do sistema via linha de comando';

    protected ConfigManager $configManager;

    /**
     * Execute the console command.
     */
    public function handle(ConfigManager $configManager): int
    {
        $this->configManager = $configManager;

        $action = $this->argument('action');
        $empresaId = $this->getEmpresaId();

        if (!$empresaId) {
            $this->error('Nenhuma empresa encontrada. Certifique-se de que existe pelo menos uma empresa no sistema.');
            return 1;
        }

        // Definir contexto baseado nos parâmetros
        $siteId = $this->getSiteId();
        $environmentId = $this->getEnvironmentId();

        $this->configManager->setContext($empresaId, $siteId, $environmentId);

        return match ($action) {
            'get' => $this->handleGet(),
            'set' => $this->handleSet(),
            'list' => $this->handleList(),
            'clear-cache' => $this->handleClearCache(),
            default => $this->handleInvalidAction($action)
        };
    }

    /**
     * Manipula ação 'get'
     */
    private function handleGet(): int
    {
        $key = $this->argument('key');

        if (!$key) {
            $this->error('Chave é obrigatória para a ação get.');
            return 1;
        }

        $value = $this->configManager->get($key);

        if ($value === null) {
            $this->warn("Configuração '{$key}' não encontrada.");
            return 1;
        }

        $this->info("Configuração: {$key}");
        $this->line("Valor: " . $this->formatValue($value));

        return 0;
    }

    /**
     * Manipula ação 'set'
     */
    private function handleSet(): int
    {
        $key = $this->argument('key');
        $value = $this->argument('value');

        if (!$key) {
            $this->error('Chave é obrigatória para a ação set.');
            return 1;
        }

        if ($value === null) {
            $this->error('Valor é obrigatório para a ação set.');
            return 1;
        }

        $siteId = $this->getSiteId();
        $environmentId = $this->getEnvironmentId();

        $success = $this->configManager->set($key, $value, $siteId, $environmentId);

        if ($success) {
            $this->info("Configuração '{$key}' definida com sucesso!");

            $scope = $this->buildScopeDescription($siteId, $environmentId);
            if ($scope) {
                $this->line("Escopo: {$scope}");
            }

            return 0;
        } else {
            $this->error("Erro ao definir configuração '{$key}'.");
            return 1;
        }
    }

    /**
     * Manipula ação 'list'
     */
    private function handleList(): int
    {
        $groupCode = $this->option('group');

        if ($groupCode) {
            return $this->listByGroup($groupCode);
        } else {
            return $this->listAll();
        }
    }

    /**
     * Lista configurações por grupo
     */
    private function listByGroup(string $groupCode): int
    {
        $configs = $this->configManager->getGroup($groupCode);

        if ($configs->isEmpty()) {
            $this->warn("Nenhuma configuração encontrada para o grupo '{$groupCode}'.");
            return 1;
        }

        $this->info("Configurações do grupo '{$groupCode}':");
        $this->line('');

        $this->table(
            ['Chave', 'Valor'],
            $configs->map(function ($value, $key) {
                return [$key, $this->formatValue($value)];
            })->toArray()
        );

        return 0;
    }

    /**
     * Lista todas as configurações
     */
    private function listAll(): int
    {
        $empresaId = $this->getEmpresaId();

        $groups = ConfigGroup::forEmpresa($empresaId)
            ->ativo()
            ->ordered()
            ->get();

        if ($groups->isEmpty()) {
            $this->warn('Nenhum grupo de configuração encontrado.');
            return 1;
        }

        foreach ($groups as $group) {
            $configs = $this->configManager->getGroup($group->codigo);

            if ($configs->isNotEmpty()) {
                $this->info("Grupo: {$group->nome} ({$group->codigo})");
                $this->line('');

                $this->table(
                    ['Chave', 'Valor'],
                    $configs->map(function ($value, $key) {
                        return [$key, $this->formatValue($value)];
                    })->toArray()
                );

                $this->line('');
            }
        }

        return 0;
    }

    /**
     * Manipula ação 'clear-cache'
     */
    private function handleClearCache(): int
    {
        $this->configManager->clearCache();
        $this->info('Cache de configurações limpo com sucesso!');
        return 0;
    }

    /**
     * Manipula ação inválida
     */
    private function handleInvalidAction(string $action): int
    {
        $this->error("Ação inválida: {$action}");
        $this->line('');
        $this->line('Ações válidas:');
        $this->line('  get      - Obtém valor de uma configuração');
        $this->line('  set      - Define valor de uma configuração');
        $this->line('  list     - Lista configurações');
        $this->line('  clear-cache - Limpa cache de configurações');

        return 1;
    }

    /**
     * Obtém ID da empresa
     */
    private function getEmpresaId(): ?int
    {
        $empresaId = $this->option('empresa');

        if ($empresaId) {
            return (int) $empresaId;
        }

        // Buscar primeira empresa disponível
        $business = \App\Models\Business\Business::first();
        return $business?->id;
    }

    /**
     * Obtém ID do site baseado no código
     */
    private function getSiteId(): ?int
    {
        $siteCode = $this->option('site');

        if (!$siteCode) {
            return null;
        }

        $site = ConfigSite::forEmpresa($this->getEmpresaId())
            ->where('codigo', $siteCode)
            ->first();

        if (!$site) {
            $this->warn("Site '{$siteCode}' não encontrado.");
            return null;
        }

        return $site->id;
    }

    /**
     * Obtém ID do ambiente baseado no código
     */
    private function getEnvironmentId(): ?int
    {
        $environmentCode = $this->option('environment');

        if (!$environmentCode) {
            return null;
        }

        $environment = ConfigEnvironment::forEmpresa($this->getEmpresaId())
            ->where('codigo', $environmentCode)
            ->first();

        if (!$environment) {
            $this->warn("Ambiente '{$environmentCode}' não encontrado.");
            return null;
        }

        return $environment->id;
    }

    /**
     * Formata valor para exibição
     */
    private function formatValue($value): string
    {
        if ($value === null) {
            return '<null>';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    }

    /**
     * Constrói descrição do escopo
     */
    private function buildScopeDescription(?int $siteId, ?int $environmentId): ?string
    {
        $parts = [];

        if ($siteId) {
            $site = ConfigSite::find($siteId);
            if ($site) {
                $parts[] = "Site: {$site->nome}";
            }
        }

        if ($environmentId) {
            $environment = ConfigEnvironment::find($environmentId);
            if ($environment) {
                $parts[] = "Ambiente: {$environment->nome}";
            }
        }

        if (empty($parts)) {
            return 'Global (todos os sites e ambientes)';
        }

        return implode(' | ', $parts);
    }
}
