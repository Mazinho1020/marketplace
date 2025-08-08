<?php

namespace App\Modules\Comerciante\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConfigDefinition extends Model
{
    protected $table = 'config_definitions';

    protected $fillable = [
        'empresa_id',
        'chave',
        'nome',
        'descricao',
        'tipo',
        'grupo_id',
        'valor_padrao',
        'opcoes',
        'validacao',
        'obrigatorio',
        'editavel',
        'ordem',
        'dica',
        'categoria',
        'ativo'
    ];

    protected $casts = [
        'obrigatorio' => 'boolean',
        'editavel' => 'boolean',
        'ativo' => 'boolean',
        'ordem' => 'integer',
        'opcoes' => 'array'
    ];

    /**
     * Relacionamento com grupo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(ConfigGroup::class, 'grupo_id');
    }

    /**
     * Relacionamento com valor atual
     */
    public function value(): HasOne
    {
        return $this->hasOne(ConfigValue::class, 'config_definition_id');
    }

    /**
     * Relacionamento com histórico
     */
    public function history(): HasMany
    {
        return $this->hasMany(ConfigHistory::class, 'config_definition_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope para configurações ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para configurações de uma empresa
     */
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para configurações editáveis
     */
    public function scopeEditaveis($query)
    {
        return $query->where('editavel', true);
    }

    /**
     * Scope para configurações obrigatórias
     */
    public function scopeObrigatorias($query)
    {
        return $query->where('obrigatorio', true);
    }

    /**
     * Obtém valor atual ou padrão
     */
    public function getValorAtual()
    {
        if ($this->value) {
            return $this->convertValue($this->value->valor);
        }

        return $this->convertValue($this->valor_padrao);
    }

    /**
     * Converte valor para o tipo correto
     */
    public function convertValue($valor)
    {
        if ($valor === null) {
            return null;
        }

        switch ($this->tipo) {
            case 'boolean':
                return filter_var($valor, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $valor;
            case 'float':
                return (float) $valor;
            case 'json':
                return is_string($valor) ? json_decode($valor, true) : $valor;
            default:
                return $valor;
        }
    }

    /**
     * Valida valor conforme regras
     */
    public function validarValor($valor)
    {
        $errors = [];

        // Validação obrigatório
        if ($this->obrigatorio && ($valor === null || $valor === '')) {
            $errors[] = "Campo {$this->nome} é obrigatório";
        }

        // Validação por tipo
        switch ($this->tipo) {
            case 'boolean':
                if ($valor !== null && !is_bool($valor) && !in_array($valor, ['true', 'false', '1', '0', 1, 0])) {
                    $errors[] = "Campo {$this->nome} deve ser boolean";
                }
                break;

            case 'integer':
                if ($valor !== null && (!is_numeric($valor) || !is_int((int) $valor))) {
                    $errors[] = "Campo {$this->nome} deve ser um número inteiro";
                }
                break;

            case 'float':
                if ($valor !== null && !is_numeric($valor)) {
                    $errors[] = "Campo {$this->nome} deve ser um número";
                }
                break;

            case 'json':
                if ($valor !== null && !is_array($valor) && json_decode($valor) === null) {
                    $errors[] = "Campo {$this->nome} deve ser um JSON válido";
                }
                break;

            case 'select':
                if ($valor !== null && $this->opcoes && !in_array($valor, array_keys($this->opcoes))) {
                    $errors[] = "Valor inválido para {$this->nome}";
                }
                break;
        }

        // Validação customizada
        if ($this->validacao && $valor !== null) {
            $pattern = $this->validacao;
            if (!preg_match($pattern, $valor)) {
                $errors[] = "Formato inválido para {$this->nome}";
            }
        }

        return $errors;
    }

    /**
     * Obtém opções formatadas para select
     */
    public function getOpcoesFormatadas()
    {
        if (!$this->opcoes) {
            return [];
        }

        $opcoes = [];
        foreach ($this->opcoes as $key => $value) {
            $opcoes[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $opcoes;
    }

    /**
     * Verifica se é configuração de sistema
     */
    public function isSistema()
    {
        return !$this->editavel;
    }
}
