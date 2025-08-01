<?php

return [
    'debug_config' => function () {
        try {
            echo "=== DEBUG CONFIGURAÇÕES ===\n";

            // Verificar se a classe existe
            if (!class_exists('App\Models\Config\ConfigDefinition')) {
                echo "❌ Classe ConfigDefinition não encontrada\n";
                return;
            }

            echo "✅ Classe ConfigDefinition encontrada\n";

            // Testar conexão básica
            $count = App\Models\Config\ConfigDefinition::count();
            echo "Total de configurações: {$count}\n";

            // Testar query simples
            $configs = App\Models\Config\ConfigDefinition::limit(3)->get();
            echo "Configurações encontradas: " . $configs->count() . "\n";

            foreach ($configs as $config) {
                echo "- {$config->chave} ({$config->tipo_dado})\n";
            }
        } catch (Exception $e) {
            echo "❌ ERRO: " . $e->getMessage() . "\n";
            echo "Arquivo: " . $e->getFile() . "\n";
            echo "Linha: " . $e->getLine() . "\n";
        }
    }
];
