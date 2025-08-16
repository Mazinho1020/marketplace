<?php

namespace App\Models\Financeiro;

use App\Models\Financial\BaseFinancialModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LancamentoItem extends BaseFinancialModel
{
    protected $table = 'lancamento_itens';

    protected $fillable = [
        'lancamento_id', 'produto_id', 'produto_variacao_id',
        'quantidade', 'valor_unitario',
        'observacoes', 'metadados', 'empresa_id'
    ];

    protected $casts = [
        'quantidade' => 'decimal:4',
        'valor_unitario' => 'decimal:4',
        'valor_total' => 'decimal:4',
        'metadados' => 'array',
    ];

    public function lancamento(): BelongsTo
    {
        return $this->belongsTo(Lancamento::class);
    }
}