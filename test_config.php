<?php

use App\Models\Config\ConfigDefinition;

try {
    echo "Testando conexão com ConfigDefinition...\n";

    $count = ConfigDefinition::count();
    echo "Total de configurações: {$count}\n";

    if ($count > 0) {
        $primeiro = ConfigDefinition::first();
        echo "Primeira configuração:\n";
        echo "ID: {$primeiro->id}\n";
        echo "Chave: {$primeiro->chave}\n";
        echo "Tipo: {$primeiro->tipo_dado}\n";
        echo "Grupo ID: {$primeiro->grupo_id}\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
}
