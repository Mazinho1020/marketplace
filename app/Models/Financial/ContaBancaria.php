<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Empresa;

class ContaBancaria extends Model
{
    protected $table = 'conta_bancaria';

    protected $fillable = [
        'empresa_id',
        'nome_conta',
        'banco',
        'agencia',
        'numero_conta',
        'saldo',
        'codigo_sistema',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    /**
     * Relacionamento com empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relacionamento com recebimentos
     */
    public function recebimentos(): HasMany
    {
        return $this->hasMany(Recebimento::class, 'conta_bancaria_id');
    }

    /**
     * Relacionamento com pagamentos
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'conta_bancaria_id');
    }

    /**
     * Scope para contas ativas - remover até implementar campo ativo
     */
    /*
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }
    */

    /**
     * Scope para empresa específica
     */
    public function scopeEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Nome completo da conta
     */
    public function getNomeCompletoAttribute(): string
    {
        return $this->banco . ' - Ag: ' . $this->agencia . ' Cc: ' . $this->numero_conta;
    }
}
