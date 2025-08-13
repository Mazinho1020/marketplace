<?php

namespace App\Models\Financial;

use App\Models\Core\BaseModel;
use App\Traits\HasSync;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContaGerencialNatureza extends BaseModel
{
    use HasSync, HasCompany;

    protected $table = 'conta_gerencial_natureza';

    protected $fillable = [
        'nome',
        'nome_completo',
        'descricao',
        'ativo',
        'empresa_id',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // RELACIONAMENTOS
    public function contasGerenciais(): BelongsToMany
    {
        return $this->belongsToMany(
            ContaGerencial::class,
            'conta_gerencial_naturezas',
            'natureza_id',
            'conta_gerencial_id'
        )->withPivot(['empresa_id'])
         ->withTimestamps();
    }

    // SCOPES
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    // MÉTODOS DE NEGÓCIO
    public function desativar(): bool
    {
        if ($this->contasGerenciais()->exists()) {
            throw new \InvalidArgumentException('Não é possível desativar uma natureza que possui contas vinculadas');
        }

        return $this->update(['ativo' => false]);
    }
}