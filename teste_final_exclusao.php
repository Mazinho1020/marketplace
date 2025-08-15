<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

echo "=== TESTE FINAL DE EXCLUSÃO E REDIRECIONAMENTO ===\n\n";

// Criar um registro de teste
$registro = DB::table('lancamentos')->insertGetId([
    'empresa_id' => 1,
    'natureza_financeira' => 'receber',
    'situacao_financeira' => 'pendente',
    'descricao' => 'Teste Final de Exclusão',
    'valor' => 150.00,
    'valor_original' => 150.00,
    'valor_final' => 150.00,
    'data' => '2025-01-13',
    'data_emissao' => '2025-01-13',
    'data_competencia' => '2025-01-13',
    'data_vencimento' => '2025-02-13',
    'usuario_id' => 1,
    'created_at' => now(),
    'updated_at' => now()
]);

echo "✅ Registro criado com ID: $registro\n";

echo "\n=== AGORA VOCÊ PODE TESTAR ===\n";
echo "1. Abra: http://127.0.0.1:8000/comerciantes/empresas/1/financeiro/contas-receber\n";
echo "2. Procure pelo registro 'Teste Final de Exclusão' (ID: $registro)\n";
echo "3. Clique no botão de excluir (ícone da lixeira)\n";
echo "4. Confirme a exclusão\n";
echo "5. Verifique se:\n";
echo "   - Aparece mensagem de sucesso\n";
echo "   - A página é recarregada/redirecionada\n";
echo "   - O registro não aparece mais na lista\n";
echo "\n✅ Sistema configurado para funcionar tanto com AJAX quanto com form submit!\n";
