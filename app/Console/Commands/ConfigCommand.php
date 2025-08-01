<?php

namespace App\Console\Commands;

use App\Services\ConfigService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:manage 
                           {action : Ação a ser executada (get|set|clear-cache|list|export)}
                           {--key= : Chave da configuração}
                           {--value= : Valor da configuração}
                           {--empresa= : ID da empresa}
                           {--site= : Código do site}
                           {--ambiente= : Código do ambiente}
                           {--group= : Código do grupo}
                           {--output= : Arquivo de saída para export}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gerenciar configurações do sistema via linha de comando';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'get':
                return $this->getConfig();
            case 'set':
                return $this->setConfig();
            case 'clear-cache':
                return $this->clearCache();
            case 'list':
                return $this->listConfigs();
            case 'export':
                return $this->exportConfigs();
            default:
                $this->error("Ação '{$action}' não reconhecida.");
                $this->info('Ações disponíveis: get, set, clear-cache, list, export');
                return 1;
        }
    }

    /**
     * Obter valor de configuração
     */
    private function getConfig()
    {
        $key = $this->option('key');
        if (!$key) {
            $this->error('A opção --key é obrigatória para a ação get.');
            return 1;
        }

        try {
            $value = ConfigService::get(
                $key,
                null,
                $this->option('empresa'),
                $this->option('site'),
                $this->option('ambiente')
            );

            if ($value === null) {
                $this->warn("Configuração '{$key}' não encontrada.");
                return 1;
            }

            $this->info("Chave: {$key}");
            $this->info("Valor: " . (is_array($value) ? json_encode($value) : $value));

            return 0;
        } catch (\Exception $e) {
            $this->error("Erro ao buscar configuração: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Definir valor de configuração
     */
    private function setConfig()
    {
        $key = $this->option('key');
        $value = $this->option('value');

        if (!$key || $value === null) {
            $this->error('As opções --key e --value são obrigatórias para a ação set.');
            return 1;
        }

        try {
            ConfigService::set(
                $key,
                $value,
                $this->option('empresa'),
                $this->option('site'),
                $this->option('ambiente')
            );

            $this->info("Configuração '{$key}' definida com sucesso!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Erro ao definir configuração: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Limpar cache
     */
    private function clearCache()
    {
        try {
            ConfigService::clearCache();
            $this->info('Cache de configurações limpo com sucesso!');
            return 0;
        } catch (\Exception $e) {
            $this->error("Erro ao limpar cache: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Listar configurações
     */
    private function listConfigs()
    {
        try {
            $query = DB::table('config_definitions as cd')
                ->leftJoin('config_groups as cg', 'cd.grupo_id', '=', 'cg.id')
                ->leftJoin('config_values as cv', 'cd.id', '=', 'cv.config_id')
                ->select([
                    'cd.chave',
                    'cd.nome',
                    'cd.tipo',
                    'cv.valor',
                    'cd.valor_padrao',
                    'cg.nome as grupo'
                ]);

            if ($this->option('group')) {
                $query->where('cg.codigo', $this->option('group'));
            }

            if ($this->option('empresa')) {
                $query->where('cd.empresa_id', $this->option('empresa'));
            }

            $configs = $query->orderBy('cg.ordem')->orderBy('cd.ordem')->get();

            if ($configs->isEmpty()) {
                $this->warn('Nenhuma configuração encontrada.');
                return 0;
            }

            $this->table(
                ['Chave', 'Nome', 'Tipo', 'Valor Atual', 'Valor Padrão', 'Grupo'],
                $configs->map(function ($config) {
                    return [
                        $config->chave,
                        $config->nome,
                        $config->tipo,
                        $config->valor ?? '-',
                        $config->valor_padrao ?? '-',
                        $config->grupo ?? '-'
                    ];
                })->toArray()
            );

            return 0;
        } catch (\Exception $e) {
            $this->error("Erro ao listar configurações: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Exportar configurações
     */
    private function exportConfigs()
    {
        try {
            $query = DB::table('config_definitions as cd')
                ->leftJoin('config_groups as cg', 'cd.grupo_id', '=', 'cg.id')
                ->leftJoin('config_values as cv', 'cd.id', '=', 'cv.config_id')
                // ->leftJoin('config_sites as cs', 'cv.site_id', '=', 'cs.id') // Removido
                ->leftJoin('config_environments as ce', 'cv.ambiente_id', '=', 'ce.id')
                ->select([
                    'cd.chave',
                    'cd.nome',
                    'cd.descricao',
                    'cd.tipo',
                    'cd.valor_padrao',
                    'cv.valor',
                    'cg.nome as grupo',
                    // 'cs.codigo as site', // Removido - config_sites não mais usado
                    'ce.codigo as ambiente'
                ]);

            if ($this->option('group')) {
                $query->where('cg.codigo', $this->option('group'));
            }

            if ($this->option('empresa')) {
                $query->where('cd.empresa_id', $this->option('empresa'));
            }

            $configs = $query->orderBy('cg.ordem')->orderBy('cd.ordem')->get();

            $output = $this->option('output') ?? 'configuracoes_' . date('Y-m-d_H-i-s') . '.json';

            file_put_contents($output, json_encode($configs, JSON_PRETTY_PRINT));

            $this->info("Configurações exportadas para: {$output}");
            $this->info("Total de configurações: " . $configs->count());

            return 0;
        } catch (\Exception $e) {
            $this->error("Erro ao exportar configurações: " . $e->getMessage());
            return 1;
        }
    }
}
