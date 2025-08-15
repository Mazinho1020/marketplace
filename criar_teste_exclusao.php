<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TESTE DE EXCLUSÃO ===\n\n";

// Vou criar um registro temporário para testar a exclusão
$registro = DB::table('lancamentos')->insertGetId([
    'empresa_id' => 1,
    'natureza_financeira' => 'receber',
    'situacao_financeira' => 'pendente',
    'descricao' => 'Teste de Exclusão',
    'valor' => 100.00,
    'valor_original' => 100.00,
    'valor_final' => 100.00,
    'data' => '2025-01-13',
    'data_emissao' => '2025-01-13',
    'data_competencia' => '2025-01-13',
    'data_vencimento' => '2025-02-13',
    'usuario_id' => 1,
    'created_at' => now(),
    'updated_at' => now()
]);

echo "Registro criado com ID: $registro\n";
echo "URL para testar exclusão: http://127.0.0.1:8000/comerciantes/empresas/1/financeiro/contas-receber/$registro\n";
echo "Use método DELETE\n";
