<?php

/**
 * Gerador de Migrations Laravel
 * Converte CREATE TABLE em migrations Laravel
 */

// Lê o arquivo SQL
$sqlFile = 'C:\\Users\\leoma\\Downloads\\teste2 - Copia.sql';
$sql = file_get_contents($sqlFile);

echo "📖 Arquivo SQL carregado\n";

// Extrai CREATE TABLE
preg_match_all('/CREATE TABLE(?:\s+IF\s+NOT\s+EXISTS)?\s+`([^`]+)`\s*\(([^;]*)\)[^;]*;/is', $sql, $matches);

$tableNames = $matches[1];
$tableDefinitions = $matches[2];

echo "🔍 Encontradas " . count($tableNames) . " tabelas\n";

// Cria diretório de migrations se não existir
$migrationDir = 'database/migrations';
if (!is_dir($migrationDir)) {
    mkdir($migrationDir, 0755, true);
}

$timestamp = date('Y_m_d_His');

foreach ($tableNames as $index => $tableName) {
    $className = 'Create' . str_replace('_', '', ucwords($tableName, '_')) . 'Table';
    $migrationFile = $migrationDir . "/{$timestamp}_create_{$tableName}_table.php";

    // Incrementa timestamp para cada migration
    $timestamp = date('Y_m_d_His', strtotime("+1 second"));

    $migrationContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Só cria se não existir (SEGURO)
        if (!Schema::hasTable('$tableName')) {
            Schema::create('$tableName', function (Blueprint \$table) {
                \$table->comment('Tabela migrada do sistema legado');
                // TODO: Definir colunas específicas baseado na estrutura original
                \$table->id();
                \$table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('$tableName');
    }
};
PHP;

    file_put_contents($migrationFile, $migrationContent);
    echo "✅ Migration criada: $migrationFile\n";
}

echo "\n🎉 Migrations geradas!\n";
echo "📝 Para executar: php artisan migrate\n";
echo "🔄 Para reverter: php artisan migrate:rollback\n";
