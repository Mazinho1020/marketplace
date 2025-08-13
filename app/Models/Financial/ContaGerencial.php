<?php

namespace App\Models\Financial;

use App\Models\Financial\BaseFinancialModel;
use App\Models\User;
use App\Traits\HasSync;
use App\Traits\HasCompany;
use App\Enums\NaturezaContaEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContaGerencial extends BaseFinancialModel
{
    use HasSync, HasCompany;

    protected $table = 'conta_gerencial';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'ativo',
        'nivel',
        'ordem_exibicao',
        'usuario_id',
        'empresa_id',
        'classificacao_dre_id',
        'tipo_id',
        'categoria_id',
        'conta_pai_id',
        'natureza',
        'aceita_lancamento',
        'e_sintetica',
        'cor',
        'icone',
        'e_custo',
        'e_despesa',
        'e_receita',
        'grupo_dre',
        'sync_status',
        'sync_hash',
        'sync_data',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'nivel' => 'integer',
        'ordem_exibicao' => 'integer',
        'conta_pai_id' => 'integer',
        'natureza' => NaturezaContaEnum::class,
        'aceita_lancamento' => 'boolean',
        'e_sintetica' => 'boolean',
        'e_custo' => 'boolean',
        'e_despesa' => 'boolean',
        'e_receita' => 'boolean',
        'sync_data' => 'datetime',
    ];

    /**
     * Relacionamentos
     */
    public function contaPai(): BelongsTo
    {
        return $this->belongsTo(ContaGerencial::class, 'conta_pai_id');
    }

    public function filhos(): HasMany
    {
        return $this->hasMany(ContaGerencial::class, 'conta_pai_id')
            ->orderBy('nome');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function classificacaoDre(): BelongsTo
    {
        return $this->belongsTo(ClassificacaoDre::class, 'classificacao_dre_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(Tipo::class, 'tipo_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaContaGerencial::class, 'categoria_id');
    }

    /**
     * Scopes
     */
    public function scopeRaizes($query)
    {
        return $query->whereNull('conta_pai_id');
    }

    public function scopeFilhos($query, int $paiId)
    {
        return $query->where('conta_pai_id', $paiId);
    }

    public function scopePorNivel($query, int $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    public function scopePermiteLancamento($query)
    {
        return $query->where('aceita_lancamento', true);
    }

    public function scopeNaoPermiteLancamento($query)
    {
        return $query->where('aceita_lancamento', false);
    }

    public function scopeSinteticas($query)
    {
        return $query->where('e_sintetica', true);
    }

    public function scopeAnaliticas($query)
    {
        return $query->where('e_sintetica', false);
    }

    public function scopePorNatureza($query, NaturezaContaEnum $natureza)
    {
        return $query->where('natureza', $natureza);
    }

    public function scopeDebito($query)
    {
        return $query->where('natureza', NaturezaContaEnum::DEBITO);
    }

    public function scopeCredito($query)
    {
        return $query->where('natureza', NaturezaContaEnum::CREDITO);
    }

    public function scopeOrdenadoPorHierarquia($query)
    {
        return $query->orderBy('ordem_exibicao')
            ->orderBy('codigo')
            ->orderBy('nome');
    }

    // Scopes por categoria/tipo
    public function scopeCustos($query)
    {
        return $query->where('e_custo', true);
    }

    public function scopeDespesas($query)
    {
        return $query->where('e_despesa', true);
    }

    public function scopeReceitas($query)
    {
        return $query->where('e_receita', true);
    }

    /**
     * Métodos auxiliares
     */
    public function getCodigoFormatadoAttribute(): string
    {
        return $this->codigo ?: str_pad($this->id, 4, '0', STR_PAD_LEFT);
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
            $atual = $atual->contaPai;
        }

        return implode(' > ', $hierarquia);
    }

    public function getIndentacaoAttribute(): string
    {
        return str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $this->nivel - 1);
    }

    public function getNaturezaLabelAttribute(): string
    {
        return $this->natureza?->label() ?? 'Não definida';
    }

    public function getNaturezaColorAttribute(): string
    {
        return $this->natureza?->color() ?? 'secondary';
    }

    public function getNaturezaIconAttribute(): string
    {
        return $this->natureza?->icon() ?? 'circle';
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
        return is_null($this->conta_pai_id);
    }

    /**
     * Verifica se pode ser excluída
     */
    public function podeSerExcluida(): bool
    {
        return !$this->temFilhos() && !$this->temLancamentos();
    }

    /**
     * Verifica se tem lançamentos (método placeholder)
     */
    public function temLancamentos(): bool
    {
        // TODO: Implementar quando criar a tabela de lançamentos
        return false;
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
        $atual = $this->contaPai;

        while ($atual) {
            $ancestrais->prepend($atual);
            $atual = $atual->contaPai;
        }

        return $ancestrais;
    }

    /**
     * Define a natureza automaticamente baseada na categoria
     */
    public function definirNaturezaAutomatica(): void
    {
        if (!$this->natureza && $this->categoria) {
            if ($this->categoria->e_receita) {
                $this->natureza = NaturezaContaEnum::CREDITO;
            } elseif ($this->categoria->e_despesa || $this->categoria->e_custo) {
                $this->natureza = NaturezaContaEnum::DEBITO;
            }
        }
    }

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($conta) {
            // Define nível automaticamente
            if (!$conta->nivel) {
                $conta->nivel = $conta->conta_pai_id
                    ? $conta->contaPai->nivel + 1
                    : 1;
            }

            // Define natureza automaticamente
            $conta->definirNaturezaAutomatica();
        });

        static::updating(function ($conta) {
            // Atualiza natureza se categoria mudou
            if ($conta->isDirty('categoria_id')) {
                $conta->definirNaturezaAutomatica();
            }
        });
    }
}
