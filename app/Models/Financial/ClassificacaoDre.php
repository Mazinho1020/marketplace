<?php

namespace App\Models\Financial;

use App\Models\Financial\BaseFinancialModel;
use App\Models\User;
use App\Traits\HasSync;
use App\Traits\HasCompany;
use App\Enums\NaturezaContaEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassificacaoDre extends BaseFinancialModel
{
    use HasSync, HasCompany;

    protected $table = 'classificacoes_dre';

    protected $fillable = [
        'codigo',
        'nivel',
        'classificacao_pai_id',
        'nome',
        'descricao',
        'tipo_id',
        'ativo',
        'ordem_exibicao',
        'empresa_id',
        'sync_status',
        'sync_hash',
        'sync_data',
    ];

    protected $casts = [
        'nivel' => 'integer',
        'ordem_exibicao' => 'integer',
        'ativo' => 'boolean',
        'sync_data' => 'datetime',
    ];

    /**
     * Relacionamentos
     */
    public function classificacaoPai(): BelongsTo
    {
        return $this->belongsTo(ClassificacaoDre::class, 'classificacao_pai_id');
    }

    public function filhos(): HasMany
    {
        return $this->hasMany(ClassificacaoDre::class, 'classificacao_pai_id')
            ->orderBy('ordem_exibicao')
            ->orderBy('codigo')
            ->orderBy('nome');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(Tipo::class, 'tipo_id');
    }

    public function contasGerenciais(): HasMany
    {
        return $this->hasMany(ContaGerencial::class, 'classificacao_dre_id');
    }

    /**
     * Scopes
     */
    public function scopeRaizes($query)
    {
        return $query->whereNull('classificacao_pai_id');
    }

    public function scopeFilhos($query, int $paiId)
    {
        return $query->where('classificacao_pai_id', $paiId);
    }

    public function scopePorNivel($query, int $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    public function scopeOrdenadoPorHierarquia($query)
    {
        return $query->orderBy('nivel')
            ->orderBy('ordem_exibicao')
            ->orderBy('codigo')
            ->orderBy('nome');
    }

    /**
     * Métodos auxiliares
     */
    public function getCodigoFormatadoAttribute(): string
    {
        return $this->codigo ?: str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    public function getNomeCompletoAttribute(): string
    {
        $codigo = $this->codigo_formatado;
        return "{$codigo} - {$this->nome}";
    }

    public function getHierarquiaAttribute(): string
    {
        $hierarquia = [];
        $atual = $this;

        while ($atual) {
            array_unshift($hierarquia, $atual->nome);
            $atual = $atual->classificacaoPai;
        }

        return implode(' > ', $hierarquia);
    }

    public function getIndentacaoAttribute(): string
    {
        return str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $this->nivel - 1);
    }

    /**
     * Verifica se tem filhos
     */
    public function temFilhos(): bool
    {
        return $this->filhos()->exists();
    }

    /**
     * Verifica se é raiz
     */
    public function isRaiz(): bool
    {
        return is_null($this->classificacao_pai_id);
    }

    /**
     * Retorna todos os descendentes
     */
    public function descendentes()
    {
        return $this->filhos()->with('descendentes');
    }

    /**
     * Retorna todos os ancestrais
     */
    public function ancestrais()
    {
        $ancestrais = collect();
        $atual = $this->classificacaoPai;

        while ($atual) {
            $ancestrais->prepend($atual);
            $atual = $atual->classificacaoPai;
        }

        return $ancestrais;
    }
}
