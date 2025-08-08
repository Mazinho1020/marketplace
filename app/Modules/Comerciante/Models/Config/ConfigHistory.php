<?php

namespace App\Modules\Comerciante\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class ConfigHistory extends Model
{
    protected $table = 'config_history';

    protected $fillable = [
        'config_definition_id',
        'valor_anterior',
        'valor_novo',
        'usuario_id',
        'ip_address',
        'user_agent',
        'observacoes'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    /**
     * Relacionamento com definição
     */
    public function definition(): BelongsTo
    {
        return $this->belongsTo(ConfigDefinition::class, 'config_definition_id');
    }

    /**
     * Relacionamento com usuário
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Scope para histórico de uma empresa
     */
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->whereHas('definition', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        });
    }

    /**
     * Scope para histórico de uma configuração
     */
    public function scopeConfiguracao($query, $chave, $empresaId = null)
    {
        return $query->whereHas('definition', function ($q) use ($chave, $empresaId) {
            $q->where('chave', $chave);
            if ($empresaId) {
                $q->where('empresa_id', $empresaId);
            }
        });
    }

    /**
     * Scope para histórico recente
     */
    public function scopeRecente($query, $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    /**
     * Obtém valor anterior convertido
     */
    public function getValorAnteriorConvertido()
    {
        return $this->definition->convertValue($this->valor_anterior);
    }

    /**
     * Obtém valor novo convertido
     */
    public function getValorNovoConvertido()
    {
        return $this->definition->convertValue($this->valor_novo);
    }

    /**
     * Verifica se houve mudança significativa
     */
    public function houveMudancaSignificativa()
    {
        return $this->valor_anterior !== $this->valor_novo;
    }

    /**
     * Obtém resumo da alteração
     */
    public function getResumoAlteracao()
    {
        return [
            'configuracao' => $this->definition->nome,
            'chave' => $this->definition->chave,
            'valor_anterior' => $this->getValorAnteriorConvertido(),
            'valor_novo' => $this->getValorNovoConvertido(),
            'usuario' => $this->usuario->name ?? 'Sistema',
            'data' => $this->created_at->format('d/m/Y H:i:s'),
            'ip' => $this->ip_address
        ];
    }
}
