<?php

namespace App\Modules\Comerciante\Models\Pessoas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PessoaDepartamento extends Model
{
    protected $table = 'pessoas_departamentos';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'responsavel_id',
        'centro_custo',
        'relacionado_producao',
        'ativo',
        'ordem'
    ];

    protected $casts = [
        'relacionado_producao' => 'boolean',
        'ativo' => 'boolean',
        'ordem' => 'integer'
    ];

    /**
     * Relacionamentos
     */
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'responsavel_id');
    }

    public function cargos(): HasMany
    {
        return $this->hasMany(PessoaCargo::class, 'departamento_id');
    }

    public function funcionarios(): HasMany
    {
        return $this->hasMany(Pessoa::class, 'departamento_id');
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

    public function scopeProducao($query)
    {
        return $query->where('relacionado_producao', true);
    }

    /**
     * Métodos de negócio
     */
    public function getTotalFuncionarios()
    {
        return $this->funcionarios()->count();
    }

    public function getTotalCargos()
    {
        return $this->cargos()->count();
    }

    public function isAtivo()
    {
        return $this->ativo;
    }

    public function temFuncionarios()
    {
        return $this->funcionarios()->exists();
    }

    public function temCargos()
    {
        return $this->cargos()->exists();
    }
}
