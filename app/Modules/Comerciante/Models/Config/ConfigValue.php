<?php

namespace App\Modules\Comerciante\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class ConfigValue extends Model
{
    protected $table = 'config_values';

    protected $fillable = [
        'config_definition_id',
        'valor',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com definição
     */
    public function definition(): BelongsTo
    {
        return $this->belongsTo(ConfigDefinition::class, 'config_definition_id');
    }

    /**
     * Relacionamento com usuário criador
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relacionamento com usuário atualizador
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtém valor convertido
     */
    public function getValorConvertido()
    {
        return $this->definition->convertValue($this->valor);
    }

    /**
     * Define valor convertido
     */
    public function setValorConvertido($valor)
    {
        switch ($this->definition->tipo) {
            case 'boolean':
                $this->valor = $valor ? 'true' : 'false';
                break;
            case 'json':
                $this->valor = is_array($valor) ? json_encode($valor) : $valor;
                break;
            default:
                $this->valor = (string) $valor;
        }
    }

    /**
     * Scope para valores de uma empresa
     */
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->whereHas('definition', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        });
    }

    /**
     * Scope para valores de um grupo
     */
    public function scopeGrupo($query, $grupoId)
    {
        return $query->whereHas('definition', function ($q) use ($grupoId) {
            $q->where('grupo_id', $grupoId);
        });
    }
}
