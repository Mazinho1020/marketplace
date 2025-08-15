<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lancamentos', function (Blueprint $table) {
            $table->unsignedBigInteger('forma_pagamento_id')->nullable()->after('conta_gerencial_id');
            $table->unsignedBigInteger('bandeira_id')->nullable()->after('forma_pagamento_id');
            $table->text('observacoes_pagamento')->nullable()->after('observacoes');
            $table->unsignedBigInteger('usuario_pagamento_id')->nullable()->after('observacoes_pagamento');

            // Adicionar índices para performance
            $table->index('forma_pagamento_id');
            $table->index('bandeira_id');
            $table->index('usuario_pagamento_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lancamentos', function (Blueprint $table) {
            $table->dropIndex(['forma_pagamento_id']);
            $table->dropIndex(['bandeira_id']);
            $table->dropIndex(['usuario_pagamento_id']);

            $table->dropColumn([
                'forma_pagamento_id',
                'bandeira_id',
                'observacoes_pagamento',
                'usuario_pagamento_id'
            ]);
        });
    }
};
