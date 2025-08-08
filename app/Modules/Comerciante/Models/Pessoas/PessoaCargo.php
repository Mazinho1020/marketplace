<?php

namespace App\Modules\Comerciante\Models\Pessoas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PessoaCargo extends Model
{
    protected $table = 'pessoas_cargos';

    protected $fillable = [
        'empresa_id',
        'departamento_id',
        'codigo',
        'nome',
        'descricao',
        'salario_base',
        'salario_maximo',
        'nivel_hierarquico',
        'requer_superior',
        'carga_horaria_semanal',
        'ativo',
        'ordem'
    ];

    protected $casts = [
        'salario_base' => 'decimal:2',
        'salario_maximo' => 'decimal:2',
        'nivel_hierarquico' => 'integer',
        'requer_superior' => 'boolean',
        'carga_horaria_semanal' => 'integer',
        'ativo' => 'boolean',
        'ordem' => 'integer'
    ];

    /**
     * Relacionamentos
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(PessoaDepartamento::class, 'departamento_id');
    }

    public function funcionarios(): HasMany
    {
        return $this->hasMany(Pessoa::class, 'cargo_id');
    }

    /**
     * Scopes
     */
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDepartamento($query, $departamentoId)
    {
        return $query->where('departamento_id', $departamentoId);
    }

    public function scopeNivel($query, $nivel)
    {
        return $query->where('nivel_hierarquico', $nivel);
    }

    /**
     * Métodos de negócio
     */
    public function getTotalFuncionarios()
    {
        return $this->funcionarios()->count();
    }

    public function isAtivo()
    {
        return $this->ativo;
    }

    public function temFuncionarios()
    {
        return $this->funcionarios()->exists();
    }

    public function getFaixaSalarial()
    {
        if ($this->salario_base && $this->salario_maximo) {
            return "R$ " . number_format($this->salario_base, 2, ',', '.') .
                " - R$ " . number_format($this->salario_maximo, 2, ',', '.');
        } elseif ($this->salario_base) {
            return "R$ " . number_format($this->salario_base, 2, ',', '.');
        }

        return 'Não definido';
    }

    public function getCargaHorariaFormatada()
    {
        return $this->carga_horaria_semanal . 'h semanais';
    }
}
