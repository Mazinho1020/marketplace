<?php

namespace App\Modules\Comerciante\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfigGroup extends Model
{
    protected $table = 'config_groups';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'grupo_pai_id',
        'icone',
        'icone_class',
        'ordem',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer'
    ];

    /**
     * Relacionamento com as definições de configuração
     */
    public function definitions(): HasMany
    {
        return $this->hasMany(ConfigDefinition::class, 'grupo_id')
            ->orderBy('ordem');
    }

    /**
     * Relacionamento com grupo pai
     */
    public function grupoPai(): BelongsTo
    {
        return $this->belongsTo(ConfigGroup::class, 'grupo_pai_id');
    }

    /**
     * Relacionamento com subgrupos
     */
    public function subgrupos(): HasMany
    {
        return $this->hasMany(ConfigGroup::class, 'grupo_pai_id')
            ->orderBy('ordem');
    }

    /**
     * Scope para grupos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para grupos de uma empresa
     */
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para grupos principais (sem pai)
     */
    public function scopePrincipais($query)
    {
        return $query->whereNull('grupo_pai_id');
    }

    /**
     * Obtém árvore completa de grupos
     */
    public static function arvoreCompleta($empresaId)
    {
        return static::empresa($empresaId)
            ->principais()
            ->ativos()
            ->with(['subgrupos' => function ($query) {
                $query->ativos()->orderBy('ordem');
            }])
            ->orderBy('ordem')
            ->get();
    }

    /**
     * Verifica se o grupo tem subgrupos
     */
    public function temSubgrupos()
    {
        return $this->subgrupos()->exists();
    }

    /**
     * Obtém todas as configurações do grupo e subgrupos
     */
    public function todasConfiguracoes()
    {
        $configuracoes = $this->definitions;

        foreach ($this->subgrupos as $subgrupo) {
            $configuracoes = $configuracoes->merge($subgrupo->definitions);
        }

        return $configuracoes;
    }
}
