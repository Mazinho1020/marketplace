<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Remover tabela intermediária desnecessária se existir
        Schema::dropIfExists('conta_gerencial_naturezas');

        // 2. Verificar e ajustar tabela categorias_conta se necessário
        if (Schema::hasTable('categorias_conta')) {
            Schema::table('categorias_conta', function (Blueprint $table) {
                if (!Schema::hasColumn('categorias_conta', 'cor')) {
                    $table->string('cor', 7)->default('#007bff')->after('descricao');
                }
                if (!Schema::hasColumn('categorias_conta', 'icone')) {
                    $table->string('icone', 50)->nullable()->after('cor');
                }
                if (!Schema::hasColumn('categorias_conta', 'e_custo')) {
                    $table->boolean('e_custo')->default(false)->after('icone');
                }
                if (!Schema::hasColumn('categorias_conta', 'e_despesa')) {
                    $table->boolean('e_despesa')->default(false)->after('e_custo');
                }
                if (!Schema::hasColumn('categorias_conta', 'e_receita')) {
                    $table->boolean('e_receita')->default(false)->after('e_despesa');
                }
            });
        } else {
            // Criar tabela categorias_conta se não existir
            Schema::create('categorias_conta', function (Blueprint $table) {
                $table->id();
                $table->string('nome', 50);
                $table->string('nome_completo', 100);
                $table->text('descricao')->nullable();
                $table->string('cor', 7)->default('#007bff');
                $table->string('icone', 50)->nullable();
                $table->boolean('e_custo')->default(false);
                $table->boolean('e_despesa')->default(false);
                $table->boolean('e_receita')->default(false);
                $table->boolean('ativo')->default(true);
                $table->unsignedBigInteger('empresa_id')->nullable();

                // Sync fields
                $table->timestamp('sync_data')->useCurrent();
                $table->string('sync_hash', 32)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                $table->timestamps();

                $table->index(['empresa_id', 'ativo']);
                $table->index(['sync_status', 'sync_data']);
                $table->index(['e_custo', 'e_despesa', 'e_receita']);
            });
        }

        // 3. Adicionar relacionamento direto na conta_gerencial se necessário
        if (Schema::hasTable('conta_gerencial')) {
            Schema::table('conta_gerencial', function (Blueprint $table) {
                if (!Schema::hasColumn('conta_gerencial', 'categoria_id')) {
                    $table->unsignedBigInteger('categoria_id')->nullable()->after('tipo_id');
                    $table->index(['categoria_id']);
                }
            });
        }

        // 4. Migrar dados existentes se houver tabela intermediária
        $this->migrarDados();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover modificações se necessário
        if (Schema::hasTable('conta_gerencial')) {
            Schema::table('conta_gerencial', function (Blueprint $table) {
                $table->dropIndex(['categoria_id']);
                $table->dropColumn('categoria_id');
            });
        }

        // Recriar tabela intermediária se necessário (não recomendado)
        Schema::create('conta_gerencial_naturezas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conta_gerencial_id');
            $table->unsignedBigInteger('natureza_id');
            $table->timestamps();

            $table->index(['conta_gerencial_id', 'natureza_id']);
        });
    }

    private function migrarDados()
    {
        // Verificar se existe tabela intermediária com dados para migrar
        if (Schema::hasTable('conta_gerencial_naturezas')) {
            try {
                $relacionamentos = DB::table('conta_gerencial_naturezas')->get();

                foreach ($relacionamentos as $rel) {
                    DB::table('conta_gerencial')
                        ->where('id', $rel->conta_gerencial_id)
                        ->update(['categoria_id' => $rel->natureza_id]);
                }

                echo "Migrados " . count($relacionamentos) . " relacionamentos.\n";
            } catch (Exception $e) {
                echo "Erro na migração de dados: " . $e->getMessage() . "\n";
            }
        }
    }
};
